<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Users\Tables;

use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

final class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'user' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),

                IconColumn::make('email_verified_at')
                    ->label('Verified')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('posts_count')
                    ->label('Posts')
                    ->counts('posts')
                    ->sortable(),

                TextColumn::make('comments_count')
                    ->label('Comments')
                    ->counts('comments')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Joined')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->options([
                        'user' => 'User',
                        'admin' => 'Admin',
                    ]),

                SelectFilter::make('email_verified_at')
                    ->label('Email Verification')
                    ->options([
                        'verified' => 'Verified',
                        'unverified' => 'Unverified',
                    ])
                    ->query(fn($query, array $data) => $query->when(
                        $data['value'] === 'verified',
                        fn ($query) => $query->whereNotNull('email_verified_at'),
                        fn ($query) => $query->whereNull('email_verified_at')
                    )),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('promote_to_admin')
                        ->label('Promote to Admin')
                        ->icon('heroicon-m-shield-check')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Promote selected users to admin?')
                        ->modalDescription('These users will have full administrative access.')
                        ->modalSubmitActionLabel('Yes, promote them')
                        ->action(function (Collection $records): void {
                            $records->each(function ($record): void {
                                $record->update(['role' => 'admin']);
                            });
                        })
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('demote_to_user')
                        ->label('Demote to User')
                        ->icon('heroicon-m-user-minus')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Demote selected admins to regular users?')
                        ->modalDescription('These users will lose administrative access.')
                        ->modalSubmitActionLabel('Yes, demote them')
                        ->action(function (Collection $records): void {
                            $records->each(function ($record): void {
                                if ($record->role === 'admin') {
                                    $record->update(['role' => 'user']);
                                }
                            });
                        })
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
