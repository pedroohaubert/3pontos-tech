<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

final class UserActivity extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Recent User Activity';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->withCount(['posts', 'comments'])
                    ->orderByRaw('(posts_count + comments_count) DESC')
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('name')
                    ->searchable(),

                TextColumn::make('email')
                    ->searchable(),

                TextColumn::make('role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'user' => 'success',
                        default => 'gray',
                    }),

                IconColumn::make('email_verified_at')
                    ->label('Verified')
                    ->boolean(),

                TextColumn::make('posts_count')
                    ->label('Posts'),

                TextColumn::make('comments_count')
                    ->label('Comments'),

                TextColumn::make('created_at')
                    ->label('Joined')
                    ->dateTime()
                    ->since(),
            ])
            ->actions([
                Action::make('view_posts')
                    ->url(fn (User $record): string => '/admin/posts?tableFilters[user][value]=' . $record->name)
                    ->icon('heroicon-m-document-text')
                    ->label('Posts'),

                Action::make('view_comments')
                    ->url(fn (User $record): string => '/admin/comments?tableFilters[user][value]=' . $record->name)
                    ->icon('heroicon-m-chat-bubble-left-right')
                    ->label('Comments'),

                Action::make('edit')
                    ->url(fn (User $record): string => sprintf('/admin/users/%d/edit', $record->id))
                    ->icon('heroicon-m-pencil-square'),
            ])
            ->emptyStateHeading('No users yet')
            ->emptyStateDescription('Users will appear here once they register.');
    }
}
