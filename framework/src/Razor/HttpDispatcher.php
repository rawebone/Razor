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

    public function __construct(Injector $injector, ServiceResolver $resolver)
    {
        $this->injector = $injector;
        $this->resolver = $resolver;
    }

    public function dispatch(Controller $controller)
    {
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
                $this->injector->inject($controller->notFound);
            }

        } catch (HttpAbortException $ex) {
            // Response has been sent already - let the framework clean up

        } catch (\Exception $ex) {

            // Allow the error handler to access the raised exception as a service
            $this->resolver->unlock($key);
            $this->resolver->registerService("exception", function () use ($ex) { return $ex; });
            $key = $this->resolver->lock();

            $this->injector->inject($controller->error);
        }

        $this->resolver->unlock($key);
    }
}
