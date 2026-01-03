<?php
namespace Daedelus\Theme\Hooks;

use Daedelus\Foundation\Hooks\Hook;
use Daedelus\Theme\Config\Shortcode;

class RegisterShortcodes extends Hook
{
    public function register():void
    {
	    collect( config( 'theme.shortcodes', [] ) )
		    ->reject( fn ( $shortcode ) => $shortcode instanceof Shortcode )
		    ->map( fn ( $shortcode ) => new $shortcode )
            ->each( fn ( Shortcode $shortcode ) => $shortcode->register() );
    }
}