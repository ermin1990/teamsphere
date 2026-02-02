<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use Illuminate\Http\Request;

class ProjectorController extends Controller
{
    /**
     * Show the projector builder interface
     * Public page where users can select competitions and generate projector URL
     */
    public function builder()
    {
        // Get all public competitions grouped by organization
        $competitions = Competition::where('is_public', true)
            ->with(['organization', 'sport'])
            ->orderBy('name')
            ->get()
            ->groupBy('organization.name');

        return view('projector.builder', compact('competitions'));
    }

    /**
     * Display the projector view with rotating competitions
     * URL format: /projector/display?ids=1,5,12&durations=20,30,15&mode=standings&layout=grid&resolution=1024x768
     */
    public function display(Request $request)
    {
        // Parse URL parameters
        $competitionIds = $request->input('ids') ? explode(',', $request->input('ids')) : [];
        $durations = $request->input('durations') ? explode(',', $request->input('durations')) : [];
        $phases = $request->input('phases') ? explode(',', $request->input('phases')) : [];
        $defaultDuration = $request->input('default_duration', 20); // Default 20 seconds
        $mode = $request->input('mode', 'both'); // standings, matches, both
        $layout = $request->input('layout', 'single'); // single, grid, split
        $resolution = $request->input('resolution', 'full'); // full, 1024x768
        $livePriority = $request->input('live_priority', false); // Auto-extend time for live matches
        $transitionSpeed = $request->input('transition', 1000); // Animation speed in ms (default 1000ms)
        $transitionType = $request->input('transition_type', 'zoom'); // fade, slide-left, slide-up, zoom, none (default zoom)

        $sequenceParam = $request->input('sequence');

        // If sequence param is provided, build rotation according to sequence tokens
        $rotationConfig = [];
        if ($sequenceParam) {
            $sequenceTokens = explode(',', urldecode($sequenceParam));

            // Decode QR payload if present so we can reference q: indices
            $qrsParam = $request->input('qrs');
            $decodedQrs = [];
            if ($qrsParam) {
                try {
                    $decodedQrs = json_decode(base64_decode(urldecode($qrsParam)), true) ?: [];
                } catch (\Throwable $e) {
                    $decodedQrs = [];
                }
            }

            // Collect competition IDs referenced in sequence to preload
            $compIdsToLoad = [];
            foreach ($sequenceTokens as $t) {
                if (strpos($t, 'c:') === 0) {
                    $compIdsToLoad[] = substr($t, 2);
                }
            }
            $compIdsToLoad = array_values(array_unique(array_filter($compIdsToLoad)));

            if (empty($compIdsToLoad)) {
                return redirect()->route('projector.builder')
                    ->with('error', 'Molimo odaberite najmanje jednu ligu/turnir.');
            }

            // Load competitions with necessary relationships
            $competitions = Competition::whereIn('id', $compIdsToLoad)
                ->where('is_public', true)
                ->with([
                'organization',
                'sport',
                'standings' => function ($query) {
                    $query->orderBy('position', 'asc');
                },
                'standings.team',
                'standings.player',
                'tournamentGroups' => function ($query) {
                    $query->orderBy('group_number', 'asc');
                },
                'tournamentGroups.standings' => function ($query) {
                    $query->orderBy('position', 'asc');
                },
                'tournamentGroups.standings.player',
                'tournamentGroups.matches' => function ($query) {
                    $query->orderBy('match_order', 'asc');
                },
                'tournamentGroups.matches.homePlayer',
                'tournamentGroups.matches.awayPlayer',
                'tournamentGroups.matches.homeTeam',
                'tournamentGroups.matches.awayTeam',
            ])
            ->get()
            ->keyBy('id');
            // Iterate sequence tokens and build rotation entries
            $durIndex = 0;
            $phaseIndex = 0;
            foreach ($sequenceTokens as $token) {
                if (strpos($token, 'q:') === 0) {
                    // QR token - inline QR slide from decoded payload
                    $qrIdx = (int) substr($token, 2);
                    if (isset($decodedQrs[$qrIdx])) {
                        $qr = $decodedQrs[$qrIdx];
                        $rotationConfig[] = [
                            'id' => 'qr_' . uniqid(),
                            'competition' => null,
                            'type' => 'qr',
                            'qr_url' => $qr['url'] ?? '',
                            'duration' => isset($qr['duration']) ? (int)$qr['duration'] : $defaultDuration,
                            'caption' => $qr['text'] ?? ($qr['caption'] ?? ''),
                            'has_live' => false,
                            'phase' => 'qr',
                        ];
                    }
                    continue;
                }

                if (strpos($token, 'c:') === 0) {
                    $id = substr($token, 2);
                    if (!isset($competitions[$id])) continue;
                    $competition = $competitions[$id];

                    // Get duration for this competition (fallback to default)
                    $duration = isset($durations[$durIndex]) ? (int)$durations[$durIndex] : $defaultDuration;

                    // Get phase selection for this competition (fallback to 'auto')
                    $phaseSelection = isset($phases[$phaseIndex]) ? $phases[$phaseIndex] : 'auto';
                    $durIndex++; $phaseIndex++;
                
                    // Load matches based on competition type
                    if ($competition->type === 'league') {
                        if ($competition->is_team_based) {
                            $competition->load([
                                'teamMatches' => function ($query) {
                                    $query->where('status', 'in_progress')
                                          ->orWhere('status', 'scheduled')
                                          ->orderBy('scheduled_at', 'desc')
                                          ->with(['homeTeam', 'awayTeam']);
                                }
                            ]);
                        } else {
                            $competition->load([
                                'leagueMatches' => function ($query) {
                                    $query->where('status', 'in_progress')
                                          ->orWhere('status', 'scheduled')
                                          ->orderBy('scheduled_at', 'desc')
                                          ->with(['homePlayer', 'awayPlayer']);
                                }
                            ]);
                        }
                    } else {
                        // Tournament matches - load ALL matches for bracket
                        $competition->load([
                            'matches' => function ($query) {
                                $query->orderBy('round_number')
                                      ->orderBy('match_order')
                                      ->with(['homePlayer', 'awayPlayer', 'tournamentGroup']);
                            }
                        ]);
                    }

                    // Check if competition has live matches
                    $hasLiveMatches = false;
                    if ($competition->type === 'league') {
                        $matches = $competition->is_team_based ? $competition->teamMatches : $competition->leagueMatches;
                        $hasLiveMatches = $matches->where('status', 'in_progress')->isNotEmpty();
                    } else {
                        $hasLiveMatches = $competition->matches->where('status', 'in_progress')->isNotEmpty();
                    }

                    // Apply live priority - extend duration if match is live
                    if ($livePriority && $hasLiveMatches) {
                        $duration = max($duration, 60); // Minimum 60 seconds for live matches
                    }

                    // For tournaments with groups:
                    // - If phase is 'knockout', show knockout bracket as single item
                    // - If phase is 'groups' or 'auto', create separate rotation item for each group, or single knockout if groups completed
                    if ($competition->type === 'tournament' && $competition->tournamentGroups->isNotEmpty()) {
                        // Strict check for 'knockout'
                        if ($phaseSelection === 'knockout') {
                            // Show knockout bracket as single item
                            $rotationConfig[] = [
                                'id' => $id,
                                'competition' => $competition,
                                'group' => null,
                                'duration' => $duration,
                                'has_live' => $hasLiveMatches,
                                'phase' => $phaseSelection,
                            ];
                        } else {
                            // Check if groups are mostly completed and knockout exists
                            $completedGroupMatches = $competition->matches->whereNotNull('tournament_group_id')->where('status', 'completed')->count();
                            $totalGroupMatches = $competition->matches->whereNotNull('tournament_group_id')->count();
                            $groupsMostlyCompleted = $totalGroupMatches > 0 && ($completedGroupMatches / $totalGroupMatches) > 0.8; // 80% completed
                            
                            if ($phaseSelection === 'groups' || ($phaseSelection === 'auto' && !$groupsMostlyCompleted)) {
                                // Show individual groups
                                foreach ($competition->tournamentGroups as $group) {
                                    // Check if this specific group has live matches
                                    $groupHasLiveMatches = $group->matches->where('status', 'in_progress')->isNotEmpty();
                                    $groupDuration = ($livePriority && $groupHasLiveMatches) ? max($duration, 60) : $duration;
                                    
                                    $rotationConfig[] = [
                                        'id' => $id . '_group_' . $group->id,
                                        'competition' => $competition,
                                        'group' => $group,
                                        'duration' => $groupDuration,
                                        'has_live' => $groupHasLiveMatches,
                                        'phase' => $phaseSelection,
                                    ];
                                }
                            } else {
                                // Groups completed, show knockout phase
                                $rotationConfig[] = [
                                    'id' => $id,
                                    'competition' => $competition,
                                    'group' => null,
                                    'duration' => $duration,
                                    'has_live' => $hasLiveMatches,
                                    'phase' => 'knockout',
                                ];
                            }
                        }
                    } else {
                        // For leagues or tournaments without groups, add as single item
                        $rotationConfig[] = [
                            'id' => $id,
                            'competition' => $competition,
                            'group' => null,
                            'duration' => $duration,
                            'has_live' => $hasLiveMatches,
                            'phase' => $phaseSelection, // Add phase selection
                        ];
                    }
                }
            }

            // Now handle qr tokens in sequence: they were already expanded as 'q:idx' and should be handled inline
            // We processed q: tokens during the sequence loop above; nothing more to do here.

        } else {
            // No sequence param - fall back to original behavior using ids order

            if (empty($competitionIds)) {
                return redirect()->route('projector.builder')
                    ->with('error', 'Molimo odaberite najmanje jednu ligu/turnir.');
            }

            // Load competitions with all necessary relationships
            $competitions = Competition::whereIn('id', $competitionIds)
                ->where('is_public', true)
                ->with([
                    'organization',
                    'sport',
                    'standings' => function ($query) {
                        $query->orderBy('position', 'asc');
                    },
                    'standings.team',
                    'standings.player',
                    'tournamentGroups' => function ($query) {
                        $query->orderBy('group_number', 'asc');
                    },
                    'tournamentGroups.standings' => function ($query) {
                        $query->orderBy('position', 'asc');
                    },
                    'tournamentGroups.standings.player',
                    'tournamentGroups.matches' => function ($query) {
                        $query->orderBy('match_order', 'asc');
                    },
                    'tournamentGroups.matches.homePlayer',
                    'tournamentGroups.matches.awayPlayer',
                    'tournamentGroups.matches.homeTeam',
                    'tournamentGroups.matches.awayTeam',
                ])
                ->get()
                ->keyBy('id');

            // Build rotation config array maintaining order from URL
            foreach ($competitionIds as $index => $id) {
                if (isset($competitions[$id])) {
                    $competition = $competitions[$id];

                    // Get duration for this competition (fallback to default)
                    $duration = isset($durations[$index]) ? (int)$durations[$index] : $defaultDuration;

                    // Get phase selection for this competition (fallback to 'auto')
                    $phaseSelection = isset($phases[$index]) ? $phases[$index] : 'auto';

                    // Load matches based on competition type
                    if ($competition->type === 'league') {
                        if ($competition->is_team_based) {
                            $competition->load([
                                'teamMatches' => function ($query) {
                                    $query->where('status', 'in_progress')
                                          ->orWhere('status', 'scheduled')
                                          ->orderBy('scheduled_at', 'desc')
                                          ->with(['homeTeam', 'awayTeam']);
                                }
                            ]);
                        } else {
                            $competition->load([
                                'leagueMatches' => function ($query) {
                                    $query->where('status', 'in_progress')
                                          ->orWhere('status', 'scheduled')
                                          ->orderBy('scheduled_at', 'desc')
                                          ->with(['homePlayer', 'awayPlayer']);
                                }
                            ]);
                        }
                    } else {
                        // Tournament matches - load ALL matches for bracket
                        $competition->load([
                            'matches' => function ($query) {
                                $query->orderBy('round_number')
                                      ->orderBy('match_order')
                                      ->with(['homePlayer', 'awayPlayer', 'tournamentGroup']);
                            }
                        ]);
                    }

                    // Check if competition has live matches
                    $hasLiveMatches = false;
                    if ($competition->type === 'league') {
                        $matches = $competition->is_team_based ? $competition->teamMatches : $competition->leagueMatches;
                        $hasLiveMatches = $matches->where('status', 'in_progress')->isNotEmpty();
                    } else {
                        $hasLiveMatches = $competition->matches->where('status', 'in_progress')->isNotEmpty();
                    }

                    // Apply live priority - extend duration if match is live
                    if ($livePriority && $hasLiveMatches) {
                        $duration = max($duration, 60); // Minimum 60 seconds for live matches
                    }

                    // For tournaments with groups:
                    // - If phase is 'knockout', show knockout bracket as single item
                    // - If phase is 'groups' or 'auto', create separate rotation item for each group, or single knockout if groups completed
                    if ($competition->type === 'tournament' && $competition->tournamentGroups->isNotEmpty()) {
                        // Strict check for 'knockout'
                        if ($phaseSelection === 'knockout') {
                            // Show knockout bracket as single item
                            $rotationConfig[] = [
                                'id' => $id,
                                'competition' => $competition,
                                'group' => null,
                                'duration' => $duration,
                                'has_live' => $hasLiveMatches,
                                'phase' => $phaseSelection,
                            ];
                        } else {
                            // Check if groups are mostly completed and knockout exists
                            $completedGroupMatches = $competition->matches->whereNotNull('tournament_group_id')->where('status', 'completed')->count();
                            $totalGroupMatches = $competition->matches->whereNotNull('tournament_group_id')->count();
                            $groupsMostlyCompleted = $totalGroupMatches > 0 && ($completedGroupMatches / $totalGroupMatches) > 0.8; // 80% completed
                            
                            if ($phaseSelection === 'groups' || ($phaseSelection === 'auto' && !$groupsMostlyCompleted)) {
                                // Show individual groups
                                foreach ($competition->tournamentGroups as $group) {
                                    // Check if this specific group has live matches
                                    $groupHasLiveMatches = $group->matches->where('status', 'in_progress')->isNotEmpty();
                                    $groupDuration = ($livePriority && $groupHasLiveMatches) ? max($duration, 60) : $duration;
                                    
                                    $rotationConfig[] = [
                                        'id' => $id . '_group_' . $group->id,
                                        'competition' => $competition,
                                        'group' => $group,
                                        'duration' => $groupDuration,
                                        'has_live' => $groupHasLiveMatches,
                                        'phase' => $phaseSelection,
                                    ];
                                }
                            } else {
                                // Groups completed, show knockout phase
                                $rotationConfig[] = [
                                    'id' => $id,
                                    'competition' => $competition,
                                    'group' => null,
                                    'duration' => $duration,
                                    'has_live' => $hasLiveMatches,
                                    'phase' => 'knockout',
                                ];
                            }
                        }
                    } else {
                        // For leagues or tournaments without groups, add as single item
                        $rotationConfig[] = [
                            'id' => $id,
                            'competition' => $competition,
                            'group' => null,
                            'duration' => $duration,
                            'has_live' => $hasLiveMatches,
                            'phase' => $phaseSelection, // Add phase selection
                        ];
                    }
                }
            }
        }

        // If no valid competitions found, redirect back
        if (empty($rotationConfig)) {
            return redirect()->route('projector.builder')
                ->with('error', 'Nijedna od odabranih liga nije dostupna.');
        }

        // If no sequence param was used, handle QR slides if passed via `qrs` parameter (base64-encoded JSON)
        if (!$sequenceParam) {
            $qrsParam = $request->input('qrs');
            if ($qrsParam) {
                try {
                    $decoded = json_decode(base64_decode(urldecode($qrsParam)), true);
                    if (is_array($decoded)) {
                        foreach ($decoded as $qr) {
                            $count = isset($qr['count']) ? (int)$qr['count'] : 1;
                            for ($i = 0; $i < max(1, $count); $i++) {
                                $rotationConfig[] = [
                                    'id' => 'qr_' . uniqid(),
                                    'competition' => null,
                                    'type' => 'qr',
                                    'qr_url' => $qr['url'] ?? '',
                                    'duration' => isset($qr['duration']) ? (int)$qr['duration'] : $defaultDuration,
                                    'caption' => $qr['text'] ?? ($qr['caption'] ?? ''),
                                    'has_live' => false,
                                    'phase' => 'qr',
                                ];
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    // ignore malformed param
                }
            }
        }

        // Prepare simplified rotation data for JavaScript
        $rotationDataForJs = array_map(function($config) {
            if (isset($config['type']) && $config['type'] === 'qr') {
                return [
                    'id' => $config['id'],
                    'duration' => $config['duration'],
                    'name' => 'QR',
                    'organization' => '',
                    'has_live' => false,
                ];
            }

            $name = $config['competition']->name;
            if (isset($config['group']) && $config['group']) {
                $name .= ' - Grupa ' . $config['group']->name;
            }
            
            return [
                'id' => $config['id'],
                'duration' => $config['duration'],
                'name' => $name,
                'organization' => $config['competition']->organization->name,
                'has_live' => $config['has_live']
            ];
        }, $rotationConfig);

        // Pass data to view
        return view('projector.display', [
            'rotationConfig' => $rotationConfig,
            'rotationDataForJs' => $rotationDataForJs,
            'mode' => $mode,
            'layout' => $layout,
            'resolution' => $resolution,
            'transitionSpeed' => $transitionSpeed,
            'transitionType' => $transitionType,
            'livePriority' => $livePriority,
            'totalCompetitions' => count($rotationConfig),
        ]);
    }

    /**
     * API endpoint to get fresh competition data for AJAX rotation
     * Returns HTML partial for a specific competition
     */
    public function getCompetitionView(Request $request, $id)
    {
        $mode = $request->input('mode', 'both');
        $layout = $request->input('layout', 'modern');
        $phase = $request->input('phase', 'auto');
        $resolution = $request->input('resolution', 'full');
        
        // Parse ID to check for specific group (format: "{$compId}_group_{$groupId}")
        $competitionId = $id;
        $groupId = null;
        $selectedGroup = null;

        if (strpos($id, '_group_') !== false) {
            $parts = explode('_group_', $id);
            $competitionId = $parts[0];
            $groupId = $parts[1];
        }

        $competition = Competition::findOrFail($competitionId);

        if ($groupId) {
            $selectedGroup = \App\Models\TournamentGroup::find($groupId);
        }
        
        // Load necessary relationships
        $competition->load([
            'organization',
            'sport',
            'standings' => function ($query) {
                $query->orderBy('position', 'asc');
            },
            'standings.team',
            'standings.player',
            'tournamentGroups.standings.player',
        ]);

        // Load matches
        if ($competition->type === 'league') {
            if ($competition->is_team_based) {
                $competition->load([
                    'teamMatches' => function ($query) {
                        $query->where('status', 'in_progress')
                              ->orWhere(function($q) {
                                  $q->where('status', 'scheduled')
                                    ->whereDate('scheduled_at', '>=', now()->subDay());
                              })
                              ->orderBy('scheduled_at', 'desc')
                              ->limit(10)
                              ->with(['homeTeam', 'awayTeam']);
                    }
                ]);
            } else {
                $competition->load([
                    'leagueMatches' => function ($query) {
                        $query->where('status', 'in_progress')
                              ->orWhere(function($q) {
                                  $q->where('status', 'scheduled')
                                    ->whereDate('scheduled_at', '>=', now()->subDay());
                              })
                              ->orderBy('scheduled_at', 'desc')
                              ->limit(10)
                              ->with(['homePlayer', 'awayPlayer']);
                    }
                ]);
            }
        } else {
            $competition->load([
                'matches' => function ($query) {
                    $query->orderBy('round_number')
                          ->orderBy('match_order')
                          ->with(['homePlayer', 'awayPlayer', 'tournamentGroup']);
                },
                'tournamentGroups.matches' => function ($query) {
                    $query->orderBy('match_order', 'asc')
                          ->with(['homePlayer', 'awayPlayer', 'homeTeam', 'awayTeam']);
                }
            ]);
        }

        return view('projector.partials.competition-view', [
            'competition' => $competition,
            'mode' => $mode,
            'layout' => $layout,
            'phase' => $phase,
            'resolution' => $resolution,
            'selectedGroup' => $selectedGroup,
        ]);
    }
}
