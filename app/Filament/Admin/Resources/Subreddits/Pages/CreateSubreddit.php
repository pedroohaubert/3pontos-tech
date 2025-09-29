<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Subreddits\Pages;

use App\Filament\Admin\Resources\Subreddits\SubredditResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateSubreddit extends CreateRecord
{
    protected static string $resource = SubredditResource::class;
}
