<?php

require_once(__DIR__ . "/../framework/razor-test.php");

foreach (new DirectoryIterator(__DIR__ . "/tests/") as $test) {
    if (in_array($test, array(".", ".."))) {
        continue;
    }

    require_once $test->getRealPath();
}
