<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiDocumentationController extends Controller
{
    /**
     * Display the API documentation page.
     */
    public function index()
    {
        $endpoints = [
            'public' => [
                [
                    'method' => 'GET',
                    'endpoint' => '/api/v1/sports',
                    'description' => 'Get all available sports',
                    'example_request' => 'GET /api/v1/sports',
                    'example_response' => '{"success":true,"data":[{"id":1,"name":"Stoni Tenis","slug":"stoni-tenis","description":"...","icon":"🏓","active":true,"leagues_count":2}],"message":"Sports retrieved successfully"}'
                ],
                [
                    'method' => 'GET',
                    'endpoint' => '/api/v1/sports/{sport}',
                    'description' => 'Get a specific sport with public leagues',
                    'example_request' => 'GET /api/v1/sports/stoni-tenis',
                    'example_response' => '{"success":true,"data":{"id":1,"name":"Stoni Tenis","leagues":[{"id":1,"name":"Liga 1"}]},"message":"Sport retrieved successfully"}'
                ],
                [
                    'method' => 'GET',
                    'endpoint' => '/api/v1/leagues',
                    'description' => 'Get all public leagues',
                    'example_request' => 'GET /api/v1/leagues',
                    'example_response' => '{"success":true,"data":[{"id":1,"name":"Liga 1","organization":{"name":"Org 1"},"sport":{"name":"Stoni Tenis"},"matches_count":5,"players_count":10}],"message":"Leagues retrieved successfully"}'
                ],
                [
                    'method' => 'GET',
                    'endpoint' => '/api/v1/leagues/{league}',
                    'description' => 'Get a specific public league',
                    'example_request' => 'GET /api/v1/leagues/1',
                    'example_response' => '{"success":true,"data":{"id":1,"name":"Liga 1","matches":[{"home_team":{"name":"Team A"},"away_team":{"name":"Team B"},"home_score":21,"away_score":19}]},"message":"League retrieved successfully"}'
                ],
                [
                    'method' => 'GET',
                    'endpoint' => '/api/v1/matches',
                    'description' => 'Get all public matches',
                    'example_request' => 'GET /api/v1/matches',
                    'example_response' => '{"success":true,"data":[{"id":1,"scheduled_at":"2025-11-03T15:00:00Z","home_team":{"name":"Team A"},"away_team":{"name":"Team B"},"status":"completed"}],"message":"Matches retrieved successfully"}'
                ],
                [
                    'method' => 'GET',
                    'endpoint' => '/api/v1/matches/{match}',
                    'description' => 'Get a specific public match',
                    'example_request' => 'GET /api/v1/matches/1',
                    'example_response' => '{"success":true,"data":{"id":1,"home_score":21,"away_score":19,"status":"completed"},"message":"Match retrieved successfully"}'
                ],
                [
                    'method' => 'GET',
                    'endpoint' => '/api/v1/organizations',
                    'description' => 'Get all public organizations',
                    'example_request' => 'GET /api/v1/organizations',
                    'example_response' => '{"success":true,"data":[{"id":1,"name":"Sport Club","url_slug":"sport-club","leagues_count":3}],"message":"Organizations retrieved successfully"}'
                ],
                [
                    'method' => 'GET',
                    'endpoint' => '/api/v1/players',
                    'description' => 'Get all public players',
                    'example_request' => 'GET /api/v1/players',
                    'example_response' => '{"success":true,"data":[{"id":1,"name":"John Doe","user":{"email":"john@example.com"}}],"message":"Players retrieved successfully"}'
                ],
                [
                    'method' => 'GET',
                    'endpoint' => '/api/v1/organizations/{org}/competitions',
                    'description' => 'Get organization competitions',
                    'example_request' => 'GET /api/v1/organizations/1/competitions',
                    'example_response' => '{"success":true,"data":[{"id":1,"name":"Tournament 2025","type":"tournament","status":"draft","players_count":16}],"message":"Organization competitions retrieved successfully"}'
                ]
            ],
            'authenticated' => [
                [
                    'method' => 'GET',
                    'endpoint' => '/api/v1/profile',
                    'description' => 'Get authenticated user profile',
                    'headers' => ['Authorization: Bearer {token}'],
                    'example_request' => 'GET /api/v1/profile',
                    'example_response' => '{"success":true,"data":{"id":1,"name":"John Doe","email":"john@example.com"},"message":"Profile retrieved successfully"}'
                ],
                [
                    'method' => 'PUT',
                    'endpoint' => '/api/v1/profile',
                    'description' => 'Update user profile',
                    'headers' => ['Authorization: Bearer {token}', 'Content-Type: application/json'],
                    'example_request' => 'PUT /api/v1/profile
{
  "name": "John Updated",
  "email": "john.updated@example.com"
}',
                    'example_response' => '{"success":true,"data":{"name":"John Updated"},"message":"Profile updated successfully"}'
                ],
                [
                    'method' => 'GET',
                    'endpoint' => '/api/v1/my-organizations',
                    'description' => 'Get user\'s organizations',
                    'headers' => ['Authorization: Bearer {token}'],
                    'example_request' => 'GET /api/v1/my-organizations',
                    'example_response' => '{"success":true,"data":[{"id":1,"name":"My Club","leagues":[{"name":"Liga 1"}]}],"message":"Your organizations retrieved successfully"}'
                ],
                [
                    'method' => 'POST',
                    'endpoint' => '/api/v1/organizations',
                    'description' => 'Create new organization',
                    'headers' => ['Authorization: Bearer {token}', 'Content-Type: application/json'],
                    'example_request' => 'POST /api/v1/organizations
{
  "name": "New Club",
  "description": "A new sports club",
  "is_public": true
}',
                    'example_response' => '{"success":true,"data":{"id":2,"name":"New Club"},"message":"Organization created successfully"}'
                ],
                [
                    'method' => 'GET',
                    'endpoint' => '/api/v1/organizations/{org}/leagues',
                    'description' => 'Get organization leagues',
                    'headers' => ['Authorization: Bearer {token}'],
                    'example_request' => 'GET /api/v1/organizations/1/leagues',
                    'example_response' => '{"success":true,"data":[{"id":1,"name":"Liga 1","sport":{"name":"Stoni Tenis"}}],"message":"Organization leagues retrieved successfully"}'
                ],
                [
                    'method' => 'POST',
                    'endpoint' => '/api/v1/organizations/{org}/leagues',
                    'description' => 'Create league in organization',
                    'headers' => ['Authorization: Bearer {token}', 'Content-Type: application/json'],
                    'example_request' => 'POST /api/v1/organizations/1/leagues
{
  "name": "New League",
  "sport_id": 1,
  "is_public": true
}',
                    'example_response' => '{"success":true,"data":{"id":2,"name":"New League"},"message":"League created successfully"}'
                ],
                [
                    'method' => 'GET',
                    'endpoint' => '/api/v1/leagues/{league}/standings',
                    'description' => 'Get league standings',
                    'headers' => ['Authorization: Bearer {token}'],
                    'example_request' => 'GET /api/v1/leagues/1/standings',
                    'example_response' => '{"success":true,"data":[{"team":"Team A","points":15,"wins":5,"losses":1}],"message":"League standings retrieved successfully"}'
                ],
                [
                    'method' => 'POST',
                    'endpoint' => '/api/v1/leagues/{league}/matches',
                    'description' => 'Create match in league',
                    'headers' => ['Authorization: Bearer {token}', 'Content-Type: application/json'],
                    'example_request' => 'POST /api/v1/leagues/1/matches
{
  "home_team_id": 1,
  "away_team_id": 2,
  "scheduled_at": "2025-11-10T15:00:00Z"
}',
                    'example_response' => '{"success":true,"data":{"id":1,"scheduled_at":"2025-11-10T15:00:00Z"},"message":"Match created successfully"}'
                ],
                [
                    'method' => 'POST',
                    'endpoint' => '/api/v1/matches/{match}/score',
                    'description' => 'Update match score',
                    'headers' => ['Authorization: Bearer {token}', 'Content-Type: application/json'],
                    'example_request' => 'POST /api/v1/matches/1/score
{
  "home_score": 21,
  "away_score": 19
}',
                    'example_response' => '{"success":true,"data":{"home_score":21,"away_score":19},"message":"Match score updated successfully"}'
                ],
                [
                    'method' => 'GET',
                    'endpoint' => '/api/v1/organizations/{org}/competitions',
                    'description' => 'Get organization competitions',
                    'headers' => ['Authorization: Bearer {token}'],
                    'example_request' => 'GET /api/v1/organizations/1/competitions',
                    'example_response' => '{"success":true,"data":[{"id":1,"name":"Tournament 2025","type":"tournament","status":"draft","players_count":16}],"message":"Organization competitions retrieved successfully"}'
                ],
                [
                    'method' => 'POST',
                    'endpoint' => '/api/v1/organizations/{org}/competitions',
                    'description' => 'Create competition in organization',
                    'headers' => ['Authorization: Bearer {token}', 'Content-Type: application/json'],
                    'example_request' => 'POST /api/v1/organizations/1/competitions
{
  "name": "New Tournament",
  "type": "tournament",
  "sport_id": 1,
  "description": "Annual tournament"
}',
                    'example_response' => '{"success":true,"data":{"id":2,"name":"New Tournament"},"message":"Competition created successfully"}'
                ],
                [
                    'method' => 'GET',
                    'endpoint' => '/api/v1/competitions/{competition}',
                    'description' => 'Get specific competition',
                    'headers' => ['Authorization: Bearer {token}'],
                    'example_request' => 'GET /api/v1/competitions/1',
                    'example_response' => '{"success":true,"data":{"id":1,"name":"Tournament 2025","status":"draft","players":[{"name":"John Doe"}]},"message":"Competition retrieved successfully"}'
                ],
                [
                    'method' => 'PUT',
                    'endpoint' => '/api/v1/competitions/{competition}',
                    'description' => 'Update competition',
                    'headers' => ['Authorization: Bearer {token}', 'Content-Type: application/json'],
                    'example_request' => 'PUT /api/v1/competitions/1
{
  "name": "Updated Tournament",
  "description": "Updated description"
}',
                    'example_response' => '{"success":true,"data":{"name":"Updated Tournament"},"message":"Competition updated successfully"}'
                ],
                [
                    'method' => 'POST',
                    'endpoint' => '/api/v1/competitions/{competition}/players',
                    'description' => 'Add player to competition',
                    'headers' => ['Authorization: Bearer {token}', 'Content-Type: application/json'],
                    'example_request' => 'POST /api/v1/competitions/1/players
{
  "player_id": 1
}',
                    'example_response' => '{"success":true,"message":"Player added to competition successfully"}'
                ],
                [
                    'method' => 'DELETE',
                    'endpoint' => '/api/v1/competitions/{competition}/players/{player}',
                    'description' => 'Remove player from competition',
                    'headers' => ['Authorization: Bearer {token}'],
                    'example_request' => 'DELETE /api/v1/competitions/1/players/1',
                    'example_response' => '{"success":true,"message":"Player removed from competition successfully"}'
                ],
                [
                    'method' => 'POST',
                    'endpoint' => '/api/v1/competitions/{competition}/start',
                    'description' => 'Start competition',
                    'headers' => ['Authorization: Bearer {token}'],
                    'example_request' => 'POST /api/v1/competitions/1/start',
                    'example_response' => '{"success":true,"message":"Competition started successfully"}'
                ],
                [
                    'method' => 'POST',
                    'endpoint' => '/api/v1/competitions/{competition}/complete',
                    'description' => 'Complete competition',
                    'headers' => ['Authorization: Bearer {token}'],
                    'example_request' => 'POST /api/v1/competitions/1/complete',
                    'example_response' => '{"success":true,"message":"Competition completed successfully"}'
                ],
                [
                    'method' => 'POST',
                    'endpoint' => '/api/v1/competitions/{competition}/reset',
                    'description' => 'Reset competition',
                    'headers' => ['Authorization: Bearer {token}'],
                    'example_request' => 'POST /api/v1/competitions/1/reset',
                    'example_response' => '{"success":true,"message":"Competition reset successfully"}'
                ]
            ]
        ];

        return view('api.documentation', compact('endpoints'));
    }

    /**
     * Test API endpoint.
     */
    public function test(Request $request)
    {
        $method = strtoupper($request->input('method'));
        $endpoint = $request->input('endpoint');
        $headers = $request->input('headers', []);
        $body = $request->input('body');

        try {
            // Build the full URL
            $baseUrl = config('app.url');
            $fullUrl = $baseUrl . $endpoint;

            // Initialize HTTP client
            $httpClient = new \GuzzleHttp\Client();

            // Prepare headers
            $requestHeaders = [];
            foreach ($headers as $header) {
                if (strpos($header, ':') !== false) {
                    [$key, $value] = explode(':', $header, 2);
                    $requestHeaders[trim($key)] = trim($value);
                }
            }

            // Prepare request options
            $options = [
                'headers' => $requestHeaders,
                'http_errors' => false, // Don't throw exceptions for HTTP errors
            ];

            // Add body for POST/PUT/PATCH requests
            if (in_array($method, ['POST', 'PUT', 'PATCH']) && $body) {
                $options['json'] = json_decode($body, true);
            }

            // Make the request
            $response = $httpClient->request($method, $fullUrl, $options);

            // Get response data
            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody()->getContents();
            $responseHeaders = $response->getHeaders();

            // Try to parse JSON response
            $parsedResponse = json_decode($responseBody, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $parsedResponse = $responseBody;
            }

            return response()->json([
                'success' => true,
                'request' => [
                    'method' => $method,
                    'url' => $fullUrl,
                    'headers' => $requestHeaders,
                    'body' => $body ? json_decode($body, true) : null,
                ],
                'response' => [
                    'status_code' => $statusCode,
                    'headers' => $responseHeaders,
                    'body' => $parsedResponse,
                ],
                'message' => 'API call executed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'request' => [
                    'method' => $method,
                    'url' => $baseUrl . $endpoint,
                    'headers' => $requestHeaders ?? [],
                    'body' => $body ? json_decode($body, true) : null,
                ],
                'error' => $e->getMessage(),
                'message' => 'API call failed'
            ], 500);
        }
    }
}
