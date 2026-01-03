<?php
namespace Daedelus\Theme\Hooks;

use Daedelus\Foundation\Hooks\Hook;
use Daedelus\Support\Filters;

class RemoveWpBodyClasses extends Hook
{
	/**
     * Remove every classes that starts with "wp-" including "page" from the body tag on pages.
     *
	 * @return void
	 */
    public function register():void
    {
        Filters::add('body_class', function ($classes) {
            return array_filter( $classes, fn(string $class) =>
                !str_starts_with( $class, 'page-') &&
                !str_starts_with( $class, 'wp-') &&
                !in_array( $class, [
                    'page', 'logged-in'
                ] )
            );
        } );
    }
}