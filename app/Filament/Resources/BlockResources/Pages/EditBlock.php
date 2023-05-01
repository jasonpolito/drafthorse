<?php

namespace App\Filament\Resources\BlockResource\Pages;

use App\Filament\Resources\BlockResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBlock extends EditRecord
{
    protected static string $resource = BlockResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
