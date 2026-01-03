<?php

namespace Daedelus\Theme;

use Closure;
use Daedelus\Theme\Admin\Action;
use Daedelus\Theme\Admin\AdminManager;
use Daedelus\Theme\Admin\Page;
use Daedelus\Theme\Admin\Subpage;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void register()
 * @method static Page page(string $slug, string $menu_title, Closure|array|string|null $handler = null)
 * @method static Subpage subpage(string $slug, string $menu_title, Closure|array|string|null $handler = null)
 * @method static Subpage hidden(string $slug, Closure|array|string|null $handler = null)
 * @method static Action action(string $slug, Closure|array $handler)
 * @method static string url(string $page_slug, mixed $parameters = [], ?bool $secure = null)
 * @method static void add(string $menu_name, string $file_name, string $capability = 'read', string $classes = '', string $id = '', string $icon = 'dashicons-forms', ?int $position = null)
 * @method static void remove(string $page_name)
 * @method static void styles(Closure $handler)
 * @method static void scripts(Closure $handler)
 */
class Admin extends Facade
{
    /**
     * @return string
     */
    public static function getFacadeAccessor(): string
    {
        return AdminManager::class;
    }
}