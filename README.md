# PHPUnit EasyMock

Helpers to build PHPUnit mock objects easily.

[![Build Status](https://travis-ci.org/mnapoli/PHP-DI.png?branch=master)](https://travis-ci.org/mnapoli/PHP-DI)
[![Coverage Status](https://coveralls.io/repos/mnapoli/PHP-DI/badge.png?branch=master)](https://coveralls.io/r/mnapoli/PHP-DI?branch=master)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/mnapoli/PHP-DI/badges/quality-score.png)](https://scrutinizer-ci.com/g/mnapoli/PHP-DI/)
[![Latest Stable Version](https://poser.pugx.org/mnapoli/php-di/v/stable.png)](https://packagist.org/packages/mnapoli/php-di)
[![Total Downloads](https://poser.pugx.org/mnapoli/php-di/downloads.png)](https://packagist.org/packages/mnapoli/php-di)

## Why?

This library is **not** a mocking library. It's just a few helpers to write the most common mocks more easily.

It doesn't reinvent anything and is not intended to cover every use case: only the most common ones.

## Installation

```bash
$ composer require --dev mnapoli/phpunit-easymock
```

## Usage

Here is what a very common PHPUnit mock looks like:

```php
// All these parameters to avoid calling the constructor
$mock = $this->getMock('My\Class', array(), array(), '', false);

$mock->expect($this->any())
    ->method('sayHello')
    ->willReturn('Hello');
```

Yuck!

Here is how to write it with EasyMock:

```php
$mock = EasyMock::mock('My\Class', array(
    'sayHello' => 'Hello',
));
```

### Features

You can mock methods so that they return values:

```php
$mock = EasyMock::mock('My\Class', array(
    'sayHello' => 'Hello',
));
```

Or so that they use a callback:

```php
$mock = EasyMock::mock('My\Class', array(
    'sayHello' => function ($name) {
        return 'Hello ' . $name;
    },
));
```

### What if?

If you want to use assertions or other PHPUnit features, just do it:

```php
$mock = EasyMock::mock('My\Class', array(
    'sayHello' => 'hello',
));

$mock->expects($this->once())
    ->method('sayGoodbye')
    ->willReturn('Goodbye');
```

Mocks are plain PHPUnit mocks, nothing special here.

## Contributing

See the [CONTRIBUTING](CONTRIBUTING.md) file.

## License

Released under the [MIT license](LICENSE).
