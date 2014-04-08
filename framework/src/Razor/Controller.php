<?php

namespace Razor;

/**
 * Represents the HTTP Handling defined by a Controller file.
 *
 * @property \Closure|null get
 * @property \Closure|null post
 * @property \Closure|null delete
 * @property \Closure|null patch
 * @property \Closure|null put
 * @property \Closure|null link
 * @property \Closure|null head
 * @property \Closure|null options
 * @property \Closure error
 * @property \Closure notFound
 */
class Controller
{
    protected $error;
    protected $name;
    protected $notFound;
    protected $verbs = array();

    public function __construct($name)
    {
        $this->name = $name;
        $this->error = function () { };
        $this->notFound = function () { };

        foreach (array("get", "post", "delete", "put", "patch", "link", "head", "options") as $method) {
            $this->verbs[$method] = null;
        }
    }

    public function __set($name, $arguments)
    {
        if (!$arguments instanceof \Closure) {
            throw new FrameworkException(sprintf(
                "Cannot set value '%s' on Controller '%s'",
                $name,
                $this->name
            ));
        }

        if ($name === "error") {
            $this->error = $arguments;
        } else if ($name === "notFound") {
            $this->notFound = $arguments;
        } else {
            $this->verbs[$name] = $arguments;
        }
    }

    public function __get($name)
    {
        if ($name === "error") {
            return $this->error;
        } else if ($name === "notFound") {
            return $this->notFound;
        } else if (array_key_exists($name, $this->verbs)) {
            return $this->verbs[$name];
        } else if ($name === "name") {
            return $this->name;
        } else {
            throw new FrameworkException(sprintf(
                "Cannot get value '%s' on Controller '%s'",
                $name,
                $this->name
            ));
        }
    }
}
