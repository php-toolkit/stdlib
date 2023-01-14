# StdLib

[![License](https://img.shields.io/github/license/php-toolkit/stdlib)](LICENSE)
[![Php Version](https://img.shields.io/badge/php-%3E8.0-brightgreen.svg?maxAge=2592000)](https://packagist.org/packages/toolkit/stdlib)
[![Latest Stable Version](http://img.shields.io/packagist/v/toolkit/stdlib.svg)](https://packagist.org/packages/toolkit/stdlib)
[![Github Actions Status](https://github.com/php-toolkit/stdlib/workflows/Unit-Tests/badge.svg)](https://github.com/php-toolkit/stdlib/actions)
[![Docs on pages](https://img.shields.io/badge/DocsOn-Pages-brightgreen.svg?maxAge=2592000)](https://php-toolkit.github.io/stdlib/)

🧰 Stdlib - Useful basic tools library for PHP development.

**Contains**:

- array, string, number, object helper
- common php, OS env information

**More Utils**

- `PhpDotEnv` Dotenv(`.env`) file load
- `AutoLoader` Simple autoloader
- `ObjectBox` simple object container
- `Optional` like java `java.util.Optional`
- and more ...

## Install

```bash
composer require toolkit/stdlib
```

## String helper

### StrBuffer

```php
use Toolkit\Stdlib\Str\StrBuffer;

$buf = StrBuffer::new("c");
$buf->prepend('ab')
$buf->append('de')

$str = (string)$buf; // "abcde"
$str = $buf->toString(); // "abcde"
// get and clean.
$str = $buf->fetch(); // "abcde"
$str = $buf->join(','); // "ab,c,de"
```

## Object helper

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

### AutoLoader

`AutoLoader` - an simple psr4 loader, can use for tests.

```php
AutoLoader::addFiles([
    // alone files
]);

$loader = AutoLoader::getLoader();
$loader->addPsr4Map([
    'namespace' => 'path'
]);

$loader->addClassMap([
 'name' => 'class file'
]);
```

### Optional

It aims to eliminate excessive if judgments.

Not use Optional:

```php
use Toolkit\Stdlib\Util\Optional;

$userModel = UserModel::findOne(23);

if ($userModel) {
    $username = $userModel->name;
} else {
    $username = 'unknown';
}
```

Use Optional:

```php
use Toolkit\Stdlib\Util\Optional;

$username = Optional::ofNullable($userModel)
    ->map(function ($userModel) {
        return $userModel->name;
    })->orElse('unknown');
```

Use arrow syntax:

```php
use Toolkit\Stdlib\Util\Optional;

$username = Optional::ofNullable($userModel)
    ->map(fn($userModel) => $userModel->name)
    ->orElse('unknown');
```

### PhpDotEnv

`PhpDotEnv` - a simple dont env file loader.

The env config file `.env` (must is 'ini' format):

```ini
APP_ENV=dev
DEBUG=true
; ... ...
```

Usage:

```php
PhpDotEnv::load(__DIR__, '.env');

env('DEBUG', false);
env('APP_ENV', 'prod');
```

### Stream

```php
use Toolkit\Stdlib\Util\Stream\DataStream;
use Toolkit\Stdlib\Util\Stream\ListStream;

$userList = ListStream::of($userModels)
    ->filter(fn($userModel) => $userModel->age > 20) // only need age > 20
    ->map(function ($userModel) {
        // only need field: age, name
        return [
            'age'  => $userModel->age,
            'name' => $userModel->name,
        ];
    })
    ->toArray();

vdump($userList);
```

### PipeFilters

```php
$pf = PipeFilters::newWithDefaultFilters();

$val = $pf->applyString('inhere', 'upper'); // 'INHERE'
$val = $pf->applyString('inhere', 'upper|substr:0,3'); // 'INH'
```

## License

[MIT](LICENSE)
