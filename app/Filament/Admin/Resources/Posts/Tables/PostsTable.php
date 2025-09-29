<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Posts\Tables;

use App\Models\Post;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

final class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        return mb_strlen($state) <= 50 ? null : $state;
                    }),

                TextColumn::make('subreddit.name')
                    ->label('Subreddit')
                    ->searchable()
                    ->sortable()
                    ->badge(),

                TextColumn::make('user.name')
                    ->label('Author')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('score')
                    ->sortable()
                    ->color(fn (int $state): string => $state >= 0 ? 'success' : 'danger'),

                TextColumn::make('comments_count')
                    ->label('Comments')
                    ->counts('comments')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('subreddit')
                    ->relationship('subreddit', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('feature')
                        ->label('Feature Posts')
                        ->icon('heroicon-m-star')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Feature selected posts?')
                        ->modalDescription('This will highlight these posts on the homepage.')
                        ->modalSubmitActionLabel('Yes, feature them')
                        ->action(function (Collection $records): void {
                            $records->each(function (Post $record): void {
                                // Add featured flag to Post model if needed
                                // For now, just update the score to make them more visible
                                $record->increment('score', 10);
                            });
                        }),

                    BulkAction::make('unfeature')
                        ->label('Unfeature Posts')
                        ->icon('heroicon-m-x-mark')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->modalHeading('Unfeature selected posts?')
                        ->modalDescription('This will remove highlighting from these posts.')
                        ->modalSubmitActionLabel('Yes, unfeature them')
                        ->action(function (Collection $records): void {
                            $records->each(function (Post $record): void {
                                // Remove featured flag or reduce score
                                $record->decrement('score', 10);
                            });
                        }),

                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
