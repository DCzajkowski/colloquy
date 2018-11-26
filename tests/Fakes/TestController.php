<?php

namespace Tests\Fakes;

use Colloquy\ColloquyContexts;

/** @ColloquyContext('session') */
class TestController
{
    use ColloquyContexts;

    /**
     * @type User
     * @ColloquyPersist
     */
    public $user;

    /**
     * @ColloquyPersist('custom-identifier-form')
     */
    public $form;

    /** @ColloquyBegin */
    private function step1()
    {
        $this->user = new User;
        $this->user->setName('John');

        $this->form = [];

        return 'step1';
    }

    private function step2()
    {
        $this->user->setAge(21);
        $this->form['name'] = 'Jack';

        return 'step2';
    }

    /** @ColloquyEnd */
    private function step3()
    {
        return $this->user;
    }
}
