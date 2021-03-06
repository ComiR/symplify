<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\CheckRequiredMethodTobeAutowireWithClassNameRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\CheckRequiredMethodTobeAutowireWithClassNameRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class CheckRequiredMethodTobeAutowireWithClassNameRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(array $filePaths, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse($filePaths, $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        $errorMessage = sprintf(
            CheckRequiredMethodTobeAutowireWithClassNameRule::ERROR_MESSAGE,
            'autowireRequiredByTrait'
        );
        yield [
            [__DIR__ . '/Fixture/ClassUsingRequiredByTrait.php', __DIR__ . '/Fixture/RequiredByTrait.php'],
            [[$errorMessage, 12]],
        ];

        $errorMessage = sprintf(
            CheckRequiredMethodTobeAutowireWithClassNameRule::ERROR_MESSAGE,
            'autowireRequiredByTraitCorrect'
        );
        yield [
            [
                __DIR__ . '/Fixture/ClassUsingRequiredByTraitCorrect.php',
                __DIR__ . '/Fixture/RequiredByTraitCorrect.php',
            ],
            [[$errorMessage, 12]],
        ];

        yield [[__DIR__ . '/Fixture/EmptyDocblock.php'], []];
        yield [[__DIR__ . '/Fixture/WithoutRequired.php'], []];
        yield [[__DIR__ . '/Fixture/WithRequiredAutowire.php'], []];

        $errorMessage = sprintf(
            CheckRequiredMethodTobeAutowireWithClassNameRule::ERROR_MESSAGE,
            'autowireWithRequiredNotAutowire'
        );
        yield [[__DIR__ . '/Fixture/WithRequiredNotAutowire.php'], [[$errorMessage, 12]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            CheckRequiredMethodTobeAutowireWithClassNameRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
