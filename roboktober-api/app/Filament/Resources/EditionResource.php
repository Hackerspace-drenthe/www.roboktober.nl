<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\EditionResource\Pages;
use App\Models\Edition;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EditionResource extends Resource
{
    protected static ?string $model = Edition::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Edities';
    protected static ?string $modelLabel = 'Editie';
    protected static ?string $pluralModelLabel = 'Edities';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Editiegegevens')
                    ->schema([
                        Forms\Components\TextInput::make('naam')
                            ->label('Naam editie')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('locatie')
                            ->label('Locatie')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DateTimePicker::make('start_at')
                            ->label('Startdatum/tijd')
                            ->required()
                            ->seconds(false),
                        Forms\Components\DateTimePicker::make('end_at')
                            ->label('Einddatum/tijd')
                            ->seconds(false)
                            ->nullable(),
                        Forms\Components\FileUpload::make('afbeelding')
                            ->label('Afbeelding')
                            ->image()
                            ->disk('public')
                            ->directory('edities')
                            ->imageEditor()
                            ->nullable(),
                        Forms\Components\Textarea::make('omschrijving')
                            ->label('Omschrijving')
                            ->rows(4)
                            ->columnSpanFull()
                            ->nullable(),
                        Forms\Components\Toggle::make('is_done')
                            ->label('Afgesloten (done)')
                            ->helperText('Zet aan om aanmeldingen voor deze editie te sluiten.')
                            ->default(false),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('afbeelding')
                    ->disk('public')
                    ->label('Afbeelding')
                    ->square(),
                Tables\Columns\TextColumn::make('naam')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('locatie')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_at')
                    ->label('Start')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_at')
                    ->label('Einde')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_done')
                    ->label('Done')
                    ->boolean(),
            ])
            ->defaultSort('start_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_done')
                    ->label('Status done'),
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
            'index' => Pages\ListEditions::route('/'),
            'create' => Pages\CreateEdition::route('/create'),
            'edit' => Pages\EditEdition::route('/{record}/edit'),
        ];
    }
}
