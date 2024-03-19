# laravel-stats

[![Latest Version on Packagist](https://img.shields.io/packagist/v/toneflix-code/laravel-stats.svg?style=flat-square)](https://packagist.org/packages/toneflix-code/laravel-stats)
[![Total Downloads](https://img.shields.io/packagist/dt/toneflix-code/laravel-stats.svg?style=flat-square)](https://packagist.org/packages/toneflix-code/laravel-stats)
![GitHub Actions](https://github.com/toneflix/laravel-stats/actions/workflows/main.yml/badge.svg)

A simple Laravel package to help you quickly get usage metrics and data from your app.

## Installation

You can install the package via composer:

```bash
composer require toneflix-code/laravel-stats
```

## Usage

```php
use ToneflixCode\Stats\Ranger;
use ToneflixCode\Stats\Stats;

$stats = (new Stats())
    ->registerMetric(
        modelClass: \App\Models\User::class,
        metric: Metric::COUNT,
        period: Ranger::years('created_at', 'M Y')->fromDate(now()->subYears(1))->toDate(now()->subYears(1)->addYear())->range('1 month'),
        aggregateField: 'id',
        label: 'old_users'
    )
    ->registerMetric(
        modelClass: \App\Models\User::class,
        period: ['from' => now()->subYears(1), 'to' => now()],
        metric: Metric::COUNT,
        aggregateField: 'id',
    )
    ->registerMetric(
        modelClass: new \App\Models\User(),
        period: ['from' => now()->subYears(1), 'to' => now()],
        metric: Metric::COUNT,
        callback: fn ($query) => $query->find(1)->posts(),
        aggregateField: 'id',
    )
    ->build()
```

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email legacy@toneflix.com.ng instead of using the issue tracker.

## Credits

- [Legacy](https://github.com/3m1n3nc3)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
