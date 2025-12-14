<?php

use App\Mail\UserContactEmail;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

// Get the first user to use as sender
$user = User::first();

if (!$user) {
    echo "No users found in database. Cannot test email.\n";
    exit;
}

// UserContactEmail(__construct($senderUser, $subject, $messageContent))
echo "Dispatching UserContactEmail with user: " . $user->name . "...\n";
Mail::to('recipient@example.com')->send(new UserContactEmail($user, 'Test Subject', 'Test Message Content'));

echo "Email dispatched. Checking jobs table count...\n";
$count = \Illuminate\Support\Facades\DB::table('jobs')->count();
echo "Jobs in queue: " . $count . "\n";
