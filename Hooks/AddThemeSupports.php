<?php
namespace Daedelus\Theme\Hooks;

use Daedelus\Framework\Hooks\Hook;
use Daedelus\Support\Actions;

class AddThemeSupports extends Hook
{
    /**
     * @return void
     */
    public function register():void
    {
        Actions::add( 'after_setup_theme', function () {
			$supports = config('theme.supports');

            if ( isset( $supports['enable'] ) ) {
                foreach ( $supports['enable'] as $key => $support ) {
                    if ( is_int( $key ) ) {
                        add_theme_support( $support );
                    } else {
                        add_theme_support( $key, $support );
                    }
                }
            }

            if ( isset( $supports['disable'] ) ) {
                foreach ( $supports['disable'] as $support ) {
                    remove_theme_support( $support );
                }
            }
        } );
    }
}