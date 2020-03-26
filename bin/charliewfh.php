#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Console\Application;

$application = new Application();

$charlieClient = new GuzzleHttp\Client([
    'base_uri' => 'https://tended.charliehr.com/',
    'cookies' => true,
]);

$command = new Robwasripped\Charliewfh\WFHCommand(
    new Robwasripped\Charliewfh\DateGenerator\DateGenerator,
    new Robwasripped\Charliewfh\Charlie\CharlieApi($charlieClient)
);
$application->add($command);
$application->setDefaultCommand($command->getName(), true);

$application->run();
