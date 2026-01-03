<?php

namespace Daedelus\Theme\Pages\Pipeline;

use Closure;

class MatchLiteralPages
{
	/**
	 * Invoke the routing pipeline handler.
	 */
	public function __invoke(State $state, Closure $next): mixed
	{
		return $state->onLastUriSegment() &&
		       file_exists( $path = $state->currentDirectory() . '/' . $state->currentUriSegment() . '.blade.php')
			? new MatchedPage( $path, $state->data )
			: $next( $state );
	}
}