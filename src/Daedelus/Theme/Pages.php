<?php

namespace Daedelus\Theme;

use Daedelus\Theme\Pages\PagesManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static PagesManager path( string $path )
 */
class Pages extends Facade
{
	/**
	 * @return string
	 */
	public static function getFacadeAccessor(): string
	{
		return PagesManager::class;
	}
}