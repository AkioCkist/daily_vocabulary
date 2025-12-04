<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DailyTestResource\Pages;
use App\Models\DailyTest;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class DailyTestResource extends Resource
{
    protected static ?string $model = DailyTest::class;

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Section::make('Test Information')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable(),
                        Forms\Components\DatePicker::make('date')
                            ->required(),
                    ]),

                Forms\Components\Section::make('Test Status & Results')
                    ->schema([
                        Forms\Components\Toggle::make('is_completed')
                            ->label('Test Completed')
                            ->default(false),
                        Forms\Components\TextInput::make('score')
                            ->numeric()
                            ->nullable()
                            ->label('Score (points)')
                            ->helperText('Optional: store test score if needed'),
                    ]),

                Forms\Components\Section::make('Additional Data')
                    ->schema([
                        Forms\Components\Textarea::make('meta')
                            ->label('Metadata (JSON)')
                            ->maxLength(65535)
                            ->nullable()
                            ->helperText('Store test configuration as JSON'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_completed')
                    ->boolean()
                    ->label('Completed')
                    ->sortable(),
                Tables\Columns\TextColumn::make('score')
                    ->numeric()
                    ->label('Score')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_completed')
                    ->label('Completion Status'),
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
            'index' => Pages\ListDailyTests::route('/'),
            'create' => Pages\CreateDailyTest::route('/create'),
            'edit' => Pages\EditDailyTest::route('/{record}/edit'),
        ];
    }
}
