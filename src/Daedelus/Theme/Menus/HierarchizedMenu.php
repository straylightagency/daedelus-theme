<?php

namespace Daedelus\Theme\Menus;

use Exception;
use Traversable;

/**
 *
 */
class HierarchizedMenu extends Menu
{
	/**
	 * @return Traversable
	 */
	public function getIterator(): Traversable
	{
		foreach ( $this->toArray() as $item) {
			yield $item;
		}
	}

    /**
     * @return array
     */
    public function toArray(): array
    {
        $menu_items = $this->items;

        $menu_items_by_id = [];

        foreach ( $menu_items as $menu_item ) {
            $menu_items_by_id[ $menu_item->ID ] = (object) [
                'post' => $menu_item,
                'children' => []
            ];
        }

        foreach ( $menu_items_by_id as $menu_item ) {
            if ( $menu_item->post->menu_item_parent !== "0" ) {
                $menu_items_by_id[ (int) $menu_item->post->menu_item_parent ]->children[] = $menu_item;
            }
        }

        return array_values( array_filter( $menu_items_by_id, function ($item) {
            return $item->post->menu_item_parent === "0";
        } ) );
    }

	/**
	 * @return HierarchizedMenu
	 * @throws Exception
	 */
	public function hierarchized():HierarchizedMenu
	{
		throw new Exception('Cannot get hierarchized menu from an HierarchizedMenu');
	}
}