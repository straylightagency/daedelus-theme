<?php

namespace Daedelus\Theme\Admin;

use Closure;
use Daedelus\Support\Actions;
use Daedelus\Theme\Admin;
use LogicException;

/**
 *
 */
class Subpage extends Page
{
    /** @var string|null */
    protected string|null $parentSlug = null;

    /**
     * @param string|null $parent_slug
     * @return $this
     */
    public function parent(string|null $parent_slug): self
    {
        $this->parentSlug = $parent_slug;

        return $this;
    }

    /**
     * @param string $icon_url
     * @return $this
     */
    public function icon(string $icon_url): self
    {
        throw new LogicException('Cannot call the icon() method on AdminSubpage class');
    }

    /**
     * @param string $subpage_slug
     * @param string $title
     * @param Closure|array|string|null $handler
     * @return self
     */
    public function subpage(string $subpage_slug, string $title, Closure|array|string|null $handler = null): self
    {
        throw new LogicException('Cannot call the subpage() method on AdminSubpage class');
    }

    /**
     * @return void
     */
    public function build(): void
    {
        $slug = add_submenu_page(
            $this->parentSlug,
            $this->menuTitle,
            $this->menuTitle,
            $this->capability,
            $this->slug,
            $this->handlerCallback(),
            $this->position
        );

        if ( $this->scriptsCallback ) {
            Actions::add(
                'admin_print_scripts-' . $slug,
                $this->scriptsCallback
            );
        }

        if ( $this->stylesHandler ) {
            Actions::add(
                'admin_print_styles-' . $slug,
                $this->stylesHandler
            );
        }
    }
}