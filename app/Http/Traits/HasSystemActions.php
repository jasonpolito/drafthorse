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
                        $tmp =  storage_path() . '/app/public/temp/' . uniqid();
                        $archive->extractTo($tmp);
                        $archive->close();
                        $records = array_diff(scandir($tmp), ['..', '.']);
                        if (count($records)) {
                            foreach ($records as $recordFile) {
                                $filePath = "$tmp/$recordFile";
                                $importData = json_decode(file_get_contents($filePath));
                                $count = $recordClass::withTrashed()->where('name', $importData->name)->count();
                                $name = $importData->name;
                                $newName = $count ? "$name ($count)" : $name;
                                $newSlug = $count ? "$importData->slug-$count" : $importData->slug;
                                $recordClass::create([
                                    'name' => $newName,
                                    'type' => $importData->type,
                                    'taxonomy_id' => $importData->taxonomy_id,
                                    'slug' => $newSlug,
                                    'uuid' => Str::orderedUuid(),
                                    'icon' => $importData->icon ?? null,
                                    'data' => $importData->data ?? []
                                ]);
                                unlink($filePath);
                            }
                        }
                        rmdir($tmp);
                    } else {
                        Log::info($archive->open($path) === TRUE);
                    }
                    // $importData = json_decode(file_get_contents(storage_path() . '/app/public/' . $data['json']));
                    // $exists = $recordClass::withTrashed()->where('name', $importData->name);
                    // $name = $importData->name;
                    // $newName = $exists ? $name . ' (' . $recordClass::all()->count() . ')' : $name;
                    // $recordClass::create([
                    //     'name' => $newName,
                    //     'icon' => $importData->icon,
                    //     'fields' => $importData->fields
                    // ]);
                })
                ->form([
                    FileUpload::make('import_archive')
                        ->label("records_export.zip")
                        ->required()
                ])
        ]);
    }
}
