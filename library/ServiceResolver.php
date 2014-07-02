<?php

namespace Razor;

use Rawebone\Injector\Func;
use Rawebone\Injector\ResolutionException;
use Rawebone\Injector\ResolverInterface;

class ServiceResolver implements ResolverInterface
{
	protected $services = [];

	/**
	 * Registers a Service identified by Name. This can be an
	 * object instance or a callable.
	 *
	 * @param string $name
	 * @param object|callable $service
	 * @throws \Rawebone\Injector\ResolutionException
	 */
	public function register($name, $service)
	{
		$delegate = $service;

		if (!$service instanceof \Closure && is_object($service)) {
			$delegate = function () use ($service) { return $service; };
		} else if (!is_callable($service)) {
			throw new ResolutionException("Service provider for '$name' is invalid");
		}

		$this->services[$name] = new Func($delegate);
	}

	public function registerMany(array $services = [])
	{
		foreach ($this->services as $name => $service) {
			$this->register($name, $service);
		}
	}

	/**
	 * Returns the service by name.
	 *
	 * @param string $service
	 * @return \Rawebone\Injector\Func
	 * @throws \Rawebone\Injector\ResolutionException
	 */
	public function resolve($service)
	{
		if (!isset($this->services[$service])) {
			throw new ResolutionException("Service '$service' not registered");
		}

		return $this->services[$service];
	}
}