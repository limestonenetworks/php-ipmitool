# php-ipmitool

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]
[![CI](https://github.com/limestonenetworks/php-ipmitool/actions/workflows/ci.yml/badge.svg)](https://github.com/limestonenetworks/php-ipmitool/actions/workflows/ci.yml)
[![codecov](https://codecov.io/github/limestonenetworks/php-ipmitool/branch/master/graph/badge.svg?token=3J2HHKQ22E)](https://codecov.io/github/limestonenetworks/php-ipmitool)

This is a php wrapper for [ipmitool](https://github.com/ipmitool/ipmitool). The goal is allow easy bootstrapping and integration of ipmitool into an existing application.

## Install

Via Composer

``` bash
$ composer require limestonenetworks/php-ipmitool
```

## Usage

``` php
$confArray = [
    'interface' => 'lanplus',     
    'username' => 'ADMIN',     
    'password',     
    'hostname',     
    'port',         
    'password_env',  
    'password_file',
    'authtype',   
    'level',
    'binary' => 'ipmitool'
];
$config = new LSN\ipmitool\Config($confArray);
$client = new LSN\ipmitool\Client(new Process(''),$config);
echo $client->run(['chassis','power','status');
```
Config options are shown with their default values. Empty means that if the option is not set then it will not be sent to the process. Each option maps to a cli flag on ipmitool aside from binary which allows you to override the name of your ipmitool binary.

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email sch43228@gmail.com instead of using the issue tracker.

## Credits

- [Trent Schmidt][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/limestonenetworks/php-ipmitool.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/limestonenetworks/php-ipmitool.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/limestonenetworks/php-ipmitool
[link-downloads]: https://packagist.org/packages/limestonenetworks/php-ipmitool
[link-author]: https://github.com/limestonenetworks
[link-contributors]: ../../contributors
