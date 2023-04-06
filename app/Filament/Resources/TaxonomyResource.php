<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaxonomyResource\Pages;
use App\Filament\Resources\TaxonomyResource\RelationManagers;
use App\Models\Taxonomy;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Guava\FilamentIconPicker\Forms\IconPicker;

class TaxonomyResource extends Resource
{
    protected static ?string $model = Taxonomy::class;
    protected static ?string $navigationGroup = 'Advanced';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-o-database';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(3)
                    ->schema([
                        Card::make()
                            ->columnSpan(2)
                            ->schema([
                                Repeater::make('fields')
                                    ->columnSpan(2)
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('name')
                                                    ->label('Field name')
                                                    ->placeholder('Field name')
                                                    ->required(),
                                                Select::make('type')
                                                    ->label('Field type')
                                                    ->required()
                                                    ->reactive()
                                                    ->searchable()
                                                    ->preload()
                                                    ->options([
                                                        'Filament\Forms\Components\TextInput' => 'Short Text',
                                                        'Filament\Forms\Components\Textarea' => 'Long Text',
                                                        'FilamentTiptapEditor\TiptapEditor' => 'Rich Content',
                                                        'Filament\Forms\Components\Builder' => 'Block Editor',
                                                        'Filament\Forms\Components\Toggle' => 'Checkbox',
                                                        'Filament\Forms\Components\Select' => 'Select',
                                                        'Filament\Forms\Components\FileUpload' => 'File Upload',
                                                        'Filament\Forms\Components\ColorPicker' => 'Color Picker',
                                                        'Creagia\FilamentCodeField\CodeField' => 'Code Editor',
                                                    ]),
                                                Checkbox::make('multiple'),
                                                Select::make('relations')
                                                    ->label('Relation(s)')
                                                    ->searchable()
                                                    ->preload()
                                                    ->multiple()
                                                    ->options(function () {
                                                        return Taxonomy::all()->pluck('name', 'id');
                                                        // if ($record) {
                                                        //     return Taxonomy::whereNotIn('id', [$record->id])->pluck('name', 'id');
                                                        // } else {
                                                        // }
                                                    })
                                            ])
                                    ])
                            ]),
                        Card::make()
                            ->columnSpan(1)
                            ->schema([
                                TextInput::make('name')
                                    ->unique(ignorable: fn ($record) => $record)
                                    ->required(),
                                IconPicker::make('icon')
                                    ->required()
                                    ->columns(5),
                            ]),
                    ])
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTaxonomies::route('/'),
            'create' => Pages\CreateTaxonomy::route('/create'),
            'edit' => Pages\EditTaxonomy::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
