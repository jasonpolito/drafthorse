<?php

namespace App\Filament\Resources\RecordResource\Pages;

use App\Filament\Resources\RecordResource;
use App\Models\Taxonomy;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords as ListRecordsClass;
use Illuminate\Support\Facades\URL;

class ListRecords extends ListRecordsClass
{
    protected static string $resource = RecordResource::class;

    protected function getActions(): array
    {
        $action = Actions\CreateAction::make();

        return [
            $action
        ];
    }
}
