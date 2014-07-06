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
	public function dispatch(Environment $environment, EndPoint $endPoint, $virtual = false)
	{
		// Do not dispatch when testing
		if ($environment->testing) {
			return;
		}

		$injector = $this->makeInjectorInstance($environment);

		try {
			$resp = $this->dispatchMethod($injector, $endPoint);

		} catch (HttpAbortException $ex) {
			// Response has been sent manually or the application is in some other
			// state - simply let the framework clean up.
			$resp = null;

		} catch (\Exception $ex) {
			if ($environment->development) {
				throw $ex;
			}

			$resp = $this->dispatchError($injector, $endPoint, $environment, $ex);
		}

		if (!$virtual && $resp instanceof Response) {
			$resp->send();
		}

		return $resp;
	}

	/**
	 * Returns an Injector instance for the current environment.
	 *
	 * @param Environment $environment
	 * @return Injector
	 */
	protected function makeInjectorInstance(Environment $environment)
	{
		$injector = new Injector();
		$injector->resolver($environment->services());
		$environment->services()->register("injector", $injector);

		return $injector;
	}

	/**
	 * Handles the dispatch of the current HTTP method against the End Point
	 *
	 * @param Injector $injector
	 * @param EndPoint $endPoint
	 * @return Response|null
	 */
	protected function dispatchMethod(Injector $injector, EndPoint $endPoint)
	{
		/** @var \Symfony\Component\HttpFoundation\Request $request */
		$request = $injector->service("request");
		$method  = strtolower($request->getMethod());

		$handler = (method_exists($endPoint, $method) && !is_null($endPoint->$method()) ? $endPoint->$method() : $endPoint->onNotFound());

		if ($handler instanceof Middleware) {
			$handler->letInjectorBe($injector);
		}

		return $injector->inject($handler);
	}

	/**
	 * Handles the dispatch of an exception to the onError handler.
	 *
	 * @param Injector $injector
	 * @param EndPoint $endPoint
	 * @param Environment $environment
	 * @param \Exception $exception
	 * @return Response|null
	 */
	protected function dispatchError(Injector $injector, EndPoint $endPoint, Environment $environment, \Exception $exception)
	{
		$environment->services()->register("exception", $exception);

		$errorHandler = $endPoint->onError();

		if ($errorHandler instanceof Middleware) {
			$errorHandler->letInjectorBe($injector);
		}

		return $injector->inject($errorHandler);
	}
} 