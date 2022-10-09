#!/usr/bin/env php
<?php
declare(strict_types=1);

foreach (array(__DIR__ . '/../../autoload.php', __DIR__ . '/../vendor/autoload.php', __DIR__ . '/vendor/autoload.php') as $file) {
    if (file_exists($file)) {
        define('SLARTY_COMPOSER_INSTALL', $file);
        break;
    }
}

unset($file);

require SLARTY_COMPOSER_INSTALL;

use SlartyBartfast\ArtifactNamesCommand;
use SlartyBartfast\DeployAssetsCommand;
use SlartyBartfast\DeployCleanup;
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
        // hidden
        'greet' => function () {
            return new TimeCommand();
        },
        'hash' => function () {
            return new HashCommand();
        },
        // hidden
        'artifact-names' => function() {
            return new ArtifactNamesCommand();
        },
        'hash-application' => function() {
            return new HashApplicationCommand();
        },
        'should-build' => function() {
            return new ShouldBuildApplicationsCommand();
        },
        'do-builds' => function() {
            return new DoBuildsCommand();
        },
        'do-deploys' => function() {
            return new DoDeploysCommand();
        },
        'deploy-assets' => function() {
            return new DeployAssetsCommand();
        },
        'do-cleanup' => function () {
            return new DeployCleanup();
        }
    ]
);

$app = new Application('Slarty Bartfast - PHP 8.1+ Edition', 'v3.0.0');
$app->setCommandLoader($commandLoader);
$app->run();
