<?php

namespace Razor;
use Rawebone\Injector\Injector;
use Symfony\Component\HttpFoundation\Response;

/**
 * Dispatcher is the core of the handling in framework. It is
 * designed to provide stateless HTTP Dispatching against an End Point.
 *
 * @package Razor
 */
class Dispatcher
{
	public function __construct(Environment $environment, EndPoint $endPoint)
	{
		// Do not dispatch when testing
		if ($environment->testing) {
			return;
		}

		$injector = new Injector();
		$injector->resolver($environment->services());
		$environment->services()->register("injector", $injector);

		/** @var \Symfony\Component\HttpFoundation\Request $request */
		$request = $injector->service("request");
		$method  = strtolower($request->getMethod());

		try {
			$handler = (method_exists($endPoint, $method) && !is_null($endPoint->$method()) ? $endPoint->$method() : $endPoint->onNotFound());
			$resp = $injector->inject($handler);

		} catch (HttpAbortException $ex) {
			// Response has been sent manually or the application is in some other
			// state - simply let the framework clean up.
			$resp = null;

		} catch (\Exception $ex) {
			if ($environment->development) {
				throw $ex;
			}

			$environment->services()->register("exception", $ex);
			$resp = $injector->inject($endPoint->onError());
		}

		if ($resp instanceof Response) {
			$resp->send();
		}
	}
} 