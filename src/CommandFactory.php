<?php
declare(strict_types=1);

namespace WriteModel;

class CommandFactory
{
    /** @var string[] */
    private $fields;

    /** @var callable */
    private $factoryMethod;

    public function __construct(string ...$fields)
    {
        $this->fields = $fields;
    }

    public function createBy(callable $factoryMethod): void
    {
        $this->factoryMethod = $factoryMethod;
    }

    public function shouldBeCreated(array $data): bool
    {
        if (empty($this->fields)) {
            return true;
        }

        // command should be created if there is data for at least one command's field
        $intersection = array_intersect(array_keys($data), $this->fields);
        return count($intersection) > 0;
    }

    public function create(array $data)
    {
        if (!is_callable($this->factoryMethod)) {
            throw LogicException::factoryMethodIsNotProvided($this->fields);
        }

        // use only data indicated in $fields array
        $data = array_filter($data, function ($key) { return in_array($key, $this->fields); }, ARRAY_FILTER_USE_KEY);

        // flip $fields to have it's value as keys in $defaults array and reset all values to null
        $defaults = array_map(function () { return null; }, array_flip($this->fields));
        $command = call_user_func($this->factoryMethod, array_merge($defaults, $data));

        if (!is_object($command)) {
            throw LogicException::commandIsNotAnObject();
        }

        return $command;
    }
}
