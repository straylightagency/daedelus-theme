<?php

namespace Daedelus\Theme\Pages;

use Closure;
use Daedelus\Theme\ViewScanner;
use Illuminate\Http\Request;
use InvalidArgumentException;

/**
 *  Manage Route Pages from views folder
 */
class PagesManager
{
	/** @var array */
	protected array $mountPaths = [];

	/** @var array */
	protected array $routes = [];

	/**
	 * @param ViewScanner $scanner
	 * @param array $paths
	 */
	public function __construct(
        protected ViewScanner $scanner,
        protected array $paths = []
    ){
	}

	/**
	 * @param string|null $path
	 * @param string $uri
	 * @param array $middleware
	 *
	 * @return MountPath
	 */
	public function path(?string $path = null, string $uri = '/', array $middleware = []): MountPath
	{
		$path = realpath( $path ?: config('view.paths')[0] . '/pages' );
		$uri = '/' . ltrim( $uri, '/');

		$this->mountPaths[] = $mountPath = new MountPath(
			$path,
			$uri,
			$middleware,
		);

		return $mountPath;
	}

	/**
	 * @return Closure
	 */
	public function handler(): Closure
	{
		return function (Request $request) {
			$mountPaths = collect( $this->mountPaths )->filter(
				fn ( MountPath $mountPath ) => is_dir( $mountPath->path ) && str_starts_with( mb_strtolower( '/' . $request->path() ), $mountPath->baseUri )
			)->all();

			$router = new PagesRouter( $mountPaths );

			return ( new RequestHandler( $router ) )( $request );
		};
	}
}