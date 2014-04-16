<?php

namespace Razor;

/**
 * Renders templates using simple token replacements.
 */
class TemplateRenderer
{
    const MATCHER = '#(\%([\w\d]+)\%)#';

    public function render(Resource $resource, array $params)
    {
        $cb = function (array $matches) use ($params) {
            $token = $matches[2];
            return (isset($params[$token]) ? $params[$token] : "");
        };

        return preg_replace_callback(self::MATCHER, $cb, $resource->contents());
    }
}
