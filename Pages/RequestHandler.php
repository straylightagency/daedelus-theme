<?php

namespace Daedelus\Theme\Pages;

use Daedelus\Theme\Pages\Pipeline\MatchedPage;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 */
class RequestHandler
{
	/**
	 * Constructor.
	 */
	public function __construct(protected PagesRouter $router)
	{
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function __invoke(Request $request): Response
	{
		$matchedPage = $this->router->match( $request );

		abort_if( $matchedPage === null, 404 );

		if ( $name = $matchedPage->name() ) {
			$request->route()->action['as'] = $name;
		}

		$middleware = $matchedPage->middleware();

		return ( new Pipeline( app() ) )
			->send( $request )
			->through( $middleware )
			->then( function ( $request ) use ( $matchedPage ) {
				return $this->toResponse( $request, $matchedPage );
			} );
	}

	/**
	 * @param Request $request
	 * @param MatchedPage $matchedPage
	 *
	 * @return Response
	 */
	protected function toResponse(Request $request, MatchedPage $matchedPage): Response {
		$view = View::file( $matchedPage->path, $matchedPage->data );

		return Route::toResponse( $request, app()->call(
			$matchedPage->render(),
			[ 'view' => $view, ...$view->getData() ]
		) );
	}
}