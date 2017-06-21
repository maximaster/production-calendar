<?php

namespace Maximaster\ProductionCalendar\Test;

use DateTime;
use Maximaster\ProductionCalendar\Calendar;
use Maximaster\ProductionCalendar\Rules;
use Maximaster\ProductionCalendar\RulesProvider\BasicdataProvider;

class CalendarTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Calendar
     */
    protected $calendar;

    /**
     * @return array Data for testIsFreeDay
     */
    public function isFreeDayDataProvider()
    {
        return [
            ['01.01.2017', true],
            ['03.01.2017', true],
            ['05.01.2017', true],
            ['11.01.2017', false],
        ];
    }

    /**
     * @param string $day
     * @param bool $expected
     * @dataProvider isFreeDayDataProvider
     */
    public function testIsFreeDay($day, $expected)
    {
        $this->dayCheckMethod('isFreeDay', $day, $expected);
    }

    /**
     * @return array Data for testRegularRestDay
     */
    public function regularRestDayProvider()
    {
        return [
            ['01.01.2017', true],
            ['02.01.2017', false],
        ];
    }

    /**
     * @param $day
     * @param $expected
     * @dataProvider regularRestDayProvider
     */
    public function testRegularRestDay($day, $expected)
    {
        $this->dayCheckMethod('isRegularRestDay', $day, $expected);
    }

    /**
     * @return array Data for testIsDay
     */
    public function isDayProvider()
    {
        return [
            ['01.01.2017', Rules::REGULAR_REST, true],
            ['01.01.2017', [Rules::REGULAR_REST], true],
            ['01.01.2017', [Rules::REGULAR_REST, Rules::REGULAR], true],
            ['01.01.2017', [Rules::REGULAR], false],
        ];
    }

    /**
     * @param $day
     * @param $types
     * @param $expected
     * @dataProvider isDayProvider
     */
    public function testIsDay($day, $types, $expected)
    {
        $this->assertSame($expected, $this->calendar->isDay($day, $types));
    }

    /**
     * @return array Data for testGetDayType
     */
    public function getDayProvider()
    {
        return [
            ['01.01.2017', Rules::REGULAR_REST],
            ['02.01.2017', Rules::HOLIDAY],
        ];
    }

    /**
     * @param $day
     * @param $expected
     * @dataProvider getDayProvider
     */
    public function testGetDayType($day, $expected)
    {
        $this->assertSame($expected, $this->calendar->getDayType($day));
    }

    /**
     * @return array Data for testGetFreeDays
     */
    public function getDaysProvider()
    {
        return [
            [
                Rules::$FREE,
                new DateTime('06.01.2017'),
                new DateTime('15.01.2017'),
                [
                    new DateTime('06.01.2017'),
                    new DateTime('07.01.2017'),
                    new DateTime('08.01.2017'),
                    new DateTime('14.01.2017'),
                    new DateTime('15.01.2017'),
                ]
            ],
            [
                Rules::$FREE,
                new DateTime('06.03.2017'),
                new DateTime('12.03.2017'),
                [
                    new DateTime('08.03.2017'),
                    new DateTime('11.03.2017'),
                    new DateTime('12.03.2017'),
                ]
            ],
        ];
    }

    /**
     * @param int|int[] $types
     * @param DateTime $from
     * @param DateTime $to
     * @param DateTime[] $expected
     * @dataProvider getDaysProvider
     */
    public function testGetDays($types, DateTime $from, DateTime $to, $expected)
    {
        $this->assertEquals($expected, $this->calendar->getDays($types, $from, $to));
    }

    /**
     * @return array Data for testGetMonthFreeDays
     */
    public function getMonthDaysProvider()
    {
        $expected0317 = [
            new DateTime('04.03.2017'),
            new DateTime('05.03.2017'),
            new DateTime('08.03.2017'),
            new DateTime('11.03.2017'),
            new DateTime('12.03.2017'),
            new DateTime('18.03.2017'),
            new DateTime('19.03.2017'),
            new DateTime('25.03.2017'),
            new DateTime('26.03.2017'),
        ];

        return [
            [Rules::$FREE, 2017, 03, $expected0317],
            [Rules::$FREE, new DateTime('05.03.2017 13:55'), null, $expected0317],
        ];
    }

    /**
     * @param int|int[] $types
     * @param int|DateTime $year
     * @param int|null $month
     * @param DateTime[] $expected
     * @dataProvider getMonthDaysProvider
     */
    public function testGetMonthDays($types, $year, $month, $expected)
    {
        $this->assertEquals($expected, $this->calendar->getMonthDays($types, $year, $month));
    }

    /**
     * @return array Data for testGetMonthDaysCount
     */
    public function getMonthDaysCountProvider()
    {
        return [
            [Rules::$FREE, 2017, 3, 9],
            [Rules::$WORK, 2017, 3, 22],
            [Rules::$WORK, new DateTime('01.04.2017'), null, 20],
        ];
    }

    /**
     * @param $types
     * @param $year
     * @param $month
     * @param $expected
     * @dataProvider getMonthDaysCountProvider
     */
    public function testGetMonthDaysCount($types, $year, $month, $expected)
    {
        $this->assertEquals($expected, $this->calendar->getMonthDaysCount($types, $year, $month));
    }

    /**
     * @return array Data for testGetMonthWorkDaysCount
     */
    public function getMonthWorkDaysCountProvider()
    {
        return [
            [2017, 03, 22],
            [2017, 06, 21],
            [2017, 07, 21],
        ];
    }

    /**
     * @param int|DateTime $year
     * @param int|null $month
     * @param int $expected
     * @dataProvider getMonthWorkDaysCountProvider
     */
    public function testGetMonthWorkDaysCount($year, $month, $expected)
    {
        $this->assertEquals($expected, $this->calendar->getMonthWorkDaysCount($year, $month));
    }

    /**
     * Обобщает методику проверки методов принимающих на вход только день, и возвращающих нечто, что надо проверить
     * @param string $method
     * @param string $day
     * @param int $expected
     */
    protected function dayCheckMethod($method, $day, $expected)
    {
        $this->assertSame($expected, $this->calendar->{$method}($day));
    }

    public function setUp()
    {
        $this->calendar = Calendar::fromProvider(new BasicdataProvider);
    }
}