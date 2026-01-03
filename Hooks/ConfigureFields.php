<?php
namespace Daedelus\Theme\Hooks;

use Daedelus\Foundation\Hooks\Hook;

class ConfigureFields extends Hook
{
	/**
	 * @var array|string[]
	 */
	protected array $hooks = [
		RegisterFields::class,
	];

    /**
     * @return void
     */
    public function register():void
    {
    }
}