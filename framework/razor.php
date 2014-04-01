<?php

///
/// Razor Framework Entry Point.
/// ===========================
///

$incl = function ($file) { require_once __DIR__ . "/$file"; };

$incl("vendor/autoload.php");
$incl("src/services.php");
$incl("src/functions.php");
$incl("src/http.php");
