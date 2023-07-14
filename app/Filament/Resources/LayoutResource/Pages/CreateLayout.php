<?php

namespace App\Filament\Resources\LayoutResource\Pages;

use App\Filament\Resources\LayoutResource;
use Filament\Pages\Actions;
use Illuminate\Support\Str;
use Filament\Resources\Pages\CreateRecord;

class CreateLayout extends CreateRecord
{
    protected static string $resource = LayoutResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['uuid'] = Str::uuid();

        return $data;
    }
}
