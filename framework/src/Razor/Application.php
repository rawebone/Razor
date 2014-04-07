<?php

namespace Razor;

class Application
{
    protected static $instance;

    /**
     * @var \Razor\Controller[]
     */
    protected $controllers = array();

    public function __construct()
    {
        self::$instance = $this;
    }

    public function controller($name)
    {
        $ctls =& $this->controllers;
        if (!isset($ctls[$name])) {
            $ctls[$name] = new Controller($name);
        }

        return $ctls[$name];
    }

    /**
     * Provides access to the application via a static interface
     * to enable syntactic sugar via the DSL.
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array(array(self::$instance, $name), $arguments);
    }
}
