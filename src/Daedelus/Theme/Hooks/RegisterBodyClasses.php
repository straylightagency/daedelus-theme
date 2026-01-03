<?php
namespace Daedelus\Theme\Hooks;

use Daedelus\Foundation\Hooks\Hook;
use Daedelus\Support\Filters;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class RegisterBodyClasses extends Hook
{
    /**
     * @return void
     */
    public function register():void
    {
	    Filters::add( 'body_class', function ( $classes ) {
		    $classes = Arr::flatten( $classes );

		    if ( !in_array( basename( get_permalink() ), $classes ) ) {
			    foreach ( $classes as $key => $value ) {
				    if ( str_contains( $value, 'page-' ) ) {
					    unset( $classes[ $key ] );
				    }
			    }
		    }

		    if ( get_page_template_slug() ) {
			    $classes[] = Str::before( basename( implode('-', array_unique( explode( '-', get_page_template_slug() ) ) ) ), '.' );
		    }

		    return array_unique( array_values( $classes ) );
	    } );
    }
}