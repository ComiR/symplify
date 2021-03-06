<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Split\FileSystem;

use Nette\Utils\Strings;
use Symplify\MonorepoBuilder\ValueObject\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\SmartFileSystem\FileSystemGuard;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\MonorepoBuilder\Split\Tests\FileSystem\DirectoryToRepositoryProvider\DirectoryToRepositoryProviderTest
 */
final class DirectoryToRepositoryProvider
{
    /**
     * @see https://regex101.com/r/BCPylL/1
     * @var string
     */
    private const ASTERISK_REGEX = '#\*#';

    /**
     * @var FileSystemGuard
     */
    private $fileSystemGuard;

    /**
     * @var ParameterProvider
     */
    private $parameterProvider;

    public function __construct(ParameterProvider $parameterProvider, FileSystemGuard $fileSystemGuard)
    {
        $this->fileSystemGuard = $fileSystemGuard;
        $this->parameterProvider = $parameterProvider;
    }

    /**
     * @return array<string, string>
     */
    public function provide(): array
    {
        $resolvedDirectoriesToRepository = [];

        $directoriesToRepositories = $this->parameterProvider->provideArrayParameter(
            Option::DIRECTORIES_TO_REPOSITORIES
        );

        foreach ($directoriesToRepositories as $directory => $repository) {
            if (! Strings::contains($directory, '*')) {
                $this->ensureDirectoryExists($directory);
                $resolvedDirectoriesToRepository[$directory] = $repository;
                continue;
            }

            $fnmatchResolvedDirectoriesToRepository = $this->resolveAsteriskDirectory($directory, $repository);

            $resolvedDirectoriesToRepository = array_merge(
                $resolvedDirectoriesToRepository,
                $fnmatchResolvedDirectoriesToRepository
            );
        }

        return $this->relativizeDirectories($resolvedDirectoriesToRepository);
    }

    private function ensureDirectoryExists(string $directory): void
    {
        $extractMessage = sprintf(
            'Check "%s" parameter in your config.',
            Option::class . '::DIRECTORIES_TO_REPOSITORIES'
        );

        $this->fileSystemGuard->ensureDirectoryExists($directory, $extractMessage);
    }

    /**
     * @return string[]
     */
    private function resolveAsteriskDirectory(string $directory, string $repository): array
    {
        $resolvedDirectoriesToRepository = [];

        // fnmatch
        $patternWithoutAsterisk = (string) trim($directory, '*');

        foreach ((array) glob($directory) as $foundDirectory) {
            /** @var string $foundDirectory */
            $exclusiveName = (string) Strings::after($foundDirectory, $patternWithoutAsterisk);
            $targetRepository = Strings::replace($repository, self::ASTERISK_REGEX, $exclusiveName);

            $resolvedDirectoriesToRepository[$foundDirectory] = $targetRepository;
        }

        return $resolvedDirectoriesToRepository;
    }

    /**
     * @param array<string, string> $directoriesToRepositories
     * @return array<string, string>
     */
    private function relativizeDirectories(array $directoriesToRepositories): array
    {
        $relativeDirectoriesToRepositories = [];

        foreach ($directoriesToRepositories as $directory => $repository) {
            $directoryFileInfo = new SmartFileInfo($directory);
            $relativeDirectory = $directoryFileInfo->getRelativeFilePathFromCwd();

            $relativeDirectoriesToRepositories[$relativeDirectory] = $repository;
        }

        return $relativeDirectoriesToRepositories;
    }
}
