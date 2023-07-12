<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LayoutResource\Pages;
use App\Filament\Resources\LayoutResource\RelationManagers;
use App\Models\Layout;
use Creagia\FilamentCodeField\CodeField;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Http\Traits\HasBlockBuilder;
use App\Http\Traits\HasSystemActions;
use App\Models\Block;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;

class LayoutResource extends Resource
{

    use HasBlockBuilder, HasSystemActions;

    protected static ?string $model = Layout::class;
    protected static ?string $navigationGroup = 'Views';
    protected static ?string $navigationIcon = 'heroicon-o-template';
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
                                Repeater::make("data.layout.value")
                                    ->collapsible()
                                    ->columnSpan('full')
                                    ->orderable()
                                    ->schema(array_merge(
                                        [
                                            Select::make('block')
                                                ->reactive()
                                                ->columnSpan('full')
                                                ->options(function () {
                                                    return Block::all()->pluck('name', 'id');
                                                }),

                                        ],
                                        self::getBlockFields("block")
                                    ))
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
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                self::exportRecordsAsJson('Layout'),
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
            'index' => Pages\ListLayouts::route('/'),
            'create' => Pages\CreateLayout::route('/create'),
            'edit' => Pages\EditLayout::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }


    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['uuid'] = Str::orderedUuid();

        return $data;
    }
}
