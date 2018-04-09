<?php
require_once __DIR__ . '/vendor/autoload.php';

$klein = new \Klein\Klein();

$klein->respond('GET', '/hello-world', require('pages/hello.php'));
$klein->respond('GET', '/', require('pages/main.php'));

$klein->dispatch();