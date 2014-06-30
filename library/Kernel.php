<?php

namespace Razor;

use Rawebone\Injector\Injector;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

/**
 * Kernel is the basis of the framework, exposing the API's required
 * for use by the DSL.
 *
 * @package Razor
 * @property \Razor\Events $events
 * @property \Razor\ServiceResolver $resolver
 * @property \Razor\Configuration $configuration
 */
class Kernel
{
	protected $configuration;
	protected $events;
	protected $injector;
	protected $resolver;

	public function __construct()
	{
		$this->injector = new Injector();

		$this->resolver = new ServiceResolver();
		$this->injector->resolver($this->resolver);

		$this->events = new Events($this->injector);

		$this->configuration = new Configuration();

		$this->buildServices();
	}

	public function __get($name)
	{
		if (in_array($name, [ "events", "resolver", "configuration" ])) {
			return $this->$name;
		}

		return null;
	}

	public function buildServices()
	{
		$this->resolver->registerService("http", function ()
		{
			return new Http(Request::createFromGlobals(), new HttpResponse());
		});
	}
}
