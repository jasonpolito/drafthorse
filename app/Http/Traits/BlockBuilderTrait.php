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
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ViewField;
use Filament\Pages\Actions\Action;

trait BlockBuilderTrait
{
    public static function getTaxonomyFields()
    {
        return Grid::make(2)
            ->schema(fn (Closure $get) => self::getTaxonomyFieldsByType($get('taxonomy_id')));
    }

    public static function getTaxonomyFieldsByType($taxonomy_id)
    {
        $taxonomy = Taxonomy::find($taxonomy_id);
        $fields = [];
        if ($taxonomy) {
            foreach ($taxonomy->fields as $field) {
                $type = $field['type'];
                $snaked = Str::snake($field['name']);
                $component = $type::make('data.' . $snaked);
                if (method_exists($type, 'placeholder')) {
                    $component->placeholder($field['name']);
                }
                if (Str::contains($type, 'ColorPicker')) {
                    $component->rgba();
                }
                if (Str::contains($type, 'TiptapEditor')) {
                    $component->columnSpan(2);
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
            Card::make()
                ->hidden(fn (Closure $get) => $get('use_template'))
                ->schema([
                    BlockBuilder::make('blocks')
                        ->columnSpan(2)
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
                                        ])
                                ]),
                            Block::make('rich-content')
                                ->label('Text Content')
                                ->icon('heroicon-o-pencil-alt')
                                ->schema([
                                    Tab::make('content')
                                        ->schema([
                                            TiptapEditor::make('content')
                                                ->label(false)
                                        ]),
                                ]),
                        ])
                ])
        ];
    }
}
