<?php
namespace Daedelus\Theme\Hooks;

use Daedelus\Framework\Hooks\Hook;

class ConfigureAjax extends Hook
{
    /** @var array|string[] */
    protected array $hooks = [
        RegisterAjaxUrl::class,
    ];

    /**
     * @return void
     */
    public function register():void
    {
    }
}