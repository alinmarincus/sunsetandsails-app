<?php

namespace App\Filament\Resources\ContentLists\Pages;

use App\Filament\Resources\ContentLists\ContentListResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListContentLists extends ListRecords
{
    protected static string $resource = ContentListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
