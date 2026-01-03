<?php

namespace Daedelus\Theme\Pages\Pipeline;

use Closure;
use Daedelus\Theme\Pages\Exceptions\PossibleDirectoryTraversal;
use Illuminate\Support\Str;

class EnsureNoDirectoryTraversal
{
	/**
	 * Invoke the routing pipeline handler.
	 *
	 * @throws PossibleDirectoryTraversal
	 */
	public function __invoke(State $state, Closure $next): mixed
	{
		if ( !( $page = $next( $state ) ) instanceof MatchedPage ) {
			return $page;
		}

		if ( !Str::of( realpath( $page->path ) )->startsWith($state->mountPath . DIRECTORY_SEPARATOR ) ) {
			throw new PossibleDirectoryTraversal;
		}

		return $page;
	}
}