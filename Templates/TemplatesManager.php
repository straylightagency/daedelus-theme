<?php

namespace Daedelus\Theme\Templates;

use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Daedelus\Fields\Location;
use InvalidArgumentException;
use UnexpectedValueException;
use Daedelus\Support\Filters;
use Daedelus\Theme\ViewScanner;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\View as ViewFacade;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

/**
 * Manage WordPress theme templates
 */
class TemplatesManager
{
	/** @var array */
	protected array $paths = [];

    /** @var TemplatesMapper */
	protected TemplatesMapper $mapper;

	/**
	 * @param ViewScanner $scanner
	 */
	public function __construct(
        protected ViewScanner $scanner
    ){
		$this->mapper = new TemplatesMapper( $this->scanner );
	}

	/**
     * Set a new templates path
     *
	 * @param string|null $path
	 *
	 * @return self
	 */
	public function path(?string $path = null): self
	{
		$path = realpath( $path ?: config('view.paths')[0] . '/templates' );

		if ( !is_dir( $path ) ) {
			throw new InvalidArgumentException( sprintf( 'The given path " %s " is not a directory.', $path ) );
		}

		$this->paths[] = $path;

		return $this;
	}

	/**
     * Load templates from the mapper and initialize them into WordPress with fields, filters, etc.
     *
	 * @return void
	 */
	public function loadTemplates():void
	{
        /**
         * Collect templates from each path with the mapper object
         */
        $theme_templates = array_reduce( $this->paths,
            fn (Collection $theme_templates, $path) => $theme_templates->merge( $this->mapper->loadFromPath( $path ) ),
            collect()
        );

		$templates_by_type = $theme_templates->groupBy( 'type', preserveKeys: true );

		foreach ( $templates_by_type as $post_type => $templates ) {
            if ( $post_type === 404 ) {
                continue;
            }

            /**
             * Show a template selector in the admin editor if there is more than one template available for this post type
             *
             * We exclude the "default" template from the list
             */
            if ( count( $templates ) > 1 ) {
                Filters::add('theme_' . $post_type . '_templates', fn () =>
                    $templates->map( fn ( $template ) => $template->name )->filter( fn (string $value, string $key) => $key !== 'default' )->toArray(),
                    99
                );
            }

			foreach ( $templates as $template ) {
				if ( $template->fields ) {
					$location = Location::make( $template->name, $template->name );

					$fields = ( $template->fields )( $location );

					if ( is_array( $fields ) && !empty( $fields ) ) {
						$location->fields( $fields );
					}

					if ( $post_type ) {
						$location->andPageTemplate( Str::before( basename( $template->path ), '.' ) );
					} else {
						$location->andPostType( $template->type );
					}
				}
			}
		}
	}

    /**
     * Do the template redirect over the WordPress basic behavior
     *
     * @param Request $request
     * @return Closure
     */
	public function templateRedirect(Request $request): Closure
	{
		return function () use ( $request ) {
			if ( !$this->mainQueryTemplateAllowed( $request ) ) {
				return;
			}

            /**
             * Bypass WordPress behavior
             */
			Filters::add('wp_using_themes', fn () => false, 99 );

			$methods = [
				'is_embed'             => fn () => $this->getTemplateEmbed(),
				'is_404'               => fn () => $this->getTemplate404(),
				'is_search'            => fn () => $this->getTemplateSearch(),
				'is_front_page'        => fn () => $this->getTemplateFrontPage(),
				'is_home'              => fn () => $this->getTemplateHome(),
				'is_privacy_policy'    => fn () => $this->getTemplatePrivacyPolicy(),
				'is_post_type_archive' => fn () => $this->getTemplatePostTypeArchive(),
				'is_tax'               => fn () => $this->getTemplateTaxonomy(),
				'is_attachment'        => fn () => $this->getTemplateAttachment(),
				'is_single'            => fn () => $this->getTemplateSingle(),
				'is_page'              => fn () => $this->getTemplatePage(),
				'is_singular'          => fn () => $this->getTemplateSingular(),
				'is_category'          => fn () => $this->getTemplateCategory(),
				'is_tag'               => fn () => $this->getTemplateTag(),
				'is_author'            => fn () => $this->getTemplateAuthor(),
				'is_date'              => fn () => $this->getTemplateDate(),
				'is_archive'           => fn () => $this->getTemplateArchive(),
			];

            $view = null;

            try {
				foreach ( $methods as $tag => $template_method ) {
					if ( call_user_func( $tag ) ) {
						$view = $template_method();
					}

					if ( $view ) {
						if ( 'is_attachment' === $tag ) {
							Filters::remove( 'the_content', 'prepend_attachment' );
						}

						break;
					}
				}
			} catch ( MethodNotAllowedHttpException | NotFoundHttpException $exception ) {
				$view = $this->getTemplate404();
			}

			if ( !$view ) {
				$view = $this->getTemplateDefault();
			}

			/** @var View $view */
			$view = Filters::apply( 'majestic/view', $view );

            if ( !$view ) {
                throw new UnexpectedValueException( 'No view available for this resource' );
            }

			echo $view->render();
		};
	}

