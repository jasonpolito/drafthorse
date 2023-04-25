<?php

namespace App\Http\Traits;

use App\Models\Record;
use App\Models\Taxonomy;
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
use Filament\Forms\Components\ViewField;
use Illuminate\Support\Facades\Request;

trait BlockBuilderTrait
{
    const FIELD_TYPES = [
        'Filament\Forms\Components\TextInput' => 'text',
        'Filament\Forms\Components\Textarea' => 'text',
        'FilamentTiptapEditor\TiptapEditor' => 'rich_content',
        'Filament\Forms\Components\Builder' => 'text',
        'Filament\Forms\Components\Toggle' => 'boolean',
        'Filament\Forms\Components\Select' => 'relation',
        'Filament\Forms\Components\FileUpload' => 'files',
        'Filament\Forms\Components\ColorPicker' => 'text',
        'Creagia\FilamentCodeField\CodeField' => 'raw',
    ];

    public static function getTaxonomyFields($page = null)
    {
        return Grid::make()
            ->schema(fn (Closure $get) => self::getTaxonomyFieldsByType($get('taxonomy_id')));
    }

    public static function isFullWidth($type)
    {
        return in_array($type, [
            'FilamentTiptapEditor\TiptapEditor',
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
                $component = $type::make("data.$name.value");
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
