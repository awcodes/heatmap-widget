<?php

namespace Awcodes\HeatmapWidget\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Awcodes\HeatmapWidget\HeatmapWidget
 */
class HeatmapWidget extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Awcodes\HeatmapWidget\HeatmapWidget::class;
    }
}
