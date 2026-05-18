<?php

namespace Daedelus\Theme;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;
use Daedelus\Theme\Templates\TemplatesManager;

/**
 * @method static TemplatesManager path(string $path)
 */
class Templates extends Facade
{
	/**
	 * @return string
	 */
	public static function getFacadeAccessor(): string
	{
		return TemplatesManager::class;
	}
}