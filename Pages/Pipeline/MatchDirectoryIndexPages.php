<?php

namespace Daedelus\Theme\Pages\Pipeline;

use Closure;

class MatchDirectoryIndexPages
{
	/**
	 * Invoke the routing pipeline handler.
	 */
	public function __invoke(State $state, Closure $next): mixed
	{
		return $state->onLastUriSegment() &&
		       $state->currentUriSegmentIsDirectory() &&
		       file_exists( $path = $state->currentUriSegmentDirectory() . '/index.blade.php' )
			? new MatchedPage( $path, $state->data )
			: $next( $state );
	}
}