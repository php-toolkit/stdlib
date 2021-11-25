# StdLib

[![License](https://img.shields.io/github/license/php-toolkit/stdlib)](LICENSE)
[![Php Version](https://img.shields.io/badge/php-%3E7.1.0-brightgreen.svg?maxAge=2592000)](https://packagist.org/packages/toolkit/stdlib)
[![Latest Stable Version](http://img.shields.io/packagist/v/toolkit/stdlib.svg)](https://packagist.org/packages/toolkit/stdlib)
[![Github Actions Status](https://github.com/php-toolkit/stdlib/workflows/Unit-Tests/badge.svg)](https://github.com/php-toolkit/stdlib/actions)

Stdlib - useful basic tools for php develop.

**Contains**:

- array, string, number, object helper
- common php, OS env information

**More Utils**

- Dotenv load `.env`
- Simple autoloader
- `ObjectBox` simple object container
- `Optional` like java `java.util.Optional`
- and more ...

## Install

```bash
composer require toolkit/stdlib
```

## Usage

### String helper

### Object box

`ObjectBox` - Simple object container.

```php
use Toolkit\Stdlib\Obj\ObjectBox;

$box = ObjectBox::global();

// set
$box->set('router', function () {
    return new MyRouter();
});

$box->set('renderer', [
    'class' => MyRenderer::class,
    'tplDir' => 'path/to/dir',
]);

// with options for create
$box->set('somObj', [
    'class' => MyObject::class,
    '__opt' => [
        // will always create new object.
        'objType' => ObjectBox::TYPE_PROTOTYPE,
    ],
]);

// get
/** @var MyRouter $router */
$router = $box->get('router');
/** @var MyRenderer $renderer */
$renderer = $box->get('renderer');
```

## Util classes

### Optional

```php

```

### DataStream

```php
use Toolkit\Stdlib\Util\Stream\DataStream;

$stream = DataStream::of($data);
```

## License

[MIT](LICENSE)
