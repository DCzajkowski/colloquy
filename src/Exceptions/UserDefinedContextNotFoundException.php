<?php

namespace Colloquy\Exceptions;

use RuntimeException;

class UserDefinedContextNotFoundException extends RuntimeException
{
    public function __construct(string $contextName)
    {
        parent::__construct(sprintf(
            'Context %s not found in user-defined bindings. Have you called Colloquy::bind?',
            $contextName
        ));
    }
}
