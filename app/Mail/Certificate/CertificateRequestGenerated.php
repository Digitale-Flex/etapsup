<?php

namespace App\Mail\Certificate;

use App\Models\Certificate\CertificateRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CertificateRequestGenerated extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public CertificateRequest $certificateRequest,
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre certificat de logement est prêt',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.certificate-request.generated',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    public function build(): self
    {
        $certificate = $this->certificateRequest->getFirstMedia('certificate');
        if ($certificate && $certificate->getPath()) {
            $this->attach($certificate->getPath(), [
                'as' => $certificate->file_name,
                'mime' => $certificate->mime_type,
            ]);
        }

        // Attacher le contrat
        $contract = $this->certificateRequest->getFirstMedia('contract');
        if ($contract && $contract->getPath()) {
            $this->attach($contract->getPath(), [
                'as' => $contract->file_name,
                'mime' => $contract->mime_type,
            ]);
        }

        return $this;
    }
}
