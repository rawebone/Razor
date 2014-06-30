<?php

namespace Razor;

use Rawebone\Injector\Func;
use Rawebone\Injector\ResolutionException;
use Rawebone\Injector\ResolverInterface;

class ServiceResolver implements ResolverInterface
{
	protected $services = array();
	protected $locked;

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
			throw new ResolutionException(sprintf(
				"Could not resolve the service named '%s'",
				$service
			));
		}

		return $this->services[$service];
	}

	public function registerService($name, $callable)
	{
		if ($this->locked) {
			throw new FrameworkException(sprintf(
				"Cannot register service '%s' as the Injector is locked",
				$name
			));
		}

		$this->services[$name] = new Func($callable);
	}

	public function lock()
	{
		if ($this->locked) {
			throw new FrameworkException("Injector is already locked, cannot attempt re-lock");
		}

		return $this->locked = uniqid();
	}

	public function unlock($key)
	{
		if ($this->locked === $key) {
			$this->locked = null;
		}

		return true;
	}
}
