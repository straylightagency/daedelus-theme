<?php
namespace Daedelus\Theme\Hooks;

use Daedelus\Foundation\Hooks\Hook;

class ConfigureTheme extends Hook
{
	/**
	 * @var array|string[]
	 */
	protected array $hooks = [
		\Daedelus\Theme\Hooks\AddThemeSupports::class,
		\Daedelus\Theme\Hooks\DisableCustomizer::class,
		\Daedelus\Theme\Hooks\ConfigImageSizes::class,
        \Daedelus\Theme\Hooks\RegisterTaxonomies::class,
		\Daedelus\Theme\Hooks\RegisterPostTypes::class,
		\Daedelus\Theme\Hooks\RegisterShortcodes::class,
		\Daedelus\Theme\Hooks\RegisterBodyClasses::class,
		\Daedelus\Theme\Hooks\RegisterMenus::class,
		\Daedelus\Theme\Hooks\RegisterFavicon::class,
		\Daedelus\Theme\Hooks\RegisterOptions::class,
		\Daedelus\Theme\Hooks\EnqueueAssets::class,
		\Daedelus\Theme\Hooks\RemoveWpBodyClasses::class,
        \Daedelus\Theme\Hooks\AutoFlushRules::class,
	];

    /**
     * @return void
     */
    public function register():void
    {
    }
}