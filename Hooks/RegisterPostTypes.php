<?php
namespace Daedelus\Theme\Hooks;

use Daedelus\Framework\Hooks\Hook;
use Daedelus\Support\Actions;
use Daedelus\Theme\Config\PostType;

class RegisterPostTypes extends Hook
{
    public function register():void
    {
        collect( config( 'theme.post_types', [] ) )
            ->reject( fn ( $post_type ) => $post_type instanceof PostType )
            ->map( fn ( $post_type ) => new $post_type )
            ->each( fn ( PostType $post_type ) => $post_type->register() );
    }
}