<?php

declare(strict_types=1);

namespace Robwasripped\Charliewfh\DateGenerator;

use GuzzleHttp\ClientInterface;

class BankHolidayFinder
{
    public const REGION_ENGLAND_AND_WALES = 'england-and-wales';
    public const REGION_SCOTLAND = 'scotland';
    public const REGION_NORTHERN_IRELAND = 'northern-ireland';
    public const REGIONS = [
        self::REGION_ENGLAND_AND_WALES,
        self::REGION_SCOTLAND,
        self::REGION_NORTHERN_IRELAND,
    ];

    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function filterBankHolidays(iterable $dates, string $region): iterable
    {
        $bankHolidayDates = $this->getBankHolidayStrings($region);

        foreach($dates as $date) {
            if(\in_array($date->format('Y-m-d'), $bankHolidayDates)) {
                continue;
            }

            yield $date;
        }
    }

    public function getBankHolidayStrings(string $region): array
    {
        $jsonData = $this->client->request('GET', 'https://www.gov.uk/bank-holidays.json');

        $bankHolidayData = \json_decode($jsonData, true);

        $bankHolidays = [];

        return \array_column($bankHolidayData[$region]['events'], 'date');
    }
}
