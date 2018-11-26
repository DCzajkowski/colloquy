<?php

namespace Tests\Fakes;

use Colloquy\ColloquyContexts;

class TestControllerWithoutContextDeclaration {
    use ColloquyContexts;

    private function step1()
    {
        return 'response';
    }
}
