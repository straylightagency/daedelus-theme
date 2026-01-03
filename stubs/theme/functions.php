<?php

use Daedelus\Foundation\Application;
use Daedelus\Theme\Theme;

if ( basename( $_SERVER['SCRIPT_NAME'] ) === basename( __FILE__ ) ) {
	die(); /** trying to access directly from URL */
}

if ( !class_exists( Application::class ) ) {
	?>
	<p style="font-family:monospace;margin:20px 10px;">
		<strong style="color:red;">ERROR:</strong> You need to install Majestic to use this theme. Please follow instructions
		<a href="https://github.com/anthonypauwels/majestic">here</a>
	</p>
	<?php
	exit;
}

/** @var Theme $theme */
$theme = app( Theme::class );

$theme->setup();