<?php

namespace App\Orchid\Screens;

use App\Models\Product;
use Illuminate\Http\Request;
use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Alert;

class ProductEditScreen extends Screen
{
    public $product;

    public function query(Product $product): iterable
    {
        return [
            'product' => $product
        ];
    }

    public function name(): ?string
    {
        return $this->product->exists ? 'Edit Product' : 'Create Product';
    }

    public function description(): ?string
    {
        return 'Product details';
    }

    public function commandBar(): iterable
    {
        return [
            Button::make('Save Product')
                ->icon('check')
                ->method('createOrUpdate')
                ->canSee(!$this->product->exists),

            Button::make('Update')
                ->icon('note')
                ->method('createOrUpdate')
                ->canSee($this->product->exists),

            Button::make('Delete')
                ->icon('trash')
                ->method('remove')
                ->canSee($this->product->exists)
                ->confirm('Are you sure you want to delete this product?'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::rows([
                Input::make('product.name')
                    ->title('Product Name')
                    ->placeholder('Enter product name')
                    ->required()
                    ->max(255),

                Input::make('product.sku')
                    ->title('SKU')
                    ->placeholder('Enter SKU')
                    ->required()
                    ->max(255),

                TextArea::make('product.description')
                    ->title('Description')
                    ->placeholder('Enter product description')
                    ->rows(3),

                Input::make('product.price')
                    ->title('Price')
                    ->placeholder('0.00')
                    ->required()
                    ->type('number')
                    ->step(0.01)
                    ->min(0),

                Input::make('product.quantity')
                    ->title('Quantity')
                    ->placeholder('0')
                    ->required()
                    ->type('number')
                    ->min(0),

                CheckBox::make('product.is_active')
                    ->value(1)
                    ->title('Active')
                    ->placeholder('Product is active')
                    ->sendTrueOrFalse(),
            ])
        ];
    }

    public function createOrUpdate(Product $product, Request $request)
    {
        $request->validate([
            'product.name' => 'required|max:255',
            'product.sku' => 'required|max:255|unique:products,sku,' . $product->id,
            'product.price' => 'required|numeric|min:0',
            'product.quantity' => 'required|integer|min:0',
        ]);

        $product->fill($request->get('product'))->save();

        Alert::info('Product was saved successfully.');

        return redirect()->route('platform.product.list');
    }

    public function remove(Product $product)
    {
        $product->delete();

        Alert::info('Product was removed.');

        return redirect()->route('platform.product.list');
    }
}
