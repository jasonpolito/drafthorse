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
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;

trait BlockBuilderTrait
{
    public static function getTaxonomyFields()
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

    public static function getTaxonomyFieldsByType($taxonomy_id)
    {
        $taxonomy = Taxonomy::find($taxonomy_id);
        $fields = [];
        if ($taxonomy) {
            foreach ($taxonomy->fields as $field) {
                $type = $field['type'];
                $snaked = Str::snake($field['name']);
                if (Str::contains($type, 'Builder')) {
                    $fields = array_merge(
                        $fields,

                        // TextInput::make('tst')
                        self::getBlockBuilderFields("data.$snaked")
                    );
                    // dd(self::getBlockBuilderFields("data.$snaked"));
                } else {
                    $component = $type::make("data.$snaked");
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
                            ->preload()
                            ->multiple();
                        $component->options(function () use ($field) {
                            return Record::where('taxonomy_id', $field['relation'])->get()->pluck('name', 'id');
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
                }
            }
        }
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
