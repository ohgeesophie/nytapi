<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Rules\Offset;

class RuleOffsetTest extends TestCase
{
    /**
     * Valid offset null
     *
     * @return void
     */
    public function testOffsetNullPass()
    {
        $rule = new Offset;
        $this->assertTrue($rule->passes('attribute', null));
    }

    /**
     * Valid offset 20
     *
     * @return void
     */
    public function testOffsetValuePass()
    {
        $rule = new Offset;
        $this->assertTrue($rule->passes('attribute', 20));
    }

    /**
     * Invalid offset 21
     *
     * @return void
     */
    public function testOffsetValueFail()
    {
        $rule = new Offset;
        $this->assertFalse($rule->passes('attribute', 21));
    }
}
