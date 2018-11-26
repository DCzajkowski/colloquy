<?php

namespace Tests\Fakes;

use Colloquy\ColloquyContexts;

/** @ColloquyContext */
class TestControllerWithInvalidContextDeclaration {
    use ColloquyContexts;

    private function step1()
    {
        return 'response';
    }
}
