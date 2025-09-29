<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Comments\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

final class CommentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Comment Details')
                    ->schema([
                        Textarea::make('content')
                            ->required()
                            ->maxLength(2000)
                            ->rows(4)
                            ->columnSpanFull(),

                        Select::make('post_id')
                            ->relationship('post', 'title')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Post'),

                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Author'),

                        Select::make('parent_id')
                            ->relationship('parent', 'content')
                            ->searchable()
                            ->preload()
                            ->label('Parent Comment (Reply)')
                            ->placeholder('Leave empty for root comment'),

                        Placeholder::make('depth')
                            ->label('Depth Level')
                            ->content(fn ($record) => $record?->depth ?? 0),

                        Placeholder::make('score')
                            ->label('Current Score')
                            ->content(fn ($record) => $record?->score ?? 0),

                        Placeholder::make('created_at')
                            ->label('Posted')
                            ->content(fn ($record) => $record?->created_at?->diffForHumans() ?? 'Not posted yet'),
                    ])
                    ->columns(2),
            ]);
    }
}
