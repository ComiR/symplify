<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoConstructorInTestRule\Fixture\Test2;

use stdClass;

final class SomeTest
{
    public function __construct()
    {
        $this->obj = new stdClass;
    }
}
