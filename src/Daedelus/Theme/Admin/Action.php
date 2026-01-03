<?php

namespace Daedelus\Theme\Admin;

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
     */
    public function __construct(
        protected string $action_slug,
        protected Closure|array $handler
    ) {
    }

    /**
     * @return void
     */
    public function build(): void
    {
        Actions::add('admin_post_' . $this->action_slug, function () {
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
}