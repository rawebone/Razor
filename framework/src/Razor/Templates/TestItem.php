<?php

namespace Razor\Templates;

use Rawebone\Jasmini\TestStatus;
use Razor\ResourceRenderer;

class TestItem extends ResourceRenderer
{
    public function render($title, $status)
    {
        $status = $this->statusToString($status);
        $params = compact("title", "status");

        return $this->renderer->render($this->resource, $params);
    }

    protected function statusToString($status)
    {
        switch ($status) {
            case TestStatus::PASSED:
                return "pass";
            case TestStatus::FAILED:
                return "fail";
            case TestStatus::PENDING:
                return "pending";
        }
    }
}
