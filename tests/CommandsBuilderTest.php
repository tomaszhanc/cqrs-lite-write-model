<?php
declare(strict_types=1);

namespace WriteModel\Tests;

use Prophecy\Argument;
use WriteModel\CommandFactory;
use WriteModel\CommandsBuilder;
use PHPUnit\Framework\TestCase;

class CommandsBuilderTest extends TestCase
{
    /** @var CommandsBuilder */
    private $builder;

    protected function setup()
    {
        $this->builder = new CommandsBuilder();
    }

    /**
     * @test
     */
    public function should_create_only_selected_commands()
    {
        $factoryA = $this->prophesizeFactory(true);
        $factoryB = $this->prophesizeFactory(false);
        $factoryC = $this->prophesizeFactory(true);

        $this->builder->addCommandFactory($factoryA);
        $this->builder->addCommandFactory($factoryB);
        $this->builder->addCommandFactory($factoryC);

        $commands = $this->builder->createCommandsFor([]);

        $this->assertCount(2, $commands);
    }

    protected function prophesizeFactory(bool $shouldBeCreated)
    {
        $factory = $this->prophesize(CommandFactory::class);
        $factory->shouldBeCreated(Argument::any())->willReturn($shouldBeCreated);

        if ($shouldBeCreated) {
            $factory->create(Argument::any())->willReturn(new \stdClass())->shouldBeCalled();
        } else {
            $factory->create(Argument::any())->shouldNotBeCalled();
        }

        return $factory->reveal();
    }
}
