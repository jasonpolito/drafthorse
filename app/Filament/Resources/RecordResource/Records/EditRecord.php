<?php

namespace App\Filament\Resources\RecordResource\Pages;

use App\Filament\Resources\RecordResource;
use App\Models\Page;
use Closure;
use Filament\Pages\Actions\Action;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord as EditRecordClass;

class EditRecord extends EditRecordClass
{
    protected static string $resource = RecordResource::class;

    protected function getActions(): array
    {
        return [
            Action::make('save')
                // ->disabled(fn (Page $page): bool => !$page->isDirty())
                ->action('save')
                ->color('primary')
                ->label('Save changes'),
        ];
    }



    protected function getFormActions(): array
    {
        return array_merge(parent::getFormActions(), [
            Action::make('delete')->action('delete'),
        ]);
    }
}
