<?php
namespace Razor\Http;

use Psr\Http\Message\RequestInterface;

/**
 * RequestFactory generates a Request from the server data. This is based off of
 * the Phly\Http\RequestFactory class, which in turn is a refactor of the ZF2
 * Zend\Http\PhpEnvironment\Request class.
 *
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
class RequestFactory
{
    protected $server;

    public function __construct(array $server)
    {
        $server = array_merge(array(), $server); // Ensure copy
        $this->server = $this->normaliseServerParameters($server);
    }

    public function makeRequest()
    {
        $request = new Request(new Stream("php://input"), $this->get("SERVER_PROTOCOL", "1.1"));
        $request->setMethod($this->get("REQUEST_METHOD", "GET"));

        $this->makeHeaders($request);
        $this->makeUrl($request);

        return $request;
    }

    protected function get($key, $default = "")
    {
        if (array_key_exists($key, $this->server)) {
            return $this->server[$key];
        }
        return $default;
    }

    protected function normaliseServerParameters(array $server)
    {
        // This seems to be the only way to get the Authorization header on Apache
        if (isset($server['HTTP_AUTHORIZATION'])
            || ! function_exists('apache_request_headers')
        ) {
            return $server;
        }

        $apacheRequestHeaders = apache_request_headers();
        if (isset($apacheRequestHeaders['Authorization'])) {
            $server['HTTP_AUTHORIZATION'] = $apacheRequestHeaders['Authorization'];
            return $server;
        }

        if (isset($apacheRequestHeaders['authorization'])) {
            $server['HTTP_AUTHORIZATION'] = $apacheRequestHeaders['authorization'];
            return $server;
        }

        return $server;
    }

    protected function makeHeaders(RequestInterface $request)
    {
        $headers = array();
        foreach ($this->server as $key => $value) {
            if (strpos($key, "HTTP_COOKIE") === 0) {
                // Cookies are handled using the $_COOKIE superglobal
                continue;
            }

            if ($value && strpos($key, "HTTP_") === 0) {
                $name = strtr(substr($key, 5), "_", " ");
                $name = strtr(ucwords(strtolower($name)), " ", "-");

                $headers[$name] = $value;
                continue;
            }

            if ($value && strpos($key, "CONTENT_") === 0) {
                $name = substr($key, 8); // Content-
                $name = "Content-" . (($name == "MD5") ? $name : ucfirst(strtolower($name)));
                $headers[$name] = $value;
                continue;
            }
        }

        $request->setHeaders($headers);
    }

    protected function makeUrl(RequestInterface $request)
    {
        $scheme = "http";
        if ($this->get("HTTPS") !== "off" || $request->getHeader("x-forwarded-proto") == "https") {
            $scheme = "https";
        }

        $port = (int)$this->server["SERVER_PORT"];
        $host = $this->server["SERVER_NAME"];

        $url = $scheme . "://" . $host;
        if (($scheme === "https" && $port !== 443) || ($scheme === "http" && $port !== 80)) {
            $url .= sprintf(':%s', $port);
        }
        return $url . "/";
    }
} 