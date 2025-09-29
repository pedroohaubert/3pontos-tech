<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Comments\Pages;

use App\Filament\Admin\Resources\Comments\CommentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

final class ListComments extends ListRecords
{
    protected static string $resource = CommentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
