<?php

namespace Daedelus\Theme;

use Closure;
use Daedelus\Theme\Ajax\Action;
use Daedelus\Theme\Ajax\AjaxManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Action action(string $slug, Closure|array $handler, bool $admin_only = false)
 */
class Ajax extends Facade
{
    /**
     * @return string
     */
    public static function getFacadeAccessor(): string
    {
        return AjaxManager::class;
    }
}