	/**
     * Check if the request needs a template or not
     *
	 * @param Request $request
	 *
	 * @return bool
	 */
	protected function mainQueryTemplateAllowed(Request $request): bool
	{
		return
			(
				!$request->isMethod('HEAD')
				|| !Filters::apply('exit_on_http_head', true)
			)
            && !$request->get('is_laravel_request')
			&& !is_robots()
			&& !is_favicon()
			&& !is_feed()
			&& !is_trackback()
			&& !is_embed();
	}

    /**
     * Transform the right template into a renderable Blade View
     *
     * @param array $templates
     * @param mixed|null $object
     * @return View|null
     */
    protected function getQueriedView(array $templates, mixed $object = null): ?View
    {
        foreach ( $templates as $callback ) {
            if ( $template = $callback() ) {
                $metadata = $this->scanner->getMetadata( $template->path );

                return (function (...$args) use ( $metadata, $template ) {
                    $buffer = array_reduce( $metadata->renders, fn ($buffer, $render) => array_merge( $buffer, $render( ...$args ) ), [] );

                    return ViewFacade::file( $template->path, $buffer );
                })( $object );
            }
        }

        return null;
    }

    /**
     * Get a template for page
     *
     * @return View|null
     */
    protected function getTemplatePage(): ?View
    {
        $object = get_queried_object();

        $templates = Filters::apply( 'majestic/page_templates', [
            fn () => $this->mapper->findByName( get_page_template_slug( $object ) ),
            fn () => $this->mapper->findByType( $object->post_type ),
            fn () => $this->mapper->findByName( $object->post_type ),
            fn () => $this->mapper->findByName( $object->post_type . '-' . urldecode( $object->post_name ) ),
            fn () => $this->mapper->findByName( $object->post_type . '-' . $object->post_name ),
            fn () => $this->mapper->findByName( urldecode( $object->post_name ) ),
            fn () => $this->mapper->findByName( $object->post_name ),
        ] );

        return $this->getQueriedView( $templates, $object );
    }

    /**
     * Get a template for single post
     *
     * @return View|null
     */
    protected function getTemplateSingle(): ?View
    {
        $object = get_queried_object();

        $templates = Filters::apply( 'majestic/single_templates', [
            fn () => $this->mapper->findByName( get_page_template_slug( $object ) ),
            fn () => $this->mapper->findByType( $object->post_type ),
            fn () => $this->mapper->findByName( $object->post_type ),
            fn () => $this->mapper->findByName( 'single-' . $object->post_type . '-' . urldecode( $object->post_name ) ),
            fn () => $this->mapper->findByName( 'single-' . $object->post_type . '-' . $object->post_name ),
            fn () => $this->mapper->findByName( 'single-' . $object->post_type ),
            fn () => $this->mapper->findByName( 'single' ),
        ] );

        return $this->getQueriedView( $templates, $object );
    }

    /**
     * Get a template for the privacy policy page
     *
     * @return View|null
     */
    protected function getTemplatePrivacyPolicy(): ?View
    {
        $templates = Filters::apply( 'majestic/privacy_policy_templates', [
            fn () => $this->mapper->findByType( 'privacy-policy' ),
            fn () => $this->mapper->findByName( 'privacy-policy' ),
        ] );

        return $this->getQueriedView( $templates, get_queried_object() );
    }

    /**
     * Get the template for the 404 page
     *
     * @return View|null
     */
    protected function getTemplate404(): ?View
    {
        global $wp_query;

        if ( !$wp_query->is_404() ) {
            $wp_query->set_404();
        }

        $templates = Filters::apply( 'majestic/404_templates', [
            fn () => $this->mapper->findByType( '404' ),
            fn () => $this->mapper->findByName( '404' ),
        ] );

        return $this->getQueriedView( $templates );
    }

    /**
     * @return View|null
     */
    protected function getTemplateArchive(): ?View
    {
        $post_type = collect( get_query_var( 'post_type' ) )->filter()->first();

        $templates = Filters::apply( 'majestic/archive_templates', [
            fn () => $this->mapper->findByType( "archive-{$post_type}" ),
            fn () => $this->mapper->findByType( 'archive' ),
        ] );

        return $this->getQueriedView( $templates );
    }

    /**
     * Get a template for a post type archive
     *
     * @return View|null
     */
    protected function getTemplatePostTypeArchive(): ?View
    {
        return $this->getTemplateArchive();
    }

