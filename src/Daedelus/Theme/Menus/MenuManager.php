<?php

namespace Daedelus\Theme\Menus;

use Daedelus\Theme\Menus\Exceptions\MenuNotFoundException;
use Illuminate\Http\Request;

/**
 * Manage Theme Menus
 */
class MenuManager
{
	/** @var array */
	protected array $cached = [];

	/**
	 * Constructor.
	 */
	public function __construct(protected Request $request)
	{
	}

	/**
	 * @param string $menu_name
	 *
	 * @return Menu
	 * @throws MenuNotFoundException
	 */
	public function get(string $menu_name):Menu
	{
		if ( isset( $this->cached[ $menu_name ] ) ) {
			return $this->cached[ $menu_name ];
		}

		$locations = get_nav_menu_locations();

		if ( isset( $locations[ $menu_name ] ) ) {
			$menu_items = wp_get_nav_menu_items( $locations[ $menu_name ] ) ?: [];

			$menu_items = array_map( function ( $item ) {
				return ( new MenuItem( $item ) )->bind( $this->request );
			}, $menu_items );

			return $this->cached[ $menu_name ] = new Menu( $menu_items  );
		}

		throw new MenuNotFoundException('Menu ' . $menu_name . ' does not exist.');
	}
}