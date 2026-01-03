<?php
namespace Daedelus\Theme\Hooks;

use Daedelus\Foundation\Hooks\Hook;
use Daedelus\Theme\Config\Option;

class RegisterOptions extends Hook
{
    public function register():void
    {
        collect( config( 'theme.options', [] ) )
            ->reject( fn ( $option ) => $option instanceof Option )
            ->map( fn ( $option ) => new $option )
            ->each( fn ( Option $option ) => $option->register() );
    }
}