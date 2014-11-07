<?php
namespace Razor\Http;

use Psr\Http\Message\OutgoingResponseInterface;
use Psr\Http\Message\StreamableInterface;

/**
 * Lifecycle is used to handle request generation and response sending in a
 * server-side context. This is essentially a rehash of the Server and
 * IncomingRequestFactory objects from Phly\Http.
 */
class Lifecycle
{
    protected $input;
    protected $server;
    protected $get;
    protected $post;
    protected $files;
    protected $cookies;

    public function __construct(StreamableInterface $input, array $server,
        array $get, array $post, array $files, array $cookies)
    {
        $this->input = $input;
        $this->server = $this->prepareServer($server);
        $this->get = $get;
        $this->post = $post;
        $this->files = $files;
        $this->cookies = $cookies;
    }

    public function makeRequest()
    {
        $headers = $this->makeHeaders();
        $url     = $this->makeUrl();
        $method  = isset($this->server["REQUEST_METHOD"]) ? $this->server["REQUEST_METHOD"] : "GET";

        return new Request(
            $method,
            $url,
            $this->input,
            $headers,
            $this->server,
            $this->get,
            $this->post,
            $this->files,
            $this->cookies,
            array()
        );
    }

    public function sendResponse(OutgoingResponseInterface $response)
    {
        if (!headers_sent()) {
            $this->sendHeaders($response);
        }

        echo $response->getBody();
    }

    protected function sendHeaders(OutgoingResponseInterface $response)
    {
        if ($response->getReasonPhrase()) {
            header(sprintf(
                "HTTP/%s %d %s",
                $response->getProtocolVersion(),
                $response->getStatusCode(),
                $response->getReasonPhrase()
            ));

        } else {
            header(sprintf(
                "HTTP/%s %d",
                $response->getProtocolVersion(),
                $response->getStatusCode()
            ));
        }

        foreach ($response->getHeaders() as $header => $values) {
            $name  = str_replace(" ", "-", ucwords(str_replace("-", " ", $header)));
            $first = true;

            foreach ($values as $value) {
                header(sprintf("%s: %s", $name, $value), $first);
                $first = false;
            }
        }
    }

    protected function prepareServer(array $server)
    {
        if (isset($server["HTTP_AUTHORIZATION"]) || !function_exists("apache_request_headers")) {
            return $server;
        }

        $headers = apache_request_headers();
        if (isset($headers["Authorization"])) {
            $server["HTTP_AUTHORIZATION"] = $headers["Authorization"];
            return $server;
        }

        if (isset($headers["authorization"])) {
            $server["HTTP_AUTHORIZATION"] = $headers["authorization"];
            return $server;
        }

        return $server;
    }

    protected function makeHeaders()
    {
        $headers = array();
        foreach ($this->server as $key => $value) {
            if (strpos($key, 'HTTP_COOKIE') === 0) {
                // Cookies are handled using the $_COOKIE superglobal
                continue;
            }

            if ($value && strpos($key, 'HTTP_') === 0) {
                $name = strtr(substr($key, 5), '_', ' ');
                $name = strtr(ucwords(strtolower($name)), ' ', '-');
                $name = strtolower($name);

                $headers[$name] = $value;
                continue;
            }

            if ($value && strpos($key, 'CONTENT_') === 0) {
                $name = substr($key, 8); // Content-
                $name = 'Content-' . (($name == 'MD5') ? $name : ucfirst(strtolower($name)));
                $name = strtolower($name);
                $headers[$name] = $value;
                continue;
            }
        }

        return $headers;
    }

    protected function makeUrl()
    {
        $server = $this->server;

        $scheme = empty($server["HTTPS"]) || $server["HTTPS"] === "off" ? "http" : "https";
        $port = (int)$server["SERVER_PORT"];
        $host = $server["SERVER_NAME"];

        $url = $scheme . "://" . $host;
        if (($scheme === "https" && $port !== 443) || ($scheme === "http" && $port !== 80)) {
            $url .= sprintf(":%s", $port);
        }
        return $url . "/";
    }
}
