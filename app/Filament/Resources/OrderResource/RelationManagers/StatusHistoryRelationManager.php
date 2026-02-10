<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use App\Enums\OrderStatus;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class StatusHistoryRelationManager extends RelationManager
{
    protected static string $relationship = 'statusHistory';

    protected static ?string $title = 'Status History';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('from_status')
                    ->label('From')
                    ->badge()
                    ->color(fn (?string $state): string => $state ? OrderStatus::from($state)->color() : 'gray')
                    ->formatStateUsing(fn (?string $state): string => $state ? OrderStatus::from($state)->label() : '—'),

                Tables\Columns\TextColumn::make('to_status')
                    ->label('To')
                    ->badge()
                    ->color(fn (string $state): string => OrderStatus::from($state)->color())
                    ->formatStateUsing(fn (string $state): string => OrderStatus::from($state)->label()),

                Tables\Columns\TextColumn::make('notes')
                    ->limit(50)
                    ->tooltip(fn ($record): ?string => $record->notes),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public function isReadOnly(): bool
    {
        return true;
    }
}
