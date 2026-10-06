<?php

namespace App\Filament\Resources\ContentLists\Pages;

use App\Filament\Resources\ContentLists\ContentListResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditContentList extends EditRecord
{
    protected static string $resource = ContentListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
