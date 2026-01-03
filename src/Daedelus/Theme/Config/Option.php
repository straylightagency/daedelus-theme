<?php

namespace Daedelus\Theme\Config;

use Daedelus\Fields\Location;

/**
 *
 */
class Option
{
	/** @var string|null */
	public ?string $slug = null;

	/** @var string */
	public string $name = '';

	/** @var Option[] */
	public array $pages = [];

	/** @var int */
	public int $menuPosition = 50;

	/** @var string */
	public string $menuIcon = 'dashicons-admin-site';

	/** @var string */
	public string $capability = 'manage_options';

	/** @var string */
	public string $updateButton = 'Update';

	/** @var string */
	public string $updatedMessage = 'Options Updated';

	/**
	 * @param Location $location
	 *
	 * @return array
	 */
	public function fields(Location $location):array
	{
		return [];
	}

	/**
	 * @return void
	 */
	public function register():void
	{
		if ( !$this->slug || !function_exists( 'acf_add_options_page' ) ) {
			return;
		}

		$pages = $this->pages;

		acf_add_options_page( [...$this->buildRegister(), 'redirect' => count( $pages ) > 1 ] );

		foreach ( $pages as $page_class ) {
			$page = new $page_class();

			acf_add_options_sub_page( [...$page->buildRegister(), 'parent_slug' => $this->slug ] );

			Location::optionsPage( $page->name, fn ( Location $location ) => $page->fields( $location ), $page->slug );
		}

		Location::optionsPage( $this->name, fn ( Location $location ) => $this->fields( $location ), $this->slug );
	}

	/**
	 * @return array
	 */
	public function buildRegister():array
	{
		return [
			'page_title' => $this->name,
			'menu_title' => $this->name,
			'menu_slug' => $this->slug,
			'capability' => $this->capability,
			'position' => $this->menuPosition,
			'icon_url' => $this->menuIcon,
			'update_button' => $this->updateButton,
			'updated_message' => $this->updatedMessage,
		];
	}
}