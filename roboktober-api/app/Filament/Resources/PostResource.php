<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\ContentFormat;
use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;
    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationLabel = 'Blog posts';
    protected static ?string $modelLabel = 'Post';
    protected static ?string $pluralModelLabel = 'Posts';
    protected static ?int $navigationSort = 1;

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
                            ->unique(Post::class, 'slug', ignoreRecord: true),
                        Forms\Components\Textarea::make('excerpt')
                            ->label('Samenvatting')
                            ->maxLength(320)
                            ->rows(2),
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
                        Forms\Components\Select::make('content_format')
                            ->label('Formaat')
                            ->options([
                                ContentFormat::Html->value => 'HTML (RichEditor)',
                                ContentFormat::Markdown->value => 'Markdown',
                            ])
                            ->default(ContentFormat::Html->value)
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Publicatie')
                    ->schema([
                        Forms\Components\Toggle::make('is_published')
                            ->label('Gepubliceerd')
                            ->default(false),
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Publicatiedatum')
                            ->nullable(),
                        Forms\Components\TextInput::make('categorie')
                            ->label('Categorie')
                            ->maxLength(100),
                        Forms\Components\TagsInput::make('tags')
                            ->label('Tags')
                            ->separator(','),
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
                Tables\Columns\TextColumn::make('categorie')
                    ->searchable()
                    ->badge(),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Gepubliceerd')
                    ->boolean(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Publicatiedatum')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Aangemaakt')
                    ->dateTime('d-m-Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published')->label('Gepubliceerd'),
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
            'index' => Pages\ListPosts::route('/'),
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


