<?php

namespace Daedelus\Theme\Config;

use Daedelus\Fields\Fields;
use Daedelus\Fields\Location;
use Closure;
use Daedelus\Support\Actions;
use Daedelus\Support\Filters;
use Illuminate\Support\Str;
use WP_Post;
use WP_REST_Posts_Controller;

/**
 *
 */
class PostType
{
	/** @var string|null */
	public ?string $slug = null;

	/** @var string */
	public string $name = '';

	/** @var string */
	public string $singularName = '';

	/** @var string  */
	public string $pluralName = '';

	/** @var string  */
	public string $description = '';

	/** @var string*/
	public string $labelsDomain = 'post_types';

	/** @var array */
	public array $messages = [];

	/**
     * @var string
     * @see https://developer.wordpress.org/resource/dashicons/
     */
	public string $menuIcon = 'dashicons-businessman';

	/** @var int */
	public int $menuPosition = 5;

	/** @var bool */
	public bool $disableGutenberg = false;

	/**
     * @var array
     * @see https://developer.wordpress.org/reference/functions/post_type_supports/#more-information
     */
	public array $supports = [ 'title', 'featured_image', 'custom-fields', 'editor' ];

	/** @var array */
	public array $taxonomies = [];

	/** @var array|bool */
	public array|bool $rewrite = [];

	/** @var bool */
	public bool $canExport = true;

	/** @var string */
	public string $capabilityType = 'page';

	/** @var array */
	public array $capabilities = [];

	/** @var bool */
	public bool $public = true;

	/** @var bool */
	public bool $publiclyQueryable = true;

	/** @var bool */
	public bool $excludeFromSearch = true;

	/** @var bool */
	public bool $showInAdminBar = true;

	/** @var bool */
	public bool $showInMenus = true;

	/** @var bool */
	public bool $showInNavMenus = true;

	/** @var bool */
	public bool $showUI = true;

	/** @var bool */
	public bool $showInRest = false;

	/** @var string */
	public string $restBase = '';

	/** @var string */
	public string $restControllerClass = '';

	/** @var bool */
	public bool $hasArchive = false;

	/** @var bool */
	public bool $hierarchical = false;

	/** @var bool */
	public bool $queryVar = false;

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
	public function fields(Location $group):array
	{
        return [];
	}

    /**
     * @param Closure $closure
     * @param bool $draft
     * @return void
     */
	public function saving(Closure $closure, bool $draft = false):void
	{
		Actions::add( 'save_post', function ($post_id, $post = null, $is_updating = false) use ($closure, $draft) {
            if ( !$post && $post_id !== null ) {
                $post = get_post( $post_id );
            }

            if (
                $this->slug !== $post->post_type ||
                !$draft && $post->post_status === 'auto-draft'
            ) {
                return;
            }

            $closure( $post, $is_updating );
		}, 15, 3 );
	}

    /**
     * @param Closure $closure
     * @param bool $draft
     * @return void
     */
	public function creating(Closure $closure, bool $draft = false):void
	{
        $this->saving( function ($post, $is_updating) use ($closure) {
            if ( !$is_updating ) {
                $closure( $post );
            }
        }, $draft );
	}

    /**
     * @param Closure $closure
     * @param bool $draft
     * @return void
     */
	public function updating(Closure $closure, bool $draft = false):void
	{
        $this->saving( function ($post, $is_updating) use ($closure) {
            if ( $is_updating ) {
                $closure( $post );
            }
        }, $draft );
	}

	/**
	 * @param Closure $closure
	 *
	 * @return void
	 */
	public function trashing(Closure $closure):void
	{
		Actions::add( 'wp_trash_post', function ($post_id) use ($closure) {
			$post = get_post( $post_id );

			if ( $post->post_type === $this->slug ) {
				$closure( $post );
			}
		} );
	}

	/**
	 * @param Closure $closure
	 *
	 * @return void
	 */
	public function restoring(Closure $closure):void
	{
		Actions::add( 'untrash_post', function ($post_id) use ($closure) {
			$post = get_post( $post_id );

			if ( $post->post_type === $this->slug ) {
				$closure( $post );
			}
		} );
	}

	/**
	 * @param Closure $closure
	 *
	 * @return void
	 */
	public function deleting(Closure $closure):void
	{
		Actions::add( 'delete_post', function ($post_id) use ($closure) {
            $post = get_post( $post_id );

			if ( $post->post_type === $this->slug ) {
				$closure( $post );
			}
		} );
	}

