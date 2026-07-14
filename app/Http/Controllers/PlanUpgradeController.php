<?php

namespace App\Http\Controllers;

use App\Mail\PlanUpgradeRequested;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PlanUpgradeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        $recipients = Setting::notificationRecipients();
        if (!empty($recipients)) {
            try {
                Mail::to($recipients)->send(new PlanUpgradeRequested(auth()->user(), $request->message));
            } catch (\Exception $e) {
                \Log::error('Failed to send plan upgrade request email', ['error' => $e->getMessage()]);
            }
        }

        return back()->with('success', __('Zahtjev je poslan! Javićemo vam se uskoro.'));
    }
}
