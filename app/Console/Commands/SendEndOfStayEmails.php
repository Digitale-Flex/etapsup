<?php

namespace App\Console\Commands;

use App\Mail\EndOfStayMail;
use App\Models\RealEstate\Reservation;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendEndOfStayEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:send-end-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoie les emails de fin de séjour';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $reservations = Reservation::query()
            ->whereDate('end_date', Carbon::yesterday()) // Envoi le lendemain de la fin
            ->whereDoesntHave('flags', function($query) {
                $query->where('name', 'end_email_sent');
            })
            ->get();

        foreach ($reservations as $reservation) {
            try {
                Mail::to($reservation->user->email)
                    ->send(new EndOfStayMail($reservation));

                $reservation->flag('end_email_sent');
                $this->info("Email envoyé pour la réservation #{$reservation->id}");

            } catch (\Exception $e) {
                $this->error("Erreur pour la réservation #{$reservation->id}: " . $e->getMessage());
                $reservation->flag('end_email_failed');
            }
        }
    }
}
