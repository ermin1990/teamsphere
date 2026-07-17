<?php

namespace App\Services;

use App\Models\City;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Turns a plain-language description of a league/tournament into suggested
 * values for the organizations.competitions.create form, via Gemini's free
 * Flash tier. Only ever produces a form-shaped suggestion for the organizer
 * to review - never creates or touches a Competition record itself.
 */
class GeminiLeagueAssistantService
{
    private const ENDPOINT = 'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent';

    private const RESPONSE_SCHEMA = [
        'type' => 'OBJECT',
        'properties' => [
            'name' => ['type' => 'STRING'],
            'description' => ['type' => 'STRING', 'nullable' => true],
            'location' => ['type' => 'STRING', 'nullable' => true],
            'organizer_contact' => ['type' => 'STRING', 'nullable' => true],
            'entry_fee' => ['type' => 'STRING', 'nullable' => true],
            'type' => ['type' => 'STRING', 'enum' => ['tournament', 'league']],
            'is_team_based' => ['type' => 'BOOLEAN'],
            'is_double_round' => ['type' => 'BOOLEAN'],
            'is_recreational' => ['type' => 'BOOLEAN'],
            'allow_rematches' => ['type' => 'BOOLEAN'],
            'players_advancing_per_group' => ['type' => 'INTEGER', 'nullable' => true],
            'start_date' => ['type' => 'STRING'],
            'end_date' => ['type' => 'STRING', 'nullable' => true],
            'city_name' => ['type' => 'STRING', 'nullable' => true],
        ],
        'required' => ['name', 'type', 'is_team_based', 'start_date'],
    ];

    public function __construct()
    {
        $model = config('services.gemini.model');
        $allowed = config('services.gemini.allowed_models', []);

        if (!in_array($model, $allowed, true)) {
            throw new RuntimeException("GEMINI_MODEL '{$model}' nije na listi dozvoljenih free-tier modela: " . implode(', ', $allowed));
        }
    }

    /**
     * @return array{data: array<string, mixed>, warnings: string[]}
     */
    public function suggest(string $description): array
    {
        $apiKey = config('services.gemini.api_key');

        if (empty($apiKey)) {
            throw new RuntimeException('GEMINI_API_KEY nije podešen.');
        }

        $model = config('services.gemini.model');
        $endpoint = sprintf(self::ENDPOINT, $model);

        $response = Http::timeout(20)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post("{$endpoint}?key={$apiKey}", [
                'contents' => [
                    ['parts' => [['text' => $this->buildPrompt($description)]]],
                ],
                'generationConfig' => [
                    'thinkingConfig' => ['thinkingBudget' => 0],
                    'responseMimeType' => 'application/json',
                    'responseSchema' => self::RESPONSE_SCHEMA,
                ],
            ])
            ->throw()
            ->json();

        $text = $response['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (!$text) {
            throw new RuntimeException('Gemini odgovor ne sadrži tekst.');
        }

        $data = json_decode($text, true);

        if (!is_array($data)) {
            throw new RuntimeException('Gemini odgovor nije validan JSON.');
        }

        return $this->matchCity($data);
    }

    private function buildPrompt(string $description): string
    {
        $today = now()->format('Y-m-d');

        return <<<PROMPT
            Ti si asistent koji pomaže organizatorima sportskih liga/turnira u Bosni i Hercegovini da popune formu za kreiranje takmičenja u aplikaciji MojTurnir.

            Iz opisa organizatora (na bosanskom/hrvatskom/srpskom jeziku) izvuci strukturisane podatke tačno prema zadatoj shemi.

            Pravila:
            - "start_date" i "end_date" MORAJU biti u formatu YYYY-MM-DD. Danas je {$today} - ako organizator ne pomene godinu, pretpostavi najbližu buduću godinu za taj datum.
            - "type" je "tournament" (grupna faza + eliminacije, obično individualno) ili "league" (ekipno prvenstvo, cijela sezona međusobnih mečeva). Ako opis nije jasan, pretpostavi "tournament".
            - "is_team_based" je true samo ako je jasno da se radi o ekipama (timovima), false za individualne igrače.
            - "is_double_round", "is_recreational", "allow_rematches" - true samo ako je eksplicitno ili jasno implicirano u opisu, inače false.
            - "players_advancing_per_group" postavi samo za turnire (1-4), inače null.
            - "city_name" postavi SAMO ako je organizator eksplicitno naveo grad - nikad ne izmišljaj/pretpostavljaj ako nije pomenuto, ostavi null.
            - "entry_fee" prepiši tačno kako je organizator naveo (npr. "20 KM po sezoni"), ne pretvaraj u broj.
            - "name" smisli kratak, prirodan naziv takmičenja na osnovu opisa ako organizator nije naveo eksplicitan naziv.

            Opis organizatora:
            "{$description}"
            PROMPT;
    }

    /**
     * @return array{data: array<string, mixed>, warnings: string[]}
     */
    private function matchCity(array $data): array
    {
        $warnings = [];

        $cityName = $data['city_name'] ?? null;
        unset($data['city_name']);

        if (!empty($cityName)) {
            $city = City::all()
                ->first(fn ($c) => Str::contains(Str::lower($c->name), Str::lower($cityName))
                    || Str::contains(Str::lower($cityName), Str::lower($c->name)));

            if ($city) {
                $data['city_id'] = $city->id;
            } else {
                $warnings[] = "Grad \"{$cityName}\" nije pronađen — izaberite ručno.";
            }
        }

        return ['data' => $data, 'warnings' => $warnings];
    }
}
