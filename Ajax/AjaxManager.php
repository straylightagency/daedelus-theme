<?php

namespace Daedelus\Theme\Ajax;

use Closure;
use Daedelus\Support\Actions;

/**
 * Manage WordPress AJAX actions
 */
class AjaxManager
{
    /** @var bool */
    protected bool $registered = false;

    /** @var Action[] */
    protected array $actions = [];

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
            Actions::add('init', function () {
                foreach ( $this->actions as $action ) {
                    $action->build();
                }
            } );

            $this->registered = true;
        }
    }

    /**
     * @param string $action_slug
     * @param Closure|array $handler
     * @param bool $admin_only
     * @return Action
     */
    public function action(string $action_slug, Closure|array $handler, bool $admin_only = false): Action
    {
        $action = new Action( $action_slug, $handler, $admin_only );

        $this->actions[ $action_slug ] = $action;

        return $action;
    }
}