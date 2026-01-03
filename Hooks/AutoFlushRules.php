<?php
namespace Daedelus\Theme\Hooks;

use Daedelus\Fields\Fields;
use Daedelus\Framework\Hooks\Hook;
use Daedelus\Support\Actions;

class AutoFlushRules extends Hook
{
    /**
     * @return void
     */
    public function register():void
    {
        Actions::add( 'after_switch_theme', 'flush_rewrite_rules' );
    }
}