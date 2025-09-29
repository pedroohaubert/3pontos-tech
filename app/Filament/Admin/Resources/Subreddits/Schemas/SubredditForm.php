<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Subreddits\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

final class SubredditForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Subreddit Details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->rules(['alpha_dash'])
                            ->afterStateUpdated(function (string $state, callable $set): void {
                                $set('display_name', 'r/'.$state);
                            })
                            ->live(onBlur: true),

                        TextInput::make('display_name')
                            ->required()
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated(false),

                        Textarea::make('description')
                            ->maxLength(500)
                            ->rows(3)
                            ->placeholder('Describe what this subreddit is about...'),

                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Created by'),
                    ])
                    ->columns(2),
            ]);
    }
}
