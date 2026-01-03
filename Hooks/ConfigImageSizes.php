<?php
namespace Daedelus\Theme\Hooks;

use Closure;
use Daedelus\Framework\Hooks\Hook;
use Daedelus\Support\Actions;
use Daedelus\Support\Filters;

class ConfigImageSizes extends Hook
{
	/**
	 * @return void
	 */
    public function register():void
    {
	    Actions::add( 'after_setup_theme', function () {
            foreach ( config('theme.image_sizes', [] ) as $name => $size ) {
                if ( !is_array( $size ) ) {
                    report( sprintf( 'Error: wrong value for <code>%s</code> image size.', $name ), 'Incorrect image size' );
                }

	            // @note - using "@" to prevent notice about undefined offset
                if ( is_string( $name ) ) {
                    @list( $width, $height, $crop ) = $size;
                } else {
                    @list( $name, $width, $height, $crop ) = $size;
                }

                $old_sizes = get_intermediate_image_sizes();

                if ( in_array( $name, $old_sizes, true ) ) {
                    remove_image_size( $name );
                }

                add_image_size( $name, $width ?: 0, $height ?: 0, $crop ?: false);
            }

            Filters::add( 'image_editor_save_pre', $this->addImageOptions() );
        } );
    }

	/**
	 * Add the theme image options
	 *
	 * @return Closure
	 */
    protected function addImageOptions():Closure
    {
		return function ($data) {
			foreach ( wp_get_additional_image_sizes() as $size => $properties ) {
				update_option( $size . '_size_w', $properties[ 'width' ] );
				update_option( $size . '_size_h', $properties[ 'height' ] );
				update_option( $size . '_crop', $properties[ 'crop' ] );
			}

			return $data;
		};
    }
}