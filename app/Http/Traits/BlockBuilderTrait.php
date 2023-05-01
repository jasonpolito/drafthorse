<?php

namespace App\Http\Traits;

use App\Models\Record;
use App\Models\Taxonomy;
use App\Models\Block;
use Closure;
use Filament\Forms\Components\Builder as BlockBuilder;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Str;
use FilamentTiptapEditor\TiptapEditor;
use Creagia\FilamentCodeField\CodeField;
use AskerAkbar\GptTrixEditor\Components\GptTrixEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ViewField;
use Illuminate\Support\Facades\Request;

trait BlockBuilderTrait
{
    const FIELD_TYPES = [
        'Filament\Forms\Components\TextInput' => 'text',
        'Filament\Forms\Components\Textarea' => 'text',
        'Filament\Forms\Components\Builder' => 'text',
        'blocks' => 'blocks',
        'FilamentTiptapEditor\TiptapEditor' => 'rich_content',
        'Filament\Forms\Components\Toggle' => 'boolean',
        'Filament\Forms\Components\Select' => 'relation',
        'Filament\Forms\Components\FileUpload' => 'files',
        'Filament\Forms\Components\SpatieMediaLibraryFileUpload' => 'files',
        'Awcodes\Curator\Components\Forms\CuratorPicker' => 'files',
        'Filament\Forms\Components\ColorPicker' => 'text',
        'Creagia\FilamentCodeField\CodeField' => 'raw',
    ];

    public static function getTaxonomyFields($page = null)
    {
        return Grid::make()
            ->schema(fn (Closure $get) => self::getTaxonomyFieldsByType($get('taxonomy_id')));
    }

    public static function getBlockFields($parent): array
    {
        $fields = [];
        $blocks = Block::orderBy('id', 'desc')->get();
        foreach ($blocks as $block) {
            $blockFields = $block->fields;
            if (is_array($blockFields)) {
                foreach ($blockFields as $blockField) {
                    $type = $blockField['type'];
                    $name = Str::snake($blockField['name']);
                    $component = $type::make("data.$name.value");
                    $component->hidden(fn (Closure $get) => $get($parent) != $block->id);
                    $component->label($blockField['name'])
                        ->columnSpan(2)
                        ->reactive();
                    if (Str::contains($type, 'repeater', true)) {
                        $schema = [];
                        $repeater = Repeater::make("data.$name.value")
                            ->columnSpan(2)
                            ->label($blockField['name'])
                            ->cloneable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => ($state['type'] ?? $state['title'] ?? null))
                            ->orderable()
                            ->schema([$component]);
                        foreach ($blockField['fields'] as $repeaterField) {
                            $type = $repeaterField['type'];
                            $name = Str::snake($repeaterField['name']);
                            $repeaterComponent = $type::make("$name");
                            $repeaterComponent->label($repeaterField['name'])
                                ->reactive();
                            array_push($schema, $repeaterComponent);
                        }
                        // dd($schema);
                        $repeater->schema($schema);
                    }
                    array_push($fields, $repeater ?? $component);
                }
            }
        }
        // dd($fields);
        return $fields;
    }

    public static function isFullWidth($type)
    {
        return in_array($type, [
            'FilamentTiptapEditor\TiptapEditor',
            'blocks',
            'Filament\Forms\Components\Builder',
            'Creagia\FilamentCodeField\CodeField'
        ]);
    }

    public static function makeHiddenFields($field)
    {
        $result = [];
        $name = Str::snake($field['name']);
        $typeName = self::FIELD_TYPES[$field['type']];
        $hidden = Hidden::make("data.$name.type")
            ->afterStateHydrated(function (Hidden $component, $state) use ($typeName) {
                $component->state($typeName);
            });
        array_push($result, $hidden);
        return $result;
    }

    public static function getTaxonomyFieldsByType($taxonomy_id)
    {
        $taxonomy = Taxonomy::find($taxonomy_id);
        // dd($taxonomy);
        $fields = [];
        if ($taxonomy) {
            foreach ($taxonomy->fields as $field) {
                $type = $field['type'];
                $name = Str::snake($field['name']);
                if ($field['type'] == 'blocks') {
                    $component = Repeater::make("data.$name.value")
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['name'] ?? $state['data']['title']['value'] ?? null)
                        ->orderable()
                        ->schema(array_merge(
                            [
                                Select::make('block')
                                    ->reactive()
                                    ->columnSpan(2)
                                    ->options(function () {
                                        return Block::all()->pluck('name', 'id');
                                    }),

                            ],
                            self::getBlockFields("block")
                        ));
                } else {
                    $component = $type::make("data.$name.value")
                        ->columnSpan(2);
                }
                $component->label($field['name']);
                if (method_exists($type, 'placeholder')) {
                    $component->placeholder($field['name']);
                }
                if (Str::contains($type, 'ColorPicker')) {
                    $component->rgba();
                }
                if (Str::contains($type, 'Select')) {
                    $component
                        ->searchable()
                        ->multiple();
                    $component->options(function () use ($field) {
                        $ids = $field['relations'];
                        return Record::whereIn('taxonomy_id', $ids)->get()->pluck('name', 'id');
                    });
                }
                if (Str::contains($type, 'CodeField')) {
                    $component
                        ->htmlField()
                        ->withLineNumbers();
                }
                if (self::isFullWidth($type)) {
                    $component->columnSpan(2);
                }
                array_push(
                    $fields,
                    $component
                );
                $fields = array_merge($fields, self::makeHiddenFields($field));
            }
        }
        // dd($fields);
        return $fields;
    }
}
