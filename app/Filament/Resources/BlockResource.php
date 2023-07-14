<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlockResource\Pages;
use App\Filament\Resources\BlockResource\RelationManagers;
use App\Http\Traits\HasBlockBuilder;
use App\Http\Traits\HasSystemActions;
use App\Models\Page;
use App\Models\Taxonomy;
use App\Models\Block;
use Closure;
use Creagia\FilamentCodeField\CodeField;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\View;

class BlockResource extends Resource
{
    use HasBlockBuilder, HasSystemActions;

    protected static ?string $model = Block::class;
    protected static ?string $navigationGroup = 'Views';
    protected static ?string $navigationIcon = 'heroicon-o-cube';
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
                            ->columnSpanFull()
                            ->schema([
                                Repeater::make('data')
                                    ->columnSpan(2)
                                    // ->collapsed()
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? $state['title'] ?? null)
                                    ->orderable()
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('name')
                                                    ->label('Field name')
                                                    ->placeholder('Field name'),
                                                // ->required(),
                                                Select::make('type')
                                                    ->label('Field type')
                                                    // ->required()
                                                    ->reactive()
                                                    ->searchable()
                                                    ->preload()
                                                    ->options(array_merge(self::blockOptions(), ['blocks' => 'Block Editor'])),

                                                Select::make('relations')
                                                    ->hidden(fn (Closure $get, $state) => $get('type') !== 'Filament\Forms\Components\Select')
                                                    ->label('Select Relation(s)')
                                                    ->placeholder('Select Relation(s)')
                                                    ->columnSpan(2)
                                                    ->searchable()
                                                    ->preload()
                                                    ->multiple()
                                                    ->options(function () {
                                                        return Taxonomy::all()->pluck('name', 'id');
                                                    }),
                                                Card::make()
                                                    ->hidden(fn (Closure $get) => $get('type') != 'Filament\Forms\Components\Repeater')
                                                    ->schema([
                                                        Grid::make(2)
                                                            ->schema([
                                                                Repeater::make('fields')
                                                                    ->columnSpan(2)
                                                                    ->collapsible()
                                                                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? $state['title'] ?? null)
                                                                    ->orderable()
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
                                                                                    ->options(self::blockOptions()),
                                                                            ])
                                                                    ]),
                                                            ])
                                                    ])
                                            ])
                                    ])
                            ]),

                        Tab::make('Markup')
                            ->columnSpanFull()
                            ->schema([
                                CodeField::make('markup')
                                    ->withLineNumbers()
                                    ->htmlField()
                            ]),
                    ]),
                Card::make()
                    ->columnSpan(1)
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                TextInput::make('name')
                                    ->columnSpanFull()
                                    ->placeholder('Block name')
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
                Tables\Columns\TextColumn::make('name'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions(array_merge(self::bulkActions('Block'), [
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]));
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
            'index' => Pages\ListBlocks::route('/'),
            'create' => Pages\CreateBlock::route('/create'),
            'edit' => Pages\EditBlock::route('/{record}/edit'),
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
