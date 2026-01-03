<?php

namespace Daedelus\Theme\Pages\Pipeline;

use Closure;

class SetMountPathOnMatchedPage
{
	/**
	 * Invoke the routing pipeline handler.
	 */
	public function __invoke(State $state, Closure $next): mixed
	{
		if ( !( $page = $next( $state ) ) instanceof MatchedPage ) {
			return $page;
		}

		return $page->withMountPath( $state->mountPath );
	}
}