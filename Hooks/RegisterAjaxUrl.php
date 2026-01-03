<?php
namespace Daedelus\Theme\Hooks;

use Daedelus\Framework\Hooks\Hook;
use Daedelus\Support\Actions;

/**
 *
 */
class RegisterAjaxUrl extends Hook
{
    /**
     * @return void
     */
    public function register():void
    {
	    Actions::add('wp_head', function () {
		    ?>
            <script>
                window.ajaxurl = "<?= admin_url( 'admin-ajax.php', 'relative' ) ?>";
            </script>
		    <?php
	    } );
    }
}