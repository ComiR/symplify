<?php

declare(strict_types=1);

namespace Symplify\NeonToYamlConverter\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

final class NeonToYamlConverterApplication extends Application
{
    /**
     * @param Command[] $commands
     */
    public function __construct(array $commands)
    {
        $this->addCommands($commands);
        parent::__construct();
    }
}
