<?php

namespace App\Jobs\RealEstate;

use App\Models\RealEstate\Reservation;
use App\Services\ContractService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateContractJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3; // Ajout du nombre de tentatives

    public $maxExceptions = 2; // Limite d'exceptions

    public function __construct(
        public int $reservationId
    ) {
        if (! is_int($this->reservationId)) {
            throw new \InvalidArgumentException('reservationId must be an integer');
        }
    }

    public function handle(ContractService $contractService): void
    {
       if (!app()->environment('production') && !$this->isLibreOfficeReady()) {
            $this->fail(new \RuntimeException('LibreOffice not ready'));
            return;
        }

        $reservation = Reservation::with([
            'user',
            'media' => fn($q) => $q->where('collection_name', 'contract'),
            'property',
        ])->findOrFail($this->reservationId);

        try {
            DB::transaction(function () use ($contractService, $reservation) {
                $contractService->generateContract($reservation);
                $reservation->flag('pdf_generated');
            });

            Log::debug($reservation->refresh()->hasMedia('contract'));
            // Vérification post-génération avant dispatch
            if ($reservation->refresh()->hasMedia('contract')) {
                SendContractGeneratedEmailJob::dispatch($reservation->id)
                    ->onQueue('emails')
                    ->delay(now()->addSeconds(30))
                    ->afterCommit();
            }

            if (!$reservation->hasMedia('contract')) {
                Log::debug($reservation->refresh()->hasMedia('Contract media missing after generation'));
                throw new \RuntimeException('Contract media missing after generation');
            }

        } catch (\Exception $e) {
            // Rollback automatique via transaction
            $this->handleFailure($reservation, $e);
        }
    }

    private function handleFailure($reservation, $e): void
    {
        $reservation->flag('pdf_generation_failed');
        Log::error('Contract gen failed', ['error' => $e->getMessage()]);
        $this->fail($e);
    }

    public function failed(\Throwable $exception): void
    {
        Log::critical("Contract generation failed for reservation {$this->reservationId}: ".$exception->getMessage(), [
            'trace' => $exception->getTraceAsString(),
        ]);
    }

    private function isLibreOfficeReady(): bool
    {
        try {
            $testFile = tempnam(sys_get_temp_dir(), 'test_');
            file_put_contents($testFile, 'test');
            exec("libreoffice --headless --convert-to pdf $testFile", $output, $code);
            return $code === 0;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
