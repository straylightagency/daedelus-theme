<?php
namespace Daedelus\Theme\Config;

/**
 *
 */
abstract class Shortcode
{
    /**
     * Get the name of the shortcode
     *
     * @return string
     */
    abstract public function getName():string;

    /**
     * Render the shortcode
     *
     * @param $attrs
     * @param null $content
     * @return string
     */
    abstract public function render($attrs, $content = null):string;

    /**
     * Register the shortcode into WordPress
     */
    public function register():void
    {
        add_shortcode( $this->getName(), [ $this, 'render' ] );
    }

    /**
     * Create the shortcode HTML tag
     *
     * @param string $tag
     * @param string|null $content
     * @param array $attributes
     *
     * @return string
     */
    protected static function createTag(string$tag, ?string $content = null, array $attributes = []):string
    {
        return '<' . $tag . ' ' . join(' ', array_map( function ( $key ) use ( $attributes ) {
                if ( is_null( $attributes[ $key ] ) ) {
                    return '';
                }

                if ( is_bool( $attributes[ $key ] ) ) {
                    return $attributes[ $key ] ? $key : '';
                }

                return $key . '="' . $attributes[ $key ] . '"';
            }, array_keys( $attributes ) ) ) . '>' . do_shortcode( $content ) . '</' . $tag . '>';
    }
}