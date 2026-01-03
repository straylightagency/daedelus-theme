<?php

namespace Daedelus\Theme\Pages;

/**
 *
 */
class MountPath
{
	/**
	 * The path based middleware for the mounted path.
	 */
	public PathBasedMiddlewareList $middleware;

	/**
	 * Create a new mounted path instance.
	 */
	public function __construct(
		public string $path,
		public string $baseUri,
		array $middleware,
	) {
		$this->path = str_replace( '/', DIRECTORY_SEPARATOR, $path );

		$this->middleware = new PathBasedMiddlewareList( $middleware );
	}

	/**
	 * @param string $baseUri
	 *
	 * @return $this
	 */
	public function uri(string $baseUri): static
	{
		$this->baseUri = $baseUri;

		return $this;
	}

	/**
	 * @param array $middleware
	 *
	 * @return $this
	 */
	public function middleware(array $middleware): static
	{
		$this->middleware = new PathBasedMiddlewareList( $middleware );

		return $this;
	}
}