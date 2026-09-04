---
title: Usage
description: Create a heatmap widget by extending HeatmapWidget and supplying daily counts, then register it on a Filament page.
---

# Usage

## Creating a widget

Create a class that extends `HeatmapWidget` and return your values from `getData()` as a
map of dates in `Y-m-d` format to counts. Any date you omit renders as an empty cell, so
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

The example above reads from an Eloquent model, but `getData()` is not tied to the
database. It can return values from an API response, a cache entry, a report table, or a
hardcoded array — anything that produces an `array<string, int>`.

Note that the query is bounded by `$this->weeks`, the same property that controls how
much history the grid renders. Reading the property rather than hardcoding a number
keeps the query and the grid in step if you change the range later.

## Registering the widget

Register it like any other Filament widget — on a dashboard, a page, or a resource:

```php
protected function getHeaderWidgets(): array
{
    return [
        OrdersHeatmap::class,
    ];
}
```

The widget spans the full width of its grid by default, which suits the shape of a
year-long heatmap.

## How values become colors

The widget does not use fixed thresholds. When the data loads it takes the largest count
in the set and divides that range into quarters, so cells are shaded relative to your own
busiest day rather than to an absolute scale. A day with no value renders as an empty
cell, and days above each quarter boundary step up through progressively stronger shades
of the configured color.

This means the grid stays readable whether your daily counts are in the single digits or
the thousands, but it also means intensity is not comparable between two heatmaps that
show different data.

Hovering a cell shows a tooltip with the count, the [unit](configuration.md) if you set
one, and the date — for example, `5 orders on Jan 1, 2026`.

See [Configuration](configuration.md) for the properties that control the heading, color,
range, and week alignment.
