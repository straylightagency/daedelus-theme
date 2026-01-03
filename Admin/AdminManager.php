<?php

namespace Daedelus\Theme\Admin;

use Closure;
use Daedelus\Support\Actions;

/**
 * Manage WordPress Admin pages, subpages and action pages
 */
class AdminManager
{
    /** @var bool */
    protected bool $registered = false;

    /** @var Page[] */
    protected array $pages = [];

    /** @var Page[] */
    protected array $hiddenPages = [];

    /** @var Action[] */
    protected array $actions = [];

	/** @var array[] */
	protected array $menus = [];

    /** @var string[] */
    protected array $removables = [];

    /** @var Closure|null */
    protected Closure|null $scriptsHandler = null;

    /** @var Closure|null */
    protected Closure|null $stylesHandler = null;

    /**
     * @param Closure|null $closure
     * @return void
     */
    public function register(Closure|null $closure = null): void
    {
        if ( $closure ) {
            $closure( $this );
        }

        if ( !$this->registered ) {
            Actions::add('admin_menu', function () {
	            global $menu;

                if ( $this->stylesHandler ) {
                    Actions::add(
                        'admin_print_styles',
                        $this->stylesHandler
                    );
                }

                if ( $this->scriptsHandler ) {
                    Actions::add(
                        'admin_print_scripts',
                        $this->scriptsHandler
                    );
                }

                foreach ( $this->pages as $page ) {
                    $page->build();
                }

                foreach ($this->hiddenPages as $subpage ) {
                    $subpage->build();
                }

                foreach ( $this->removables as $item ) {
                    remove_menu_page( $item );
                }

	            foreach ( $this->menus as $item ) {
					$position = $item[ 7 ];

		            if ( is_int( $position ) ) {
			            $menu[ $position ] = $item;
		            } else {
			            $menu[] = $item;
		            }
	            }
            } );

            Actions::add('admin_init', function () {
                foreach ( $this->actions as $action ) {
                    $action->build();
                }
            } );


            $this->registered = true;
        }
    }

	/**
	 * @param string $page_slug
	 * @param string $menu_title
	 * @param Closure|array|string|null $handler
	 *
	 * @return Page
	 */
    public function page(string $page_slug, string $menu_title, Closure|array|string|null $handler = null): Page
    {
        $page = new Page( $page_slug, $menu_title, $handler );

        $this->pages[ $page_slug ] = $page;

        return $page;
    }

    /**
     * Hidden pages are just subpages without parent
     *
     * @param string $page_slug
     * @param Closure|array|string|null $handler
     * @return Subpage
     */
    public function hidden(string $page_slug, Closure|array|string|null $handler = null): Subpage
    {
        $page = new Subpage( $page_slug, '', $handler );

        $this->hiddenPages[ $page_slug ] = $page;

        return $page;
    }

    /**
     * @param string $action_slug
     * @param Closure|array $handler
     * @return Action
     */
    public function action(string $action_slug, Closure|array $handler): Action
    {
        $action = new Action( $action_slug, $handler );

        $this->actions[ $action_slug ] = $action;

        return $action;
    }

    /**
     * @param string $page_name
     * @return void
     */
    public function remove(string $page_name): void
    {
        $this->removables[] = $page_name;
    }

	/**
	 * @param string $menu_name
	 * @param string $file_name
	 * @param string $capability
	 * @param string $classes
	 * @param string $id
	 * @param string $icon
	 * @param int|null $position
	 *
	 * @return void
	 */
	public function add(string $menu_name, string $file_name, string $capability = 'read', string $classes = '', string $id = '', string $icon = 'dashicons-forms', ?int $position = null):void
	{
		$this->menus[] = [ $menu_name, $capability, $file_name, '', $classes, $id, $icon, $position ];
	}

    /**
     * @param string $page_slug
     * @param mixed $parameters
     * @param bool|null $secure
     * @return string
     */
    public function url(string $page_slug, mixed $parameters = [], ?bool $secure = null): string
    {
        return add_query_arg( ['page' => $page_slug, ...$parameters], admin_url('admin.php', $secure ? 'https' : 'http' ) );
    }

    /**
     * @param Closure $handler
     * @return void
     */
    public function styles(Closure $handler): void
    {
        $this->stylesHandler = $handler;
    }

    /**
     * @param Closure $handler
     * @return void
     */
    public function scripts(Closure $handler): void
    {
        $this->scriptsHandler = $handler;
    }
}