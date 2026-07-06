<?php

declare(strict_types=1);

use Awcodes\HeatmapWidget\HeatmapWidget;

use function Pest\Livewire\livewire;

/**
 * A concrete widget supplying data, standing in for a consumer's subclass.
 */
class TestHeatmapWidget extends HeatmapWidget
{
    /** @var array<string, int> */
    public array $rows = [
        '2026-01-01' => 2,
        '2026-01-02' => 5,
        '2026-01-03' => 9,
    ];

    public function getData(): array
    {
        return $this->rows;
    }
}

it('wraps getData() and computes the max', function () {
    $widget = new TestHeatmapWidget;
    $widget->rows = ['2026-01-01' => 3, '2026-01-02' => 7, '2026-01-03' => 5];

    expect($widget->getHeatmapData())->toBe([
        'data' => ['2026-01-01' => 3, '2026-01-02' => 7, '2026-01-03' => 5],
        'max' => 7,
    ]);
});

it('reports a zero max when there is no data', function () {
    expect((new HeatmapWidget)->getHeatmapData())->toBe([
        'data' => [],
        'max' => 0,
    ]);
});

it('orders day labels for a Sunday week start', function () {
    $widget = new HeatmapWidget;
    $widget->weekStartsOn = 0;

    expect($widget->getDayLabels())->toBe(['', 'Mon', '', 'Wed', '', 'Fri', '']);
});

it('orders day labels for a Monday week start', function () {
    $widget = new HeatmapWidget;
    $widget->weekStartsOn = 1;

    expect($widget->getDayLabels())->toBe(['Mon', '', 'Wed', '', 'Fri', '', '']);
});

it('renders the heatmap chrome', function () {
    livewire(TestHeatmapWidget::class)
        ->assertOk()
        ->assertSee('Activity')
        ->assertSee('Less')
        ->assertSee('More');
});

it('exposes heatmap data over the wire', function () {
    livewire(TestHeatmapWidget::class)
        ->call('getHeatmapData')
        ->assertOk();
});
