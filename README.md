<p align="center"><img width="256" src="https://colloquy.netlify.com/assets/img/logo-text.svg"></p>

<p align="center">A framework-agnostic package for managing persistent conversation contexts.</p>

<p align="center">
    <a href="https://packagist.org/packages/dczajkowski/colloquy"><img alt="Latest Stable Version" src="https://poser.pugx.org/dczajkowski/colloquy/version"></a>
    <a href="https://github.com/DCzajkowski/colloquy/blob/master/LICENSE.md"><img alt="License" src="https://poser.pugx.org/dczajkowski/colloquy/license"></a>
    <a href="https://scrutinizer-ci.com/g/DCzajkowski/colloquy/?branch=master"><img alt="Scrutinizer Code Quality" src="https://scrutinizer-ci.com/g/DCzajkowski/colloquy/badges/quality-score.png?b=master"></a>
    <a href="https://scrutinizer-ci.com/g/DCzajkowski/colloquy/?branch=master"><img alt="Code Coverage" src="https://scrutinizer-ci.com/g/DCzajkowski/colloquy/badges/coverage.png?b=master"></a>
    <a href="https://scrutinizer-ci.com/g/DCzajkowski/colloquy/build-status/master"><img alt="Build Status" src="https://scrutinizer-ci.com/g/DCzajkowski/colloquy/badges/build.png?b=master"></a>
</p>

# Installation
```bash
composer install dczajkowski/colloquy
```

# Usage

## Using in auto-mode with annotations
**Identifier resolver declaration**
```php
<?php

namespace App;

class SessionIdentifierResolver implements \Colloquy\IdentifierResolverInterface
{
    public function get($controller): string
    {
        return // code to get session id 
    }
}
```

**Context binding**
```php
<?php

\Colloquy\Colloquy::bind(
    'session',
    new \App\SessionIdentifierResolver,
    new \Colloquy\Drivers\FileDriver($pathToWritableDirectory)
);
```

**Use in a controller**
```php
<?php

namespace App\Http\Controllers;

/** @ColloquyContext('session') */
class FormController
{
    use \Colloquy\ColloquyContexts;
    
    /** @ColloquyPersist */
    protected $user;

    /** @ColloquyBegin */
    private function step1()
    {
        $this->user = new \App\Models\User;
    }

    private function step2()
    {
        $this->user->name = 'John';
    }

    /** @ColloquyEnd */
    private function step3()
    {
        echo $this->user->name; // John
    }
}
```

## Manual Use

```php
<?php

use Colloquy\Colloquy;
use Colloquy\Drivers\FileDriver;

class User
{
   private $name;
   
   public function __construct(string $name)
   {
       $this->name = $name;
   }
}

/** Starting a Conversation */

$wrapper = new Colloquy(new FileDriver('storage'));

$homeContext = $wrapper->context('Home');
$formContext = $wrapper->context('Form');

/** Primitive types */

$homeContext->add('Joe', 'name');
$homeContext->add('ilovecats', 'password');
$formContext->add('Jane', 'name');

$name = $homeContext->get('name'); // Joe

$homeContext->set('John', 'name');

$name = $homeContext->get('name'); // John

/** Objects */

$user = new User('Jack');

$homeContext->add($user, 'user');

var_dump($user); // User { name: "Jack" }

$user = $homeContext->get('user');

var_dump($user); // User { name: "Jack" }

/** Ending a Conversation */

$wrapper->end('Form');
$wrapper->end('Home');
```

# Contribution
Contributions are very welcome. If you want, just drop a PR with any feature you'd like to see.

# Authors
The library was made by [Dariusz Czajkowski](https://dczajkowski.com), Grzegorz Tłuszcz, Aleksander Kuźma, Karol Piwnicki.

# License
The Colloquy package is open-sourced software licensed under the MIT license.
