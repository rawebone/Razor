<?php
namespace Razor\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamableInterface;
use Symfony\Component\Yaml\Exception\RuntimeException;

/**
 * Request provides an implementation of the PSR-7 RequestInterface
 * based off of Phly\Http\Request.
 *
 * @link https://github.com/phly/http
 */
class Request extends MessageAbstract implements RequestInterface
{
    protected $method;

    protected $url;

    public function __construct(StreamableInterface $stream, $protocolVersion)
    {
        $this->setBody($stream);
        $this->protocol = $protocolVersion;
    }

    /**
     * Gets the HTTP method of the request.
     *
     * @return string Returns the request method.
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Sets the method to be performed on the resource identified by the Request-URI.
     *
     * While HTTP method names are typically all uppercase characters, HTTP
     * method names are case-sensitive and thus implementations SHOULD NOT
     * modify the given string.
     *
     * @param string $method Case-insensitive method.
     *
     * @return void
     */
    public function setMethod($method)
    {
        if ($this->method !== null) {
            throw new RuntimeException("Method cannot be overwritten");
        }

        $this->method = $method;
    }

    /**
     * Gets the absolute request URL.
     *
     * @return string|object Returns the URL as a string, or an object that
     *    implements the `__toString()` method. The URL must be an absolute URI
     *    as specified in RFC 3986.
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     */
    public function getUrl()
    {
        $this->url;
    }

    /**
     * Sets the request URL.
     *
     * The URL MUST be a string, or an object that implements the
     * `__toString()` method. The URL must be an absolute URI as specified
     * in RFC 3986.
     *
     * @param string|object $url Request URL.
     *
     * @return void
     *
     * @throws \InvalidArgumentException If the URL is invalid.
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }
}
