<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Users\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

final class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User Information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Select::make('role')
                            ->options([
                                'user' => 'User',
                                'admin' => 'Admin',
                            ])
                            ->required()
                            ->default('user'),

                        Placeholder::make('email_verified_at')
                            ->label('Email Verified')
                            ->content(fn ($record) => $record?->email_verified_at ? 'Yes' : 'No'),

                        Placeholder::make('posts_count')
                            ->label('Total Posts')
                            ->content(fn ($record) => $record?->posts()->count() ?? 0),

                        Placeholder::make('comments_count')
                            ->label('Total Comments')
                            ->content(fn ($record) => $record?->comments()->count() ?? 0),

                        Placeholder::make('created_at')
                            ->label('Joined')
                            ->content(fn ($record) => $record?->created_at?->diffForHumans() ?? 'Not joined yet'),
                    ])
                    ->columns(2),
            ]);
    }
}
