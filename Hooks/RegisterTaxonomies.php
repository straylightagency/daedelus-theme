<?php
namespace Daedelus\Theme\Hooks;

use Daedelus\Framework\Hooks\Hook;
use Daedelus\Support\Actions;
use Daedelus\Theme\Config\Taxonomy;

class RegisterTaxonomies extends Hook
{
    public function register():void
    {
        collect( config( 'theme.taxonomies', [] ) )
            ->reject( fn ( $taxonomy ) => $taxonomy instanceof Taxonomy )
            ->map( fn ( $taxonomy ) => new $taxonomy )
            ->each( fn ( Taxonomy $taxonomy ) => $taxonomy->register() );
    }
}