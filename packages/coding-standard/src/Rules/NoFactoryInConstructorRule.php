<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\ThisType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeWithClassName;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symplify\CodingStandard\ValueObject\MethodName;
use Symplify\CodingStandard\ValueObject\PHPStanAttributeKey;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoFactoryInConstructorRule\NoFactoryInConstructorRuleTest
 */
final class NoFactoryInConstructorRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not use factory/method call in constructor, put factory in config and get service with dependency injection';

    /**
     * @var string[]
     */
    private const ALLOWED_TYPES = [
        ParameterProvider::class,
        ParameterBagInterface::class,
        EntityManagerInterface::class,
    ];

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /**
     * @param MethodCall $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $this->isInConstructMethod($scope)) {
            return [];
        }

        if (! $node->var instanceof Variable) {
            return [];
        }

        // just assign
        $parent = $node->getAttribute(PHPStanAttributeKey::PARENT);
        if ($parent instanceof ArrayDimFetch) {
            return [];
        }

        $callerType = $scope->getType($node->var);
        if ($callerType instanceof ThisType) {
            return [];
        }

        if ($this->isAllowedType($callerType)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    private function isInConstructMethod(Scope $scope): bool
    {
        $reflectionFunction = $scope->getFunction();
        if (! $reflectionFunction instanceof MethodReflection) {
            return false;
        }

        return $reflectionFunction->getName() === MethodName::CONSTRUCTOR;
    }

    private function isAllowedType(Type $type): bool
    {
        if (! $type instanceof TypeWithClassName) {
            return false;
        }

        foreach (self::ALLOWED_TYPES as $allowedType) {
            if (is_a($type->getClassName(), $allowedType, true)) {
                return true;
            }
        }

        return false;
    }
}
