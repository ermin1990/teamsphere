<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Verifies Firebase Authentication ID tokens (the JWT the Firebase JS SDK
 * hands back after "Sign in with Google") directly against Google's public
 * signing certs - no Firebase Admin SDK / service account needed, since we
 * only need to verify a token, not manage users on Firebase's side.
 */
class FirebaseAuthService
{
    private const CERTS_URL = 'https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com';

    public function __construct(private readonly ?string $projectId)
    {
    }

    /**
     * Verify a Firebase ID token and return its decoded claims (email,
     * email_verified, name, picture, sub/uid, ...), or null if the token
     * is invalid, expired, or issued for a different Firebase project.
     */
    public function verify(string $idToken): ?array
    {
        if (empty($this->projectId)) {
            Log::warning('Firebase project_id is not configured; cannot verify ID token.');
            return null;
        }

        try {
            $keys = [];
            foreach ($this->certificates() as $kid => $cert) {
                $keys[$kid] = new Key($this->publicKeyFromCertificate($cert), 'RS256');
            }

            $payload = (array) JWT::decode($idToken, $keys);

            if (($payload['aud'] ?? null) !== $this->projectId) {
                return null;
            }

            if (($payload['iss'] ?? null) !== "https://securetoken.google.com/{$this->projectId}") {
                return null;
            }

            if (empty($payload['sub'])) {
                return null;
            }

            return $payload;
        } catch (\Throwable $e) {
            Log::warning('Firebase ID token verification failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    private function certificates(): array
    {
        return Cache::remember('firebase_google_certs', now()->addHours(6), function () {
            return Http::get(self::CERTS_URL)->throw()->json();
        });
    }

    private function publicKeyFromCertificate(string $cert): string
    {
        $key = openssl_pkey_get_public($cert);
        $details = openssl_pkey_get_details($key);

        return $details['key'];
    }
}
