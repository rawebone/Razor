<?php

namespace Razor;

class ResourceRenderer
{
    protected $resource;
    protected $renderer;

    public function __construct(Resource $resource, TemplateRenderer $renderer)
    {
        $this->resource = $resource;
        $this->renderer = $renderer;
    }

    public function render()
    {
        return $this->renderer->render($this->resource, array());
    }
}
