<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoNewOutsideFactoryRule\Fixture;

use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

final class SkipException
{
    public function run()
    {
        throw new ShouldNotHappenException();
    }
}
