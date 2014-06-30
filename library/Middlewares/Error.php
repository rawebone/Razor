<?php

namespace Razor\Middlewares;

use Razor\Http;
use Razor\Middleware;

class Error extends Middleware
{
	protected $displayErrors = false;

	public function __invoke(Http $http)
	{
		try {
			return $this->invokeTarget();

		} catch (\Exception $ex) {
			$content = ($this->displayErrors ? $ex : "");
			return $http->response->standard($content, 500);
		}
	}

	public function displayErrors($bool)
	{
		$this->displayErrors = (bool)$bool;
	}
}
