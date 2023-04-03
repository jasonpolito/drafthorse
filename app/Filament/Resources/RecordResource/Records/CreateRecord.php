<?php

namespace App\Filament\Resources\RecordResource\Pages;

use App\Filament\Resources\RecordResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord as CreateRecordClass;

class CreateRecord extends CreateRecordClass
{
    protected static string $resource = RecordResource::class;
}
