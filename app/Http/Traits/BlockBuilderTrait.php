<?php

namespace App\Http\Traits;

use App\Models\Record;
use App\Models\Taxonomy;
use App\Models\Template;
use Closure;
use Filament\Forms\Components\Builder as BlockBuilder;
use Filament\Forms\Components\Builder\Block;
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

    public static function getTemplateFields($parent): array
    {
        $fields = [];
        $templates = Template::orderBy('id', 'desc')->get();
        foreach ($templates as $template) {
            $templateFields = $template->fields;
            if (is_array($templateFields)) {
                foreach ($templateFields as $templateField) {
                    $type = $templateField['type'];
                    $name = Str::snake($templateField['name']);
                    $component = $type::make("data.$name.value");
                    $component->hidden(fn (Closure $get) => $get($parent) != $template->id);
                    $component->label($templateField['name'])
                        ->reactive();
                    if (Str::contains($type, 'repeater', true)) {
                        // dd($templateField);
                        $schema = [];
                        $repeater = Repeater::make("data.$name.value")
                            ->columnSpan(2)
                            ->label($templateField['name'])
                            ->cloneable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? $state['title'] ?? null)
                            ->orderable()
                            ->schema([$component]);
                        foreach ($templateField['fields'] as $repeaterField) {
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
        $fields = [];
        if ($taxonomy) {
            foreach ($taxonomy->fields as $field) {
                $type = $field['type'];
                $name = Str::snake($field['name']);
                if ($field['type'] == 'blocks') {
                    $component = Repeater::make("data.$name.value")
                        ->collapsible()
                        // ->collapsed()
                        ->itemLabel(fn (array $state): ?string => $state['name'] ?? $state['data']['title']['value'] ?? null)
                        ->orderable()
                        ->schema(array_merge(
                            [
                                Select::make('template')
                                    ->reactive()
                                    ->options(function () {
                                        return Template::all()->pluck('name', 'id');
                                    }),

                            ],
                            self::getTemplateFields("template")
                        ));
                } else {
                    $component = $type::make("data.$name.value");
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
                        ->columnSpanFull()
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
                    $component->columnSpanFull();
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

    public static function getBlockBuilderFields($name = null)
    {
        $name = $name ?? 'blocks';
        return [
            Grid::make(1)
                ->schema([
                    BlockBuilder::make($name)
                        ->createItemButtonLabel('Add Block')
                        ->label(false)
                        ->columnSpan(2)
                        ->collapsible()
                        ->blocks([
                            Block::make('hero')
                                ->label('Hero')
                                ->icon('heroicon-o-pencil-alt')
                                ->schema([
                                    TiptapEditor::make('content')
                                        ->label(false)
                                        ->profile('simple'),
                                ]),
                            Block::make('rich_content')
                                ->label('Text Content')
                                ->icon('heroicon-o-pencil-alt')
                                ->schema([
                                    TiptapEditor::make('content')
                                        ->label(false)
                                ]),
                            Block::make('big_image')
                                ->label('Big Image')
                                ->icon('heroicon-o-pencil-alt')
                                ->schema([
                                    FileUpload::make('content')
                                    // ->label('false')
                                ]),
                            Block::make('blocks')
                                ->label('Render Blocks')
                                ->icon('heroicon-o-code')
                                ->schema([
                                    TextInput::make('content')
                                        ->label('Render Blocks')
                                ]),
                            Block::make('code')
                                ->label('Raw Code')
                                ->icon('heroicon-o-code')
                                ->schema([
                                    CodeField::make('content')
                                        ->label('Raw Code')
                                        ->htmlField()
                                        ->withLineNumbers()
                                ]),
                        ])
                ])
        ];
    }
}
