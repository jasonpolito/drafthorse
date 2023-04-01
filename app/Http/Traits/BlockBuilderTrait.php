<?php

namespace App\Http\Traits;

use App\Models\Component;
use App\Models\Page;
use App\Models\Taxonomy;
use App\Models\Template;
use App\View\Components\Blocks\Tempalte;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Builder as BlockBuilder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use FilamentTiptapEditor\TiptapEditor;
use Creagia\FilamentCodeField\CodeField;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ViewField;
use Filament\Pages\Actions\Action;

trait BlockBuilderTrait
{
    public static function getTemplateFields()
    {
        return Grid::make(1)
            ->hidden(fn (Closure $get) => !$get('use_template'))
            ->schema(fn (Closure $get) => self::getTemplateFieldsByType($get('template_id')));
    }

    public static function getTemplateFieldsByType($template_id)
    {
        $template = Template::find($template_id);
        $fields = [];
        if ($template) {
            foreach ($template->fields as $field) {
                $type = $field['type'];
                $snaked = Str::snake($field['name']);
                $component = $type::make('template_data.' . $snaked);
                if (method_exists($type, 'placeholder')) {
                    $component->placeholder($field['name']);
                }
                if (Str::contains($type, 'ColorPicker')) {
                    $component->rgba();
                }
                array_push(
                    $fields,
                    $component
                );
            }
        }
        return $fields;
    }

    public static function getBlockBuilderFields()
    {
        return [
            Grid::make()
                ->hidden(fn (Closure $get) => $get('use_template'))
                ->schema([
                    BlockBuilder::make('blocks')
                        ->columnSpan(2)
                        ->label('Content')
                        ->collapsible()
                        ->blocks([
                            Block::make('hero')
                                ->label('Hero')
                                ->icon('heroicon-o-pencil-alt')
                                ->schema([
                                    Tabs::make('content')
                                        ->schema([
                                            Tab::make('Content')
                                                ->schema([
                                                    TiptapEditor::make('content')
                                                        ->label(false)
                                                        ->profile('simple'),
                                                ]),
                                            Tab::make('Style')
                                                ->schema([
                                                    FileUpload::make('bg_image')
                                                        ->label('Background')
                                                        ->extraAttributes(['class' => '-mb-3'])
                                                        ->visible(fn (Closure $get) => !$get('bg_image_use_field')),
                                                    Select::make('bg_image')
                                                        ->visible(fn (Closure $get) => $get('bg_image_use_field'))
                                                        ->label('Background')
                                                        ->placeholder('Select a template field')
                                                        ->options(fn (?Template $record) => $record->getFieldOptions('image')),
                                                    Checkbox::make('bg_image_use_field')
                                                        ->label('Use template field')
                                                        ->reactive(),
                                                    Select::make('bg_overlay')
                                                        ->label('Background Overlay')
                                                        ->options(fn (?Template $record) => $record->getFieldOptions('color')),
                                                    // ColorPicker::make('bg_image_cover')
                                                    //     ->label('Background Overlay')
                                                    //     ->rgba(),
                                                ]),
                                        ])
                                ]),
                            Block::make('template')
                                ->icon('heroicon-o-template')
                                ->schema([
                                    Select::make('taxonomy')
                                        ->options(Taxonomy::all()->pluck('title', 'id'))
                                ]),
                            Block::make('rich-content')
                                ->label('Text Content')
                                ->icon('heroicon-o-pencil-alt')
                                ->schema([
                                    Tabs::make('editor')
                                        ->tabs([
                                            Tab::make('content')
                                                ->schema([
                                                    TiptapEditor::make('content')
                                                        ->label(false)
                                                    // ->profile('simple')
                                                ]),
                                            Tab::make('settings')
                                                ->schema([
                                                    ColorPicker::make('bg-color'),
                                                    Select::make('bg-color')
                                                        ->options([
                                                            'blue-500' => 'Primary',
                                                            'green-500' => 'Secondary',
                                                            'custom' => 'Custom',
                                                        ])
                                                ])
                                        ])
                                ]),

                            Block::make('component-include')
                                ->label('Component')
                                ->icon('heroicon-o-chip')
                                ->schema([
                                    Select::make('component_id')
                                        ->label('Component')
                                        ->options(Component::all()->pluck('title', 'id'))
                                        ->searchable()
                                ]),

                        ])
                ])
        ];
    }
}
