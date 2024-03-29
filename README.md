# Route

[![Scrutinizer Build Status][scrutinizer-build-image]][scrutinizer-build-url]
[![Scrutinizer Code Quality][scrutinizer-code-quality-image]][scrutinizer-code-quality-url]
[![Scrutinizer Code Coverage][scrutinizer-code-coverage-image]][scrutinizer-code-coverage-url]
[![Packagist Latest Stable Version][packagist-image]][packagist-url]
[![MIT License][license-image]][license-url]

> Frameware-agnostic, Middleware-compatible routing framework for PHP 7+.


## Installation

### Composer

To install using [Composer](https://getcomposer.org/), enter the following at the command line:

```cli
composer require meraki/route
```

## Usage

### Getting Started

```php
<?php
require_once 'vendor/autoload.php';

use Meraki\Route\Collection;
use Meraki\Route\Mapper;
use Meraki\Route\Matcher;
use Meraki\Route\MatchResult;

// any psr7 and psr11 compliant library will work
use Laminas\Diactoros\ServerRequestFactory;

$map = new Mapper(new Collection());

$map->get('/', new ShowHomepage());
$map->get('/contact', new ShowContactForm());
$map->post('/contact', new SendContactForm());

$map->get('/users/:id', new DisplayEditUserForm())
	->name('display.user.profile')
	->constrain(':id', Constraint::digit());

$map->get('/users/:id');

$matcher = new Matcher($map->getRules());
$result = $match->match(ServerRequestFactory::fromGlobals());

if ($result->isSuccessful()) {
	// handle a successful match
} else {
	// handle a failed match 404,405,406,etc.
}

```

### Full Documentation

See the [Wiki](https://github.com/merakiframework/route/wiki).

## Testing

To run the tests, run the following script:

```cli
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email nbish11@hotmail.com instead of using the issue tracker.

## Credits

Without help from the following people, software and services, this project would not have been possible. So a big thank you to all.

### Authors

- [Nathan Bishop](https://github.com/nbish11)

### Contributors

### Software

- [PHPUnit](https://github.com/sebastianbergmann/phpunit)
- [PHPStan](https://github.com/phpstan/phpstan)

### Services

- [Travis CI](https://travis-ci.com/)
- [Code Climate](https://codeclimate.com)

## License

The MIT License (MIT). Please see the [LICENSE](LICENSE.md) file for more information.

[scrutinizer-build-url]: https://scrutinizer-ci.com/g/merakiframework/route/build-status/master
[scrutinizer-build-image]: https://scrutinizer-ci.com/g/merakiframework/route/badges/build.png?b=master
[scrutinizer-code-quality-url]: https://scrutinizer-ci.com/g/merakiframework/route/?branch=master
[scrutinizer-code-quality-image]: https://scrutinizer-ci.com/g/merakiframework/route/badges/quality-score.png?b=master
[scrutinizer-code-coverage-url]: https://scrutinizer-ci.com/g/merakiframework/route/?branch=master
[scrutinizer-code-coverage-image]: https://scrutinizer-ci.com/g/merakiframework/route/badges/coverage.png?b=master
[packagist-url]: https://packagist.org/packages/meraki/route
[packagist-image]: https://poser.pugx.org/meraki/route/v/stable
[license-url]: https://raw.githubusercontent.com/merakiframework/route/master/LICENSE.md
[license-image]: https://img.shields.io/badge/license-MIT-blue.svg
