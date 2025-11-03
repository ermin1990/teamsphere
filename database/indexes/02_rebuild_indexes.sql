-- =====================================================
-- TeamSphere - Obnavljanje indeksa (DROP + CREATE)
-- =====================================================
-- Skripta BRIŠE postojeće indekse i ponovo ih kreira.
-- Koristi ovo ako:
-- - Sumneš da su indeksi oštećeni
-- - Želiš osigurati da svi indeksi postoje i ispravni su
-- - Nakon velikih migracija podataka
--
-- NAPOMENA: Ova skripta može uzeti više vremena jer prvo
-- briše indekse pa ih ponovo kreira. Za veliku bazu,
-- preporučujemo pokretanje van radnog vremena.
-- 
-- Pokretanje: mysql -u username -p database_name < 02_rebuild_indexes.sql
-- =====================================================

SET @db = DATABASE();

SELECT 'ZAPOČINJEM OBNAVLJANJE INDEKSA...' AS status;

-- ===========================================
-- MATCHES TABLE
-- ===========================================

-- League + Scheduled composite
DROP INDEX IF EXISTS `idx_matches_league_scheduled` ON `matches`;
ALTER TABLE `matches` ADD INDEX `idx_matches_league_scheduled` (`league_id`,`scheduled_at`);

-- Status
DROP INDEX IF EXISTS `idx_matches_status` ON `matches`;
ALTER TABLE `matches` ADD INDEX `idx_matches_status` (`status`);

-- League + Status composite
DROP INDEX IF EXISTS `idx_matches_league_status` ON `matches`;
ALTER TABLE `matches` ADD INDEX `idx_matches_league_status` (`league_id`,`status`);

-- Players
DROP INDEX IF EXISTS `idx_matches_home_player` ON `matches`;
ALTER TABLE `matches` ADD INDEX `idx_matches_home_player` (`home_player_id`);

DROP INDEX IF EXISTS `idx_matches_away_player` ON `matches`;
ALTER TABLE `matches` ADD INDEX `idx_matches_away_player` (`away_player_id`);

-- Teams
DROP INDEX IF EXISTS `idx_matches_home_team` ON `matches`;
ALTER TABLE `matches` ADD INDEX `idx_matches_home_team` (`home_team_id`);

DROP INDEX IF EXISTS `idx_matches_away_team` ON `matches`;
ALTER TABLE `matches` ADD INDEX `idx_matches_away_team` (`away_team_id`);

-- Round
DROP INDEX IF EXISTS `idx_matches_round` ON `matches`;
ALTER TABLE `matches` ADD INDEX `idx_matches_round` (`round`);

-- Created_at
DROP INDEX IF EXISTS `idx_matches_created_at` ON `matches`;
ALTER TABLE `matches` ADD INDEX `idx_matches_created_at` (`created_at`);

SELECT 'Matches indeksi obnovljeni ✓' AS status;

-- ===========================================
-- STANDINGS TABLE
-- ===========================================

DROP INDEX IF EXISTS `idx_standings_league` ON `standings`;
ALTER TABLE `standings` ADD INDEX `idx_standings_league` (`league_id`);

DROP INDEX IF EXISTS `idx_standings_team_player` ON `standings`;
ALTER TABLE `standings` ADD INDEX `idx_standings_team_player` (`team_id`,`player_id`);

DROP INDEX IF EXISTS `idx_standings_position` ON `standings`;
ALTER TABLE `standings` ADD INDEX `idx_standings_position` (`position`);

SELECT 'Standings indeksi obnovljeni ✓' AS status;

-- ===========================================
-- PLAYERS TABLE
-- ===========================================

DROP INDEX IF EXISTS `idx_players_organization` ON `players`;
ALTER TABLE `players` ADD INDEX `idx_players_organization` (`organization_id`);

DROP INDEX IF EXISTS `idx_players_user` ON `players`;
ALTER TABLE `players` ADD INDEX `idx_players_user` (`user_id`);

DROP INDEX IF EXISTS `idx_players_name` ON `players`;
ALTER TABLE `players` ADD INDEX `idx_players_name` (`name`);

