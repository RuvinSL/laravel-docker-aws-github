<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class WelcomeTest extends DuskTestCase
{
    public function test_welcome_page_loads()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                   // ->assertSee('Laravel')
                    ->assertTitle('Laravel');
        });
    }

    public function test_navigation_works()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->clickLink('Documentation')
                    ->assertUrlContains('laravel.com');
        });
    }
}
