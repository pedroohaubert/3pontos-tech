<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Posts\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

final class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Post Details')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('content')
                            ->required()
                            ->maxLength(10000)
                            ->rows(8)
                            ->columnSpanFull()
                            ->placeholder('Write your post content here...'),

                        Select::make('subreddit_id')
                            ->relationship('subreddit', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Subreddit'),

                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Author'),

                        Placeholder::make('score')
                            ->label('Current Score')
                            ->content(fn ($record) => $record?->score ?? 0),

                        Placeholder::make('created_at')
                            ->label('Created')
                            ->content(fn ($record) => $record?->created_at?->diffForHumans() ?? 'Not created yet'),
                    ])
                    ->columns(2),
            ]);
    }
}
