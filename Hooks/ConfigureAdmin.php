<?php
namespace Daedelus\Theme\Hooks;

use Daedelus\Framework\Hooks\Hook;

class ConfigureAdmin extends Hook
{
	/**
	 * @var array|string[]
	 */
	protected array $hooks = [
		RegisterAdmin::class,
	];

    /**
     * @return void
     */
    public function register():void
    {
    }
}