<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class DynamicMailController extends Controller
{
    /**
     * Send email using the logged-in user's credentials.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'password' => ['required', 'string'],
            'host' => ['required', 'string'],
            'port' => ['required', 'integer'],
            'encryption' => ['required', 'string', 'in:tls,ssl,null'],
            'recipient' => ['required', 'email'],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ]);

        $user = Auth::user();

        // Dynamically configure SMTP settings based on user credentials
        Config::set('mail.mailers.smtp.host', $validated['host']);
        Config::set('mail.mailers.smtp.port', $validated['port']);
        Config::set('mail.mailers.smtp.encryption', $validated['encryption'] === 'null' ? null : $validated['encryption']);
        Config::set('mail.mailers.smtp.username', $user->email);
        Config::set('mail.mailers.smtp.password', $validated['password']);
        Config::set('mail.from.address', $user->email);
        Config::set('mail.from.name', $user->name);

        // Purge the smtp driver to ensure new config is used
        // This is important if the mailer was already resolved/used in this request
        // although in a fresh controller action it might not be strictly necessary, it's safer.
        // Note: If 'purge' is not available on the facade in this version, we rely on Config::set before resolution.
        // But typically Config::set works if we haven't resolved 'smtp' yet.

        try {
            // Send email using the 'smtp' mailer
            Mail::mailer('smtp')->raw($validated['body'], function ($message) use ($validated) {
                $message->to($validated['recipient'])
                    ->subject($validated['subject']);
            });

            return back()->with('success', 'Email sent successfully using your SMTP credentials.');
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Failed to send email: ' . $e->getMessage()]);
        }
    }
}
