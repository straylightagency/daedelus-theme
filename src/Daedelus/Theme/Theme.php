<?php
namespace Daedelus\Theme;

use Daedelus\Support\Actions;
use Daedelus\Support\Filters;
use Daedelus\Theme\Pages\PagesManager;
use Daedelus\Theme\Templates\TemplatesManager;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Support\Facades\Route;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 *
 */
class Theme
{
	/**
	 * Constructor.
	 */
	public function __construct(
		protected ApplicationContract $app,
		protected TemplatesManager $templates,
		protected PagesManager $pages,
		protected ViewScanner $scanner
	) {
	}

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
	public function setup():void
	{
		$this->handlePagesRouting();

		$this->handleTemplates();
	}

    /**
     * @return void
     */
	public function handlePagesRouting():void
	{
		Route::fallback( $this->pages->handler() );
	}

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
	public function handleTemplates():void
	{
		$this->templates->loadTemplates();

		Actions::add('template_redirect', $this->templates->templateRedirect( $this->app->get('request') ), 99 );
	}

	/**
	 * @param string $path
	 *
	 * @return string
	 */
	public static function path(string $path = ''): string
	{
		return __DIR__ . ( $path != '' ? DIRECTORY_SEPARATOR . $path : '');
	}
}