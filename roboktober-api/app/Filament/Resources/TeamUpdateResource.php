<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\ContentFormat;
use App\Filament\Resources\TeamUpdateResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TeamUpdateResource extends Resource
{
    protected static ?string $model = TeamUpdate::class;
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationLabel = 'Team updates';
    protected static ?string $modelLabel = 'Team update';
    protected static ?string $pluralModelLabel = 'Team updates';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Voortgangsbericht')
                    ->schema([
                        Forms\Components\Select::make('team_id')
                            ->label('Team')
                            ->relationship('team', 'naam')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('titel')
                            ->label('Titel')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('excerpt')
                            ->label('Korte intro')
                            ->maxLength(320)
                            ->rows(2),
                        Forms\Components\Select::make('content_format')
                            ->label('Formaat')
                            ->options([
                                ContentFormat::Html->value => 'HTML',
                                ContentFormat::Markdown->value => 'Markdown',
                            ])
                            ->default(ContentFormat::Html->value)
                            ->required(),
                        Forms\Components\RichEditor::make('content')
                            ->label('Inhoud')
                            ->required()
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold', 'italic', 'underline', 'strike',
                                'h2', 'h3',
                                'bulletList', 'orderedList', 'blockquote',
                                'link', 'codeBlock',
                            ]),
                    ])->columns(2),

                Forms\Components\Section::make('Publicatie')
                    ->schema([
                        Forms\Components\Toggle::make('is_published')
                            ->label('Publiek zichtbaar')
                            ->default(true)
                            ->live(),
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Publicatiedatum')
                            ->helperText('Leeglaten = direct publiceren')
                            ->nullable(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('titel')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('team.naam')
                    ->label('Team')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Publiek')
                    ->boolean(),
                Tables\Columns\TextColumn::make('media_count')
                    ->label('Afbeeldingen')
                    ->counts('media')
                    ->badge(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Gepubliceerd op')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Aangemaakt')
                    ->dateTime('d-m-Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Publiek zichtbaar'),
                Tables\Filters\SelectFilter::make('team_id')
                    ->label('Team')
                    ->relationship('team', 'naam'),
            ])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeamUpdates::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }
}
