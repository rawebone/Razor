<?php

namespace Razor;

class Application
{
    /**
     * @var \Razor\Controller[]
     */
    protected $controllers = array();
    protected $http;
    protected $resolver;

    public function __construct(HttpDispatcher $http, ServiceResolver $resolver)
    {
        $this->http = $http;
        $this->resolver = $resolver;
    }

    public function controller($name)
    {
        $ctls =& $this->controllers;
        if (!isset($ctls[$name])) {
            $ctls[$name] = new Controller($name);
        }

        return $ctls[$name];
    }

    public function run($controller)
    {
        $this->http->dispatch($this->controller($controller));
    }

    public function service($name, $factory)
    {
        $this->resolver->registerService($name, $factory);
    }
}
