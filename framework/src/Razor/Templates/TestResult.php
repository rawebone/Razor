<?php

namespace Razor\Templates;

use Razor\ResourceRenderer;

class TestResult extends ResourceRenderer
{
    public function render($described = "")
    {
        $params = compact("described");

        return $this->renderer->render($this->resource, $params);
    }
}
