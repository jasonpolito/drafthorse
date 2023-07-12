<?php

namespace App\Filament\Resources\RecordResource\Pages;

use App\Filament\Resources\RecordResource;
use App\Http\Traits\HasSystemActions;
use App\Models\Taxonomy;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords as ListRecordsClass;
use Illuminate\Support\Facades\URL;

class ListRecords extends ListRecordsClass
{
    use HasSystemActions;

    protected static string $resource = RecordResource::class;

    protected function getActions(): array
    {
        $createAction = Actions\CreateAction::make();
        return array_merge([
            $createAction,
        ], self::recordsListActions('Record'));
    }
}
