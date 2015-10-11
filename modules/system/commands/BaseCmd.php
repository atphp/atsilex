<?php

namespace atsilex\module\system\commands;

use Symfony\Component\Console\Command\Command;

/**
 * @TODO: How to hide this command to end user?
 */
abstract class BaseCmd extends Command
{
    const NAME        = '';
    const DESCRIPTION = '';

    public function __construct()
    {
        if (!static::NAME) {
            throw new \RuntimeException('Command name is not defined in ' . get_class($this));
        }

        if (static::DESCRIPTION) {
            $this->setDescription(static::DESCRIPTION);
        }

        return parent::__construct(static::NAME);
    }
}
