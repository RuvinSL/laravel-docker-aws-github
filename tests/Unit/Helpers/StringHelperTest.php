<?php

namespace Tests\Unit\Helpers;

use App\Helpers\StringHelper;
use Tests\TestCase;

class StringHelperTest extends TestCase
{
    /** @test */
    public function it_converts_string_to_slug()
    {
        $this->assertEquals('hello-world', StringHelper::toSlug('Hello World'));
        $this->assertEquals('laravel-php', StringHelper::toSlug('Laravel & PHP'));
        $this->assertEquals('test-123', StringHelper::toSlug('Test @#$ 123'));
    }

    /** @test */
    public function it_truncates_string_correctly()
    {
        $text = 'This is a very long string that needs to be truncated';
        
        $this->assertEquals('This is a...', StringHelper::truncate($text, 10));
        $this->assertEquals('This is a very long***', StringHelper::truncate($text, 20, '***'));
        $this->assertEquals($text, StringHelper::truncate($text, 100));
    }

    /** @test */
    public function it_masks_sensitive_data()
    {
        $this->assertEquals('john****@example.com', StringHelper::maskEmail('john.doe@example.com'));
        $this->assertEquals('****5678', StringHelper::maskCreditCard('1234567812345678'));
        $this->assertEquals('+1******890', StringHelper::maskPhone('+1234567890'));
    }

    /** @test */
    public function it_generates_random_string_with_correct_length()
    {
        $string = StringHelper::random(10);
        $this->assertEquals(10, strlen($string));
        
        $string = StringHelper::random(32);
        $this->assertEquals(32, strlen($string));
        
        // Test uniqueness
        $string1 = StringHelper::random(16);
        $string2 = StringHelper::random(16);
        $this->assertNotEquals($string1, $string2);
    }
}