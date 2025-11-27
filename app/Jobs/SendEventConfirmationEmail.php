<?php

namespace App\Jobs;

use App\Models\EventRegistration;
use App\Mail\EventConfirmationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

// Refonte: Story 1.1.1 - Event Confirmation Email Job
class SendEventConfirmationEmail implements ShouldQueue
{
    use Queueable;

    public $registration;

    /**
     * Create a new job instance.
     */
    public function __construct(EventRegistration $registration)
    {
        $this->registration = $registration;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Send the actual email using EtatSup template
            Mail::to($this->registration->email)->send(new EventConfirmationMail($this->registration));

            // Log successful sending
            Log::info('Event confirmation email sent successfully', [
                'registration_id' => $this->registration->id,
                'email' => $this->registration->email,
                'name' => $this->registration->name,
                'country' => $this->registration->country
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send event confirmation email', [
                'registration_id' => $this->registration->id,
                'error' => $e->getMessage()
            ]);

            throw $e; // Re-throw to trigger job failure and retry
        }
    }
}
