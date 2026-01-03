<?php
namespace Daedelus\Theme\Hooks;

use Daedelus\Foundation\Hooks\Hook;
use Daedelus\Theme\Admin;

class RegisterAdmin extends Hook
{
    /**
     * @return void
     */
    public function register():void
    {
	    Admin::register();
    }
}