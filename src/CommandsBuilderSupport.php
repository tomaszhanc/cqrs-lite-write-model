<?php
declare(strict_types=1);

namespace WriteModel;

trait CommandsBuilderSupport
{
    /** @var CommandsBuilder */
    protected $commandsBuilder;

    protected function addCommandFor(...$keys): CommandFactory
    {
        if ($this->commandsBuilder === null) {
            $this->commandsBuilder = new CommandsBuilder();
        }

        $factory = new CommandFactory(...$keys);
        $this->commandsBuilder->addCommandFactory($factory);

        return $factory;
    }
}
