<?php

namespace Daedelus\Theme\Templates;

use Daedelus\Support\Filters;
use Daedelus\Theme\ViewScanner;
use Illuminate\Support\Stringable;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

/**
 *
 */
class TemplatesMapper
{
	/** @var array */
	protected array $templates = [];

    /**
     * @param ViewScanner $scanner
     */
	public function __construct(protected ViewScanner $scanner)
	{
	}

	/**
	 * @param string $path
	 *
	 * @return array
	 */
	public function loadFromPath(string $path):array
	{
        $templates = [];

        /** @var SplFileInfo $file */
        foreach ( $this->templatesFiles( $path ) as $file ) {
            $metadata = $this->scanner->getMetadata( $file->getPathname() );

            $basename = $this->fileBaseName( $file );

            $templates[ $basename->toString() ] = $this->templates[ $basename->toString() ] = (object) [
                'path' => $file->getPathname(),
                'type' => $metadata->type ?: 'page',
                'name' => $metadata->name ?: $basename->ucfirst()->toString(),
                'fields' => $metadata->fields,
            ];
        }

		return $templates;
	}

	/**
	 * @param string $template_name
	 *
	 * @return object|null
	 */
	public function findByName(string $template_name): ?object
	{
		if ( isset( $this->templates[ $template_name ] ) ) {
			return $this->templates[ $template_name ];
		}

		return null;
	}

	/**
	 * @param string $type
	 *
	 * @return object|null
	 */
	public function findByType(string $type): ?object
	{
		$template_name = collect( $this->templates )->filter( fn ( $tpl ) => $tpl->type === $type )->keys()->first();

		if ( $template_name ) {
			return $this->templates[ $template_name ];
		}

		return null;
	}

	/**
	 * @param string $path
	 *
	 * @return array
	 */
	protected function templatesFiles(string $path):array
	{
		return iterator_to_array(
			Finder::create()
			      ->name('*.blade.php')
			      ->files()
			      ->ignoreDotFiles( true )
			      ->in( $path )
			      ->sortByName(),
			false
		);
	}

	/**
	 * @param SplFileInfo $file
	 *
	 * @return Stringable
	 */
	protected function fileBaseName(SplFileInfo $file):Stringable
	{
		return ( new Stringable( $file->getBasename() ) )->before('.');
	}
}