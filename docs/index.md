---
title: Heatmap Widget
description: A GitHub-style contribution heatmap widget for Filament that renders daily counts as a grid of color-graded cells.
---

# Heatmap Widget

Heatmap Widget renders a year of daily activity as a grid of color-graded cells, in the
style of a GitHub contribution graph. Anything you can express as a count per day works:
downloads, orders, sign-ups, commits, support tickets.

You supply the data by overriding a single method. The widget handles everything else —
the grid layout, the intensity buckets, the month and weekday labels, the per-cell
tooltips, dark mode, and responsive cell sizing.

## Who this is for

This package is for Filament applications that need to show activity over time at a
glance, rather than as a line chart or a table. It is a Filament widget, so it can be
placed anywhere Filament widgets are supported: a dashboard, a custom page, or a
resource page.

## Requirements

- PHP 8.2 or higher
- Filament v4 or v5

## How it works

You create a widget class that extends `HeatmapWidget` and return a map of dates to
counts from `getData()`. The widget requests that data over Livewire when it mounts,
finds the largest value in the set, and buckets every day into one of four intensity
levels based on how it compares to that maximum. Days you omit from the array render as
empty cells, so you only need to return the days that actually have a value.

## Next steps

- [Installation](installation.md) — install the package and register its views with your theme.
- [Usage](usage.md) — create your first heatmap widget and register it.
- [Configuration](configuration.md) — the properties that control the widget's appearance and range.
