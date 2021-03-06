# PHPUnit EasyMock

Helpers to build PHPUnit mock objects easily.

[![Total Downloads](https://poser.pugx.org/mnapoli/phpunit-easymock/downloads)](https://packagist.org/packages/mnapoli/phpunit-easymock)

## Why?

This library is **not** a mocking library. It's just a few helpers to write the most common mocks more easily.

It doesn't reinvent anything and is not intended to cover every use case: only the most common ones.

## Installation

```bash
$ composer require --dev mnapoli/phpunit-easymock
```

To be able to use EasyMock in your tests **you must include the trait in your class**:

```php
class MyTest extends \PHPUnit\Framework\TestCase
{
    use \EasyMock\EasyMock;

    // ...
}
```

## Usage

Here is what a very common PHPUnit mock looks like:

```php
$mock = $this->createMock('My\Class');

$mock->expect($this->any())
    ->method('sayHello')
    ->willReturn('Hello');
```

Yuck!

Here is how to write it with EasyMock:

```php
$mock = $this->easyMock('My\Class', [
    'sayHello' => 'Hello',
]);
```

What if you want to assert that the method is called once (i.e. `$mock->expect($this->once())`)? Use `spy()` instead:

```php
$mock = $this->easySpy('My\Class', [
    'sayHello' => 'Hello',
]);
```

### Features

You can mock methods so that they return values:

```php
$mock = $this->easyMock('My\Class', [
    'sayHello' => 'Hello',
]);
```

Or so that they use a callback:

```php
$mock = $this->easyMock('My\Class', [
    'sayHello' => function ($name) {
        return 'Hello ' . $name;
    },
]);
```

You can also have methods throw exceptions by providing an `Exception` instance:

```php
$mock = $this->easyMock('My\Class', [
    'sayHello' => new \RuntimeException('Whoops'),
]);
```

It is possible to call the `mock()` method again on an existing mock:

```php
$mock = $this->easyMock('My\Class');

$mock = $this->easyMock($mock, [
    'sayHello' => 'Hello',
]);
```

### What if?

If you want to use assertions or other PHPUnit features, just do it:

```php
$mock = $this->easyMock('My\Class', [
    'sayHello' => 'hello',
]);

$mock->expects($this->once())
    ->method('sayGoodbye')
    ->willReturn('Goodbye');
```

Mocks are plain PHPUnit mocks, nothing special here.

## Contributing

See the [CONTRIBUTING](CONTRIBUTING.md) file.

## License

Released under the [MIT license](LICENSE).
