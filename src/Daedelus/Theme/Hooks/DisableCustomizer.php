<?php
namespace Daedelus\Theme\Hooks;

use Daedelus\Foundation\Hooks\Hook;
use Daedelus\Support\Actions;

class DisableCustomizer extends Hook
{
	/** Disable the customizer in admin menu
	 *
	 * @return void
	 */
    public function register():void
    {
	    Actions::add('admin_menu', function () {
		    global $submenu;

            /** Remove "Appearance > Patterns" from menu */
            remove_submenu_page('themes.php', 'site-editor.php?path=/patterns');

            /** Remove "Appearance > Customize" from menu */
		    foreach ( $submenu as $name => $items ) {
			    if ( $name === 'themes.php' ) {
				    foreach ( $items as $index => $value ) {
					    if ( in_array('customize', $value, true ) ) {
						    unset( $submenu[ $name ][ $index ] );

						    return;
					    }
				    }
			    }
		    }
        } );
    }
}