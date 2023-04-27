<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TemplateResource\Pages;
use App\Filament\Resources\TemplateResource\RelationManagers;
use App\Http\Traits\BlockBuilderTrait;
use App\Models\Page;
use App\Models\Taxonomy;
use App\Models\Template;
use Closure;
use Creagia\FilamentCodeField\CodeField;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
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

class TemplateResource extends Resource
{
    use BlockBuilderTrait;

    protected static ?string $model = Template::class;
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
                                Repeater::make('fields')
                                    ->columnSpan(2)
                                    ->collapsed()
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? count($state['fields']))
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
                                                    ->options([
                                                        'Filament\Forms\Components\TextInput' => 'Short Text',
                                                        'Filament\Forms\Components\Textarea' => 'Long Text',
                                                        'FilamentTiptapEditor\TiptapEditor' => 'Rich Content',
                                                        'Filament\Forms\Components\FileUpload' => 'File Upload',
                                                        'Filament\Forms\Components\Select' => 'Relationship',
                                                        'Filament\Forms\Components\Toggle' => 'Checkbox',
                                                        'Filament\Forms\Components\ColorPicker' => 'Color Picker',
                                                        'Creagia\FilamentCodeField\CodeField' => 'Code Editor',
                                                    ]),
                                            ])
                                    ])
                            ]),

                        Tab::make('View')
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
                                    ->placeholder('Template name')
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
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime(),
                // Tables\Columns\TextColumn::make('blocks'),
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
            'index' => Pages\ListTemplates::route('/'),
            'create' => Pages\CreateTemplate::route('/create'),
            'edit' => Pages\EditTemplate::route('/{record}/edit'),
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
