<?php

namespace App\Http\Traits;

use App\Models\Record;
use App\Models\Taxonomy;
use App\Models\Block;
use App\Models\Partial;
use Closure;
use Filament\Forms\Components\Builder as BlockBuilder;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Str;
use FilamentTiptapEditor\TiptapEditor;
use Creagia\FilamentCodeField\CodeField;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ViewField;
use Illuminate\Support\Facades\Request;
use AskerAkbar\GptTrixEditor\Components\GptTrixEditor;

trait HasBlockBuilder
{

    public static function getTaxonomyFields($page = null)
    {
        return Grid::make(1)
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
                    $type = (string) $blockField['type'];
                    $isRepeater = Str::contains($type, 'repeater', true);
                    if (!$type) continue;
                    $name = Str::snake($blockField['name']);
                    $component = $type::make("data.$name.value");
                    $component->hidden(fn (Closure $get) => $get($parent) != $block->uuid);
                    $component->label($blockField['name'])
                        ->columnSpan('full')
                        ->reactive();
                    self::configureAdditionalFieldSetup($blockField, $component);
                    if ($isRepeater) {

                        $repeaterSchema = [];
                        foreach ($blockField['fields'] as $repeaterField) {
                            $repeaterType = (string) $repeaterField['type'];
                            if (!$repeaterType) continue;
                            $name = Str::snake($repeaterField['name']);
                            $repeaterComponent = $repeaterType::make("data.$name.value");
                            $repeaterComponent->label($repeaterField['name'])
                                ->columnSpan('full')
                                ->reactive();

                            // add field specific attributes
                            self::configureAdditionalFieldSetup($repeaterField, $repeaterComponent);

                            $repeaterSchema[] = $repeaterComponent;
                        }
                        $component
                            ->collapsible()
                            ->cloneable()

                            ->schema($repeaterSchema);
                    }
                    array_push($fields, $component);
                }
            }
        }
        return $fields;
    }

    public static function makeHiddenFields($field)
    {
        $result = [];
        $name = Str::snake($field['name']);
        $typeName = config('fieldtypes')[$field['type']];
        $hidden = Hidden::make("data.$name.type")
            ->afterStateHydrated(function (Hidden $component, $state) use ($typeName) {
                $component->state($typeName);
            });
        array_push($result, $hidden);
        return $result;
    }

    public static function configureAdditionalFieldSetup($field, $component)
    {
        $type = $field['type'];
        if (method_exists($type, 'placeholder')) {
            $component->placeholder($field['name']);
        }
        if (Str::contains($type, 'ColorPicker')) {
            $component->rgba()
                ->extraAttributes(['class' => 'w-60 float-right'])
                ->inlineLabel();
        }
        if (
            Str::contains($type, 'FileUpload') ||
            Str::contains($type, 'TextInput')
        ) {
            $component->inlineLabel();
        }
        if (Str::contains($type, 'Select')) {
            $component
                ->inlineLabel()
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
                        ->columnSpan('full')
                        ->itemLabel(fn (array $state): ?string => $state['name'] ?? $state['data']['title']['value'] ?? null)
                        ->orderable()
                        ->schema(array_merge(
                            [
                                Select::make('block')
                                    ->reactive()
                                    ->columnSpan('full')
                                    ->options(function () {
                                        $blocks = Block::all()->map(function ($item) {
                                            $item->name .= ' (block)';
                                            return $item;
                                        })->pluck('name', 'uuid');
                                        $partials = Partial::all()->map(function ($item) {
                                            $item->name .= ' (partial)';
                                            return $item;
                                        })->pluck('name', 'uuid');
                                        $items = $blocks->merge($partials);
                                        return $items;
                                    }),

                            ],
                            self::getBlockFields("block")
                        ));
                } else {
                    $component = $type::make("data.$name.value")
                        ->columnSpan('full');
                }
                $component->label($field['name']);

                // add field specific attributes
                self::configureAdditionalFieldSetup($field, $component);

                array_push(
                    $fields,
                    $component
                );
                $fields = array_merge($fields, self::makeHiddenFields($field));
            }
        }
        return $fields;
    }
}
