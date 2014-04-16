<?php

namespace Razor;

/**
 * Abstracts paths and returns Resource objects.
 */
class ResourceManager
{
    protected $frameworkPath;
    protected $filesystem;

    public function __construct(Filesystem $filesystem, $frameworkPath)
    {
        $this->filesystem = $filesystem;
        $this->frameworkPath = $frameworkPath;
    }

    public function framework($file)
    {
        return new Resource($this->frameworkPath . $file, $this->filesystem);
    }
}
