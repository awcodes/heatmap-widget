<?php

declare(strict_types=1);

namespace Awcodes\HeatmapWidget;

use Filament\Widgets\Widget;

class HeatmapWidget extends Widget
{
    /**
     * The Filament color used for populated cells (e.g. 'primary', 'success').
     */
    public string $color = 'primary';

    /**
     * The day the week starts on. 0 = Sunday, 1 = Monday, ... 6 = Saturday.
     */
    public int $weekStartsOn = 0;

    /**
     * How many weeks of history to render.
     */
    public int $weeks = 52;

    /**
     * Unit shown in a cell's tooltip, e.g. 'downloads' => "5 downloads on Jan 1, 2026".
     * Leave empty for just "5 on Jan 1, 2026".
     */
    public string $unit = '';

    protected ?string $heading = 'Activity';

    protected string $view = 'heatmap-widget::widget';

    protected int | string | array $columnSpan = 'full';

    /**
     * Supply the heatmap's values as a map of dates to counts: ['Y-m-d' => int].
     *
     * Override this in your own widget and return the data from any source
     * (an Eloquent query, an API, a cache, etc.). Missing dates render as empty
     * cells, so you only need to return the days that have a value.
     *
     * @return array<string, int>
     */
    public function getData(): array
    {
        return [];
    }

    /**
     * Livewire-callable payload consumed by the Alpine grid. Wraps getData()
     * and computes the max so the view can bucket cells into intensity levels.
     *
     * @return array{data: array<string, int>, max: int}
     */
    public function getHeatmapData(): array
    {
        $data = $this->getData();
        $max = $data !== [] ? max($data) : 0;

        return [
            'data' => $data,
            'max' => $max,
        ];
    }

    /**
     * Weekday labels ordered to match $weekStartsOn. Only Mon/Wed/Fri are shown
     * (the rest are blank) to keep the axis readable, mirroring GitHub.
     *
     * @return array<int, string>
     */
    public function getDayLabels(): array
    {
        $labels = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        $startIndex = $this->weekStartsOn % 7;
        $reordered = [];

        for ($i = 0; $i < 7; $i++) {
            $index = ($startIndex + $i) % 7;
            // Show only Mon, Wed, Fri labels for readability
            $reordered[] = match ($labels[$index]) {
                'Mon', 'Wed', 'Fri' => $labels[$index],
                default => '',
            };
        }

        return $reordered;
    }

    public function getHeading(): ?string
    {
        return $this->heading;
    }
}
