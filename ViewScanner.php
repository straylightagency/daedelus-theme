<?php

namespace Daedelus\Theme;

use Closure;

/**
 *
 */
class ViewScanner
{
	/** @var bool */
	protected bool $listening = false;

	/** @var bool */
	protected bool $rendering = false;

	/** @var array  */
	protected array $cache = [];

	/**
	 * @param string $file_path
	 *
	 * @return ViewMetadata
	 */
	public function getMetadata(string $file_path):ViewMetadata
	{
		if ( isset( $this->cache[ $file_path ] ) ) {
			return $this->cache[ $file_path ];
		}

		try {
			$this->listen( function () use ( $file_path ) {
				ob_start();

				$__path = $file_path;

				( static function () use ( $__path ) {
					require $__path;
				} )();
			} );
		} finally {
			ob_get_clean();

			/** @var ViewMetadata $metadata */
			$metadata = tap( ViewMetadata::instance(), fn () => ViewMetadata::flush() );
		}

		return $this->cache[ $file_path ] = $metadata;
	}

	/**
	 * Execute the callback while listening for metadata.
	 *
	 * @param Closure $closure
	 *
	 * @return void
	 */
	public function listen(Closure $closure): void
	{
		$this->listening = true;

		try {
			$closure();
		} finally {
			$this->listening = false;
		}
	}

	/**
	 * Execute the callback if the scanner is listening for metadata.
	 *
	 * @param Closure $closure
	 */
	public function whenListening(Closure $closure):void
	{
		if ( $this->listening ) {
			$closure();
		}
	}

	/**
	 * Execute the callback if the scanner is not listening for metadata, meaning it's in rendering phase
	 *
	 * @param Closure $closure
	 *
	 * @return void
	 */
	public function whenRendering(Closure $closure):void
	{
		if ( !$this->listening ) {
			$closure();
		}
	}
}