<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

// Get date arguments
try {
    $commandOptions = Robwasripped\Charliewfh\CommandOptions::createFromOptions(getopt(
        Robwasripped\Charliewfh\CommandOptions::OPT_INCLUDE_WEEKENDS,
        [
            Robwasripped\Charliewfh\CommandOptions::LONGOPT_FROM . ':',
            Robwasripped\Charliewfh\CommandOptions::LONGOPT_TO . ':',
        ]
    ));
} catch(\InvalidArgumentException $exception) {
    echo $exception->getMessage() . PHP_EOL;
    exit(1);
}

$client = new GuzzleHttp\Client([
    'base_uri' => sprintf('https://tended.charliehr.com/team_members/%s/', $commandOptions->getUser()),
]);

$charlieApi = new Robwasripped\Charliewfh\Charlie\CharlieApi($client);

// Generate array of dates
$dateGenerator = new Robwasripped\Charliewfh\DateGenerator();
$dates = $dateGenerator->getDatesForRange($commandOptions->getFrom(), $commandOptions->getTo(), !$commandOptions->includeWeekends());

foreach($dates as $date) {
    echo $date->format('Y-m-d') . PHP_EOL;
}
