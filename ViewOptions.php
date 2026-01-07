<?php

namespace Daedelus\Theme;

use Closure;

/**
 * @method ViewOptions name(string $template_name)
 * @method ViewOptions render(Closure $callback)
 * @method ViewOptions withPost(string $key = 'post')
 * @method ViewOptions withFields(Closure|null $callback = null, string $key = 'fields')
 * @method ViewOptions fields(Closure $callback)
 * @method ViewOptions type(string $type)
 */
class ViewOptions
{
	/**
	 * @param string $name
	 * @param array $arguments
	 *
	 * @return mixed
	 */
	public function __call( string $name, array $arguments )
	{
		return call_user_func_array( $name, $arguments );
	}
}