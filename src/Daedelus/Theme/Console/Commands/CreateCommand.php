<?php

namespace Daedelus\Theme\Console\Commands;

use Daedelus\Theme\Theme;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'theme:create')]
class CreateCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'theme:create {name=majestic}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Install the theme files into the [wp-]content/themes directory';

	/**
	 * @param Filesystem $files
	 */
	public function __construct(protected Filesystem $files)
	{
		parent::__construct();
	}

	/**
	 * Get the stubs path
	 *
	 * @return string
	 */
	protected function getStubs(): string
	{
		return Theme::path( '/stubs/theme' );
	}

	/**
	 * Build the directory for the theme if necessary.
	 *
	 * @param  string  $path
	 * @return string
	 */
	protected function makeDirectory(string $path): string
	{
		if ( !$this->files->isDirectory( $path ) ) {
			$this->files->makeDirectory( $path, 0777, true, true );
		}

		return $path;
	}

	/**
	 * @return bool
	 */
	public function handle(): bool
	{
		$name = trim( $this->argument('name' ) );

		$path = app()->contentPath('themes' . DIRECTORY_SEPARATOR . $name );

		$this->makeDirectory( $path );

		if ( ( !$this->hasOption('force') || !$this->option('force')) && !$this->files->isEmptyDirectory( $path ) ) {
			$this->components->error( sprintf( 'Theme [%s] already exists.', $path ) );

			return false;
		}

		$this->files->copyDirectory( $this->getStubs(), $path );

		$this->components->info( sprintf('Theme [%s] created successfully.', $path ) );

		return true;
	}
}