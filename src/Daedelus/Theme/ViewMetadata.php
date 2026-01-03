<?php

namespace Daedelus\Theme;

use Closure;

/**
 *
 */
class ViewMetadata
{
	/**
	 * The current global instance of the metadata, if any.
	 */
	protected static ?self $instance = null;

	/**
	 * Constructor.
	 */
	public function __construct(
		public array $renders = [],
		public ?Closure $fields = null,
		public ?string  $name = null,
		public ?string  $type = null,
		public array    $middleware = [],
	)
	{
		//
	}

	/**
	 * Get the current metadata instance or create a new one.
	 */
	public static function instance(): static
	{
		return static::$instance ??= new static;
	}

	/**
	 * Flush the current global instance of the metadata.
	 */
	public static function flush(): void
	{
		static::$instance = null;
	}
}