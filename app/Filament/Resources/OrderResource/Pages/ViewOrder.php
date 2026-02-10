<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('changeStatus')
                ->label('Change Status')
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
                ->action(function (array $data): void {
                    /** @var Order $record */
                    $record = $this->getRecord();
                    $record->transitionTo(
                        OrderStatus::from($data['status']),
                        $data['notes'] ?? null
                    );

                    Notification::make()
                        ->success()
                        ->title('Order status updated')
                        ->send();

                    $this->refreshFormData(['status']);
                }),
        ];
    }
}
