<?php

namespace Daedelus\Theme\Config;

use Daedelus\Fields\Fields;
use Daedelus\Fields\Location;
use Daedelus\Support\Actions;
use Illuminate\Support\Str;
use WP_REST_Terms_Controller;

/**
 *
 */
class Taxonomy
{
	/** @var string|null */
	public ?string $slug = null;

	/** @var string */
	public string $name = 'category';

	/** @var string */
	public string $singularName = 'category';

	/** @var string  */
	public string $pluralName = 'categories';

	/** @var string  */
	public string $description = '';

    /** @var string*/
    public string $labelsDomain = 'taxonomies';

	/** @var string|array */
	public string|array $postTypes = [];

	/** @var array */
	public array $rewrite = [];

	/** @var array */
	public array $capabilities = [];

	/** @var bool */
	public bool $public = true;

	/** @var bool */
	public bool $publiclyQueryable = true;

	/** @var bool */
	public bool $showAdminColumn = true;

	/** @var bool */
	public bool $showInMenu = true;

	/** @var bool */
	public bool $showInNavMenus = true;

	/** @var bool */
	public bool $showUI = true;

	/** @var bool */
	public bool $showInQuickEdit = true;

	/** @var bool */
	public bool $showInRest = false;

	/** @var string|null */
	public ?string $restBase;

	/** @var string|null */
	public ?string $restControllerClass;

	/** @var bool */
	public bool $hierarchical = false;

	/** @var bool */
	public bool $showTagCloud = false;

	/** @var bool */
	public bool $queryVar = false;

	/** @var array */
	public array $defaultTerm = [];

	/** @var bool */
	public bool $sort = false;

	/** @var array */
	public array $args = [];

	/**
	 * @return void
	 */
	public function boot(): void
	{
	}

	/**
	 * @param Location $group
	 *
	 * @return array
	 */
    protected function fields(Location $group):array
	{
        return [];
	}

    /**
     * @return array
     */
    protected function labels(): array
    {
        return collect( [
            'name' => [ 'name' => $this->singularName ],
            'singular_name' => [ 'name' => $this->singularName ],
            'search_items' => [ 'name' => $this->singularName ],
            'popular_items' => [ 'name' => $this->pluralName ],
            'all_items' => [ 'name' => $this->pluralName ],
            'parent_item' => [],
            'parent_item_colon' => [],
            'edit_item' => [ 'name' => $this->singularName ],
            'update_item' => [ 'name' => $this->singularName ],
            'add_new_item' => [ 'name' => $this->singularName ],
            'new_item_name' => [ 'name' => $this->singularName ],
            'separate_items_with_commas' => [ 'name' => $this->pluralName ],
            'add_or_remove_items' => [ 'name' => $this->pluralName ],
            'choose_from_most_used' => [ 'name' => $this->singularName ],
            'menu_name' => [ 'name' => $this->pluralName ],
        ] )
            ->map( fn ( $args, $key ) => _t( $this->labelsDomain . '.' . $key, $args ) )
            ->toArray();
    }

	/**
	 * @return void
	 */
	public function register():void
	{
		if ( empty( $this->slug ) ) {
			$this->slug = Str::slug( $this->name, '_' );
		}

		if ( empty( $this->restBase ) ) {
			$this->restBase = $this->slug;
		}

		if (empty( $this->restControllerClass ) ) {
			$this->restControllerClass = WP_REST_Terms_Controller::class;
		}

		if ( empty( $this->rewrite ) ) {
			$this->rewrite = [ 'slug' => $this->slug ] ;
		}

        Actions::add('init', function () {
            register_taxonomy( $this->slug, $this->postTypes, [
                'labels' => $this->labels(),
                'slug' => $this->slug,
                'name' => ucfirst( $this->name ),
                'public' => $this->public,
                'publicly_queryable' => $this->publiclyQueryable,
                'hierarchical' => $this->hierarchical,
                'show_ui' => $this->showUI,
                'show_admin_column' => $this->showAdminColumn,
                'show_in_menu' => $this->showInMenu,
                'show_in_nav_menus' => $this->showInNavMenus,
                'show_in_rest' => $this->showInRest,
                'rest_base' => $this->restBase,
                'rest_controller_class' => $this->restControllerClass,
                'show_tagcloud' => $this->showTagCloud,
                'show_in_quick_edit' => $this->showInQuickEdit,
                'capabilities' => $this->capabilities,
                'rewrite' => $this->rewrite,
                'query_var' => $this->queryVar,
                'default_term' => $this->defaultTerm,
                'sort' => $this->sort,
                'args' => $this->args,
            ] );
        } );

		Fields::taxonomy( $this->slug, ucfirst( $this->name ) . ' Fields', function ( Location $group ) {
            $fields = $this->fields( $group );

            if ( !empty( $fields ) ) {
                $group->fields( $fields );
            }
		} );

		$this->boot();
	}
}