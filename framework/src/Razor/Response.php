<?php

namespace Razor;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response as GeneralResponse;

class Response
{
    public function json(array $data, $status = 200, array $headers = array())
    {
        return new JsonResponse($data, $status, $headers);
    }

    public function general($data, $status = 200, array $headers = array())
    {
        return new GeneralResponse($data, $status, $headers);
    }
}
