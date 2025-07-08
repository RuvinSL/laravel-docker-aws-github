<?php

namespace Tests\Integration\External;

use App\Services\PaymentGateway;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PaymentGatewayTest extends TestCase
{
    use RefreshDatabase;

    private PaymentGateway $gateway;

    protected function setUp(): void
    {
        parent::setUp();
        $this->gateway = new PaymentGateway(config('services.payment.key'));
    }

    /** @test */
    public function it_processes_successful_payment()
    {
        Http::fake([
            'api.payment-gateway.com/charge' => Http::response([
                'success' => true,
                'transaction_id' => 'txn_123456',
                'status' => 'completed',
                'amount' => 99.99
            ], 200)
        ]);

        $order = Order::factory()->create(['total' => 99.99]);

        $result = $this->gateway->charge($order, [
            'card_number' => '4111111111111111',
            'exp_month' => '12',
            'exp_year' => '2025',
            'cvv' => '123'
        ]);

        $this->assertTrue($result->isSuccessful());
        $this->assertEquals('txn_123456', $result->getTransactionId());
        
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'transaction_id' => 'txn_123456',
            'amount' => 99.99,
            'status' => 'completed'
        ]);
    }

    /** @test */
    public function it_handles_declined_payment()
    {
        Http::fake([
            'api.payment-gateway.com/charge' => Http::response([
                'success' => false,
                'error' => 'Card declined',
                'error_code' => 'CARD_DECLINED'
            ], 402)
        ]);

        $order = Order::factory()->create(['total' => 99.99]);

        $result = $this->gateway->charge($order, [
            'card_number' => '4000000000000002',
            'exp_month' => '12',
            'exp_year' => '2025',
            'cvv' => '123'
        ]);

        $this->assertFalse($result->isSuccessful());
        $this->assertEquals('Card declined', $result->getError());
        
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'status' => 'failed',
            'error_message' => 'Card declined'
        ]);
    }

    /** @test */
    public function it_retries_on_network_failure()
    {
        Http::fake([
            'api.payment-gateway.com/charge' => Http::sequence()
                ->pushStatus(500) // First attempt fails
                ->pushStatus(500) // Second attempt fails
                ->push([          // Third attempt succeeds
                    'success' => true,
                    'transaction_id' => 'txn_123456',
                    'status' => 'completed'
                ])
        ]);

        $order = Order::factory()->create(['total' => 99.99]);

        $result = $this->gateway->charge($order, [
            'card_number' => '4111111111111111',
            'exp_month' => '12',
            'exp_year' => '2025',
            'cvv' => '123'
        ]);

        $this->assertTrue($result->isSuccessful());
        
        // Verify retry attempts were logged
        $this->assertDatabaseHas('payment_logs', [
            'order_id' => $order->id,
            'action' => 'retry',
            'attempt' => 3
        ]);
    }

    /** @test */
    public function it_implements_idempotency_for_payments()
    {
        Http::fake([
            'api.payment-gateway.com/charge' => Http::response([
                'success' => true,
                'transaction_id' => 'txn_123456',
                'status' => 'completed'
            ])
        ]);

        $order = Order::factory()->create(['total' => 99.99]);
        $idempotencyKey = 'order_' . $order->id;

        // First charge
        $result1 = $this->gateway->charge($order, [
            'card_number' => '4111111111111111',
            'exp_month' => '12',
            'exp_year' => '2025',
            'cvv' => '123'
        ], $idempotencyKey);

        // Second charge with same idempotency key
        $result2 = $this->gateway->charge($order, [
            'card_number' => '4111111111111111',
            'exp_month' => '12',
            'exp_year' => '2025',
            'cvv' => '123'
        ], $idempotencyKey);

        // Should return the same transaction
        $this->assertEquals($result1->getTransactionId(), $result2->getTransactionId());
        
        // Should only have one payment record
        $this->assertEquals(1, Payment::where('order_id', $order->id)->count());
    }
}