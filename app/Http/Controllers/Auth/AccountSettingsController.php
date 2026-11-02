<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\BrandingSetting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AccountSettingsController extends Controller
{
    public function edit()
    {
        return view('site.account', [
            'branding' => BrandingSetting::current(),
        ]);
    }

    public function updateEmail(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->getKey())],
            'email_current_password' => ['required', 'current_password'],
        ]);

        if ($user->email !== $validated['email']) {
            $user->email = $validated['email'];
            $user->email_verified_at = null;
            $user->save();
        }

        return back()->with('success', 'Je e-mailadres is bijgewerkt.');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $request->user()->update(['password' => $validated['password']]);

        return back()->with('success', 'Je wachtwoord is bijgewerkt.');
    }
}
