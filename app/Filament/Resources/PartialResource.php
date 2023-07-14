<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PartialResource\Pages;
use App\Http\Traits\HasBlockBuilder;
use App\Http\Traits\HasSystemActions;
use App\Models\Block;
use App\Models\Partial;
use App\Models\Record;
use Creagia\FilamentCodeField\CodeField;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Str;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Repeater;

class PartialResource extends Resource
{
    use HasBlockBuilder, HasSystemActions;

    protected static ?string $model = Partial::class;
    protected static ?string $navigationGroup = 'Views';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Tabs::make('record')
                    ->columnSpan(2)
                    ->schema([
                        Tab::make('Data')
                            ->schema([
                                self::blockBuilderField("data.content.value")
                            ]),
                    ]),
                Card::make()
                    ->columnSpan(1)
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                TextInput::make('name')
                                    ->columnSpanFull()
                                    ->placeholder('Layout name')
                                    ->reactive()
                                    ->required()
                                    ->unique(ignorable: fn ($record) => $record),
                            ])
                    ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->limit(50)
                    ->toggleable()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->toggleable()
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions(array_merge(
                self::bulkActions('Record'),
                [
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]
            ));
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
            'index' => Pages\ListPartials::route('/'),
            'create' => Pages\CreatePartial::route('/create'),
            'edit' => Pages\EditPartial::route('/{record}/edit'),
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
