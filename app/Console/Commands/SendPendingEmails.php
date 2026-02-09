<?php

namespace App\Console\Commands;

use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use App\Models\PendingEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendPendingEmails extends Command
{
    protected $signature = 'emails:send-pending';

    protected $description = 'Send pending emails that failed on first attempt';

    public function handle(): int
    {
        $pending = PendingEmail::unsent()->get();

        if ($pending->isEmpty()) {
            $this->info('No pending emails to send.');

            return self::SUCCESS;
        }

        $sent = 0;

        foreach ($pending as $email) {
            try {
                $mailable = $this->buildMailable($email);

                if (! $mailable) {
                    $newAttempts = $email->attempts + 1;
                    $delay = $newAttempts < 5 ? (int) pow(2, $newAttempts) : null;

                    $email->update([
                        'attempts' => $newAttempts,
                        'last_error' => 'Could not build mailable',
                        'next_attempt_at' => $delay ? now()->addMinutes($delay) : null,
                    ]);

                    continue;
                }

                Mail::to($email->to_email)->send($mailable);

                $email->update(['sent_at' => now()]);

                // Update confirmation_sent_at on the order if applicable
                if ($email->mailable_class === OrderConfirmationMail::class) {
                    $orderId = $email->mailable_data['order_id'] ?? null;
                    if ($orderId) {
                        Order::where('id', $orderId)
                            ->whereNull('confirmation_sent_at')
                            ->update(['confirmation_sent_at' => now()]);
                    }
                }

                $sent++;
            } catch (\Throwable $e) {
                $newAttempts = $email->attempts + 1;
                $delay = $newAttempts < 5 ? (int) pow(2, $newAttempts) : null;

                $email->update([
                    'attempts' => $newAttempts,
                    'last_error' => substr($e->getMessage(), 0, 500),
                    'next_attempt_at' => $delay ? now()->addMinutes($delay) : null,
                ]);
            }
        }

        $this->info("Sent {$sent} of {$pending->count()} pending email(s).");

        return self::SUCCESS;
    }

    private function buildMailable(PendingEmail $email): ?\Illuminate\Mail\Mailable
    {
        if ($email->mailable_class === OrderConfirmationMail::class) {
            $orderId = $email->mailable_data['order_id'] ?? null;
            $order = $orderId ? Order::with('items')->find($orderId) : null;

            if (! $order) {
                return null;
            }

            return new OrderConfirmationMail($order);
        }

        return null;
    }
}
