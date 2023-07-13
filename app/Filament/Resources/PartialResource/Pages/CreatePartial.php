<?php

namespace App\Filament\Resources\PartialResource\Pages;

use App\Filament\Resources\PartialResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreatePartial extends CreateRecord
{
    protected static string $resource = PartialResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['uuid'] = Str::uuid();

        return $data;
    }
}
