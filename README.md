# Heatmap Widget for Filament

[![Latest Version on Packagist](https://img.shields.io/packagist/v/awcodes/heatmap-widget.svg?style=flat-square)](https://packagist.org/packages/awcodes/heatmap-widget)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/awcodes/heatmap-widget/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/awcodes/heatmap-widget/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/awcodes/heatmap-widget/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/awcodes/heatmap-widget/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/awcodes/heatmap-widget.svg?style=flat-square)](https://packagist.org/packages/awcodes/heatmap-widget)



A GitHub-style contribution heatmap widget for Filament. Render a year of daily activity
as a grid of color-graded cells — downloads, orders, sign-ups, commits, anything you can
express as a count per day. Supply your own data by overriding a single method; the widget
handles the grid, intensity buckets, month/day labels, tooltips, dark mode, and responsive
sizing for you.

## Installation

You can install the package via composer:

```bash
composer require awcodes/heatmap-widget
```

> [!IMPORTANT]
> If you have not set up a custom theme and are using Filament Panels follow the instructions in the [Filament Docs](https://filamentphp.com/docs/4.x/styling/overview#creating-a-custom-theme) first.

After setting up a custom theme add the plugin's views to your theme css file or your app's css file if using the standalone packages.

```css
@source '../../../../vendor/awcodes/heatmap-widget/resources/**/*.blade.php';
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="heatmap-widget-views"
```

## Usage

Create a widget that extends `HeatmapWidget` and return your values from `getData()`
as a map of dates (`Y-m-d`) to counts. Any date you omit renders as an empty cell, so
you only need to return the days that have a value.

```php
use App\Models\Order;
use Awcodes\HeatmapWidget\HeatmapWidget;

class OrdersHeatmap extends HeatmapWidget
{
    protected ?string $heading = 'Orders';

    public string $color = 'success';

    public int $weeks = 52;

    public int $weekStartsOn = 1; // 0 = Sunday, 1 = Monday

    public string $unit = 'orders';

    public function getData(): array
    {
        return Order::query()
            ->selectRaw('DATE(created_at) as date, count(*) as total')
            ->where('created_at', '>=', now()->subWeeks($this->weeks))
            ->groupBy('date')
            ->pluck('total', 'date')
            ->all();
    }
}
```

Then register it like any other Filament widget — on a dashboard, page, or resource:

```php
protected function getHeaderWidgets(): array
{
    return [
        OrdersHeatmap::class,
    ];
}
```

### Configuration

| Property | Default | Description |
| --- | --- | --- |
| `$heading` | `'Activity'` | Section heading shown above the heatmap. |
| `$color` | `'primary'` | Any Filament color used for populated cells. |
| `$weeks` | `52` | Number of weeks of history to render. |
| `$weekStartsOn` | `0` | First day of the week (`0` = Sunday … `6` = Saturday). |
| `$unit` | `''` | Unit shown in a cell's tooltip, e.g. `"5 orders on Jan 1, 2026"`. |

## Testing

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [Adam Weston](https://github.com/awcodes)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
