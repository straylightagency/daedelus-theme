<?php

namespace Daedelus\Theme\Menus;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 *
 */
class Menu implements ArrayAccess, Countable, IteratorAggregate
{
	public function __construct(protected array $items)
	{
	}

	/**
	 * @return HierarchizedMenu
	 */
	public function hierarchized():HierarchizedMenu
	{
		return new HierarchizedMenu( $this->items );
	}

	/**
	 * @param mixed $offset
	 *
	 * @return bool
	 */
	public function offsetExists( mixed $offset ): bool
	{
		return isset( $this->items[ $offset ] );
	}

	/**
	 * @param mixed $offset
	 *
	 * @return mixed
	 */
	public function offsetGet( mixed $offset ): mixed
	{
		return $this->items[ $offset ];
	}

	/**
	 * @param mixed $offset
	 * @param mixed $value
	 *
	 * @return void
	 */
	public function offsetSet( mixed $offset, mixed $value ): void
	{
		$this->items[ $offset ] = $value;
	}

	/**
	 * @param mixed $offset
	 *
	 * @return void
	 */
	public function offsetUnset( mixed $offset ): void
	{
		unset( $this->items[ $offset ] );
	}

	/**
	 * @return int
	 */
	public function count(): int
	{
		return count( $this->items );
	}

	/**
	 * @return Traversable
	 */
	public function getIterator(): Traversable
	{
		foreach ( $this->items as $item) {
			yield $item;
		}
	}

	/**
	 * @return bool
	 */
	public function isEmpty(): bool
	{
		return empty( $this->items );
	}

	/**
	 * @return array
	 */
	public function toArray(): array
	{
		return $this->items;
	}
}