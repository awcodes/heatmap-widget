<?php

declare(strict_types=1);

namespace Awcodes\HeatmapWidget;

use Awcodes\HeatmapWidget\Testing\TestsHeatmapWidget;
use Filament\Support\Assets\Asset;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Livewire\Features\SupportTesting\Testable;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class HeatmapWidgetServiceProvider extends PackageServiceProvider
{
    public static string $name = 'heatmap-widget';

    public static string $viewNamespace = 'heatmap-widget';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->askToStarRepoOnGitHub('awcodes/heatmap-widget');
            });

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void {}

    public function packageBooted(): void
    {
        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        // Icon Registration
        FilamentIcon::register($this->getIcons());

        // Testing
        Testable::mixin(new TestsHeatmapWidget);
    }

    protected function getAssetPackageName(): ?string
    {
        return 'awcodes/heatmap-widget';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }
}
