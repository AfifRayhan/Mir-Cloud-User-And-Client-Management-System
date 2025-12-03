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
     * Show the form for composing a new email.
     */
    public function create()
    {
        // Get all users except the current user
        $users = User::where('id', '!=', Auth::id())
            ->orderBy('name')
            ->get();

        return view('mail.create', compact('users'));
    }

    /**
     * Send the email.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => ['required', 'exists:users,id'],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ]);

        $receiver = User::findOrFail($validated['receiver_id']);
        $sender = Auth::user();

        // Send email
        Mail::to($receiver->email)->send(new UserContactEmail($sender, $validated['subject'], $validated['body']));

        return redirect()->route('dashboard')
            ->with('success', 'Email sent successfully to ' . $receiver->name . '!');
    }
}
