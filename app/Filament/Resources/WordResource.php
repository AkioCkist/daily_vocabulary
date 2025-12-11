<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WordResource\Pages;
use App\Models\Word;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class WordResource extends Resource
{
    protected static ?string $model = Word::class;

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Section::make('Word Information')
                    ->schema([
                        Forms\Components\TextInput::make('word')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('pronunciation')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('source')
                            ->maxLength(255)
                            ->nullable(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Definitions & Examples')
                    ->schema([
                        Forms\Components\Textarea::make('definition')
                            ->required()
                            ->label('Definition')
                            ->maxLength(65535),
                        Forms\Components\Textarea::make('example')
                            ->required()
                            ->label('Example Sentence')
                            ->maxLength(65535),
                        Forms\Components\Textarea::make('meaning')
                            ->label('Meaning')
                            ->maxLength(65535)
                            ->nullable(),
                    ]),

                Forms\Components\Section::make('Classification')
                    ->schema([
                        Forms\Components\TextInput::make('topic')
                            ->maxLength(255)
                            ->nullable(),
                        Forms\Components\Select::make('cefr_level')
                            ->options([
                                'A1' => 'A1 (Beginner)',
                                'A2' => 'A2 (Elementary)',
                                'B1' => 'B1 (Intermediate)',
                                'B2' => 'B2 (Upper Intermediate)',
                                'C1' => 'C1 (Advanced)',
                                'C2' => 'C2 (Mastery)',
                            ])
                            ->nullable(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('word')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pronunciation')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('topic')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cefr_level')
                    ->label('CEFR Level')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('topic')
                    ->options(fn () => \App\Models\Word::query()
                        ->whereNotNull('topic')
                        ->distinct()
                        ->pluck('topic', 'topic')
                        ->toArray()),
                Tables\Filters\SelectFilter::make('cefr_level')
                    ->options([
                        'A1' => 'A1 (Beginner)',
                        'A2' => 'A2 (Elementary)',
                        'B1' => 'B1 (Intermediate)',
                        'B2' => 'B2 (Upper Intermediate)',
                        'C1' => 'C1 (Advanced)',
                        'C2' => 'C2 (Mastery)',
                    ]),
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
            'index' => Pages\ListWords::route('/'),
            'create' => Pages\CreateWord::route('/create'),
            'edit' => Pages\EditWord::route('/{record}/edit'),
        ];
    }
}
