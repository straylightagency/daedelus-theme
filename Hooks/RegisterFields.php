<?php
namespace Daedelus\Theme\Hooks;

use Daedelus\Fields\Fields;
use Daedelus\Foundation\Hooks\Hook;

class RegisterFields extends Hook
{
    /**
     * @return void
     */
    public function register():void
    {
	    Fields::config( config('custom_fields') );

	    Fields::register();
    }
}