<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;

use App\Models\User;
use App\Orchid\Layouts\UserListLayout;


class UserListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
  'users' => User::with('roles')
                ->filters(UserListLayout::class)
                ->defaultSort('id', 'desc')
                ->paginate(),


        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'UserListScreen';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [UserListLayout::class];
    }
}
