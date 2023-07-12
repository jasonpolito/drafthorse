<?php

namespace App\Http\Traits;

use Filament\Tables\Actions\BulkAction;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait SystemActionsTrait
{
    public static function bulkActions($type)
    {
        return [
            self::exportRecordsAsJson($type)
        ];
    }

    public static function exportRecordsAsJson($type)
    {
        return BulkAction::make('export')
            ->color('secondary')
            ->label(__('Export selected as JSON'))
            ->icon('heroicon-s-download')
            ->action(function (Collection $records) use ($type) {
                $archive = new \ZipArchive;
                $fileName = Str::lower(Str::plural($type)) . '_export.zip';
                $archive->open($fileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
                foreach ($records as $record) {
                    $name = Str::slug($record->name, '_') . '.json';
                    $return = $record->attributesToArray();
                    $return['type'] = $type;
                    $content = json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
                    $archive->addFromString($name, $content);
                }
                $archive->close();
                return response()->download($fileName);
            });
    }
}
