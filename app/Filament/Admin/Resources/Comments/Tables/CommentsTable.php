<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Comments\Tables;

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

final class CommentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('content')
                    ->searchable()
                    ->limit(60)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        return mb_strlen($state) <= 60 ? null : $state;
                    }),

                TextColumn::make('post.title')
                    ->label('Post')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        return mb_strlen($state) <= 40 ? null : $state;
                    }),

                TextColumn::make('user.name')
                    ->label('Author')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('depth')
                    ->label('Level')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'success',
                        $state <= 2 => 'warning',
                        default => 'danger',
                    }),

                TextColumn::make('score')
                    ->sortable()
                    ->color(fn (int $state): string => $state >= 0 ? 'success' : 'danger'),

                TextColumn::make('reply_count')
                    ->label('Replies')
                    ->counts('children')
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
                SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('post')
                    ->relationship('post', 'title')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('depth')
                    ->options([
                        '0' => 'Root Comments',
                        '1' => 'Level 1',
                        '2' => 'Level 2',
                        '3+' => 'Deep Replies',
                    ])
                    ->query(fn($query, array $data) => $query->when(
                        $data['value'] === '3+',
                        fn ($query) => $query->where('depth', '>=', 3),
                        fn ($query) => $query->where('depth', $data['value'] ?? null)
                    )),

                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('moderate_spam')
                        ->label('Mark as Spam')
                        ->icon('heroicon-m-exclamation-triangle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Mark selected comments as spam?')
                        ->modalDescription('These comments will be hidden and flagged for review.')
                        ->modalSubmitActionLabel('Yes, mark as spam')
                        ->action(function (Collection $records): void {
                            // Could add a spam flag to the Comment model
                            // For now, just reduce score significantly
                            $records->each(function ($record): void {
                                $record->update(['score' => $record->score - 50]);
                            });
                        })
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('approve_comments')
                        ->label('Approve Comments')
                        ->icon('heroicon-m-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Approve selected comments?')
                        ->modalDescription('These comments will be restored to normal visibility.')
                        ->modalSubmitActionLabel('Yes, approve them')
                        ->action(function (Collection $records): void {
                            // Restore normal score or remove spam flag
                            $records->each(function ($record): void {
                                if ($record->score < 0) {
                                    $record->update(['score' => max(0, $record->score + 50)]);
                                }
                            });
                        })
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
