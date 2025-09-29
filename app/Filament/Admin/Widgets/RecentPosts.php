<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Models\Post;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

final class RecentPosts extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Recent Posts';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Post::query()
                    ->with(['user', 'subreddit'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('title')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        return mb_strlen($state) <= 50 ? null : $state;
                    }),

                TextColumn::make('subreddit.name')
                    ->label('Subreddit')
                    ->badge(),

                TextColumn::make('user.name')
                    ->label('Author'),

                TextColumn::make('score')
                    ->color(fn (int $state): string => $state >= 0 ? 'success' : 'danger'),

                TextColumn::make('comments_count')
                    ->label('Comments')
                    ->counts('comments'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->since(),
            ])
            ->actions([
                Action::make('view')
                    ->url(fn (Post $record): string => route('posts.show', $record))
                    ->icon('heroicon-m-eye')
                    ->openUrlInNewTab(),

                Action::make('edit')
                    ->url(fn (Post $record): string => sprintf('/admin/posts/%d/edit', $record->id))
                    ->icon('heroicon-m-pencil-square'),
            ])
            ->emptyStateHeading('No posts yet')
            ->emptyStateDescription('Posts will appear here once they are created.');
    }
}
