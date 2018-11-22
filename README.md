# ⚠️ WIP ⚠️ - Colloquy
A framework-agnostic package for managing persistent conversation contexts.

# Installation
```bash
composer install dczajkowski/colloquy
```

# Usage

```php
<?php

use Colloquy\Colloquy;
use Colloquy\Drivers\FileDriver;

class User
{
   private $name;
   
   public function __construct(string $name) {
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

/** User-defined classes */

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
The app was made by Dariusz Czajkowski, Grzegorz Tłuszcz, Aleksander Kuźma, Karol Piwnicki.

# License
The Colloquy package is open-sourced software licensed under the MIT license.
