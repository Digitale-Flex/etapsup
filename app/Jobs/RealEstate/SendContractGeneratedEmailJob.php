<?php

namespace App\Jobs\RealEstate;

use App\Mail\ContractGenerated;
use App\Models\RealEstate\Reservation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendContractGeneratedEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $maxExceptions = 2;

    public function __construct(
        public int $reservationId // ✅ Type corrigé en int
    ) {
        if (! is_int($this->reservationId)) {
            throw new \InvalidArgumentException('reservationId must be an integer');
        }
    }

    public function handle(): void
    {
        $reservation = Reservation::with(['user', 'media'])
            ->findOrFail($this->reservationId);

        $this->validatePrerequisites($reservation);

        try {
            Mail::to($reservation->user->email)
                ->sendNow(new ContractGenerated($reservation)); // Envoi asynchrone

            $reservation->flag('email_sent');
            $reservation->unflag('email_failed');

        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            $this->handleFailure($reservation, $e);
            throw $e; // Déclenche la gestion de retry automatique
        }
    }

    private function validatePrerequisites(Reservation $reservation): void
    {
        if (! $reservation->hasFlag('pdf_generated')) {
            throw new \LogicException('Contract not generated');
        }

        if (! $reservation->getFirstMedia('contract')) {
            throw new \LogicException('Contract file missing');
        }
    }

    private function handleFailure(Reservation $reservation, \Throwable $e): void
    {
        $reservation->flag('email_failed');
        $reservation->unflag('email_sent');

        Log::error("Email send failed - Reservation {$reservation->id}", [
            'error' => $e->getMessage(),
            'user' => $reservation->user->id,
            'trace' => $e->getTraceAsString(),
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::critical("Email job failed after {$this->tries} attempts", [
            'reservation_id' => $this->reservationId,
            'exception' => (array) $exception,
        ]);

        // Notification supplémentaire aux admins
        /* Mail::to(config('app.admin_email'))
             ->send(new JobFailedNotification($this->reservationId, $exception)); */
    }
}
