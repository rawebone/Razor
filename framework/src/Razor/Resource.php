<?php

namespace Razor;

/**
 * Represents a file in the filesystem; this representation can be passed
 * around easily between functions and is a thin wrapper around the Filesystem
 * object which does the meaty processing.
 */
class Resource
{
    protected $file;
    protected $filesystem;

    public function __construct($file, Filesystem $filesystem)
    {
        $this->file = $file;
        $this->filesystem = $filesystem;
    }

    public function exists()
    {
        return $this->filesystem->isFile($this->file);
    }

    public function contents()
    {
        return $this->filesystem->read($this->file);
    }
}
