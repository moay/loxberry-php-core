<?php

namespace LoxBerry\Tests;

use LoxBerry\LoxBerry;
use PHPUnit\Framework\TestCase;

/**
 * Class LoxBerryTest.
 */
class LoxBerryTest extends TestCase
{
    public function testSetupWorks()
    {
        $loxBerry = new LoxBerry();
        $this->assertTrue($loxBerry->testSetup());
    }
}
