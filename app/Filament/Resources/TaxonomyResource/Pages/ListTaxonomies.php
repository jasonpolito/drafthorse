<?php

namespace App\Filament\Resources\TaxonomyResource\Pages;

use App\Filament\Resources\TaxonomyResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTaxonomies extends ListRecords
{
    protected static string $resource = TaxonomyResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
