<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Subreddits;

use App\Filament\Admin\Resources\Subreddits\Pages\CreateSubreddit;
use App\Filament\Admin\Resources\Subreddits\Pages\EditSubreddit;
use App\Filament\Admin\Resources\Subreddits\Pages\ListSubreddits;
use App\Filament\Admin\Resources\Subreddits\Schemas\SubredditForm;
use App\Filament\Admin\Resources\Subreddits\Tables\SubredditsTable;
use App\Models\Subreddit;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

final class SubredditResource extends Resource
{
    protected static ?string $model = Subreddit::class;

    public static function form(Schema $schema): Schema
    {
        return SubredditForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SubredditsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSubreddits::route('/'),
            'create' => CreateSubreddit::route('/create'),
            'edit' => EditSubreddit::route('/{record}/edit'),
        ];
    }
}
