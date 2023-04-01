<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Http\Traits\BlockBuilderTrait;
use App\Models\Page;
use Closure;
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
use Filament\Tables\Filters\SelectFilter;

class PageResource extends Resource
{
    use BlockBuilderTrait;

    protected static ?string $model = Page::class;
    protected static ?string $navigationGroup = 'Content';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Tabs::make('page')
                    ->tabs([
                        Tab::make('General')
                            ->schema([
                                Grid::make()
                                    ->columns(6)
                                    ->schema([
                                        TextInput::make('name')
                                            ->columnSpan(2)
                                            ->afterStateUpdated(function (Closure $get, Closure $set, ?string $state) {
                                                if (!$get('is_slug_changed_manually') && filled($state)) {
                                                    $set('slug', Str::slug($state));
                                                }
                                            })
                                            ->reactive()
                                            ->required()
                                            ->unique(ignorable: fn ($record) => $record),
                                        TextInput::make('slug')
                                            ->columnSpan(2)
                                            ->afterStateUpdated(function (Closure $set) {
                                                $set('is_slug_changed_manually', true);
                                            })
                                            ->required(),
                                        Hidden::make('is_slug_changed_manually')
                                            ->default(false)
                                            ->dehydrated(false),
                                        Select::make('taxonomy_id')
                                            ->reactive()
                                            ->required()
                                            ->relationship('taxonomy', 'name'),
                                        Select::make('template_id')
                                            ->reactive()
                                            ->required()
                                            ->relationship('template', 'name')
                                    ]),
                                self::getTaxonomyFields()
                            ]),
                        Tab::make('Details')
                            ->label('SEO & Settings')
                            ->schema([
                                Tabs::make('details')
                                    ->schema([
                                        Tab::make("SEO")
                                            ->schema([
                                                Grid::make(2)
                                                    ->schema([
                                                        TextInput::make('seo.meta_title')
                                                            ->placeholder('Title Tag')
                                                            ->reactive()
                                                            ->afterStateUpdated(function (Closure $set, $state) {
                                                                $set('seo.og_title', $state);
                                                            })
                                                            ->label('Title Tag'),
                                                        TextInput::make('seo.og_title')
                                                            ->placeholder('OpenGraph Title')
                                                            ->label('OpenGraph Title'),
                                                        Textarea::make('seo.meta_description')
                                                            ->rows(2)
                                                            ->maxLength(200)
                                                            ->placeholder('Meta Description')
                                                            ->reactive()
                                                            ->afterStateUpdated(function (Closure $set, $state) {
                                                                $set('seo.og_description', $state);
                                                            })
                                                            ->label('Meta Description'),
                                                        Textarea::make('seo.og_description')
                                                            ->rows(2)
                                                            ->maxLength(200)
                                                            ->placeholder('OpenGraph Description')
                                                            ->label('OpenGraph Description'),
                                                    ]),
                                            ])
                                    ])
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('slug')
                    ->label('URL')
                    ->searchable()
                    ->toggleable()
                    ->url(fn (Page $record): string => route('pages.view', ['slug' => $record->slug])),
                TextColumn::make('taxonomy.name')
                    ->toggleable()
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
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
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
