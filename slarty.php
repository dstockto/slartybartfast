#!/usr/bin/env php
<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use SlartyBartfast\ArtifactNamesCommand;
use SlartyBartfast\DoBuildsCommand;
use SlartyBartfast\DoDeploysCommand;
use SlartyBartfast\HashApplicationCommand;
use SlartyBartfast\HashCommand;
use SlartyBartfast\ShouldBuildApplicationsCommand;
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
        'artifact-names' => function() {
            return new ArtifactNamesCommand();
        },
        'hash-application' => function() {
            return new HashApplicationCommand();
        },
        'do-builds' => function() {
            return new DoBuildsCommand();
        },
        'do-deploys' => function() {
            return new DoDeploysCommand();
        },
        'should-build' => function() {
            return new ShouldBuildApplicationsCommand();
        },
    ]
);

$app = new Application('Starty Bartfast', 'v1.0.0');
$app->setCommandLoader($commandLoader);
$app->run();
