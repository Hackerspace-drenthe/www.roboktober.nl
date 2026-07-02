<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\Gewichtsklasse;
use App\Enums\RobotStatus;
use App\Filament\Resources\RobotResource\Pages;
use App\Models\Robot;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RobotResource extends Resource
{
    protected static ?string $model = Robot::class;
    protected static ?string $navigationIcon = 'heroicon-o-bolt';
    protected static ?string $navigationLabel = 'Robots';
    protected static ?string $modelLabel = 'Robot';
    protected static ?string $pluralModelLabel = 'Robots';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('team_id')
                    ->label('Team')
                    ->relationship('team', 'naam')
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('naam')
                    ->label('Naam')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('gewichtsklasse')
                    ->label('Gewichtsklasse')
                    ->options([
                        Gewichtsklasse::Antweight->value => Gewichtsklasse::Antweight->label(),
                        Gewichtsklasse::Beetleweight->value => Gewichtsklasse::Beetleweight->label(),
                        Gewichtsklasse::Featherweight->value => Gewichtsklasse::Featherweight->label(),
                    ])
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        RobotStatus::InOntwikkeling->value => RobotStatus::InOntwikkeling->label(),
                        RobotStatus::Gereed->value => RobotStatus::Gereed->label(),
                        RobotStatus::BattleReady->value => RobotStatus::BattleReady->label(),
                    ])
                    ->default(RobotStatus::InOntwikkeling->value)
                    ->required(),
                Forms\Components\Textarea::make('beschrijving')
                    ->label('Beschrijving')
                    ->rows(3)
                    ->columnSpanFull(),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('team.naam')
                    ->label('Team')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('naam')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('gewichtsklasse')
                    ->colors(['primary' => fn ($state) => $state === Gewichtsklasse::Antweight->value]),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray' => RobotStatus::InOntwikkeling->value,
                        'info' => RobotStatus::Gereed->value,
                        'success' => RobotStatus::BattleReady->value,
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Aangemaakt')
                    ->dateTime('d-m-Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('gewichtsklasse')
                    ->options([
                        Gewichtsklasse::Antweight->value => Gewichtsklasse::Antweight->label(),
                        Gewichtsklasse::Beetleweight->value => Gewichtsklasse::Beetleweight->label(),
                        Gewichtsklasse::Featherweight->value => Gewichtsklasse::Featherweight->label(),
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        RobotStatus::InOntwikkeling->value => RobotStatus::InOntwikkeling->label(),
                        RobotStatus::Gereed->value => RobotStatus::Gereed->label(),
                        RobotStatus::BattleReady->value => RobotStatus::BattleReady->label(),
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
            'index' => Pages\ListRobots::route('/'),
            'create' => Pages\CreateRobot::route('/create'),
            'edit' => Pages\EditRobot::route('/{record}/edit'),
        ];
    }
}
