<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\NamespaceNotation\SingleBlankLineBeforeNamespaceFixer;
use SlevomatCodingStandard\Sniffs\Namespaces\UselessAliasSniff;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(UselessAliasSniff::class);

    $services->set(NoUnusedImportsFixer::class);

    $services->set(OrderedImportsFixer::class);

    $services->set(SingleBlankLineBeforeNamespaceFixer::class);
};
