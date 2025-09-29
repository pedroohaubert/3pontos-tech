<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Subreddits\Pages;

use App\Filament\Admin\Resources\Subreddits\SubredditResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

final class EditSubreddit extends EditRecord
{
    protected static string $resource = SubredditResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
