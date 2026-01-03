<?php

namespace Daedelus\Theme;

use Daedelus\Theme\Templates\TemplatesManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;

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