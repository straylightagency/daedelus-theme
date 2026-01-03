<?php

namespace Daedelus\Theme;

use Daedelus\Theme\Admin\AdminManager;
use Daedelus\Theme\Ajax\AjaxManager;
use Daedelus\Theme\Console\Commands\CreateCommand;
use Daedelus\Theme\Pages\PagesManager;
use Daedelus\Theme\Templates\TemplatesManager;
use Illuminate\Support\ServiceProvider;

/**
 *
 */
class ThemeServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register():void
	{
		$this->app->singleton(ViewScanner::class );
		$this->app->singleton(PagesManager::class );
        $this->app->singleton(TemplatesManager::class );
        $this->app->singleton(AdminManager::class );
        $this->app->singleton(AjaxManager::class );

		$this->commands( [
			CreateCommand::class,
		] );
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides():array
	{
		return [
			ViewScanner::class,
		];
	}
}