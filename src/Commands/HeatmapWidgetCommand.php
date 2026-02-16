<?php

namespace Awcodes\HeatmapWidget\Commands;

use Illuminate\Console\Command;

class HeatmapWidgetCommand extends Command
{
    public $signature = 'heatmap-widget';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
