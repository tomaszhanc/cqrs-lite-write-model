<?php
declare(strict_types=1);

namespace WriteModel\Tests;

use Prophecy\Argument;
use WriteModel\CommandFactory;
use PHPUnit\Framework\TestCase;
use WriteModel\Tests\Fixtures\FactoryMethod;

class CommandFactoryTest extends TestCase
{
    /** @var FactoryMethod */
    private $factoryMethod;

    /** @var CommandFactory */
    private $commandFactory;

    protected function setup()
    {
        $this->commandFactory = new CommandFactory('name', 'description');
        $this->factoryMethod = $this->prophesize(FactoryMethod::class);
        $this->factoryMethod->create(Argument::any())->willReturn(new \stdClass());
    }

    /**
     * @test
     */
    public function should_create_a_command_always_when_fields_are_not_provided()
    {
        $factory = new CommandFactory();
        $this->assertTrue($factory->shouldBeCreated(['name' => 'John', 'description' => '']));
    }

    /**
     * @test
     */
    public function should_create_a_command()
    {
        $this->givenThatFactoryIsProvided();
        $this->assertTrue($this->commandFactory->shouldBeCreated(['name' => 'John', 'description' => '']));

        $this->commandFactory->create(['name' => 'John', 'description' => '']);
        $this->factoryMethod->create(['name' => 'John', 'description' => ''])->shouldHaveBeenCalled();
    }

    /**
     * @test
     */
    public function should_return_false_if_there_is_no_data_indicated_for_command()
    {
        $this->givenThatFactoryIsProvided();
        $this->assertFalse($this->commandFactory->shouldBeCreated(['firstName' => 'John']));

        // but still command can be created
        $this->commandFactory->create(['firstName' => 'John']);
        $this->factoryMethod->create(['name' => null, 'description' => null])->shouldHaveBeenCalled();
    }

    /**
     * @test
     * @expectedException \WriteModel\LogicException
     */
    public function should_prevent_from_creating_command_when_factory_method_is_not_provided()
    {
        $this->commandFactory->create([]);
    }

    /**
     * @test
     * @expectedException \WriteModel\LogicException
     */
    public function should_prevent_from_creating_command_when_factory_method_does_not_retunr_an_object()
    {
        $this->commandFactory->createBy(function () {});
        $this->commandFactory->create([]);
    }

    protected function givenThatFactoryIsProvided(): void
    {
        $this->commandFactory->createBy([$this->factoryMethod->reveal(), 'create']);
    }
}
