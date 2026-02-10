<?php

namespace App\Filament\Resources;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Orders';

    protected static ?int $navigationSort = 20;

    protected static ?string $recordTitleAttribute = 'order_number';

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', OrderStatus::Pending)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Order')
                    ->tabs([
                        self::orderDetailsTab(),
                        self::customerTab(),
                        self::notesTab(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    private static function orderDetailsTab(): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make('Order Details')
            ->icon('heroicon-o-document-text')
            ->schema([
                Forms\Components\Section::make()
                    ->columns(4)
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->disabled(),
                        Forms\Components\TextInput::make('status')
                            ->disabled()
                            ->formatStateUsing(fn (Order $record): string => $record->status->label()),
                        Forms\Components\TextInput::make('payment_status')
                            ->disabled()
                            ->formatStateUsing(fn (Order $record): string => $record->payment_status->label()),
                        Forms\Components\TextInput::make('payment_method')
                            ->label('Payment Method')
                            ->disabled()
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'stripe' => 'Credit/Debit Card',
                                'bank_transfer' => 'Bank Transfer',
                                'store_pickup' => 'Store Pickup',
                                default => $state ?? 'N/A',
                            }),
                    ]),

                Forms\Components\Section::make('Financials')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->prefix('EUR')
                            ->disabled(),
                        Forms\Components\TextInput::make('vat_amount')
                            ->label('VAT')
                            ->prefix('EUR')
                            ->disabled(),
                        Forms\Components\TextInput::make('shipping_amount')
                            ->label('Shipping')
                            ->prefix('EUR')
                            ->disabled(),
                        Forms\Components\TextInput::make('total')
                            ->prefix('EUR')
                            ->disabled(),
                        Forms\Components\TextInput::make('currency')
                            ->disabled(),
                        Forms\Components\TextInput::make('vat_rate')
                            ->suffix('%')
                            ->disabled(),
                    ]),

                Forms\Components\Section::make('Shipping Method')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('shipping_label')
                            ->label('Method')
                            ->disabled(),
                        Forms\Components\TextInput::make('shipping_zone_code')
                            ->label('Zone')
                            ->disabled(),
                    ]),

                Forms\Components\Section::make('Dates')
                    ->columns(3)
                    ->schema([
                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Placed at')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('paid_at')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('shipped_at')
                            ->disabled(),
                    ]),
            ]);
    }

    private static function customerTab(): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make('Customer & Addresses')
            ->icon('heroicon-o-user')
            ->schema([
                Forms\Components\Section::make('Customer')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->disabled(),
                        Forms\Components\TextInput::make('phone')
                            ->disabled(),
                        Forms\Components\TextInput::make('user.name')
                            ->label('Account')
                            ->disabled()
                            ->default('Guest'),
                    ]),

                Forms\Components\Section::make('Billing Address')
                    ->schema([
                        Forms\Components\KeyValue::make('billing_address')
                            ->label('')
                            ->disabled(),
                    ]),

                Forms\Components\Section::make('Shipping Address')
                    ->schema([
                        Forms\Components\KeyValue::make('shipping_address')
                            ->label('')
                            ->disabled(),
                    ]),
            ]);
    }

    private static function notesTab(): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make('Notes')
            ->icon('heroicon-o-chat-bubble-left-ellipsis')
            ->schema([
                Forms\Components\Textarea::make('notes')
                    ->label('')
                    ->rows(6)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('user')->withCount('items'))
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Account')
                    ->default('Guest')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (OrderStatus $state): string => $state->color())
                    ->formatStateUsing(fn (OrderStatus $state): string => $state->label()),

                Tables\Columns\TextColumn::make('payment_status')
                    ->badge()
                    ->color(fn (PaymentStatus $state): string => $state->color())
                    ->formatStateUsing(fn (PaymentStatus $state): string => $state->label()),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Payment')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'stripe' => 'success',
                        'bank_transfer' => 'warning',
                        'store_pickup' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'stripe' => 'Card',
                        'bank_transfer' => 'Bank',
                        'store_pickup' => 'Pickup',
                        default => $state ?? 'N/A',
                    }),

                Tables\Columns\TextColumn::make('items_count')
                    ->label('Items')
                    ->counts('items')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total')
                    ->money('EUR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(OrderStatus::class),

                Tables\Filters\SelectFilter::make('payment_status')
                    ->options(PaymentStatus::class),

                Tables\Filters\SelectFilter::make('payment_method')
                    ->options([
                        'stripe' => 'Card',
                        'bank_transfer' => 'Bank Transfer',
                        'store_pickup' => 'Store Pickup',
                    ]),

                Tables\Filters\TernaryFilter::make('guest')
                    ->label('Customer type')
                    ->placeholder('All')
                    ->trueLabel('Guest only')
                    ->falseLabel('Registered only')
                    ->queries(
                        true: fn ($query) => $query->whereNull('user_id'),
                        false: fn ($query) => $query->whereNotNull('user_id'),
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('changeStatus')
                    ->icon('heroicon-o-arrow-path')
                    ->color('primary')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('New Status')
                            ->options(OrderStatus::class)
                            ->required(),
                        Forms\Components\Textarea::make('notes')
                            ->rows(3),
                    ])
                    ->action(function (Order $record, array $data): void {
                        $record->transitionTo(
                            OrderStatus::from($data['status']),
                            $data['notes'] ?? null
                        );

                        Notification::make()
                            ->success()
                            ->title('Order status updated')
                            ->send();
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
            RelationManagers\StatusHistoryRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }
}
