<?php

namespace App\Filament\Resources\BlockResource\Pages;

use App\Filament\Resources\BlockResource;
use App\Http\Traits\HasSystemActions;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBlocks extends ListRecords
{
    use HasSystemActions;

    protected static string $resource = BlockResource::class;

    protected function getActions(): array
    {
        return array_merge([
            Actions\CreateAction::make(),
        ], self::recordsListActions('Block'));
    }
}
