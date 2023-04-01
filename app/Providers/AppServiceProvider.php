<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        Filament::serving(function () {
            Filament::registerNavigationGroups([
                NavigationGroup::make()
                    ->label('Content')
                    ->icon('heroicon-o-document-text'),
                NavigationGroup::make()
                    ->label('Advanced')
                    ->icon('heroicon-o-beaker'),
                // NavigationGroup::make()
                //     ->label('Settings')
                //     ->icon('heroicon-o-cog')
                //     ->collapsed(),
            ]);
            Filament::registerViteTheme('resources/css/filament.css');
        });
    }
}
