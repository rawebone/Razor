<?php

namespace Razor;

class Dsl
{
    protected static $instance;

    public static function init(Application $app)
    {
        self::$instance = $app;
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
