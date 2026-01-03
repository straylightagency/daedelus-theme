<?php

namespace Daedelus\Theme\Pages\Pipeline;

use Daedelus\Theme\ViewScanner;

/**
 *
 */
class MatchedPage
{
	/**
	 * Create a new matched page instance.
	 */
	public function __construct(
		public string $path,
		public array $data,
		public ?string $mountPath = null
	) {
	}

	/**
	 * Set the mount path on the matched view, returning a new instance.
	 */
	public function withMountPath(string $mountPath): static
	{
		return new static( path: $this->path, data: $this->data, mountPath: $mountPath );
	}

	/**
	 * @return ?string
	 */
	public function name(): ?string
	{
		return app( ViewScanner::class )->getMetadata( $this->path )->name;
	}

	/**
	 * @return array
	 */
	public function middleware(): array
	{
		return app( ViewScanner::class )->getMetadata( $this->path )->middleware;
	}

	/**
	 * @return callable
	 */
	public function render(): callable
	{
		return app( ViewScanner::class )->getMetadata( $this->path )->render ?? fn ($view) => $view;
	}

	/**
	 * @return string
	 */
	public function relativePath(): string
	{
		$path = str_replace( $this->mountPath, '', $this->path );

		return '/' . trim( str_replace(DIRECTORY_SEPARATOR, '/', $path ), '/' );
	}
}