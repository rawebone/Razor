<?php

namespace Razor;

/**
 * Represents the HTTP Handling defined by a Controller file.
 *
 * @property \Closure get
 * @property \Closure post
 * @property \Closure delete
 * @property \Closure patch
 * @property \Closure put
 * @property \Closure link
 * @property \Closure head
 * @property \Closure options
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
            $this->verbs[$method] = function () { };
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
        } else if (isset($this->verbs[$name])) {
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
