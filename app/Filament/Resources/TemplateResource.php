<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TemplateResource\Pages;
use App\Filament\Resources\TemplateResource\RelationManagers;
use App\Http\Traits\BlockBuilderTrait;
use App\Models\Page;
use App\Models\Template;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
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
    protected static ?string $navigationGroup = 'Advanced';
    protected static ?string $navigationIcon = 'heroicon-o-template';

    public static function form(Form $form): Form
    {
        // dd(get_class_methods($form));
        return $form
            ->schema([
                Grid::make(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->columnSpan(1)
                            ->maxLength(255),
                        Tabs::make('tabs')
                            ->columnSpanFull()
                            ->tabs([
                                Tab::make('Blocks')
                                    ->schema(self::getBlockBuilderFields()),
                                // Tab::make('Preview')
                                //     ->schema([
                                //         View::make('admin.preview')
                                //             ->viewData(['form' => 'sfdsdsdfs'])
                                //     ])
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
