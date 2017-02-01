# Bowler

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

## Install

Via Composer

``` bash
$ composer require ediasep/bowler
```

Then add this line into providers array in `config/app.php` file.

``` php
Ediasep\Bowler\BowlerServiceProvider::class,
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Usage

Create migration from a table

``` bash
$ php artisan bowler:migration laravel user
```

Create migration from a table along with seeder

``` bash
$ php artisan bowler:migration laravel user --withseeder
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Credits

- [Asep Edi Kurniawan][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/ediasep/bowler.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/ediasep/bowler/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/ediasep/bowler.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/ediasep/bowler.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/ediasep/bowler.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/ediasep/bowler
[link-travis]: https://travis-ci.org/ediasep/bowler
[link-scrutinizer]: https://scrutinizer-ci.com/g/ediasep/bowler/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/ediasep/bowler
[link-downloads]: https://packagist.org/packages/ediasep/bowler
[link-author]: https://github.com/ediasep
[link-contributors]: ../../contributors
