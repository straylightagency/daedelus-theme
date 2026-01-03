<?php
namespace Daedelus\Theme\Hooks;

use Daedelus\Support\Actions;
use Daedelus\Framework\Hooks\Hook;

class EnqueueAssets extends Hook
{
	/**
	 * @return void
	 */
    public function register():void
    {
        Actions::add( 'wp_enqueue_scripts', function () {
            $this->registerAssets( config('theme.assets', [] ) );
        } );

        Actions::add( 'admin_enqueue_scripts', function () {
            $this->registerAdminAssets( config('theme.assets_admin', [] ) );
        } );
    }

    /**
     * @param array $config
     * @return void
     */
    protected function registerAssets(array $config):void
    {
        if ( isset( $config[ 'styles' ] ) ) {
            $this->enqueueStyles( $config[ 'styles' ] );
        }

        if ( isset( $config[ 'scripts' ] ) ) {
            $this->enqueueScripts( $config[ 'scripts' ] );
        }

        if ( is_single() && comments_open() && get_option( 'thread_comments' ) ) {
            wp_enqueue_script('comment-reply');
        }
    }

    /**
     * @param array $config
     * @return void
     */
    protected function registerAdminAssets(array $config):void
    {
        if ( isset( $config[ 'styles' ] ) ) {
            $this->enqueueStyles( $config[ 'styles' ] );
        }

        if ( isset( $config[ 'scripts' ] ) ) {
            $this->enqueueScripts( $config[ 'scripts' ] );
        }

        if ( isset( $config[ 'editor' ] ) ) {
            add_editor_style( $config[ 'editor' ] );
        }
    }

	/**
	 * @param array $styles
	 * @return void
	 */
	protected function enqueueStyles(array $styles): void
	{
		foreach ( $styles as $handle => $style ) {
			$default = [
				'src' => false,
				'ver' => false,
                'media' => 'all',
				'deps' => [],
			];

			if ( is_string( $style ) ) {
				$style = [
					'src' => $style,
				];
			}

			$style = (object) [ ...$default, ...$style ];

            if ( !$style->ver ) {
                $style->ver = $this->getFileVersion( $style->src );
            }

            wp_enqueue_style( $handle, $style->src, $style->deps, $style->ver );
		}
	}

    /**
     * @param array $scripts
     * @return void
     */
    protected function enqueueScripts(array $scripts):void
    {
        foreach ( $scripts as $handle => $script ) {
	        $default = [
		        'src' => false,
		        'ver' => false,
		        'in_footer' => false,
		        'strategy' => 'async',
                'deps' => [],
	        ];

			if ( is_string( $script ) ) {
				$script = [
					'src' => $script,
				];
			}

			$script = (object) [ ...$default, ...$script ];

            if ( !$script->ver ) {
                $script->ver = $this->getFileVersion( $script->src );
            }

            wp_enqueue_script( $handle, $script->src, $script->deps, $script->ver, [
                'in_footer' => $script->in_footer,
                'strategy' => $script->strategy,
            ] );
        }
    }

    /**
     * @param string $file_path
     * @return int|false
     */
    protected function getFileVersion(string $file_path):int|false
    {
        return str_starts_with( $file_path, '/' ) && file_exists( public_path( $file_path ) ) ?
            filemtime( public_path( $file_path ) ) : false;
    }
}