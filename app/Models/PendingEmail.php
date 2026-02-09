<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property array<string, mixed> $mailable_data
 */
class PendingEmail extends Model
{
    protected $fillable = [
        'mailable_class',
        'mailable_data',
        'to_email',
        'attempts',
        'last_error',
        'sent_at',
        'next_attempt_at',
    ];

    protected function casts(): array
    {
        return [
            'mailable_data' => 'array',
            'sent_at' => 'datetime',
            'next_attempt_at' => 'datetime',
        ];
    }

    /**
     * @param  Builder<PendingEmail>  $query
     * @return Builder<PendingEmail>
     */
    public function scopeUnsent(Builder $query): Builder
    {
        return $query->whereNull('sent_at')
            ->where('attempts', '<', 5)
            ->where(function (Builder $q): void {
                $q->whereNull('next_attempt_at')
                    ->orWhere('next_attempt_at', '<=', now());
            });
    }
}
