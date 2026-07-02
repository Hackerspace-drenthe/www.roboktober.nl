<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\ContentFormat;
use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = "Pagina's";
    protected static ?string $modelLabel = "Pagina";
    protected static ?string $pluralModelLabel = "Pagina's";
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Inhoud')
                    ->schema([
                        Forms\Components\TextInput::make('titel')
                            ->label('Titel')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set('slug', Str::slug((string) $state))),
                        Forms\Components\TextInput::make('slug')
                            ->label('URL slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(Page::class, 'slug', ignoreRecord: true),
                        Forms\Components\RichEditor::make('content')
                            ->label('Inhoud')
                            ->required()
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold', 'italic', 'underline',
                                'h2', 'h3',
                                'bulletList', 'orderedList', 'blockquote',
                                'link',
                            ]),
                        Forms\Components\Select::make('content_format')
                            ->label('Formaat')
                            ->options([
                                ContentFormat::Html->value => 'HTML (RichEditor)',
                                ContentFormat::Markdown->value => 'Markdown',
                            ])
                            ->default(ContentFormat::Html->value)
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Publicatie & SEO')
                    ->schema([
                        Forms\Components\Toggle::make('is_published')
                            ->label('Gepubliceerd')
                            ->default(false),
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Publicatiedatum'),
                        Forms\Components\KeyValue::make('seo')
                            ->label('SEO metadata')
                            ->keyLabel('Sleutel')
                            ->valueLabel('Waarde')
                            ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Gepubliceerd')
                    ->boolean(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Gepubliceerd op')
                    ->dateTime('d-m-Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Bijgewerkt')
                    ->dateTime('d-m-Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published')->label('Gepubliceerd'),
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
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}


