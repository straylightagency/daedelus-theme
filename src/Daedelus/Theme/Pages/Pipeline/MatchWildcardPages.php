<?php

namespace Daedelus\Theme\Pages\Pipeline;

use Closure;
use Illuminate\Support\Str;

/**
 *
 */
class MatchWildcardPages
{
	use FindsWildcardViews;

	/**
	 * Invoke the routing pipeline handler.
	 */
	public function __invoke(State $state, Closure $next): mixed
	{
		if ( $state->onLastUriSegment() &&
		    $path = $this->findWildcardPage( $state->currentDirectory() ) ) {
			return new MatchedPage( $state->currentDirectory() . '/' . $path, $state->withData(
				Str::of( $path )
				   ->before('.blade.php')
				   ->match('/\[(.*)\]/')->value(),
				$state->currentUriSegment(),
			)->data );
		}

		return $next( $state );
	}
}