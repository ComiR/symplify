<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use Symplify\CodingStandard\PhpParser\NodeNameResolver;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\PrefferedStaticCallOverFuncCallRule\PrefferedStaticCallOverFuncCallRuleTest
 */
final class PrefferedStaticCallOverFuncCallRule extends AbstractPrefferedCallOverFuncRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use "%s::%s()" static call over "%s()" func call';

    /**
     * @var array<string, string[]>
     */
    private $funcCallToPrefferedStaticCalls = [];

    /**
     * @param array<string, string[]> $funcCallToPrefferedStaticCalls
     */
    public function __construct(NodeNameResolver $nodeNameResolver, array $funcCallToPrefferedStaticCalls = [])
    {
        parent::__construct($nodeNameResolver);

        $this->funcCallToPrefferedStaticCalls = $funcCallToPrefferedStaticCalls;
    }

    /**
     * @param FuncCall $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $errorMessageParameters = $this->getErrorMessageParameters(
            $node,
            $scope,
            $this->funcCallToPrefferedStaticCalls
        );
        if ($errorMessageParameters === []) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, ...$errorMessageParameters);
        return [$errorMessage];
    }
}
