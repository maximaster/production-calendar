<?php

namespace Maximaster\ProductionCalendar\Test\RulesProvider;

use Maximaster\ProductionCalendar\RulesProvider\BasicdataProvider;
use Maximaster\ProductionCalendar\Rules;

class BasicdataProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $provider = new BasicdataProvider;
        $rules = $provider->get();

        $this->assertSame($rules->getDay(2, 1, 2017), Rules::HOLIDAY);
        $this->assertNotSame($rules->getDay(17, 7, 2017), Rules::HOLIDAY);

        $this->assertSame($rules->getDay(22, 2, 2017), Rules::PRE_HOLIDAY);
        $this->assertNotSame($rules->getDay(18, 7, 2017), Rules::HOLIDAY);

        $this->assertSame($rules->getDay(7, 1, 2017), Rules::UNKNOWN);
        $this->assertNotSame($rules->getDay(3, 1, 2017), Rules::REGULAR);

        $this->assertSame($rules->getDay(9, 1, 2017), Rules::UNKNOWN);
    }
}
