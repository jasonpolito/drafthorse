<?php

namespace App\Filament\Resources\TaxonomyResource\Pages;

use App\Filament\Resources\TaxonomyResource;
use App\Http\Traits\HasSystemActions;
use App\Models\Taxonomy;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Pages\Actions;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Log;
use Konnco\FilamentImport\Actions\ImportAction;
use Konnco\FilamentImport\Actions\ImportField;

class ListTaxonomies extends ListRecords
{
    use HasSystemActions;

    protected static string $resource = TaxonomyResource::class;

    protected function getActions(): array
    {
        return array_merge([
            Actions\CreateAction::make(),
        ], self::recordsListActions('Taxonomy'));
    }
}
