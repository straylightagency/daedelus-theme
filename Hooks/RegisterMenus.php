<?php
namespace Daedelus\Theme\Hooks;

use Daedelus\Framework\Hooks\Hook;
use Daedelus\Support\Actions;

class RegisterMenus extends Hook
{
    public function register():void
    {
        Actions::add( 'after_setup_theme', function () {
            register_nav_menus( config('theme.menus', [] ) );
        } );
    }
}