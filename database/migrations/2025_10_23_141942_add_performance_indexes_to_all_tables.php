<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // MATCHES TABLE - Most frequently queried table
        Schema::table('matches', function (Blueprint $table) {
            // Status is used in almost every match query
            $table->index('status', 'idx_matches_status');
            
            // League/Competition filtering
            $table->index('league_id', 'idx_matches_league_id');
            $table->index('competition_id', 'idx_matches_competition_id');
            
            // Round filtering for match lists
            $table->index('round', 'idx_matches_round');
            
            // Scheduled/played date filtering
            $table->index('scheduled_at', 'idx_matches_scheduled_at');
            $table->index('played_at', 'idx_matches_played_at');
            
            // Referee assignments
            $table->index('referee_user_id', 'idx_matches_referee_user_id');
            $table->index('moderator_id', 'idx_matches_moderator_id');
            
            // Tournament group filtering
            $table->index('tournament_group_id', 'idx_matches_tournament_group_id');
            
            // Composite indexes for common queries
            $table->index(['league_id', 'status', 'scheduled_at'], 'idx_matches_league_status_scheduled');
            $table->index(['competition_id', 'status', 'scheduled_at'], 'idx_matches_competition_status_scheduled');
            $table->index(['status', 'referee_user_id'], 'idx_matches_status_referee');
            $table->index(['status', 'moderator_id'], 'idx_matches_status_moderator');
        });

        // STANDINGS TABLE - Frequently sorted and filtered
        Schema::table('standings', function (Blueprint $table) {
            // League/Competition filtering
            $table->index('league_id', 'idx_standings_league_id');
            $table->index('competition_id', 'idx_standings_competition_id');
            
            // Team/Player lookups
            $table->index('team_id', 'idx_standings_team_id');
            $table->index('player_id', 'idx_standings_player_id');
            
            // Tournament group filtering
            $table->index('tournament_group_id', 'idx_standings_tournament_group_id');
            
            // Sorting by position
            $table->index('position', 'idx_standings_position');
            
            // Composite indexes for leaderboard queries
            $table->index(['league_id', 'position'], 'idx_standings_league_position');
            $table->index(['competition_id', 'position'], 'idx_standings_competition_position');
            $table->index(['tournament_group_id', 'position'], 'idx_standings_group_position');
            
            // Points sorting (fallback if position not set)
            $table->index(['league_id', 'points', 'goal_difference'], 'idx_standings_league_points_gd');
            $table->index(['competition_id', 'points', 'sets_won'], 'idx_standings_competition_points_sets');
        });

        // PLAYERS TABLE - Lookups by user and organization
        Schema::table('players', function (Blueprint $table) {
            // User association
            $table->index('user_id', 'idx_players_user_id');
            
            // Organization filtering
            $table->index('organization_id', 'idx_players_organization_id');
            
            // Active players filtering
            $table->index('is_active', 'idx_players_is_active');
            
            // Composite for active players in organization
            $table->index(['organization_id', 'is_active'], 'idx_players_org_active');
            $table->index(['user_id', 'organization_id'], 'idx_players_user_org');
        });

        // TEAMS TABLE - Competition and league lookups
        Schema::table('teams', function (Blueprint $table) {
            // Competition/League filtering
            $table->index('competition_id', 'idx_teams_competition_id');
            $table->index('competition_type', 'idx_teams_competition_type');
            
            // Composite for finding teams in specific competition
            $table->index(['competition_type', 'competition_id'], 'idx_teams_competition_lookup');
        });

        // LEAGUES TABLE - Organization and status filtering
        Schema::table('leagues', function (Blueprint $table) {
            // Organization filtering
            $table->index('organization_id', 'idx_leagues_organization_id');
            
            // Status filtering
            $table->index('status', 'idx_leagues_status');
            
            // Sport filtering
            $table->index('sport_id', 'idx_leagues_sport_id');
            
            // Public leagues
            $table->index('is_public', 'idx_leagues_is_public');
            
            // Team-based filtering
            $table->index('is_team_based', 'idx_leagues_is_team_based');
            
            // Composite indexes
            $table->index(['organization_id', 'status'], 'idx_leagues_org_status');
            $table->index(['is_public', 'status'], 'idx_leagues_public_status');
            $table->index(['organization_id', 'sport_id', 'status'], 'idx_leagues_org_sport_status');
        });

        // COMPETITIONS TABLE - Similar to leagues
        Schema::table('competitions', function (Blueprint $table) {
            // Organization filtering
            $table->index('organization_id', 'idx_competitions_organization_id');
            
            // Status filtering
            $table->index('status', 'idx_competitions_status');
            
            // Sport filtering
            $table->index('sport_id', 'idx_competitions_sport_id');
            
            // Type filtering (tournament vs league)
            $table->index('type', 'idx_competitions_type');
            
            // Public competitions
            $table->index('is_public', 'idx_competitions_is_public');
            
            // Team-based filtering
            $table->index('is_team_based', 'idx_competitions_is_team_based');
            
            // Current phase for tournaments
            $table->index('current_phase', 'idx_competitions_current_phase');
            
            // Composite indexes
            $table->index(['organization_id', 'status'], 'idx_competitions_org_status');
            $table->index(['is_public', 'status'], 'idx_competitions_public_status');
            $table->index(['organization_id', 'type', 'status'], 'idx_competitions_org_type_status');
            $table->index(['type', 'current_phase'], 'idx_competitions_type_phase');
        });

        // TOURNAMENT_GROUPS TABLE - Competition and round filtering
        Schema::table('tournament_groups', function (Blueprint $table) {
            // Competition filtering
            $table->index('competition_id', 'idx_tournament_groups_competition_id');
            
            // Round filtering
            $table->index('round', 'idx_tournament_groups_round');
            
            // Group name for sorting
            $table->index('name', 'idx_tournament_groups_name');
            
            // Composite indexes
            $table->index(['competition_id', 'round'], 'idx_tournament_groups_comp_round');
            $table->index(['competition_id', 'name'], 'idx_tournament_groups_comp_name');
        });

        // ORGANIZATIONS TABLE - User and slug lookups
        Schema::table('organizations', function (Blueprint $table) {
            // Owner lookups
            $table->index('user_id', 'idx_organizations_user_id');
            
            // Slug for URL routing (already unique but index helps)
            $table->index('slug', 'idx_organizations_slug');
            
            // Plan lookups
            $table->index('plan_id', 'idx_organizations_plan_id');
        });

        // ORGANIZATION_USER TABLE - Permission checks
        Schema::table('organization_user', function (Blueprint $table) {
            // User lookups
            $table->index('user_id', 'idx_organization_user_user_id');
            
            // Organization lookups
            $table->index('organization_id', 'idx_organization_user_org_id');
            
            // Role filtering (referee, member, etc)
            $table->index('role', 'idx_organization_user_role');
            
            // Composite indexes for permission checks
            $table->index(['user_id', 'organization_id'], 'idx_organization_user_user_org');
            $table->index(['organization_id', 'role'], 'idx_organization_user_org_role');
            $table->index(['user_id', 'role'], 'idx_organization_user_user_role');
        });

        // LEAGUE_PLAYER TABLE - Player participation
        Schema::table('league_player', function (Blueprint $table) {
            // League lookups
            $table->index('league_id', 'idx_league_player_league_id');
            
            // Player lookups
            $table->index('player_id', 'idx_league_player_player_id');
            
            // Team assignments
            $table->index('team_id', 'idx_league_player_team_id');
            
            // Composite indexes
            $table->index(['league_id', 'player_id'], 'idx_league_player_league_player');
            $table->index(['league_id', 'team_id'], 'idx_league_player_league_team');
        });

        // COMPETITION_PLAYER TABLE - Similar to league_player
        Schema::table('competition_player', function (Blueprint $table) {
            // Competition lookups
            $table->index('competition_id', 'idx_competition_player_comp_id');
            
            // Player lookups
            $table->index('player_id', 'idx_competition_player_player_id');
            
            // Team assignments
            $table->index('team_id', 'idx_competition_player_team_id');
            
            // Composite indexes
            $table->index(['competition_id', 'player_id'], 'idx_competition_player_comp_player');
            $table->index(['competition_id', 'team_id'], 'idx_competition_player_comp_team');
        });

        // TEAM_USER TABLE - Team membership
        Schema::table('team_user', function (Blueprint $table) {
            // Team lookups
            $table->index('team_id', 'idx_team_user_team_id');
            
            // User lookups
            $table->index('user_id', 'idx_team_user_user_id');
            
            // Composite index
            $table->index(['team_id', 'user_id'], 'idx_team_user_team_user');
        });

        // TABLES (Physical Tables) - Organization lookups
        Schema::table('tables', function (Blueprint $table) {
            // Organization filtering
            $table->index('organization_id', 'idx_tables_organization_id');
            
            // Number for sorting
            $table->index('number', 'idx_tables_number');
            
            // Composite for organization table list
            $table->index(['organization_id', 'number'], 'idx_tables_org_number');
        });

        // USER_PLANS TABLE - User subscription lookups
        Schema::table('user_plans', function (Blueprint $table) {
            // User lookups
            $table->index('user_id', 'idx_user_plans_user_id');
            
            // Plan lookups
            $table->index('plan_id', 'idx_user_plans_plan_id');
            
            // Active subscriptions
            $table->index('ends_at', 'idx_user_plans_ends_at');
            
            // Composite for active user plans
            $table->index(['user_id', 'ends_at'], 'idx_user_plans_user_active');
        });

        // USERS TABLE - Email and remember token already indexed by Laravel
        Schema::table('users', function (Blueprint $table) {
            // Name search (if implemented)
            $table->index('name', 'idx_users_name');
        });

        // SPORTS TABLE - Active sports filtering
        Schema::table('sports', function (Blueprint $table) {
            // Active filtering
            $table->index('is_active', 'idx_sports_is_active');
            
            // Slug for URL routing
            $table->index('slug', 'idx_sports_slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // MATCHES TABLE
        Schema::table('matches', function (Blueprint $table) {
            $table->dropIndex('idx_matches_status');
            $table->dropIndex('idx_matches_league_id');
            $table->dropIndex('idx_matches_competition_id');
            $table->dropIndex('idx_matches_round');
            $table->dropIndex('idx_matches_scheduled_at');
            $table->dropIndex('idx_matches_played_at');
            $table->dropIndex('idx_matches_referee_user_id');
            $table->dropIndex('idx_matches_moderator_id');
            $table->dropIndex('idx_matches_tournament_group_id');
            $table->dropIndex('idx_matches_league_status_scheduled');
            $table->dropIndex('idx_matches_competition_status_scheduled');
            $table->dropIndex('idx_matches_status_referee');
            $table->dropIndex('idx_matches_status_moderator');
        });

        // STANDINGS TABLE
        Schema::table('standings', function (Blueprint $table) {
            $table->dropIndex('idx_standings_league_id');
            $table->dropIndex('idx_standings_competition_id');
            $table->dropIndex('idx_standings_team_id');
            $table->dropIndex('idx_standings_player_id');
            $table->dropIndex('idx_standings_tournament_group_id');
            $table->dropIndex('idx_standings_position');
            $table->dropIndex('idx_standings_league_position');
            $table->dropIndex('idx_standings_competition_position');
            $table->dropIndex('idx_standings_group_position');
            $table->dropIndex('idx_standings_league_points_gd');
            $table->dropIndex('idx_standings_competition_points_sets');
        });

        // PLAYERS TABLE
        Schema::table('players', function (Blueprint $table) {
            $table->dropIndex('idx_players_user_id');
            $table->dropIndex('idx_players_organization_id');
            $table->dropIndex('idx_players_is_active');
            $table->dropIndex('idx_players_org_active');
            $table->dropIndex('idx_players_user_org');
        });

        // TEAMS TABLE
        Schema::table('teams', function (Blueprint $table) {
            $table->dropIndex('idx_teams_competition_id');
            $table->dropIndex('idx_teams_competition_type');
            $table->dropIndex('idx_teams_competition_lookup');
        });

        // LEAGUES TABLE
        Schema::table('leagues', function (Blueprint $table) {
            $table->dropIndex('idx_leagues_organization_id');
            $table->dropIndex('idx_leagues_status');
            $table->dropIndex('idx_leagues_sport_id');
            $table->dropIndex('idx_leagues_is_public');
            $table->dropIndex('idx_leagues_is_team_based');
            $table->dropIndex('idx_leagues_org_status');
            $table->dropIndex('idx_leagues_public_status');
            $table->dropIndex('idx_leagues_org_sport_status');
        });

        // COMPETITIONS TABLE
        Schema::table('competitions', function (Blueprint $table) {
            $table->dropIndex('idx_competitions_organization_id');
            $table->dropIndex('idx_competitions_status');
            $table->dropIndex('idx_competitions_sport_id');
            $table->dropIndex('idx_competitions_type');
            $table->dropIndex('idx_competitions_is_public');
            $table->dropIndex('idx_competitions_is_team_based');
            $table->dropIndex('idx_competitions_current_phase');
            $table->dropIndex('idx_competitions_org_status');
            $table->dropIndex('idx_competitions_public_status');
            $table->dropIndex('idx_competitions_org_type_status');
            $table->dropIndex('idx_competitions_type_phase');
        });

        // TOURNAMENT_GROUPS TABLE
        Schema::table('tournament_groups', function (Blueprint $table) {
            $table->dropIndex('idx_tournament_groups_competition_id');
            $table->dropIndex('idx_tournament_groups_round');
            $table->dropIndex('idx_tournament_groups_name');
            $table->dropIndex('idx_tournament_groups_comp_round');
            $table->dropIndex('idx_tournament_groups_comp_name');
        });

        // ORGANIZATIONS TABLE
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropIndex('idx_organizations_user_id');
            $table->dropIndex('idx_organizations_slug');
            $table->dropIndex('idx_organizations_plan_id');
        });

        // ORGANIZATION_USER TABLE
        Schema::table('organization_user', function (Blueprint $table) {
            $table->dropIndex('idx_organization_user_user_id');
            $table->dropIndex('idx_organization_user_org_id');
            $table->dropIndex('idx_organization_user_role');
            $table->dropIndex('idx_organization_user_user_org');
            $table->dropIndex('idx_organization_user_org_role');
            $table->dropIndex('idx_organization_user_user_role');
        });

        // LEAGUE_PLAYER TABLE
        Schema::table('league_player', function (Blueprint $table) {
            $table->dropIndex('idx_league_player_league_id');
            $table->dropIndex('idx_league_player_player_id');
            $table->dropIndex('idx_league_player_team_id');
            $table->dropIndex('idx_league_player_league_player');
            $table->dropIndex('idx_league_player_league_team');
        });

        // COMPETITION_PLAYER TABLE
        Schema::table('competition_player', function (Blueprint $table) {
            $table->dropIndex('idx_competition_player_comp_id');
            $table->dropIndex('idx_competition_player_player_id');
            $table->dropIndex('idx_competition_player_team_id');
            $table->dropIndex('idx_competition_player_comp_player');
            $table->dropIndex('idx_competition_player_comp_team');
        });

        // TEAM_USER TABLE
        Schema::table('team_user', function (Blueprint $table) {
            $table->dropIndex('idx_team_user_team_id');
            $table->dropIndex('idx_team_user_user_id');
            $table->dropIndex('idx_team_user_team_user');
        });

        // TABLES TABLE
        Schema::table('tables', function (Blueprint $table) {
            $table->dropIndex('idx_tables_organization_id');
            $table->dropIndex('idx_tables_number');
            $table->dropIndex('idx_tables_org_number');
        });

        // USER_PLANS TABLE
        Schema::table('user_plans', function (Blueprint $table) {
            $table->dropIndex('idx_user_plans_user_id');
            $table->dropIndex('idx_user_plans_plan_id');
            $table->dropIndex('idx_user_plans_ends_at');
            $table->dropIndex('idx_user_plans_user_active');
        });

        // USERS TABLE
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_name');
        });

        // SPORTS TABLE
        Schema::table('sports', function (Blueprint $table) {
            $table->dropIndex('idx_sports_is_active');
            $table->dropIndex('idx_sports_slug');
        });
    }
};
