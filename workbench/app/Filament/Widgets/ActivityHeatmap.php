<?php

declare(strict_types=1);

namespace Workbench\App\Filament\Widgets;

use Awcodes\HeatmapWidget\HeatmapWidget;

class ActivityHeatmap extends HeatmapWidget
{
    public string $color = 'success';

    public int $weeks = 26;

    public int $weekStartsOn = 1;

    public string $unit = 'orders';

    protected static bool $isLazy = false;

    protected ?string $heading = 'Orders';

    public function getData(): array
    {
        return [
            now()->subDays(2)->format('Y-m-d') => 3,
            now()->subDays(5)->format('Y-m-d') => 8,
            now()->subDays(9)->format('Y-m-d') => 15,
            now()->subDays(14)->format('Y-m-d') => 24,
        ];
    }
}
