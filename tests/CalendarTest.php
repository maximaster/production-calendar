<?php

namespace Maximaster\ProductionCalendar\Test;

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