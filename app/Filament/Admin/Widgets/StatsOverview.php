<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Subreddit;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

final class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::query()->count())
                ->description('Registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('Total Posts', Post::query()->count())
                ->description('Published posts')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),

            Stat::make('Total Comments', Comment::query()->count())
                ->description('User comments')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('warning'),

            Stat::make('Total Subreddits', Subreddit::query()->count())
                ->description('Active communities')
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('info'),

            Stat::make('Posts This Week', Post::query()->where('created_at', '>=', now()->subWeek())->count())
                ->description('Recent activity')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('New Users This Month', User::query()->where('created_at', '>=', now()->subMonth())->count())
                ->description('User growth')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('primary'),
        ];
    }
}
