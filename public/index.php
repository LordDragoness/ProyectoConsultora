<?php

use Pecee\SimpleRouter\SimpleRouter;

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../routes/main-routes.php';
require_once __DIR__.'/../routes/api.php';
require_once __DIR__.'/../config.php';
require __DIR__ . '/../vendor/pecee/simple-router/helpers.php';



if( session_status() === PHP_SESSION_NONE ) {
    session_start();
}

SimpleRouter::start();