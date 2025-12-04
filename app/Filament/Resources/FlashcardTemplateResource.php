<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FlashcardTemplateResource\Pages;
use App\Models\FlashcardTemplate;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class FlashcardTemplateResource extends Resource
{
    protected static ?string $model = FlashcardTemplate::class;

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Section::make('Template Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                    ]),

                Forms\Components\Section::make('Template Settings')
                    ->schema([
                        Forms\Components\Textarea::make('settings')
                            ->label('Settings (JSON)')
                            ->required()
                            ->maxLength(65535)
                            ->helperText('Store template configuration as JSON'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Created By')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->relationship('user', 'name'),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFlashcardTemplates::route('/'),
            'create' => Pages\CreateFlashcardTemplate::route('/create'),
            'edit' => Pages\EditFlashcardTemplate::route('/{record}/edit'),
        ];
    }
}
