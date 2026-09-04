---
title: Configuration
description: Properties available on a heatmap widget to control its heading, color, range, week alignment, and tooltip unit.
---

# Configuration

The widget is configured by overriding properties on your own subclass. There is no
config file to publish — every option is a property, so different widgets in the same
application can be configured independently.

## Available properties

| Property | Default | Description |
| --- | --- | --- |
| `$heading` | `'Activity'` | Section heading shown above the heatmap. |
| `$color` | `'primary'` | Any Filament color used for populated cells. |
| `$weeks` | `52` | Number of weeks of history to render. |
| `$weekStartsOn` | `0` | First day of the week (`0` = Sunday … `6` = Saturday). |
| `$unit` | `''` | Unit shown in a cell's tooltip, e.g. `"5 orders on Jan 1, 2026"`. |

`$heading` is a `protected` property; the rest are `public`.

```php
use Awcodes\HeatmapWidget\HeatmapWidget;

class SignupsHeatmap extends HeatmapWidget
{
    protected ?string $heading = 'Sign-ups';

    public string $color = 'info';

    public int $weeks = 26;

    public int $weekStartsOn = 1;

    public string $unit = 'sign-ups';

    public function getData(): array
    {
        // ...
    }
}
```

## Notes on individual options

**`$color`** accepts any color registered with Filament, so it follows your panel's
palette rather than introducing its own. The widget picks appropriate shades for light
and dark mode automatically and switches between them when the theme changes.

**`$weeks`** controls the width of the grid. Because cell size is calculated from the
available container width divided by the number of weeks, a shorter range produces larger
cells rather than a narrower graph.

**`$weekStartsOn`** rotates the rows so the first row of the grid is the day you choose.
The weekday axis only labels Monday, Wednesday, and Friday, which keeps it readable at
small cell sizes.

**`$unit`** is appended to the count in a cell's tooltip. Left empty, the tooltip reads
`5 on Jan 1, 2026`; set to `orders`, it reads `5 orders on Jan 1, 2026`.

If you need to change something these properties do not cover, publish the views as
described in [Installation](installation.md) and edit the markup directly.
