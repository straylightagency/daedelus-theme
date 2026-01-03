<?php

namespace Daedelus\Theme\Pages\Pipeline;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

trait FindsWildcardViews
{
	/**
	 * Attempt to find a wildcard multi-segment page at the given directory.
	 */
	protected function findWildcardMultiSegmentPage(string $directory): ?string
	{
		return $this->findPageWith($directory, '[...', ']');
	}

	/**
	 * Attempt to find a wildcard page at the given directory.
	 */
	protected function findWildcardPage(string $directory): ?string
	{
		return $this->findPageWith( $directory, '[', ']' );
	}

	/**
	 * Attempt to find a wildcard page at the given directory with the given beginning and ending strings.
	 */
	protected function findPageWith(string $directory, $startsWith, $endsWith): ?string
	{
		$files = ( new Filesystem )->files( $directory );

		return collect( $files )->first( function ( $file ) use ( $startsWith, $endsWith ) {
			$filename = Str::of( $file->getFilename() );

			if ( !$filename->endsWith('.blade.php') ) {
				return null;
			}

			$filename = $filename->before('.blade.php');

			return $filename->startsWith( $startsWith ) && $filename->endsWith( $endsWith );
		} )?->getFilename();
	}
}