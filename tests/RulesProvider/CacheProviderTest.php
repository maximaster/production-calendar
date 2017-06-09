<?php

namespace Maximaster\ProductionCalendar\Test\RulesProvider;

use Maximaster\ProductionCalendar\Rules;
use Maximaster\ProductionCalendar\RulesProvider\BasicdataProvider;
use Maximaster\ProductionCalendar\RulesProvider\CacheProvider;
use PHPUnit_Framework_MockObject_MockObject;

class CacheProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        /**
         * @var int $cnt Количество вызовов провайдера данных
         */
        $cnt = 0;

        /**
         * @var BasicdataProvider|PHPUnit_Framework_MockObject_MockObject $mock
         */
        $mock = $this->getMock('Maximaster\ProductionCalendar\RulesProvider\BasicdataProvider');
        $mock
            ->method('get')
            ->will($this->returnCallback(function () use (&$cnt) {
                $cnt++;
                return new Rules;
            }));

        $cache = new CacheProvider($mock);
        $cache->clear();

        $cache->get();
        $this->assertSame(1, $cnt);

        $cache->get();
        $this->assertSame(1, $cnt);

        $cache->clear();
        $cache->get();
        $this->assertSame(2, $cnt);
    }
}
