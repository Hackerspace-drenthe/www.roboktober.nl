<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\LinkCategorie;
use App\Filament\Resources\LinkResource\Pages;
use App\Models\Link;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LinkResource extends Resource
{
    protected static ?string $model = Link::class;
    protected static ?string $navigationIcon = 'heroicon-o-link';
    protected static ?string $navigationLabel = 'Links';
    protected static ?string $modelLabel = 'Link';
    protected static ?string $pluralModelLabel = 'Links';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('titel')
                    ->label('Titel')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('url')
                    ->label('URL')
                    ->url()
                    ->required()
                    ->maxLength(2048)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('beschrijving')
                    ->label('Beschrijving')
                    ->rows(2)
                    ->columnSpanFull(),
                Forms\Components\Select::make('categorie')
                    ->label('Categorie')
                    ->options([
                        LinkCategorie::Wallie->value => LinkCategorie::Wallie->label(),
                        LinkCategorie::Community->value => LinkCategorie::Community->label(),
                        LinkCategorie::Competitie->value => LinkCategorie::Competitie->label(),
                        LinkCategorie::Tools->value => LinkCategorie::Tools->label(),
                        LinkCategorie::Onderdelen->value => LinkCategorie::Onderdelen->label(),
                        LinkCategorie::Documentatie->value => LinkCategorie::Documentatie->label(),
                    ])
                    ->required(),
                Forms\Components\TextInput::make('eigenaar')
                    ->label('Eigenaar / beheerder')
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('verified_at')
                    ->label('Laatste verificatie'),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('titel')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('url')
                    ->searchable()
                    ->limit(50)
                    ->url(fn (Link $record): string => $record->url, shouldOpenInNewTab: true),
                Tables\Columns\BadgeColumn::make('categorie'),
                Tables\Columns\TextColumn::make('eigenaar')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('verified_at')
                    ->label('Geverifieerd')
                    ->dateTime('d-m-Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('categorie')
                    ->options([
                        LinkCategorie::Wallie->value => LinkCategorie::Wallie->label(),
                        LinkCategorie::Community->value => LinkCategorie::Community->label(),
                        LinkCategorie::Competitie->value => LinkCategorie::Competitie->label(),
                        LinkCategorie::Tools->value => LinkCategorie::Tools->label(),
                        LinkCategorie::Onderdelen->value => LinkCategorie::Onderdelen->label(),
                        LinkCategorie::Documentatie->value => LinkCategorie::Documentatie->label(),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLinks::route('/'),
            'create' => Pages\CreateLink::route('/create'),
            'edit' => Pages\EditLink::route('/{record}/edit'),
        ];
    }
}

