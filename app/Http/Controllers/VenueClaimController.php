<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use Illuminate\Http\Request;

class VenueClaimController extends Controller
{
    /**
     * List admin-created, unclaimed venues whose contact email matches the
     * current user's email - these can be linked to their account.
     */
    public function index(Request $request)
    {
        $venues = Venue::whereNull('user_id')
            ->whereRaw('LOWER(contact_email) = ?', [mb_strtolower($request->user()->email)])
            ->with('city')
            ->get();

        return view('venues.claim', compact('venues'));
    }

    /**
     * Claim an unclaimed venue whose contact email matches the current
     * user's email.
     */
    public function store(Request $request, Venue $venue)
    {
        abort_unless(
            $venue->user_id === null
                && $venue->contact_email !== null
                && strcasecmp($venue->contact_email, $request->user()->email) === 0,
            403
        );

        $venue->update(['user_id' => $request->user()->id]);

        return redirect()->route('venues.edit', $venue)->with('success', 'Teren je povezan sa vašim nalogom.');
    }
}
