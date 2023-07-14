<?php

namespace App\Http\Traits;

use App\Models\Taxonomy;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Filament\Pages\Actions\Action;
use Filament\Pages\Actions\ActionGroup;
use Illuminate\Support\Facades\Log;

trait HasSystemActions
{

    public static function recordsListActions($type): array
    {
        return [
            self::importRecordsFromZip($type)
        ];
    }

    public static function bulkActions($type): array
    {
        return [
            self::exportRecordsAsJson($type)
        ];
    }

    public static function exportRecordsAsJson($type)
    {
        return BulkAction::make('export')
            ->color('secondary')
            ->label('Export selected records')
            ->icon('heroicon-s-download')
            ->action(function (Collection $records) use ($type) {
                $archive = new \ZipArchive;
                $fileName = Str::lower(Str::plural($type)) . '_export.zip';
                $archive->open($fileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
                foreach ($records as $record) {
                    $name = Str::slug($record->name, '_') . '_' . $record->uuid . '.json';
                    $return = $record->attributesToArray();
                    $return['type'] = $type;
                    $content = json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
                    $archive->addFromString($name, $content);
                }
                $archive->close();
                return response()->download($fileName);
            });
    }

    public static function importRecordsFromZip($type)
    {
        $recordClass = "App\\Models\\$type";
        $plural = Str::lower(Str::plural($type));

        return ActionGroup::make([
            Action::make('import')
                ->label("Import $plural")
                ->icon('heroicon-o-upload')
                ->action(function (array $data) use ($recordClass): void {
                    $archive = new \ZipArchive;
                    $public =  storage_path() . '/app/public/';
                    $path = $public . $data['import_archive'];
                    if ($archive->open($path) === TRUE) {
                        $tmp =  storage_path() . '/app/public/.tmp/' . uniqid();
                        $archive->extractTo($tmp);
                        $archive->close();
                        $records = array_diff(scandir($tmp), ['..', '.']);
                        if (count($records)) {
                            foreach ($records as $recordFile) {
                                $filePath = "$tmp/$recordFile";
                                if (!is_dir($filePath)) {
                                    $importData = json_decode(file_get_contents($filePath));
                                    $exists = $recordClass::withTrashed()->where('uuid', $importData->uuid)->exists();
                                    $uuid = $exists ? (string) Str::orderedUuid() : $importData->uuid;
                                    $newRecord = [
                                        'name' => $importData->name,
                                        'type' => $importData->type,
                                        'uuid' => $uuid,
                                        'data' => $importData->data ?? [],
                                        'fields' => $importData->fields ?? []
                                    ];
                                    Log::info($newRecord);
                                    if ($importData->type == 'Record') {
                                        $newSlug = $exists ? "$importData->slug-$uuid" : $importData->slug;
                                        $newRecord['taxonomy_id'] = $importData->taxonomy_id;
                                        $newRecord['slug'] = $newSlug;
                                        $newRecord['icon'] = $importData->icon ?? null;
                                    }
                                    $recordClass::create($newRecord);
                                    unlink($filePath);
                                } else {
                                    rmdir($filePath);
                                }
                            }
                        }
                        rmdir($tmp);
                    }
                    unlink($path);
                })
                ->form([
                    FileUpload::make('import_archive')
                        ->acceptedFileTypes(['application/zip'])
                        ->label("records_export.zip")
                        ->required()
                ])
        ]);
    }
}
