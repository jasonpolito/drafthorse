<?php

namespace App\Filament\Resources\LayoutResource\Pages;

use App\Filament\Resources\LayoutResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLayout extends EditRecord
{
    protected static string $resource = LayoutResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
