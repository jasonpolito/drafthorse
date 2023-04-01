<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TemplateResource\Pages;
use App\Filament\Resources\TemplateResource\RelationManagers;
use App\Http\Traits\BlockBuilderTrait;
use App\Models\Template;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
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

class TemplateResource extends Resource
{
    use BlockBuilderTrait;

    protected static ?string $model = Template::class;
    protected static ?string $navigationGroup = 'Content';
    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-template';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(1)
                    ->schema([
                        TextInput::make('title')->required(),
                        Tabs::make('page')
                            ->tabs([
                                Tab::make('Data')
                                    ->label('Template Fields')
                                    ->schema([
                                        Repeater::make('fields')
                                            ->collapsible()
                                            ->schema([
                                                Grid::make()
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
                                    ]),
                                Tab::make('View')
                                    ->label('Template View')

                                    ->schema(self::getBlockBuilderFields()),
                            ])
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                //
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