    /**
     * Get the template for the home page
     *
     * @return View|null
     */
    protected function getTemplateHome(): ?View
    {
        $templates = Filters::apply( 'majestic/home_templates', [
            fn () => $this->mapper->findByType( 'home' ),
            fn () => $this->mapper->findByName( 'home' ),
        ] );

        return $this->getQueriedView( $templates, get_queried_object() );
    }

    /**
     * Get the template for the front page
     *
     * @return View|null
     */
    protected function getTemplateFrontPage(): ?View
    {
        $templates = Filters::apply( 'majestic/front_page_templates', [
            fn () => $this->mapper->findByType( 'front-page' ),
            fn () => $this->mapper->findByName( 'front-page' ),
        ] );

        return $this->getQueriedView( $templates, get_queried_object() );
    }

    /**
     * Get a template for taxonomy
     *
     * @return View|null
     */
    protected function getTemplateTaxonomy(): ?View
    {
        $templates = Filters::apply( 'majestic/taxonomy_templates', [
            fn () => $this->mapper->findByType( 'taxonomy' ),
            fn () => $this->mapper->findByName( 'taxonomy' ),
        ] );

        return $this->getQueriedView( $templates );
    }

    /**
     * Get the template for the search page
     *
     * @return View|null
     */
    protected function getTemplateSearch(): ?View
    {
        $templates = Filters::apply( 'majestic/search_templates', [
            fn () => $this->mapper->findByType( 'search' ),
            fn () => $this->mapper->findByName( 'search' ),
        ] );

        return $this->getQueriedView( $templates );
    }

    /**
     * Get a template for embed
     *
     * @return View|null
     */
    protected function getTemplateEmbed(): ?View
    {
        $object = get_queried_object();

        $post_format = get_post_format( $object );

        $templates = Filters::apply( 'majestic/embed_templates', [
            fn () => $this->mapper->findByType( "embed-{$object->post_type}-{$post_format}" ),
            fn () => $this->mapper->findByType( "embed-{$object->post_type}" ),
            fn () => $this->mapper->findByName( 'embed' ),
        ] );

        return $this->getQueriedView( $templates, $object );
    }

    /**
     * Get a template for singular page
     *
     * @return View|null
     */
    protected function getTemplateSingular(): ?View
    {
        $templates = Filters::apply( 'majestic/singular_templates', [
            fn () => $this->mapper->findByType( 'singular' ),
            fn () => $this->mapper->findByName( 'singular' ),
        ] );

        return $this->getQueriedView( $templates );
    }

    /**
     * Get a template for category
     *
     * @return View|null
     */
    protected function getTemplateCategory(): ?View
    {
        $templates = Filters::apply( 'majestic/category_templates', [
            fn () => $this->mapper->findByType( 'category' ),
            fn () => $this->mapper->findByName( 'category' ),
        ] );

        return $this->getQueriedView( $templates );
    }

    /**
     * @return View|null
     */
    protected function getTemplateTag(): ?View
    {
        $templates = Filters::apply( 'majestic/tag_templates', [
            fn () => $this->mapper->findByType( 'tag' ),
            fn () => $this->mapper->findByName( 'tag' ),
        ] );

        return $this->getQueriedView( $templates );
    }

    /**
     * Get a template for author
     *
     * @return View|null
     */
    protected function getTemplateAuthor(): ?View
    {
        $templates = Filters::apply( 'majestic/author_templates', [
            fn () => $this->mapper->findByType( 'author' ),
            fn () => $this->mapper->findByName( 'author' ),
        ] );

        return $this->getQueriedView( $templates );
    }

    /**
     * Get a template for date
     *
     * @return View|null
     */
    protected function getTemplateDate(): ?View
    {
        $templates = Filters::apply( 'majestic/date_templates', [
            fn () => $this->mapper->findByType( 'date' ),
            fn () => $this->mapper->findByName( 'date' ),
        ] );

        return $this->getQueriedView( $templates );
    }

    /**
     * Get a template for attachment
     *
     * @return View|null
     */
    protected function getTemplateAttachment(): ?View
    {
        $object = get_queried_object();

        $mimetype = $object->post_mime_type;

        if ( str_contains( $object->post_mime_type, '/' ) ) {
            $mimetype = str_replace( '/', '-', $object->post_mime_type );
        }

        $templates = Filters::apply( 'majestic/attachment_templates', [
            fn () => $this->mapper->findByType( "attachment-{$mimetype}" ),
            fn () => $this->mapper->findByName( 'attachment' ),
        ] );

        return $this->getQueriedView( $templates, $object );
    }

    /**
     * Get the default template
     *
     * @return View|null
     */
    protected function getTemplateDefault(): ?View
    {
        $templates = Filters::apply( 'majestic/default_templates', [
            fn () => $this->mapper->findByType( 'default' ),
            fn () => $this->mapper->findByName( 'default' ),
        ] );

        return $this->getQueriedView( $templates, get_queried_object() );
    }
}