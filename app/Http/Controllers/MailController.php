<?php

namespace App\Http\Controllers;

use App\Mail\UserContactEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    

    /**
     * Send the email.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => ['required', 'exists:users,id'],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'cc_ids' => ['nullable', 'array'],
            'cc_ids.*' => ['exists:users,id'],
        ]);

        $receiver = User::findOrFail($validated['receiver_id']);
        $sender = Auth::user();

        // Get CC recipients if any
        $ccRecipients = [];
        if (!empty($validated['cc_ids'])) {
            $ccRecipients = User::whereIn('id', $validated['cc_ids'])->pluck('email')->toArray();
        }

        // Send email
        $mail = Mail::to($receiver->email);
        
        if (!empty($ccRecipients)) {
            $mail->cc($ccRecipients);
        }
        
        $mail->send(new UserContactEmail($sender, $validated['subject'], $validated['body']));

        return redirect()->route('dashboard')
            ->with('success', 'Email sent successfully to ' . $receiver->name . '!');
    }
}
