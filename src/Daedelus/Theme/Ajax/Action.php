<?php

namespace Daedelus\Theme\Ajax;

use Closure;
use Daedelus\Support\Actions;

/**
 *
 */
class Action
{
    /**
     * @param string $action_slug
     * @param Closure|array $handler
     * @param bool $admin_only
     */
    public function __construct(
        protected string $action_slug,
        protected Closure|array $handler,
        protected bool $admin_only = false,
    ) {
    }

    /**
     * @return void
     */
    public function build(): void
    {
        $hook_name = $this->admin_only ? 'wp_ajax_nopriv_' : 'wp_ajax_';

        Actions::add($hook_name . $this->action_slug, function () {
            if ( is_array( $this->handler ) ) {
                list( $controller, $method ) = $this->handler;
                $controller = app( $controller );

                echo app()->call( [ $controller, $method ] );
            } else {
                echo app()->call( $this->handler );
            }

            exit();
        } );
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function isAdminOnly(bool $value = true): self
    {
        $this->admin_only = $value;

        return $this;
    }
}