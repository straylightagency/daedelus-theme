<?php

namespace Daedelus\Theme\Admin;

use Closure;
use Daedelus\Support\Actions;
use Illuminate\Http\Request;
use LogicException;

/**
 *
 */
class Page
{
    /** @var string */
    protected string $menuTitle = '';

    /** @var Closure|array|string|null[] */
    protected array $actionHandlers = [];

    /** @var Subpage[] */
    protected array $subpages = [];

    /** @var string */
    protected string $capability = 'manage_options';

    /** @var string */
    protected string $iconUrl = '';

    /** @var int|float|null */
    protected int|float|null $position = null;

    /** @var Closure|null */
    protected Closure|null $scriptsHandler = null;

    /** @var Closure|null */
    protected Closure|null $stylesHandler = null;

    /**
     * @param string $slug
     * @param string|null $menu_title
     * @param Closure|array|string|null $handler
     */
    public function __construct(
        protected string $slug,
        string|null $menu_title = '',
        Closure|array|string|null $handler = null,
    )
    {
        $this->menuTitle( $menu_title );
        $this->action( $handler, 'index' );
    }

    /**
     * @param string $menu_title
     * @return $this
     */
    public function menuTitle(string $menu_title): self
    {
        $this->menuTitle = $menu_title;

        return $this;
    }

    /**
     * @param string $capability
     * @return $this
     */
    public function capability(string $capability): self
    {
        $this->capability = $capability;

        return $this;
    }

    /**
     * @param string $icon_url
     * @return $this
     */
    public function icon(string $icon_url): self
    {
        $this->iconUrl = $icon_url;

        return $this;
    }

    /**
     * @param int|float $position
     * @return $this
     */
    public function position(int|float $position): self
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @param Closure|array|string|null $handler
     * @param string $name
     * @return $this
     */
    public function action(Closure|array|string|null $handler, string $name = 'index'):self
    {
        $this->actionHandlers[ $name ] = $handler;

        return $this;
    }

    /**
     * @param string $subpage_slug
     * @param string $title
     * @param Closure|array|string|null $handler
     * @return self
     */
    public function subpage(string $subpage_slug, string $title, Closure|array|string|null $handler = null): self
    {
        $this->subpages[ $subpage_slug ] = $subpage = new Subpage( $subpage_slug, $title, $handler );
        $subpage->parent( $this->slug );

        return $subpage;
    }

    /**
     * @param Closure $handler
     * @return $this
     */
    public function scripts(Closure $handler):self
    {
        $this->scriptsHandler = $handler;

        return $this;
    }

    /**
     * @param Closure $handler
     * @return $this
     */
    public function styles(Closure $handler):self
    {
        $this->stylesHandler = $handler;

        return $this;
    }

    /**
     * @return void
     */
    public function build(): void
    {
        $slug = add_menu_page(
            $this->menuTitle,
            $this->menuTitle,
            $this->capability,
            $this->slug,
            $this->handlerCallback(),
            $this->iconUrl,
            $this->position
        );

        if ( $this->stylesHandler ) {
            Actions::add(
                'admin_print_styles-' . $slug,
                $this->stylesHandler
            );
        }

        if ( $this->scriptsHandler ) {
            Actions::add(
                'admin_print_scripts-' . $slug,
                $this->scriptsHandler
            );
        }

        foreach ( $this->subpages as $subpage ) {
            $subpage->build();
        }
    }

    /**
     * @return Closure
     */
    protected function handlerCallback(): Closure
    {
        return function () {
            /** @var Request $request */
            $request = app('request');

            $action = $request->get('action', 'index');

            $handler = $this->actionHandlers[ $action ];

            if ( is_array( $handler ) ) {
                list( $controller, $method ) = $handler;
                $controller = app( $controller );

                echo app()->call( [ $controller, $method ] );
            } else if ( is_string( $handler ) ) {
                echo view( $handler );
            } else if ( is_callable( $handler ) ) {
                echo app()->call( $handler );
            } else {
                $page_type = $this instanceof Subpage ? 'Subpage' : 'Page';

                throw new LogicException( sprintf( 'Missing handler for Admin %s " %s "', $page_type, $this->slug ) );
            }
        };
    }
}