<?php

namespace Razor\Middlewares;

use Razor\Http;
use Razor\Middleware;

class Dispatcher extends Middleware
{
	protected $controller;

	public function __invoke(Http $http)
	{
	}

	public function displayErrors($bool)
	{
		$this->displayErrors = (bool)$bool;
	}
}
