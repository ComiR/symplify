<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\RequireDataProviderTestMethodRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\RequireDataProviderTestMethodRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class RequireDataProviderTestMethodRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    /**
     * @return Iterator<mixed>
     */
    public function provideData(): Iterator
    {
        $errorMessage = sprintf(RequireDataProviderTestMethodRule::ERROR_MESSAGE, 'testThis');
        yield [__DIR__ . '/Fixture/SomeExtendingTestClass.php', [[$errorMessage, 11]]];

        yield [__DIR__ . '/Fixture/SkipUnmatchedClass.php', []];
        yield [__DIR__ . '/Fixture/SkipProvider.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            RequireDataProviderTestMethodRule::class,
            __DIR__ . '/config/standalone_rule.neon'
        );
    }
}
