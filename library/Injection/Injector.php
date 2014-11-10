<?php

namespace Razor\Injection;

/**
 * Injector provides a simple Service Injection mechanism. Service Injection
 * allows us to define dependencies easily and lazily which can then be resolved
 * on an ad-hoc basis.
 *
 * @package Razor\Injection
 */
class Injector
{
	/**
	 * @var callable[]
	 */
	protected $services = array();

	/**
	 * @var callable[]
	 */
	protected $extensions = array();

	/**
	 * @var mixed[]
	 */
	protected $resolved = array();

	/**
	 * @var string[]
	 */
	protected $beingResolved = array();

	/**
	 * Returns whether a service has been defined.
	 *
	 * @param string $name
	 * @return bool
	 */
	public function defined($name)
	{
		return isset($this->services[$name]) || isset($this->resolved[$name]);
	}

	/**
	 * Resolves the services for and executes the given callable. The result of
	 * the executed callable is returned to the caller, if applicable.
	 *
	 * @param callable $fn
	 * @return mixed
	 * @throws \InvalidArgumentException
	 */
	public function inject($fn)
	{
		return call_user_func_array($fn, $this->dependencies($fn));
	}

	/**
	 * Returns the dependencies for a function. These can then be invoked
	 * with call_user_func_array().
	 *
	 * @param callable $fn
	 * @return array
	 * @throws \InvalidArgumentException
	 */
	public function dependencies($fn)
	{
		if (!is_callable($fn)) {
			throw new \InvalidArgumentException("\$fn is expected to be a callable");
		}

		$args = array();
		foreach ($this->requires($fn) as $dependency) {
			$args[$dependency] = $this->resolve($dependency);
		}

		return $args;
	}

	/**
	 * Registers a service with the Injector.
	 *
	 * @param string $name
	 * @param callable $fn
	 * @throws \RuntimeException
	 * @throws \InvalidArgumentException
	 * @return $this
	 */
	public function service($name, $fn)
	{
		if (!is_callable($fn)) {
			throw new \InvalidArgumentException("\$fn is expected to be a callable");
		}

		if (isset($this->resolved[$name])) {
			throw new \RuntimeException("Service '$name' cannot be set as it has already been resolved");
		}

		$this->services[$name] = $fn;
		return $this;
	}

	/**
	 * Allows for a service to be extended with new functionality or configuration.
	 * Multiple extensions can be registered for services.
	 *
	 * @param string $name
	 * @param callable $fn
	 * @throws \InvalidArgumentException
	 * @return $this
	 */
	public function extend($name, $fn)
	{
		if (!is_callable($fn)) {
			throw new \InvalidArgumentException("\$fn is expected to be a callable");
		}

		if (!isset($this->extensions[$name])) {
			$this->extensions[$name] = array();
		}

		$this->extensions[$name][] = $fn;
		return $this;
	}

	/**
	 * Registers an instance of an object into the resolved cache.
	 *
	 * @param string $name
	 * @param mixed $object
	 * @throws \InvalidArgumentException
	 */
	public function instance($name, $object)
	{
		if ($this->defined($name)) {
			throw new \InvalidArgumentException("'$name' is already registered");
		}

		$this->resolved[$name] = $object;
		return $this;
	}

	/**
	 * Resolves a service by name and returns the resultant object.
	 *
	 * @param string $name
	 * @return mixed
	 * @throws \LogicException
	 * @throws \InvalidArgumentException
	 */
	public function resolve($name)
	{
		if (isset($this->resolved[$name])) {
			return $this->resolved[$name];
		}

		if (!$this->defined($name)) {
			throw new \InvalidArgumentException("Service $name cannot be resolved as it is not defined");
		}

		if (isset($this->beingResolved[$name])) {
			$last = end($this->beingResolved);
			throw new \LogicException("Cyclic Dependency found - $last depends on $name which is currently being resolved");
		}

		$this->beingResolved[$name] = true;

		$args = array();
		foreach ($this->requires($this->services[$name]) as $dependency) {
			$args[$dependency] = $this->resolve($dependency);
		}

		unset($this->beingResolved[$name]);

		$resolved = $this->resolved[$name] = call_user_func_array($this->services[$name], $args);

		if (isset($this->extensions[$name])) {
			foreach ($this->extensions[$name] as $extension) {
				$extension($resolved);
			}
		}

		return $resolved;
	}

	/**
	 * Returns the names of the services required by a callable.
	 *
	 * @param callable $fn
	 * @return array
	 * @throws \InvalidArgumentException
	 */
	public function requires($fn)
	{
		if (!is_callable($fn)) {
			throw new \InvalidArgumentException("\$fn is expected to be a callable");
		}

		if (is_object($fn) && method_exists($fn, "__invoke")) {
			$reflection = new \ReflectionMethod($fn, "__invoke");
		} else if (is_array($fn)) {
			$reflection = new \ReflectionMethod($fn[0], $fn[1]);
		} else {
			$reflection = new \ReflectionFunction($fn);
		}

		$required = array();

		foreach ($reflection->getParameters() as $parameter) {
			$required[] = $parameter->getName();
		}

		return $required;
	}
}
