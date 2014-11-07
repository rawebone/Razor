<?php

require_once __DIR__ . "/../vendor/autoload.php";

use Razor\EndPoint;
use Psr\Http\Message\IncomingRequestInterface as Req;
use Psr\Http\Message\OutgoingResponseInterface as Resp;

EndPoint::create()

        ->get(function (Req $req, Resp $resp)
        {
            $resp->setHeader("Content-Type", "application/json");
            $resp->getBody()->write(var_export($req, true));
        })

        ->run();
