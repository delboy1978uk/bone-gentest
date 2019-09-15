# bone-gentest
Test converting a standard Bone Mvc Framework entity module into a composer package
## installation
Use composer to install
```
composer require delboy1978uk/bone-gentest
```
## usage
Simply add the Package to Bone's module config
```php
<?php

// use statements here
use Random\Developer\Awesome\AwesomePackage;

return [
    'packages' => [
        // packages here...,
        AwesomePackage::class,
    ],
    // ...
];
