<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Push-notification device token registration. A mobile app calls `store`
 * after obtaining an FCM/APNs token (on login and whenever the OS reissues
 * one), and `destroy` on logout so a signed-out device stops receiving
 * notifications meant for that user.
 */
class DeviceTokenController extends Controller
{
    use ApiResponses;

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string', 'max:255'],
            'platform' => ['required', 'in:ios,android,web'],
        ]);

        // A token belongs to one physical device - if it was previously
        // registered under a different account (e.g. a shared/reset device,
        // or a different user logging in on the same phone), re-point it
        // to the current user instead of erroring on the unique constraint.
        $deviceToken = DeviceToken::updateOrCreate(
            ['token' => $validated['token']],
            ['user_id' => $request->user()->id, 'platform' => $validated['platform']]
        );

        return $this->ok(['id' => $deviceToken->id], 'Device token registered');
    }

    public function destroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
        ]);

        $request->user()->deviceTokens()->where('token', $validated['token'])->delete();

        return $this->ok(null, 'Device token removed');
    }
}
