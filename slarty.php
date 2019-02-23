#!/usr/bin/env php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use SlartyBartfast\HashCommand;
use SlartyBartfast\TimeCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\CommandLoader\FactoryCommandLoader;

$commandLoader = new FactoryCommandLoader(
    [
        'greet' => function () {
            return new TimeCommand();
        },
        'hash' => function () {
            return new HashCommand();
        },
    ]
);

$app = new Application('Console App', 'v1.0.0');
$app->setCommandLoader($commandLoader);
$app->run();
