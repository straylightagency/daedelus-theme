<?php

namespace Daedelus\Theme\Menus;

use Illuminate\Http\Request;
use WP_Post;

/**
 *
 */
class MenuItem
{
	/** @var Request */
	protected Request $request;

	/**
	 * @param WP_Post $post
	 */
	public function __construct(public readonly WP_Post $post)
	{
	}

	/**
	 * @param Request $request
	 *
	 * @return $this
	 */
	public function bind(Request $request):static
	{
		$this->request = $request;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isActive():bool
	{
		$uri = $this->request->getSchemeAndHttpHost() . $this->request->getBaseUrl() . $this->request->getPathInfo();
        $uri = trim( $uri, '/' );

        $post_url = trim( $this->post->url, '/' );

        return str_contains( $uri, $post_url ) || $post_url === $uri;
	}

	/**
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function __get(string $key):mixed
	{
		if ( $key == 'is_active' ) {
			return $this->isActive();
		}

		return $this->post->$key;
	}
}