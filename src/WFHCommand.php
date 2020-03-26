<?php

declare(strict_types=1);

namespace Robwasripped\Charliewfh;

use Robwasripped\Charliewfh\Charlie\CharlieApi;
use Robwasripped\Charliewfh\DateGenerator\BankHolidayFinder;
use Robwasripped\Charliewfh\DateGenerator\DateGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class WFHCommand extends Command
{
    private const OPT_INCLUDE_WEEKENDS = 'include-weekends';
    private const OPT_EXCLUDE_BANK_HOLIDAYS = 'exclude-bank-holidays';
    private const ARG_FROM = 'from';
    private const ARG_TO = 'to';
    private const ARG_USER = 'user';

    private $dateGenerator;
    private $charlieApi;

    public function __construct(dateGenerator $dateGenerator, CharlieApi $charlieApi)
    {
        parent::__construct();

        $this->dateGenerator = $dateGenerator;
        $this->charlieApi = $charlieApi;
    }

    protected function configure(): void
    {
        $this
            ->setName('charliewfh:wfh')
            ->setDescription('Update your work from home days in CharlieHR')

            ->addArgument(self::ARG_FROM, InputArgument::REQUIRED, 'The start date')
            ->addArgument(self::ARG_TO, InputArgument::REQUIRED, 'The end date')
            ->addArgument(self::ARG_USER, InputArgument::REQUIRED, 'The user name on CharlieHR')

            ->addOption(self::OPT_INCLUDE_WEEKENDS, 'w', InputOption::VALUE_NONE, 'Include weekends when submitting dates')
            ->addOption(self::OPT_EXCLUDE_BANK_HOLIDAYS, 'b', InputOption::VALUE_OPTIONAL, 'Exclude UK bank holidays when submitting dates', BankHolidayFinder::REGION_ENGLAND_AND_WALES);
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $questionHelper = $this->getHelper('question');

        $arguments = [
            self::ARG_FROM => 'Start date:',
            self::ARG_TO => 'End date:',
            self::ARG_USER => 'Charlie HR username:',
        ];

        foreach($arguments as $argumentName => $questionLabel) {
            if($input->getArgument($argumentName) !== null) {
                continue;
            }

            $question = new Question($questionLabel);
            $answer = $questionHelper->ask($input, $output, $question);
            $input->setArgument($argumentName, $answer);
        }

        $question = new Question('Enter password for CharlieHR user:');
        $question->setHidden(true);
        $passwordAnswer = $questionHelper->ask($input, $output, $question);

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $from = $this->getDateFromString($input->getArgument(self::ARG_FROM));
        $to = $this->getDateFromString($input->getArgument(self::ARG_TO));

        $dates = $this->dateGenerator->getDatesForRange($from, $to, (bool) $input->getOption(self::OPT_INCLUDE_WEEKENDS));

        foreach($dates as $date) {
            $output->writeln($date->format('Y-m-d'));
        }
        return 0;
    }

    private function getDateFromString(string $dateString): \DateTimeImmutable
    {
        return new \DateTimeImmutable($dateString);
    }
}
