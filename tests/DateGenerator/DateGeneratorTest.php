<?php

declare(strict_types=1);

namespace Tests\Robwasipped\Charliewfh\DateGenerator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Robwasripped\Charliewfh\DateGenerator\DateGenerator;

class DateGeneratorTest extends TestCase
{
    /**
     * MockObject|DateGenerator
     */
    private $dateGenerator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dateGenerator = new DateGenerator();
    }

    /**
     * @test
     * @dataProvider dateProvider
     */
    public function getDatesForRange_returns_expected_dates(\DateTimeImmutable $from, \DateTimeImmutable $to, bool $includeWeekends, array $expectedDateStrings): void
    {
        $dates = [];

        foreach($this->dateGenerator->getDatesForRange($from, $to, $includeWeekends) as $date) {
            $dates[] = $date->format('Y-m-d');
        }

        $this->assertSame($expectedDateStrings, $dates);
    }

    public function dateProvider(): iterable
    {
        yield 'single day' => [new \DateTimeImmutable('2000-01-03 00:00:00'), new \DateTimeImmutable('2000-01-03 00:00:00'), false, ['2000-01-03']];
        yield 'single day as weekend' => [new \DateTimeImmutable('2000-01-01 00:00:00'), new \DateTimeImmutable('2000-01-01 00:00:00'), false, []];
        yield 'ignores weekends in range' => [new \DateTimeImmutable('2000-01-01 00:00:00'), new \DateTimeImmutable('2000-01-03 00:00:00'), false, ['2000-01-03']];
        yield 'full week range' => [new \DateTimeImmutable('2000-01-03 00:00:00'), new \DateTimeImmutable('2000-01-07 00:00:00'), false, ['2000-01-03', '2000-01-04', '2000-01-05', '2000-01-06', '2000-01-07']];

        yield 'single day included weekends' => [new \DateTimeImmutable('2000-01-01 00:00:00'), new \DateTimeImmutable('2000-01-01 00:00:00'), true, ['2000-01-01']];
        yield 'includes weekends in range' => [new \DateTimeImmutable('2000-01-01 00:00:00'), new \DateTimeImmutable('2000-01-03 00:00:00'), true, ['2000-01-01', '2000-01-02', '2000-01-03']];
    }
}
