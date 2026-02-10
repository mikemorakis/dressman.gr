<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CustomerResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Orders';

    protected static ?int $navigationSort = 21;

    protected static ?string $navigationLabel = 'Customers';

    protected static ?string $modelLabel = 'Customer';

    protected static ?string $pluralModelLabel = 'Customers';

    protected static ?string $slug = 'customers';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('is_admin', false);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->withCount('orders')
                ->withSum('orders', 'total')
                ->withMax('orders', 'created_at')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('orders_count')
                    ->label('Orders')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('orders_sum_total')
                    ->label('Total Spent')
                    ->money('EUR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('orders_max_created_at')
                    ->label('Last Order')
                    ->dateTime('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registered')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('orders_count', 'desc')
            ->actions([
                Tables\Actions\Action::make('viewOrders')
                    ->label('Orders')
                    ->icon('heroicon-o-shopping-bag')
                    ->url(fn (User $record): string => OrderResource::getUrl('index', [
                        'tableFilters[email][value]' => $record->email,
                    ])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
        ];
    }
}
