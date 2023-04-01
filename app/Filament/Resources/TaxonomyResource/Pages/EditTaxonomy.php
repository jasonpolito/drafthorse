<?php

namespace App\Filament\Resources\TaxonomyResource\Pages;

use App\Filament\Resources\TaxonomyResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTaxonomy extends EditRecord
{
    protected static string $resource = TaxonomyResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
