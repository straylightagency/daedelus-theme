<?php

namespace Daedelus\Theme\Pages;

use Daedelus\Theme\Pages\Pipeline\MatchedPage;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 *
 */
class PathBasedMiddlewareList
{
	/**
	 * Create a new path based middleware list instance.
	 */
	public function __construct(public array $middleware) {}

	/**
	 * Find the middleware that match the given matched page's path.
	 */
	public function match(MatchedPage $page): Collection
	{
		$matched = [];

		$relative_path = trim( $page->relativePath(), '/' );

		foreach ( $this->middleware as $pattern => $middleware ) {
			if ( Str::is( trim( $pattern, '/' ), $relative_path ) ) {
				$matched = array_merge( $matched, Arr::wrap( $middleware ) );
			}
		}

		return collect( $matched );
	}
}