DROP INDEX IF EXISTS `idx_players_email` ON `players`;
ALTER TABLE `players` ADD INDEX `idx_players_email` (`email`);

DROP INDEX IF EXISTS `idx_players_created_at` ON `players`;
ALTER TABLE `players` ADD INDEX `idx_players_created_at` (`created_at`);

SELECT 'Players indeksi obnovljeni ✓' AS status;

-- ===========================================
-- TEAMS TABLE
-- ===========================================

DROP INDEX IF EXISTS `idx_teams_league` ON `teams`;
ALTER TABLE `teams` ADD INDEX `idx_teams_league` (`league_id`);

DROP INDEX IF EXISTS `idx_teams_captain` ON `teams`;
ALTER TABLE `teams` ADD INDEX `idx_teams_captain` (`captain_id`);

SELECT 'Teams indeksi obnovljeni ✓' AS status;

-- ===========================================
-- PIVOT TABLES
-- ===========================================

-- team_user
DROP INDEX IF EXISTS `idx_team_user_team` ON `team_user`;
ALTER TABLE `team_user` ADD INDEX `idx_team_user_team` (`team_id`);

DROP INDEX IF EXISTS `idx_team_user_user` ON `team_user`;
ALTER TABLE `team_user` ADD INDEX `idx_team_user_user` (`user_id`);

-- league_user
DROP INDEX IF EXISTS `idx_league_user_league` ON `league_user`;
ALTER TABLE `league_user` ADD INDEX `idx_league_user_league` (`league_id`);

DROP INDEX IF EXISTS `idx_league_user_user` ON `league_user`;
ALTER TABLE `league_user` ADD INDEX `idx_league_user_user` (`user_id`);

-- league_player
DROP INDEX IF EXISTS `idx_league_player_league` ON `league_player`;
ALTER TABLE `league_player` ADD INDEX `idx_league_player_league` (`league_id`);

DROP INDEX IF EXISTS `idx_league_player_player` ON `league_player`;
ALTER TABLE `league_player` ADD INDEX `idx_league_player_player` (`player_id`);

SELECT 'Pivot table indeksi obnovljeni ✓' AS status;

-- ===========================================
-- LEAGUES TABLE
-- ===========================================

DROP INDEX IF EXISTS `idx_leagues_organization` ON `leagues`;
ALTER TABLE `leagues` ADD INDEX `idx_leagues_organization` (`organization_id`);

DROP INDEX IF EXISTS `idx_leagues_sport` ON `leagues`;
ALTER TABLE `leagues` ADD INDEX `idx_leagues_sport` (`sport_id`);

DROP INDEX IF EXISTS `idx_leagues_slug` ON `leagues`;
ALTER TABLE `leagues` ADD INDEX `idx_leagues_slug` (`slug`);

DROP INDEX IF EXISTS `idx_leagues_status` ON `leagues`;
ALTER TABLE `leagues` ADD INDEX `idx_leagues_status` (`status`);

SELECT 'Leagues indeksi obnovljeni ✓' AS status;

-- ===========================================
-- ORGANIZATIONS TABLE
-- ===========================================

DROP INDEX IF EXISTS `idx_organizations_user` ON `organizations`;
ALTER TABLE `organizations` ADD INDEX `idx_organizations_user` (`user_id`);

DROP INDEX IF EXISTS `idx_organizations_slug` ON `organizations`;
ALTER TABLE `organizations` ADD INDEX `idx_organizations_slug` (`slug`);

SELECT 'Organizations indeksi obnovljeni ✓' AS status;

-- ===========================================
-- USERS TABLE
-- ===========================================

DROP INDEX IF EXISTS `idx_users_email` ON `users`;
ALTER TABLE `users` ADD INDEX `idx_users_email` (`email`);

DROP INDEX IF EXISTS `idx_users_created_at` ON `users`;
ALTER TABLE `users` ADD INDEX `idx_users_created_at` (`created_at`);

SELECT 'Users indeksi obnovljeni ✓' AS status;

-- ===========================================
-- ZAVRŠENO
-- ===========================================

SELECT '✅ SVI INDEKSI SU USPJEŠNO OBNOVLJENI!' AS status;
SELECT 'Baza podataka je optimizovana i svi indeksi su svježi.' AS info;
