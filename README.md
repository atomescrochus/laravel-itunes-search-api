# laravel-itunes-store-api

Here is to have a simple way to interact with the iTunes Store API from a Laravel >= 5.3 app.

**Not suitable for production yet.**

## Install

You can install this package via composer:

``` bash
$ composer require atomescrochus/laravel-itunes-search-api
```

Then you have to install the package' service provider and alias:

```php
// config/app.php
'providers' => [
    ...
    Atomescrochus\ItunesStore\ItunesSearchAPIProvider::class,
];
```

You will have to publish the configuration files also if you want to change the default value:
```bash
php artisan vendor:publish --provider="Atomescrochus\ItunesStore\ItunesSearchAPIProvider" --tag="config"
```

## Usage

Soon.

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

Soon.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email jp@atomescroch.us instead of using the issue tracker.

## Credits

- [Jean-Philippe Murray][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.