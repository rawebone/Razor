<?php

namespace Razor\Middlewares;

use Razor\Http;
use Razor\Middleware;
use Razor\Controller;

class MethodNotAvailable extends Middleware
{
	protected $controller;

	public function __invoke(Http $http)
	{
		$method = strtolower($http->request->getMethod());

		if (is_null($this->controller->$method)) {
			return $http->response->standard("", 405);
		} else {
			return $this->invokeTarget();
		}
	}

	public function letControllerBe(Controller $controller)
	{
		$this->controller = $controller;
	}
}