	/**
	 * @return void
	 */
	public function register():void
	{
		if ( empty( $this->slug ) ) {
			$this->slug = Str::slug( $this->name, '_' );
		}

        $is_post = $this->slug === 'post';

        if ( !$is_post && empty( $this->restBase ) ) {
			$this->restBase = $this->slug;
		}

		if ( !$is_post && empty( $this->restControllerClass ) ) {
			$this->restControllerClass = WP_REST_Posts_Controller::class;
		}

		if ( !$is_post && empty( $this->rewrite ) ) {
			$this->rewrite = [ 'slug' => $this->slug, 'with_front' => true ] ;
		}

		Actions::add('after_setup_theme', function () {
            register_post_type( $this->slug, [
                'name' => $this->name,
                'singular_name' => $this->singularName,
                'plural_name' => $this->pluralName,
                'can_export' => $this->canExport,
                'capability_type' => $this->capabilityType,
                'capabilities' => $this->capabilities,
                'description' => $this->description,
                'exclude_from_search' => $this->excludeFromSearch,
                'has_archive' => $this->hasArchive,
                'hierarchical' => $this->hierarchical,
                'label' => $this->name,
                'labels' => $this->labels(),
                'menu_icon' => $this->menuIcon,
                'menu_position' => $this->menuPosition,
                'public' => $this->public,
                'publicly_queryable' => $this->publiclyQueryable,
                'rest_base' => $this->restBase,
                'rest_controller_class' => $this->restControllerClass,
                'rewrite' => $this->rewrite,
                'show_in_admin_bar' => $this->showInAdminBar,
                'show_in_menu' => $this->showInMenus,
                'show_in_nav_menus' => $this->showInNavMenus,
                'show_in_rest' => $this->showInRest,
                'show_ui' => $this->showUI,
                'supports' => $this->supports,
                'query_var' => $this->queryVar,
            ] );

            foreach ( $this->taxonomies as $taxonomy_name ) {
                register_taxonomy_for_object_type( $taxonomy_name, $this->slug );
            }
        } );

        if ( !$is_post ) {
            Filters::add( 'use_block_editor_for_post_type', function (bool $is_enabled, string $post_type) {
                return $post_type === $this->slug ? $this->disableGutenberg : $is_enabled;
            }, 10, 2);

            Filters::add( 'post_updated_messages', function (array $messages ) {
                global $post;

                return collect( $this->messages( $post ) )->prepend( '')->toArray();
            } );
        }

        Fields::postType( $this->slug, ucfirst( $this->name ) . ' Fields', function ( Location $group ) {
            $fields = $this->fields( $group );

            if ( !empty( $fields ) ) {
                $group->fields( $fields );
            }
		} );

		$this->boot();
	}

    /**
     * @return array
     */
    protected function labels(): array
    {
        return collect( [
            'add_new' => [],
            'add_new_item' => [ 'name' => $this->singularName ],
            'all_items' => [ 'name' => $this->pluralName ],
            'archives' => [ 'name' => $this->pluralName ],
            'attributes' => [ 'name' => $this->singularName ],
            'edit_item' => [ 'name' => $this->singularName ],
            'featured_image' => [],
            'filter_items_list' => [],
            'insert_into_item' => [],
            'items_list' => [ 'name' => $this->pluralName ],
            'items_list_navigation' => [ 'name' => $this->pluralName ],
            'menu_name' => [ 'name' => $this->pluralName ],
            'name' => [ 'name' => $this->singularName ],
            'name_admin_bar' => [ 'name' => $this->singularName ],
            'new_item' => [ 'name' => $this->singularName ],
            'not_found' => [],
            'not_found_in_trash' => [],
            'parent_item_colon' => [ 'name' => $this->singularName ],
            'remove_featured_image' => [],
            'search_items' => [ 'name' => $this->pluralName ],
            'set_featured_image' => [],
            'singular_name' => [ 'name' => $this->singularName ],
            'update_item' => [ 'name' => $this->singularName ],
            'uploaded_to_this_item' => [],
            'use_featured_image' => [],
            'view_item' => [ 'name' => $this->singularName ],
            'view_items' => [ 'name' => $this->pluralName ],
        ] )
            ->map( fn ( $args, $key ) => _t( $this->labelsDomain . '.' . $key, $args ) )
            ->toArray();
    }

    /**
     * @param WP_Post $post
     * @return array
     */
    protected function messages(WP_Post $post): array
    {
        $permalink = esc_url( get_permalink( $post ) );

        $view_item = _t( $this->labelsDomain . '.' . 'view_item', [ 'name' => $this->singularName ] );
        $preview_item = _t( $this->labelsDomain . '.' . 'preview_item', [ 'name' => $this->singularName ] );

        return [
            _t( $this->labelsDomain . '.' . 'item_updated', [ 'name' => $this->singularName ] ) .
                sprintf( '<a target="_blank" href="%s">%s</a>', $permalink, $view_item ),

            _t( $this->labelsDomain . '.' . 'field_updated', [ 'name' => $this->singularName ] ),

            _t( $this->labelsDomain . '.' . 'field_deleted', [ 'name' => $this->singularName ] ),

            _t( $this->labelsDomain . '.' . 'item_updated', [ 'name' => $this->singularName ] ),

            isset( $_GET['revision'] ) ? _t( $this->labelsDomain . '.' . 'item_restored', [ 'name' => $this->singularName, 'date' => wp_post_revision_title( (int) $_GET['revision'], false ) ] ) : false,

            _t( $this->labelsDomain . '.' . 'item_published', [ 'name' => $this->singularName ] ) .
                sprintf( '<a href="%s">View :singularName</a>', $permalink ),

            _t( $this->labelsDomain . '.' . 'item_saved', [ 'name' => $this->singularName ] ),

            _t( $this->labelsDomain . '.' . 'item_submitted', [ 'name' => $this->singularName ] ) .
                sprintf( '<a target="_blank" href="%s">%s</a>', esc_url( add_query_arg( 'preview', 'true', $permalink ) ), $preview_item ),

            _t( $this->labelsDomain . '.' . 'item_scheduled', [ 'name' => $this->singularName, 'date' => date_i18n( __( 'M j, Y @ G:i'), strtotime( $post->post_date ) ) ] ) .
                sprintf( '<a target="_blank" href="%s">%s</a>', $permalink, $preview_item ),

            _t( $this->labelsDomain . '.' . 'item_draft_updated', [ 'name' => $this->singularName ] ) .
                sprintf( '<a target="_blank" href="%s">%s</a>', esc_url( add_query_arg( 'preview', 'true', $permalink ) ), $preview_item ),
        ];
    }
}