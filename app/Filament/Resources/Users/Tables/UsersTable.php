<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar_path')->label('')->circular()
                    ->defaultImageUrl(asset('images/avatar-placeholder.svg')),

                TextColumn::make('name')->label('Nume')->searchable()->sortable()->weight('bold'),
                TextColumn::make('email')->label('Email')->searchable()->copyable(),
                TextColumn::make('phone')->label('Telefon')->toggleable(),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => $state === 'member' ? 'Membru' : 'Înscris')
                    ->color(fn (string $state) => $state === 'member' ? 'success' : 'gray'),

                TextColumn::make('trips_count')->label('Croaziere')->counts('trips')->alignCenter(),

                IconColumn::make('is_admin')->label('Admin')->boolean()->toggleable(),

                TextColumn::make('joined_club_at')->label('Înscris')->date('d.m.Y')->sortable(),
            ])
            ->defaultSort('joined_club_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options(['prospect' => 'Înscris', 'member' => 'Membru']),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    // Nu-ți poți șterge propriul cont din panou
                    ->hidden(fn ($record) => $record->id === auth()->id())
                    ->modalDescription('Se șterg și înscrierile lui la croaziere, și bifele de bagaj.'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
