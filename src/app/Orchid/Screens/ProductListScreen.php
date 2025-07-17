<?php

namespace App\Orchid\Screens;

use App\Models\Product;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Support\Facades\Toast;

class ProductListScreen extends Screen
{
    public function query(): iterable
    {
        return [
            //'products' => Product::filters()->defaultSort('id', 'desc')->paginate(10)
            'products' => Product::orderBy('id', 'desc')->paginate(10)
        ];
    }

    public function name(): ?string
    {
        return 'Product Management';
    }

    public function description(): ?string
    {
        return 'List of all products';
    }

    public function commandBar(): iterable
    {
        return [
            Link::make('Create Product')
                ->icon('plus')
                ->route('platform.product.edit')
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::table('products', [
                TD::make('id', 'ID')
                    ->sort()
                    ->cantHide()
                    ->filter(TD::FILTER_NUMERIC),

                TD::make('name', 'Name')
                    ->sort()
                    ->cantHide()
                    ->filter(TD::FILTER_TEXT),

                TD::make('sku', 'SKU')
                    ->sort()
                    ->filter(TD::FILTER_TEXT),

                TD::make('price', 'Price')
                    ->sort()
                    ->render(function (Product $product) {
                        return '$' . number_format($product->price, 2);
                    }),

                TD::make('quantity', 'Quantity')
                    ->sort()
                    ->align(TD::ALIGN_CENTER),

                TD::make('is_active', 'Status')
                    ->sort()
                    ->render(function (Product $product) {
                        return $product->is_active
                            ? '<span class="badge badge-success">Active</span>'
                            : '<span class="badge badge-danger">Inactive</span>';
                    }),

                TD::make('created_at', 'Created')
                    ->sort()
                    ->render(function (Product $product) {
                        return $product->created_at->toDateString();
                    }),

                TD::make(__('Actions'))
                    ->align(TD::ALIGN_CENTER)
                    ->width('100px')
                    ->render(function (Product $product) {
                        return DropDown::make()
                            ->icon('options-vertical')
                            ->list([
                                Link::make(__('Edit'))
                                    ->route('platform.product.edit', $product->id)
                                    ->icon('pencil'),

                                Button::make(__('Delete'))
                                    ->icon('trash')
                                    ->confirm(__('Are you sure you want to delete this product?'))
                                    ->method('remove', [
                                        'id' => $product->id,
                                    ]),
                            ]);
                    }),
            ]),
        ];
    }

    public function remove(Product $product)
    {
        $product->delete();

        Toast::info(__('Product was removed'));
    }
}
