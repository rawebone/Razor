<?php

namespace Razor;

class Application
{
    /**
     * @var \Razor\Controller[]
     */
    protected $controllers = array();
    protected $http;

    public function __construct(HttpDispatcher $http)
    {
        $this->http = $http;
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
}
