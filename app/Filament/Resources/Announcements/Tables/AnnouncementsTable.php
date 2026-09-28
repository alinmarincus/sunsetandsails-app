<?php

namespace App\Filament\Resources\Announcements\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AnnouncementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('pinned')->label('')->boolean()
                    ->trueIcon('heroicon-s-star')
                    ->falseIcon('heroicon-o-minus')
                    ->color('warning'),

                TextColumn::make('title')
                    ->label('Titlu')
                    ->getStateUsing(fn ($record) => $record->getTranslation('title', 'ro'))
                    ->weight('bold')->wrap(),

                TextColumn::make('trip.slug')
                    ->label('Croazieră')
                    ->placeholder('Toți membrii')
                    ->badge(),

                TextColumn::make('published_at')
                    ->label('Publicat')
                    ->dateTime('d.m.Y H:i')
                    ->placeholder('Ciornă')
                    ->sortable(),
            ])
            ->defaultSort('published_at', 'desc')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
