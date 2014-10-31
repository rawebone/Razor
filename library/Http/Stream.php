<?php
namespace Razor\Http;

use Psr\Http\Message\StreamableInterface;

/**
 * Stream provides an implementation of the PSR-7 StreamableInterface
 * based off of Phly\Http\Stream.
 *
 * @link https://github.com/phly/http
 */
class Stream implements StreamableInterface
{
    /**
     * @var resource
     */
    protected $resource;

    public function __construct($stream, $mode = "r")
    {
        $this->attach($stream, $mode);
    }

    /**
     * Closes the stream and any underlying resources.
     *
     * @return void
     */
    public function close()
    {
        if (($resource = $this->detach())) {
            fclose($resource);
        }
    }

    /**
     * Separates any underlying resources from the stream.
     *
     * After the stream has been detached, the stream is in an unusable state.
     *
     * @return resource|null Underlying PHP stream, if any
     */
    public function detach()
    {
        $resource = $this->resource;
        $this->resource = null;
        return $resource;
    }

    /**
     * Replaces the underlying stream resource with the provided stream.
     *
     * Use this method to replace the underlying stream with another; as an
     * example, in server-side code, if you decide to return a file, you
     * would replace the original content-oriented stream with the file
     * stream.
     *
     * Any internal state such as caching of cursor position should be reset
     * when attach() is called, as the stream has changed.
     *
     * @return void
     */
    public function attach($stream, $mode = "r")
    {
        if (is_resource($stream)) {
            $this->resource = $stream;
        } elseif (is_string($stream)) {
            $this->resource = fopen($stream, $mode);
        } else {
            throw new \InvalidArgumentException("Invalid stream provided; must be a string stream identifier or resource");
        }
    }

    /**
     * Get the size of the stream if known
     *
     * @return int|null Returns the size in bytes if known, or null if unknown
     */
    public function getSize()
    {
        return null;
    }

    /**
     * Returns the current position of the file read/write pointer
     *
     * @return int|bool Position of the file pointer or false on error
     */
    public function tell()
    {
        return ($this->resource ? ftell($this->resource) : false);
    }

    /**
     * Returns true if the stream is at the end of the stream.
     *
     * @return bool
     */
    public function eof()
    {
        return ($this->resource ? feof($this->resource) : true);
    }

    /**
     * Returns whether or not the stream is seekable
     *
     * @return bool
     */
    public function isSeekable()
    {
        return $this->getMetadata("seekable");
    }

    /**
     * Seek to a position in the stream
     *
     * @param int $offset Stream offset
     * @param int $whence Specifies how the cursor position will be calculated
     *                    based on the seek offset. Valid values are identical
     *                    to the built-in PHP $whence values for `fseek()`.
     *                    SEEK_SET: Set position equal to offset bytes
     *                    SEEK_CUR: Set position to current location plus offset
     *                    SEEK_END: Set position to end-of-stream plus offset
     *
     * @return bool Returns TRUE on success or FALSE on failure
     * @link   http://www.php.net/manual/en/function.fseek.php
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        if (!$this->resource || !$this->isSeekable()) {
            return false;
        }

        $result = fseek($this->resource, $offset, $whence);
        return ($result === 0);
    }

    /**
     * Returns whether or not the stream is writable
     *
     * @return bool
     */
    public function isWritable()
    {
        return is_writable($this->getMetadata("uri"));
    }

    /**
     * Write data to the stream.
     *
     * @param string $string The string that is to be written.
     *
     * @return int|bool Returns the number of bytes written to the stream on
     *                  success or FALSE on failure.
     */
    public function write($string)
    {
        if (!$this->resource) {
            return false;
        }

        return fwrite($this->resource, $string);
    }

    /**
     * Returns whether or not the stream is readable
     *
     * @return bool
     */
    public function isReadable()
    {
        $mode = $this->getMetadata("mode");
        return (strstr($mode, "r") || strstr($mode, "+"));
    }

    /**
     * Read data from the stream
     *
     * @param int $length Read up to $length bytes from the object and return
     *                    them. Fewer than $length bytes may be returned if
     *                    underlying stream call returns fewer bytes.
     *
     * @return string     Returns the data read from the stream.
     */
    public function read($length)
    {
        if (!$this->resource || !$this->isReadable()) {
            return "";
        }

        if ($this->eof()) {
            return "";
        }

        return fread($this->resource, $length);
    }

    /**
     * Returns the remaining contents in a string
     *
     * @return string
     */
    public function getContents()
    {
        if (!$this->isReadable()) {
            return "";
        }

        return stream_get_contents($this->resource);
    }

    /**
     * Get stream metadata as an associative array or retrieve a specific key.
     *
     * The keys returned are identical to the keys returned from PHP's
     * stream_get_meta_data() function.
     *
     * @param string $key Specific metadata to retrieve.
     *
     * @return array|mixed|null Returns an associative array if no key is
     *                          provided. Returns a specific key value if a key
     *                          is provided and the value is found, or null if
     *                          the key is not found.
     * @see http://php.net/manual/en/function.stream-get-meta-data.php
     */
    public function getMetadata($key = null)
    {
        if (!$this->resource) {
            return null;
        }

        if ($key === null) {
            return stream_get_meta_data($this->resource);
        }

        $metadata = stream_get_meta_data($this->resource);
        if (!array_key_exists($key, $metadata)) {
            return null;
        }

        return $metadata[$key];
    }

    public function __toString()
    {
        if (!$this->isReadable()) {
            return "";
        }

        return stream_get_contents($this->resource, -1, 0);
    }
}
