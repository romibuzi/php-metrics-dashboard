<?php

if (php_sapi_name() === 'cli' || php_sapi_name() === 'cli-server') {
    return false;
}

$app = require __DIR__ . '/../app/app.php';

$app->run();
