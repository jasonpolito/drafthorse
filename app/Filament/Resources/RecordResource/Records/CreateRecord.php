<?php

namespace App\Filament\Resources\RecordResource\Pages;

use App\Filament\Resources\RecordResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord as CreateRecordClass;
use Illuminate\Support\Str;

class CreateRecord extends CreateRecordClass
{
    protected static string $resource = RecordResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['uuid'] = Str::uuid();

        return $data;
    }
}
