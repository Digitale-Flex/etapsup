<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Find or create a registration with the specific email
$email = 'bashala.gaspard@gmail.com';

$registration = App\Models\EventRegistration::where('email', $email)->first();

if (!$registration) {
    // Create a test registration with this email
    $registration = App\Models\EventRegistration::create([
        'name' => 'Gaspard BASHALA',
        'email' => $email,
        'country' => 'CD',
        'phone' => '+243123456789',
        'study_level' => 'Master',
        'ip_address' => '192.168.1.1',
        'user_agent' => 'Test Browser',
        'registered_at' => now()
    ]);
    echo "Created new registration for testing.\n";
} else {
    echo "Using existing registration.\n";
}

echo "Test with registration:\n";
echo "- ID: {$registration->id}\n";
echo "- Name: {$registration->name}\n";
echo "- Email: {$registration->email}\n";
echo "- Country: {$registration->country}\n\n";

// Dispatch email job
echo "Sending email to: {$email}\n";
App\Jobs\SendEventConfirmationEmail::dispatch($registration);
echo "Email job dispatched successfully!\n";
echo "Please check ElasticEmail logs and the Gmail inbox.\n";