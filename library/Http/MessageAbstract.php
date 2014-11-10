<?php
namespace Razor\Http;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamableInterface;

/**
 * MessageAbstract class 
 */
abstract class MessageAbstract implements MessageInterface
{
    /**
     * @var StreamableInterface
     */
    protected $stream;

    /**
     * @var array
     */
    protected $headers = array();

    /**
     * @var string
     */
    protected $protocolVersion = "1.1";

    /**
     * Gets the HTTP protocol version as a string.
     *
     * The string MUST contain only the HTTP version number (e.g., "1.1", "1.0").
     *
     * @return string HTTP protocol version.
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * Gets the body of the message.
     *
     * @return StreamableInterface|null Returns the body, or null if not set.
     */
    public function getBody()
    {
        return $this->stream;
    }

    /**
     * Gets all message headers.
     *
     * The keys represent the header name as it will be sent over the wire, and
     * each value is an array of strings associated with the header.
     *
     *     // Represent the headers as a string
     *     foreach ($message->getHeaders() as $name => $values) {
     *         echo $name . ": " . implode(", ", $values);
     *     }
     *
     *     // Emit headers iteratively:
     *     foreach ($message->getHeaders() as $name => $values) {
     *         foreach ($values as $value) {
     *             header(sprintf('%s: %s', $name, $value), false);
     *         }
     *     }
     *
     * @return array Returns an associative array of the message's headers. Each
     *     key MUST be a header name, and each value MUST be an array of strings.
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Checks if a header exists by the given case-insensitive name.
     *
     * @param string $header Case-insensitive header name.
     * @return bool Returns true if any header names match the given header
     *     name using a case-insensitive string comparison. Returns false if
     *     no matching header name is found in the message.
     */
    public function hasHeader($header)
    {
        return array_key_exists(strtolower($header), $this->headers);
    }

    /**
     * Retrieve a header by the given case-insensitive name, as a string.
     *
     * This method returns all of the header values of the given
     * case-insensitive header name as a string concatenated together using
     * a comma.
     *
     * NOTE: Not all header values may be appropriately represented using
     * comma concatenation.
     *
     * @param string $header Case-insensitive header name.
     * @return string
     */
    public function getHeader($header)
    {
        if (!$this->hasHeader($header)) {
            return "";
        }

        return join(",", $this->headers[strtolower($header)]);
    }

    /**
     * Retrieves a header by the given case-insensitive name as an array of strings.
     *
     * @param string $header Case-insensitive header name.
     * @return string[]
     */
    public function getHeaderAsArray($header)
    {
        if (!$this->hasHeader($header)) {
            return array();
        }

        $headers = $this->headers[strtolower($header)];
        return (is_array($headers) ? $headers : array($headers));
    }

    /**
     * Returns a valid header value, or throws an exception.
     *
     * @param mixed $input
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function validateHeaderValue($input)
    {
        if (is_string($input)) {
            return array($input);
        }

        $check = function (array $strings)
        {
            foreach ($strings as $string) {
                if (!is_string($string)) {
                    return false;
                }
            }

            return true;
        };

        if (is_array($input) && $check($input)) {
            return $input;
        }

        throw new \InvalidArgumentException("Passed header value is invalid; must be a string or an array of strings");
    }
}