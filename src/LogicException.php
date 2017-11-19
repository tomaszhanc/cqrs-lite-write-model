<?php
declare(strict_types=1);

namespace WriteModel;

class LogicException extends \LogicException
{
    public static function factoryMethodIsNotProvided(array $fields): self
    {
        return new self(sprintf(
            'You need to provide factory method for fields: [%s]',
            implode(',', $fields)
        ));
    }

    public static function commandIsNotAnObject(): self
    {
        return new self('Factory method should return an object');
    }
}
