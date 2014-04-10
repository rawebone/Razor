<?php

namespace Razor;

/**
 * Provides an abstraction over the file system to provide
 * safety and consistent handling.
 */
class Filesystem
{
    public function isFile($path)
    {
        return is_file($path);
    }

    public function isDir($path)
    {
        return is_dir($path);
    }

    public function touch($file)
    {
        return touch($file);
    }

    public function read($file)
    {
        if (!$this->isFile($file)) {
            throw new \InvalidArgumentException("Could not find file '$file''");
        }

        return file_get_contents($file);
    }

    public function write($file, $data, $append = false)
    {
        $flags = ($append ? FILE_APPEND : null);

        return file_put_contents($file, $data, $flags) > 0;
    }
}
