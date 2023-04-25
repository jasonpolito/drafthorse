<?php

namespace App\Filament\Resources\LayoutResource\Pages;

use App\Filament\Resources\LayoutResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLayouts extends ListRecords
{
    protected static string $resource = LayoutResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
