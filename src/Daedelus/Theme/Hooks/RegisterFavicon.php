<?php
namespace Daedelus\Theme\Hooks;

use Daedelus\Fields\Fields;
use Daedelus\Foundation\Hooks\Hook;
use Daedelus\Support\Actions;

class RegisterFavicon extends Hook
{
    /**
     * @return void
     */
    public function register():void
    {
        Actions::remove(  'do_favicon', 'do_favicon' );

        Actions::add( 'do_favicon', function () {
            $favicon = site_url( config('theme.favicon') );

            wp_redirect( get_site_icon_url( 32, $favicon ) );
            exit;
        } );
    }
}