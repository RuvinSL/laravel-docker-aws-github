<?php

namespace App\Orchid\Layouts;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class UserListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = '';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('name', 'Name')->sort(),
            TD::make('email', 'Email')->sort(),
            TD::make('created_at', 'Created')->sort(),
        ];
    }
}
