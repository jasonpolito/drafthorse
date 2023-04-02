<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Http\Traits\BlockBuilderTrait;
use App\Models\Page;
use Closure;
use Filament\Forms\Components\Card;
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

class PageResource extends Resource
{
    use BlockBuilderTrait;

    protected static ?string $label = 'record';
    protected static ?string $model = Page::class;
    protected static ?string $navigationGroup = 'Content';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $recordTitleAttribute = 'name';
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Tabs::make('page')
                    ->columnSpan(2)
                    ->tabs([
                        Tab::make('Content')
                            ->schema(
                                array_merge([
                                    TextInput::make('name')
                                        ->columnSpan(2)
                                        ->label('Record Title')
                                        ->placeholder('Record Title')
                                        ->afterStateUpdated(function (Closure $get, Closure $set, ?string $state) {
                                            if (!$get('is_slug_changed_manually') && filled($state)) {
                                                $set('slug', Str::slug($state));
                                            }
                                        })
                                        ->reactive()
                                        ->required()
                                        ->unique(ignorable: fn ($record) => $record),
                                ], [self::getTaxonomyFields()])

                            ),
                        Tab::make('Details')
                            ->label('SEO & Settings')
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
                            ]),
                    ]),
                Card::make()
                    ->columnSpan(1)
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                TextInput::make('slug')
                                    ->label('Permalink')
                                    ->placeholder('Permalink')
                                    ->reactive()
                                    ->helperText(function (Closure $get, $record) {
                                        if ($record) {
                                            $domain = env('APP_URL');
                                            $parent = $record->parent()->exists() ? $record->parent->getSlug() : null;
                                            $slug = $get('slug');
                                            $fullUrl = implode('/', [$domain, $parent, $slug]);
                                            $displayUrl = implode('/', [$parent, $slug]);
                                            return "<a href='$fullUrl'>$displayUrl</a>";
                                        } else {
                                            return $get('slug');
                                        }
                                    })
                                    // ->helperText(fn (Closure $get) => env('APP_URL') . '/' . $get('slug'))
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
                                    ->searchable()
                                    ->preload()
                                    ->relationship('taxonomy', 'name'),
                                Select::make('template_id')
                                    ->reactive()
                                    ->searchable()
                                    ->preload()
                                    ->relationship('template', 'name'),
                                Select::make('parent_id')
                                    ->label('Parent')
                                    ->reactive()
                                    ->searchable()
                                    ->preload()
                                    ->options(function (?Page $record) {
                                        if ($record) {
                                            return Page::whereNotIn('id', [$record->id])->get()->pluck('name', 'id');
                                        } else {
                                            return Page::all()->pluck('name', 'id');
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
                    // ->description(fn (Page $record): string => $record->taxonomy->name)
                    ->description(function ($record) {
                        return !request()->input('tableFilters') ? $record->taxonomy->name : false;
                    })
                    ->limit(50)
                    ->sortable()
                    // ->icon(fn (Page $record): string => $record->taxonomy->icon)
                    // ->icon(function ($record) {
                    //     return !request()->input('tableFilters') ? $record->taxonomy->icon : false;
                    // })
                    ->searchable(),
                TextColumn::make('slug')
                    ->label('URL')
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(function (Page $record) {
                        return $record->getSlug();
                    })
                    ->limit(50)
                    ->toggleable()
                    ->url(fn (Page $record): string => route('pages.show', ['slug' => $record->getSlug()])),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->sortable()
                    ->since(),
                // TextColumn::make('taxonomy.name')
                //     ->toggleable()
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
