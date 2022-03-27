<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Rules\ISBNLength;

class RuleISBNLengthTest extends TestCase
{
    /**
     * Valid length 10
     *
     * @return void
     */
    public function testLengthShortPass()
    {
        $rule = new ISBNLength;
        $this->assertTrue($rule->passes('attribute', '1234512345'));
    }

    /**
     * Valid length 13
     *
     * @return void
     */
    public function testLengthLongPass()
    {
        $rule = new ISBNLength;
        $this->assertTrue($rule->passes('attribute', '123451234512X'));
    }

    /**
     * Invalid length 9
     *
     * @return void
     */
    public function testLengthFail()
    {
        $rule = new ISBNLength;
        $this->assertFalse($rule->passes('attribute', '123451234'));
    }

    /**
     * Invalid length null
     *
     * @return void
     */
    public function testLengthNullFail()
    {
        $rule = new ISBNLength;
        $this->assertTrue($rule->passes('attribute', null));
    }
}
