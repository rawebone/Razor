<?php

namespace Razor;

use Psr\Log\AbstractLogger;

class AppLog extends AbstractLogger
{
    protected $file;
    protected $filesystem;

    public function __construct($file, Filesystem $filesystem)
    {
        $this->file = $file;
        $this->filesystem = $filesystem;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        $msg = sprintf("(%s) %s - %s", $level, date("d/m/Y H:i:s"), $message);
        $this->filesystem->write($this->file, $msg);
    }
}
