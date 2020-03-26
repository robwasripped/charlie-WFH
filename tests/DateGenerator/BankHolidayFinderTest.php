<?php

declare(strict_types=1);

namespace Tests\Robwasipped\Charliewfh\DateGenerator;

use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Robwasripped\Charliewfh\DateGenerator\BankHolidayFinder;

class BankHolidayFinderTest extends TestCase
{
    private $client;
    private $bankHolidayFinder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = $this->createMock(ClientInterface::class);
        $this->bankHolidayFinder = new BankHolidayFinder($this->client);
    }

    /**
     * @test
     */
    public function getBankHolidayStrings_returns_an_array_of_bank_holiday_dates(): void
    {
        $bankHolidayDateStrings = [
            '2000-12-25',
            '2000-01-01',
        ];


        $this->client
            ->expects($this->once())
            ->method('request')
            ->willReturn($this->createClientJsonFromDateStrings($bankHolidayDateStrings));

        $result = $this->bankHolidayFinder->getBankHolidayStrings(BankHolidayFinder::REGION_ENGLAND_AND_WALES);

        $this->assertEquals($bankHolidayDateStrings, $result);
    }

    /**
     * @test
     */
    public function filterBankHolidays_filters_bank_holidays(): void
    {
        $yielding = static function(): iterable
        {
            yield new \DateTimeImmutable('2020-01-01');
            yield new \DateTimeImmutable('2020-01-02');
            yield new \DateTimeImmutable('2020-01-03');
            yield new \DateTimeImmutable('2020-01-04');
        };

        $bankHolidayDateStrings = [
            '2020-01-01',
            '2000-01-04',
        ];


        $this->client
            ->expects($this->once())
            ->method('request')
            ->willReturn($this->createClientJsonFromDateStrings($bankHolidayDateStrings));

        $result = $this->bankHolidayFinder->filterBankHolidays($yielding(), BankHolidayFinder::REGION_ENGLAND_AND_WALES);

        foreach($result as $resultDate) {
            $this->assertNotContains($resultDate->format('Y-m-d'), $bankHolidayDateStrings);
        }
    }

    private function createClientJsonFromDateStrings(array $dateStrings): string
    {
        $data = [
            'england-and-wales' => [
                'division' => 'england-and-wales',
                'events' => [],
            ],
        ];

        foreach($dateStrings as $dateString) {
            $data['england-and-wales']['events'][] = [
                'title' => 'bank holiday',
                'date' => $dateString,
                'notes' => '',
                'bunting' => true,
            ];
        }

        return json_encode($data);

    }
}
