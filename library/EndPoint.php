<?php

namespace Razor;
use Razor\Injection\Injector;
use SebastianBergmann\Exporter\Exception;

/**
 * EndPoints are essentially the framework, dispatching the request to the
 * appropriate HTTP Verb handler.
 *
 * @method $this get(callable ...$middleware)
 * @method $this post(callable ...$middleware)
 * @method $this put(callable ...$middleware)
 * @method $this delete(callable ...$middleware)
 * @method $this head(callable ...$middleware)
 * @method $this link(callable ...$middleware)
 * @method $this options(callable ...$middleware)
 *
 * @package Razor
 */
class EndPoint
{
	protected $injector;
	protected $handlers;
	protected $error;
	protected $invalidMethod;

	public function __construct(CoreServices $coreServices = null)
	{
		$this->injector = new Injector();
		$this->handlers = array();

		$this->error = function () {};
		$this->invalidMethod = function () {};

		$this->provider($coreServices ?: new CoreServices());
	}

	/**
	 * *So* looking forward to variadics... Until then this will intercept calls
	 * to HTTP Verb handlers, ensure all arguments are callable and pass this
	 * into the handler array.
	 *
	 * @param string $name
	 * @param array $arguments
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function __call($name, array $arguments)
	{
		foreach ($arguments as $i => $argument) {
			if (!is_callable($argument)) {
				throw new \InvalidArgumentException("Argument at position $i is not callable");
			}
		}

		$this->handlers[strtolower($name)] = $arguments;
		return $this;
	}

	/**
	 * Called when an error occurs in dispatch.
	 *
	 * @param callable $handler
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function error($handler)
	{
		if (!is_callable($handler)) {
			throw new \InvalidArgumentException("\$handler should be callable");
		}

		$this->error = $handler;
		return $this;
	}

	/**
	 * Called when the HTTP Method that has been called cannot be handled.
	 *
	 * @param callable $handler
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function invalidMethod($handler)
	{
		if (!is_callable($handler)) {
			throw new \InvalidArgumentException("\$handler should be callable");
		}

		$this->invalidMethod = $handler;
		return $this;
	}

	/**
	 * Registers a provider with the Injector.
	 *
	 * @param ProviderInterface $provider
	 * @return $this
	 */
	public function provider(ProviderInterface $provider)
	{
		$provider->register($this->injector);
		return $this;
	}

	public function run()
	{
		/** @var \Psr\Http\Message\RequestInterface $request */
		$request = $this->injector->resolve("req");

		/** @var \Psr\Http\Message\ResponseInterface $response */
		$response = $this->injector->resolve("resp");

		$method = strtolower($request->getMethod());

		try {
			$handler = (isset($this->handlers[$method]) ? $this->handlers[$method] : $this->invalidMethod);
			$this->injector->inject($handler);

		} catch (\Exception $exception) {
			$this->injector->instance("exception", $exception);
			$this->injector->inject($this->error);
		}

		/** @var ResponseSender $sender */
		$sender = $this->injector->resolve("sender");
		$sender->send($response);
	}

	/**
	 * @return static
	 */
	public static function create(CoreServices $coreServices = null)
	{
		return new static($coreServices);
	}
}
