<?php

namespace Razor;

use Rawebone\Injector\Injector;

/**
 * Handles dispatching
 */
class HttpDispatcher
{
	protected $injector;
	protected $resolver;
	protected $events;

	public function __construct(Injector $injector, ServiceResolver $resolver, Events $events)
	{
		$this->injector = $injector;
		$this->resolver = $resolver;
		$this->events   = $events;
	}

	public function dispatch(Controller $controller)
	{
		$this->events->fire("http.pre-dispatch");

		// Prevent modification to services inside of a controller
		// as such behaviour will make it difficult to in-line later.
		$key = $this->resolver->lock();

		/** @var \Symfony\Component\HttpFoundation\Request $request */
		$request = $this->injector->service("request");
		$method  = strtolower($request->getMethod());

		try {
			if (($handler = $controller->$method) !== null) {
				$this->injector->inject($handler);
			} else {
				$this->events->fire("http.not-found");
			}

		} catch (HttpAbortException $ex) {
			// Response has been sent already - let the framework clean up
			$this->events->fire("http.aborted");

		} catch (MethodNotSupportedException $ex) {
			$this->events->fire("http.method-not-supported");

		} catch (\Exception $ex) {

			// Allow the error handler to access the raised exception as a service
			$this->resolver->unlock($key);
			$this->resolver->registerService("exception", function () use ($ex) { return $ex; });
			$key = $this->resolver->lock();

			$this->events->fire("http.error");
		}

		$this->resolver->unlock($key);

		$this->events->fire("http.post-dispatch");
	}
}
