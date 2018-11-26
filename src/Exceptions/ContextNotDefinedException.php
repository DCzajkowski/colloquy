<?php

namespace Colloquy\Exceptions;

use RuntimeException;

class ContextNotDefinedException extends RuntimeException
{
    public function __construct(object $object) {
        parent::__construct(sprintf(
            'There is no defined context on %s. Have you added the @ColloquyContext(\'context-id\') annotation?',
            get_class($object)
        ));
    }
}
