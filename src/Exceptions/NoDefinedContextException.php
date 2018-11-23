<?php

namespace Colloquy;

use RuntimeException;

class NoDefinedContextException extends RuntimeException
{
    public function __construct(object $object) {
        parent::__construct(sprintf(
            'There is no defined context on %s. Have you added the @ColloquyContext(\'context-id\') annotation?',
            get_class($object)
        ));
    }
}
