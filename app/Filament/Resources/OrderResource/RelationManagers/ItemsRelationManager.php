<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Order Items';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product_name')
                    ->label('Product')
                    ->description(fn ($record): ?string => $record->variant_label),

                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU'),

                Tables\Columns\TextColumn::make('quantity')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('unit_price')
                    ->label('Unit Price')
                    ->money('EUR'),

                Tables\Columns\TextColumn::make('line_total')
                    ->label('Total')
                    ->money('EUR'),
            ]);
    }

    public function isReadOnly(): bool
    {
        return true;
    }
}
