<?php

namespace Razor\Templates;

use Razor\ResourceRenderer;

class TestDescription extends ResourceRenderer
{
    public function render($description = "", $described = "")
    {
        $params = compact("description", "described");

        return $this->renderer->render($this->resource, $params);
    }
}
