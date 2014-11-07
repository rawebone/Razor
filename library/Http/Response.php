<?php
namespace Razor\Http;

use Psr\Http\Message\OutgoingResponseInterface;
use Psr\Http\Message\StreamableInterface;

/**
 * Response class 
 */
class Response extends MessageAbstract implements OutgoingResponseInterface
{
    /**
     * Map of standard HTTP status code/reason phrases
     *
     * @var array
     */
    protected $phrases = array(
        // INFORMATIONAL CODES
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',

        // SUCCESS CODES
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-status',
        208 => 'Already Reported',

        // REDIRECTION CODES
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy', // Deprecated
        307 => 'Temporary Redirect',

        // CLIENT ERROR
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',

        // SERVER ERROR
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        511 => 'Network Authentication Required',
    );

    /**
     * @var int
     */
    protected $statusCode = 200;

    /**
     * @var null|string
     */
    protected $reasonPhrase;

    /**
     * Set the HTTP protocol version.
     *
     * The version string MUST contain only the HTTP version number (e.g.,
     * "1.1", "1.0").
     *
     * @param string $version HTTP protocol version
     * @return void
     */
    public function setProtocolVersion($version)
    {
        $this->protocolVersion = $version;
    }

    /**
     * Gets the response Status-Code.
     *
     * The Status-Code is a 3-digit integer result code of the server's attempt
     * to understand and satisfy the request.
     *
     * @return integer Status code.
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Sets the status code, and optionally reason phrase,  of this response.
     *
     * If no Reason-Phrase is specified, implementations MAY choose to default
     * to the RFC 7231 or IANA recommended reason phrase for the response's
     * Status-Code.
     *
     * @link http://tools.ietf.org/html/rfc7231#section-6
     * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * @param integer $code The 3-digit integer result code to set.
     * @param null|string $reasonPhrase The reason phrase to use with the
     *     provided status code; if none is provided, implementations MAY
     *     use the defaults as suggested in the HTTP specification.
     * @throws \InvalidArgumentException For invalid status code arguments.
     */
    public function setStatus($code, $reasonPhrase = null)
    {
        if (!is_int($code) || ($code < 100 || $code > 599)) {
            throw new \InvalidArgumentException("Status code must be an integer between 100 and 599");
        }

        $this->statusCode = $code;
        $this->reasonPhrase = $reasonPhrase;
    }

    /**
     * Gets the response Reason-Phrase, a short textual description of the Status-Code.
     *
     * Because a Reason-Phrase is not a required element in a response
     * Status-Line, the Reason-Phrase value MAY be null. Implementations MAY
     * choose to return the default RFC 7231 recommended reason phrase (or those
     * listed in the IANA HTTP Status Code Registry) for the response's
     * Status-Code.
     *
     * @link http://tools.ietf.org/html/rfc7231#section-6
     * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * @return string|null Reason phrase, or null if unknown.
     */
    public function getReasonPhrase()
    {
        if (!$this->reasonPhrase && ($phrase = $this->getReasonPhraseForCode($this->statusCode))) {
            $this->reasonPhrase = $phrase;
        }

        return $this->reasonPhrase;
    }

    /**
     * Returns the reason phrase for the given code.
     *
     * @param int $code
     * @return null|string
     */
    public function getReasonPhraseForCode($code)
    {
        return isset($this->phrases[$code]) ? $this->phrases[$code] : null;
    }

    /**
     * Sets a header, replacing any existing values of any headers with the
     * same case-insensitive name.
     *
     * The header name is case-insensitive. The header values MUST be a string
     * or an array of strings.
     *
     * @param string $header Header name
     * @param string|string[] $value Header value(s).
     * @return void
     * @throws \InvalidArgumentException for invalid header names or values.
     */
    public function setHeader($header, $value)
    {
        $this->headers[strtolower($header)] = $this->validateHeaderValue($value);
    }

    /**
     * Appends a header value for the specified header.
     *
     * Existing values for the specified header will be maintained. The new
     * value(s) will be appended to the existing list.
     *
     * @param string $header Header name to add
     * @param string|string[] $value Header value(s).
     * @return void
     * @throws \InvalidArgumentException for invalid header names or values.
     */
    public function addHeader($header, $value)
    {
        if (!$this->hasHeader($header)) {
            $this->setHeader($header, $value);
            return;
        }

        $lower = strtolower($header);
        $this->headers[$lower] = array_merge($this->headers[$lower], $this->validateHeaderValue($value));
    }

    /**
     * Remove a specific header by case-insensitive name.
     *
     * @param string $header HTTP header to remove
     * @return void
     */
    public function removeHeader($header)
    {
        if ($this->hasHeader($header)) {
            unset($this->headers[strtolower($header)]);
        }
    }

    /**
     * Sets the body of the message.
     *
     * The body MUST be a StreamableInterface object. Setting the body to null MUST
     * remove the existing body.
     *
     * @param StreamableInterface $body Body.
     * @return void
     * @throws \InvalidArgumentException When the body is not valid.
     */
    public function setBody(StreamableInterface $body)
    {
        $this->stream = $body;
    }
}
