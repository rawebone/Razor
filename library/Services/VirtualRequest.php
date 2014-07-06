<?php

namespace Razor\Services;

use Razor\Dispatcher;
use Razor\Environment;
use Razor\Razor;
use Symfony\Component\HttpFoundation\Request;

/**
 * VirtualRequest provides the ability to make subsequent
 * EndPoint calls on server, without requiring addition
 * HTTP overhead. This also helps to keep with the REST
 * API together by making sure we keep our particular
 * logic for resources together.
 *
 * @package Razor\Services
 */
class VirtualRequest
{
	public function call($endPoint, Request $request)
	{
		$environment = Razor::environment();
		$environment->services()
					->register("request", $request);

		// Prevent the EndPoint from immediately running
		// so that we can dispatch manually ourselves
		$testing = $environment->testing;
		$environment->testing = true;

		require $endPoint;

		$environment->testing = $testing;

		return (new Dispatcher())->dispatch($environment, Razor::endPoint(), true);
	}
}

