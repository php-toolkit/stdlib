# StdLib

[![License](https://img.shields.io/github/license/php-toolkit/stdlib)](LICENSE)
[![Php Version](https://img.shields.io/badge/php-%3E7.1.0-brightgreen.svg?maxAge=2592000)](https://packagist.org/packages/toolkit/stdlib)
[![Latest Stable Version](http://img.shields.io/packagist/v/toolkit/stdlib.svg)](https://packagist.org/packages/toolkit/stdlib)
[![Github Actions Status](https://github.com/php-toolkit/stdlib/workflows/Unit-Tests/badge.svg)](https://github.com/php-toolkit/stdlib/actions)

Some useful basic tools for php.

Contains:

- array helper
- object helper
- string helper
- common php helper
- OS env information
- dotenv load `.env`
- simple autoloader
- simple object container
- and more ...

## Install

```bash
composer require toolkit/stdlib
```

## Usage

### Object Box

```php
$box = \Toolkit\Stdlib\Obj\ObjectBox::global();

// set
$box->set('router', function () {
    return new MyRouter();
});

$box->set('renderer', [
    'class' => MyRenderer::class,
    'tplDir' => 'path/to/dir',
]);

// get
/** @var MyRouter $router */
$router = $box->get('router');
/** @var MyRenderer $renderer */
$renderer = $box->get('renderer');
```

## License

[MIT](LICENSE)
