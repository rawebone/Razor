<?php

namespace Razor;

/**
 * Represents the HTTP Handling defined by a Controller file.
 *
 * @property callable|null get
 * @property callable|null post
 * @property callable|null delete
 * @property callable|null patch
 * @property callable|null put
 * @property callable|null link
 * @property callable|null head
 * @property callable|null options
 */
class Controller
{
    protected $verbs = array();

    public function __construct()
    {
        foreach ([ "get", "post", "delete", "put", "patch", "link", "head", "options"] as $method) {
            $this->verbs[$method] = null;
        }
    }

    public function __set($name, callable $arguments)
    {
		if (!isset($this->verbs[$name])) {
			throw new MethodNotSupportedException(sprintf(
				"Cannot get value '%s' on Controller",
				$name
			));
		}

		$this->verbs[$name] = $arguments;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->verbs)) {
            return $this->verbs[$name];
        } else {
            throw new MethodNotSupportedException(sprintf(
                "Cannot get value '%s' on Controller",
                $name
            ));
        }
    }
}
