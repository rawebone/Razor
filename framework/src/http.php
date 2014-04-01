<?php

class Http
{
    protected $log;
    protected $handlers = array();

    public function __construct(\Psr\Log\LoggerInterface $log)
    {
        $this->log = $log;
        $this->handlers["not_found"] = function () {};
        $this->handlers["error"] = function () {};
    }

    public function registerVerb($type, $handler)
    {
        if (!is_callable($handler)) {
            throw new \ErrorException("Cannot register handler for HTTP Verb $type as it is not callable");
        }

        $this->handlers["verb_" . strtolower($type)] = $handler;
    }

    public function registerErrorHandler($handler)
    {
        if (!is_callable($handler)) {
            throw new \ErrorException("Cannot register error handler as it is not callable");
        }

        $this->handlers["error"] = $handler;
    }

    public function registerNotFoundHandler($handler)
    {
        if (!is_callable($handler)) {
            throw new \ErrorException("Cannot register Not Found handler as it is not callable");
        }

        $this->handlers["not_found"] = $handler;
    }

    public function run()
    {
        /** @var \Symfony\Component\HttpFoundation\Request $req */
        $req = injector("_request");

        $method = "verb_" . strtolower($req->getMethod());
        if (!isset($this->handlers[$method])) {
            injector()->inject($this->handlers["not_found"]);
            return;
        }

        try {
            injector()->inject($this->handlers[$method]);
        } catch (\Exception $ex) {
            injector()->inject($this->handlers["error"]);
        }
    }
}
