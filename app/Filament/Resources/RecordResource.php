<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RecordResource\Pages;
use App\Http\Traits\HasBlockBuilder;
use App\Http\Traits\HasSystemActions;
use App\Models\Layout;
use App\Models\Record;
use Closure;
use Filament\Forms\Components\Card;
use Creagia\FilamentCodeField\CodeField;
use Illuminate\Support\Str;
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
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Tables\Filters\SelectFilter;

class RecordResource extends Resource
{
    use HasBlockBuilder, HasSystemActions;

    protected static ?string $label = 'record';
    protected static ?string $model = Record::class;
    protected static ?string $navigationGroup = 'Content';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $recordTitleAttribute = 'name';
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Tabs::make('record')
                    ->columnSpan(2)
                    ->tabs([
                        Tab::make('Data')
                            ->schema(
                                array_merge([], [self::getTaxonomyFields()])
                            ),
                        Tab::make('Markup')
                            ->schema([
                                Select::make('data.layout')
                                    ->default(1)
                                    ->required()
                                    ->options(function (?Record $record) {
                                        return Layout::all()->pluck('name', 'id');
                                    }),
                                CodeField::make('data.markup')
                                    ->withLineNumbers()
                                    ->htmlField()
                            ])

                    ]),
                Tabs::make('General')
                    ->columnSpan(1)
                    ->tabs([
                        Tab::make('Overview')
                            ->label(false)
                            ->icon('heroicon-o-bell')
                            ->schema([
                                TextInput::make('name')
                                    ->columnSpanFull()
                                    ->placeholder('Record Title')
                                    ->afterStateUpdated(function (Closure $get, Closure $set, ?string $state) {
                                        if (!$get('is_slug_changed_manually') && filled($state)) {
                                            $set('slug', Str::slug($state));
                                        }
                                    })
                                    ->reactive()
                                    ->required()
                                    ->unique(ignorable: fn ($record) => $record),
                                TextInput::make('slug')
                                    ->label('Permalink')
                                    ->placeholder('Permalink')
                                    ->reactive()
                                    ->helperText(function (Closure $get, $record) {
                                        if ($record) {
                                            $domain = env('APP_URL');
                                            $parent = $record->parent()->exists() ? $record->parent->fullSlug() : null;
                                            $slug = $get('slug');
                                            $fullUrl = implode('/', array_filter([$domain, $parent, $slug]));
                                            $displayUrl = implode('/', array_filter([$parent, $slug]));
                                            return "<a href='$fullUrl'>$displayUrl</a>";
                                        } else {
                                            return $get('slug');
                                        }
                                    })
                                    ->afterStateUpdated(function (Closure $set) {
                                        $set('is_slug_changed_manually', true);
                                    })
                                    ->required(),
                                Hidden::make('is_slug_changed_manually')
                                    ->default(false)
                                    ->dehydrated(false),
                            ]),
                        Tab::make('Details')
                            ->label(false)
                            ->schema([
                                Select::make('taxonomy_id')
                                    ->reactive()
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->relationship('taxonomy', 'name'),
                                Select::make('parent_id')
                                    ->label('Parent')
                                    ->reactive()
                                    ->searchable()
                                    ->preload()
                                    ->options(function (?Record $record) {
                                        if ($record) {
                                            $exclude = array_merge($record->children->pluck('id')->toArray(), [$record->id]);
                                            return Record::whereNotIn('id', $exclude)->get()->pluck('name', 'id');
                                        } else {
                                            return Record::all()->pluck('name', 'id');
                                        }
                                    }),
                            ])
                    ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->description(function ($record) {
                        return !request()->input('tableFilters') ? ($record->taxonomy ? $record->taxonomy->name : 'N/A') : false;
                    })
                    ->limit(50)
                    ->toggleable()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('slug')
                    ->label('Permalink')
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(function (Record $record) {
                        return $record->fullSlug();
                    })
                    ->limit(50)
                    ->toggleable()
                    ->url(fn (Record $record): string => route('records.show', ['slug' => $record->fullSlug()])),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->toggleable()
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                SelectFilter::make('taxonomy')
                    ->multiple()
                    ->relationship('taxonomy', 'name')
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
            'index' => Pages\ListRecords::route('/'),
            'create' => Pages\CreateRecord::route('/create'),
            'edit' => Pages\EditRecord::route('/{record}/edit'),
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
