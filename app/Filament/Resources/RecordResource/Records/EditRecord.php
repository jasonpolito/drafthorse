<?php

namespace App\Filament\Resources\RecordResource\Pages;

use App\Filament\Resources\RecordResource;
use App\Models\Record;
use Closure;
use Filament\Pages\Actions\Action;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord as EditRecordClass;

class EditRecord extends EditRecordClass
{
    protected static string $resource = RecordResource::class;

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    protected function getActions(): array
    {
        return [
            Actions\ActionGroup::make([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ]),
        ];
    }

    protected function getFormActions(): array
    {
        return array_merge(parent::getFormActions(), []);
    }

    protected function afterSave(): void
    {
    }
}
