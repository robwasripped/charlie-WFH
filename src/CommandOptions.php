<?php

declare(strict_types=1);

namespace Robwasripped\Charliewfh;

class CommandOptions
{
    public const OPT_INCLUDE_WEEKENDS = 'w';
    public const LONGOPT_FROM = 'from';
    public const LONGOPT_TO = 'to';
    public const LONGOPT_USER = 'user';

    private $includeWeekends = false;

    private $from;

    private $to;

    private $user;

    private function __construct()
    {
    }

    public static function createFromOptions(array $options): self
    {
        $commandOptions = new self;

        if(array_key_exists(self::OPT_INCLUDE_WEEKENDS, $options)) {
            $commandOptions->includeWeekends = true;
        }

        if(!array_key_exists(self::LONGOPT_FROM, $options)) {
            throw new \InvalidArgumentException('Missing argument "--from"');
        }

        try {
            $fromDate = new \DateTimeImmutable($options[self::LONGOPT_FROM]);
        } catch(\Exception $exception) {
            throw new \InvalidArgumentException('"from" must be in a date format.');
        }

        $commandOptions->from = $fromDate;


        if(!array_key_exists(self::LONGOPT_TO, $options)) {
            throw new \InvalidArgumentException('Missing argument "--to"');
        }

        try {
            $toDate = new \DateTimeImmutable($options[self::LONGOPT_TO]);
        } catch(\Exception $e) {
            throw new \InvalidArgumentException('"to" must be in a date format.');
        }

        $commandOptions->to = $toDate;

        if(!\array_key_exists(self::LONGOPT_USER, $options)) {
            throw new \InvalidArgumentException('Missing argument "--user"');
        }
        $commandOptions->user = $options[self::LONGOPT_USER];

        return $commandOptions;
    }

    public function includeWeekends(): bool
    {
        return $this->includeWeekends;
    }

    public function getFrom(): \DateTimeImmutable
    {
        return $this->from;
    }

    public function getTo(): \DateTimeImmutable
    {
        return $this->to;
    }

    public function getUser(): string
    {
        return $this->user;
    }
}
