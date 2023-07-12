<?php

namespace App\Filament\Resources\TaxonomyResource\Pages;

use App\Filament\Resources\TaxonomyResource;
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
    protected static string $resource = TaxonomyResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\ActionGroup::make([
                Action::make('import')
                    ->label('Import taxonomy')
                    ->icon('heroicon-o-upload')
                    ->action(function (array $data): void {
                        $importData = json_decode(file_get_contents(storage_path() . '/app/public/' . $data['json']));
                        $exists = Taxonomy::withTrashed()->where('name', $importData->name);
                        $name = $importData->name;
                        $newName = $exists ? $name . ' (' . Taxonomy::all()->count() . ')' : $name;
                        Taxonomy::create([
                            'name' => $newName,
                            'icon' => $importData->icon,
                            'fields' => $importData->fields
                        ]);
                    })
                    ->form([
                        FileUpload::make('json')
                            ->label('Taxonomy JSON file')
                            ->required()
                    ])
            ])
        ];
    }
}
