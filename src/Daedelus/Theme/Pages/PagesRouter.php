<?php

namespace Daedelus\Theme\Pages;

use Daedelus\Theme\Pages\Pipeline\ContinueIterating;
use Daedelus\Theme\Pages\Pipeline\EnsureNoDirectoryTraversal;
use Daedelus\Theme\Pages\Pipeline\MatchDirectoryIndexPages;
use Daedelus\Theme\Pages\Pipeline\MatchedPage;
use Daedelus\Theme\Pages\Pipeline\MatchLiteralDirectories;
use Daedelus\Theme\Pages\Pipeline\MatchLiteralPages;
use Daedelus\Theme\Pages\Pipeline\MatchRootIndex;
use Daedelus\Theme\Pages\Pipeline\MatchWildcardDirectories;
use Daedelus\Theme\Pages\Pipeline\MatchWildcardPages;
use Daedelus\Theme\Pages\Pipeline\MatchWildcardViewsThatCaptureMultipleSegments;
use Daedelus\Theme\Pages\Pipeline\SetMountPathOnMatchedPage;
use Daedelus\Theme\Pages\Pipeline\State;
use Daedelus\Theme\Pages\Pipeline\StopIterating;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;

/**
 *
 */
class PagesRouter
{
	/**
	 * Constructor.
	 */
	public function __construct(protected array $mountPaths)
	{
	}

	/**
	 * @param Request $request
	 *
	 * @return MatchedPage|null
	 */
	public function match(Request $request):?MatchedPage
	{
		/** @var MountPath $mountPath */
		foreach ( $this->mountPaths as $mountPath ) {
			$request_path = '/' . ltrim( $request->path(), '/' );

			$uri = '/' . ltrim( substr( $request_path, strlen( $mountPath->baseUri ) ), '/' );

			if ( $page = $this->matchAtPath( $mountPath->path, $uri ) ) {
				return $page;
			}
		}

		return null;
	}

	/**
	 * Resolve the given URI via page based routing at the given mount path.
	 */
	protected function matchAtPath(string $path, string $uri): ?MatchedPage
	{
		$uri = strlen( $uri ) > 1 ? trim( $uri , '/' ) : $uri;

		$state = new State(
			uri: $uri,
			mountPath: $path,
			segments: explode('/', $uri )
		);

		for ( $i = 0; $i < $state->uriSegmentCount(); $i++ ) {
			$value = ( new Pipeline )
				->send( $state->forIteration( $i ) )
				->through( [
					new EnsureNoDirectoryTraversal,
					new SetMountPathOnMatchedPage,
					new MatchRootIndex,
					new MatchDirectoryIndexPages,
					new MatchWildcardViewsThatCaptureMultipleSegments,
					new MatchLiteralDirectories,
					new MatchWildcardDirectories,
					new MatchLiteralPages,
					new MatchWildcardPages,
				] )->then( fn () => new StopIterating );

			if ( $value instanceof MatchedPage ) {
				return $value;
			} elseif( $value instanceof ContinueIterating ) {
				$state = $value->state;
			} elseif ( $value instanceof StopIterating ) {
				break;
			}
		}

		return null;
	}
}