<?php
declare(strict_types=1);

namespace WriteModel;

final class CommandsBuilder
{
    /** @var CommandFactory[] */
    private $factories;

    public function addCommandFactory(CommandFactory $factory): void
    {
        $this->factories[] = $factory;
    }

    public function createCommandsFor(array $data): array
    {
        foreach ($this->factories as $factory) {
            if ($factory->shouldBeCreated($data)) {
                $commands[] = $factory->create($data);
            }
        }

        return $commands ?? [];
    }
}
