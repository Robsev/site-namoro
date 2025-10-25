<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailPreferencesController extends Controller
{
    /**
     * Show email preferences form.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('preferences.email', compact('user'));
    }

    /**
     * Update email preferences.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'email_notifications_enabled' => ['boolean'],
            'email_new_matches' => ['boolean'],
            'email_new_likes' => ['boolean'],
            'email_new_messages' => ['boolean'],
            'email_photo_approvals' => ['boolean'],
            'email_marketing' => ['boolean'],
        ]);

        $user->update([
            'email_notifications_enabled' => $request->boolean('email_notifications_enabled'),
            'email_new_matches' => $request->boolean('email_new_matches'),
            'email_new_likes' => $request->boolean('email_new_likes'),
            'email_new_messages' => $request->boolean('email_new_messages'),
            'email_photo_approvals' => $request->boolean('email_photo_approvals'),
            'email_marketing' => $request->boolean('email_marketing'),
        ]);

        return redirect()->route('email-preferences.edit')
            ->with('success', 'Preferências de e-mail atualizadas com sucesso!');
    }
}