<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get latest registration
$registration = App\Models\EventRegistration::latest()->first();

if ($registration) {
    echo "Test with registration:\n";
    echo "- ID: {$registration->id}\n";
    echo "- Name: {$registration->name}\n";
    echo "- Email: {$registration->email}\n";
    echo "- Country: {$registration->country}\n\n";

    // Dispatch email job
    echo "Sending email...\n";
    App\Jobs\SendEventConfirmationEmail::dispatch($registration);
    echo "Email job dispatched successfully!\n";

} else {
    echo "No registrations found.\n";
}