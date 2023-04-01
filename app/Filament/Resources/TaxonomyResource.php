<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaxonomyResource\Pages;
use App\Filament\Resources\TaxonomyResource\RelationManagers;
use App\Models\Taxonomy;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                Grid::make(2)
                    ->schema([
                        TextInput::make('name')->required(),
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
                                            ->options([
                                                'Filament\Forms\Components\TextInput' => 'Short Text',
                                                'Filament\Forms\Components\Textarea' => 'Long Text',
                                                'FilamentTiptapEditor\TiptapEditor' => 'Rich Content',
                                                'Filament\Forms\Components\Toggle' => 'Checkbox',
                                                'Filament\Forms\Components\Select' => 'Select',
                                                'Filament\Forms\Components\FileUpload' => 'File Upload',
                                                'Filament\Forms\Components\ColorPicker' => 'Color Picker',
                                            ])
                                    ])
                            ])
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
