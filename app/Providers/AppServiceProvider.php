<?php

namespace App\Providers;

use App\Models\Page;
use App\Models\Taxonomy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;

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
        $ts = Schema::hasTable('taxonomies') ? Taxonomy::all() : [];
        $menu = [];
        $link = NavigationItem::make('All')
            ->url("/admin/pages")
            ->isActiveWhen(fn (): bool => URL::full() == env('APP_URL') . '/admin/pages')
            ->icon('heroicon-o-collection')
            ->activeIcon('heroicon-s-collection')
            ->group('Records');
        array_push($menu, $link);
        foreach ($ts as $item) {
            $url = "/admin/pages?tableFilters[taxonomy][values][0]=$item->id";
            $link = NavigationItem::make(Str::plural($item->name))
                ->url($url)
                ->isActiveWhen(function () use ($url, $item) {
                    $record = Page::find(Request::route()->parameter('record'));
                    if ($record) {
                        return $record->taxonomy->id == $item->id;
                    } else {
                        return Str::contains(urldecode(URL::full()), $url);
                    }
                })
                ->icon($item->icon ?? 'heroicon-o-presentation-chart-line')
                ->activeIcon($item->icon ? Str::replace('-o-', '-s-', $item->icon) : 'heroicon-s-presentation-chart-line')
                ->group('Records');
            array_push($menu, $link);
        }

        Filament::serving(function () use ($menu) {
            Filament::registerNavigationGroups([
                NavigationGroup::make()
                    ->label('Records')
                    ->icon('heroicon-o-collection'),
                NavigationGroup::make()
                    ->label('Advanced')
                    ->icon('heroicon-o-beaker'),
            ]);

            Filament::registerNavigationItems($menu);
            Filament::registerViteTheme('resources/css/filament.css');
        });
    }
}
