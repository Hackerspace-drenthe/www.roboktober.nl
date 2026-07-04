<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\TeamStatus;
use App\Filament\Resources\TeamResource\Pages;
use App\Models\Team;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Teams';
    protected static ?string $modelLabel = 'Team';
    protected static ?string $pluralModelLabel = 'Teams';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Teamgegevens')
                    ->schema([
                        Forms\Components\Select::make('edition_id')
                            ->label('Editie')
                            ->relationship('edition', 'naam')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('naam')
                            ->label('Teamnaam')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('contactpersoon')
                            ->label('Contactpersoon')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('E-mailadres')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('volwassenen')
                            ->label('Aantal volwassenen')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->default(1),
                        Forms\Components\TextInput::make('kinderen')
                            ->label('Aantal kinderen')
                            ->numeric()
                            ->minValue(0)
                            ->nullable(),
                    ])->columns(2),

                Forms\Components\Section::make('Beheer')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                TeamStatus::Pending->value => TeamStatus::Pending->label(),
                                TeamStatus::Approved->value => TeamStatus::Approved->label(),
                                TeamStatus::Rejected->value => TeamStatus::Rejected->label(),
                            ])
                            ->default(TeamStatus::Pending->value)
                            ->required(),
                        Forms\Components\Textarea::make('opmerkingen')
                            ->label('Interne opmerkingen (niet zichtbaar voor team)')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('naam')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('edition.naam')
                    ->label('Editie')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contactpersoon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('volwassenen')
                    ->label('Volw.')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => TeamStatus::Pending->value,
                        'success' => TeamStatus::Approved->value,
                        'danger' => TeamStatus::Rejected->value,
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Aangemeld')
                    ->dateTime('d-m-Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        TeamStatus::Pending->value => TeamStatus::Pending->label(),
                        TeamStatus::Approved->value => TeamStatus::Approved->label(),
                        TeamStatus::Rejected->value => TeamStatus::Rejected->label(),
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
            'index' => Pages\ListTeams::route('/'),
            'create' => Pages\CreateTeam::route('/create'),
            'edit' => Pages\EditTeam::route('/{record}/edit'),
        ];
    }
}


