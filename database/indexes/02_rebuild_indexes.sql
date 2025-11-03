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
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'matches' AND INDEX_NAME = 'idx_matches_league_scheduled') > 0, 'DROP INDEX `idx_matches_league_scheduled` ON `matches`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
ALTER TABLE `matches` ADD INDEX `idx_matches_league_scheduled` (`league_id`,`scheduled_at`);

-- Status
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'matches' AND INDEX_NAME = 'idx_matches_status') > 0, 'DROP INDEX `idx_matches_status` ON `matches`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
ALTER TABLE `matches` ADD INDEX `idx_matches_status` (`status`);

-- League + Status composite
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'matches' AND INDEX_NAME = 'idx_matches_league_status') > 0, 'DROP INDEX `idx_matches_league_status` ON `matches`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
ALTER TABLE `matches` ADD INDEX `idx_matches_league_status` (`league_id`,`status`);

-- Players
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'matches' AND INDEX_NAME = 'idx_matches_home_player') > 0, 'DROP INDEX `idx_matches_home_player` ON `matches`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
ALTER TABLE `matches` ADD INDEX `idx_matches_home_player` (`home_player_id`);

SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'matches' AND INDEX_NAME = 'idx_matches_away_player') > 0, 'DROP INDEX `idx_matches_away_player` ON `matches`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
ALTER TABLE `matches` ADD INDEX `idx_matches_away_player` (`away_player_id`);

-- Teams
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'matches' AND INDEX_NAME = 'idx_matches_home_team') > 0, 'DROP INDEX `idx_matches_home_team` ON `matches`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
ALTER TABLE `matches` ADD INDEX `idx_matches_home_team` (`home_team_id`);

SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'matches' AND INDEX_NAME = 'idx_matches_away_team') > 0, 'DROP INDEX `idx_matches_away_team` ON `matches`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
ALTER TABLE `matches` ADD INDEX `idx_matches_away_team` (`away_team_id`);

-- Round
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'matches' AND INDEX_NAME = 'idx_matches_round') > 0, 'DROP INDEX `idx_matches_round` ON `matches`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
ALTER TABLE `matches` ADD INDEX `idx_matches_round` (`round`);

-- Created_at
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'matches' AND INDEX_NAME = 'idx_matches_created_at') > 0, 'DROP INDEX `idx_matches_created_at` ON `matches`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
ALTER TABLE `matches` ADD INDEX `idx_matches_created_at` (`created_at`);

SELECT 'Matches indeksi obnovljeni ✓' AS status;

-- ===========================================
-- STANDINGS TABLE
-- ===========================================

SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'standings' AND INDEX_NAME = 'idx_standings_league') > 0, 'DROP INDEX `idx_standings_league` ON `standings`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
ALTER TABLE `standings` ADD INDEX `idx_standings_league` (`league_id`);

SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'standings' AND INDEX_NAME = 'idx_standings_team_player') > 0, 'DROP INDEX `idx_standings_team_player` ON `standings`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
ALTER TABLE `standings` ADD INDEX `idx_standings_team_player` (`team_id`,`player_id`);

SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'standings' AND INDEX_NAME = 'idx_standings_position') > 0, 'DROP INDEX `idx_standings_position` ON `standings`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
ALTER TABLE `standings` ADD INDEX `idx_standings_position` (`position`);

SELECT 'Standings indeksi obnovljeni ✓' AS status;

-- ===========================================
-- PLAYERS TABLE
-- ===========================================

SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'players' AND INDEX_NAME = 'idx_players_organization') > 0, 'DROP INDEX `idx_players_organization` ON `players`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
ALTER TABLE `players` ADD INDEX `idx_players_organization` (`organization_id`);

SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'players' AND INDEX_NAME = 'idx_players_user') > 0, 'DROP INDEX `idx_players_user` ON `players`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
ALTER TABLE `players` ADD INDEX `idx_players_user` (`user_id`);

SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'players' AND INDEX_NAME = 'idx_players_name') > 0, 'DROP INDEX `idx_players_name` ON `players`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
ALTER TABLE `players` ADD INDEX `idx_players_name` (`name`);

SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'players' AND INDEX_NAME = 'idx_players_email') > 0, 'DROP INDEX `idx_players_email` ON `players`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
ALTER TABLE `players` ADD INDEX `idx_players_email` (`email`);

SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'players' AND INDEX_NAME = 'idx_players_created_at') > 0, 'DROP INDEX `idx_players_created_at` ON `players`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
ALTER TABLE `players` ADD INDEX `idx_players_created_at` (`created_at`);

SELECT 'Players indeksi obnovljeni ✓' AS status;

-- ===========================================
-- TEAMS TABLE
-- ===========================================

SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'teams' AND INDEX_NAME = 'idx_teams_league') > 0, 'DROP INDEX `idx_teams_league` ON `teams`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
ALTER TABLE `teams` ADD INDEX `idx_teams_league` (`league_id`);

SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'teams' AND INDEX_NAME = 'idx_teams_captain') > 0, 'DROP INDEX `idx_teams_captain` ON `teams`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
ALTER TABLE `teams` ADD INDEX `idx_teams_captain` (`captain_id`);

SELECT 'Teams indeksi obnovljeni ✓' AS status;

-- ===========================================
-- PIVOT TABLES
-- ===========================================

-- team_user
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'team_user' AND INDEX_NAME = 'idx_team_user_team') > 0, 'DROP INDEX `idx_team_user_team` ON `team_user`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
ALTER TABLE `team_user` ADD INDEX `idx_team_user_team` (`team_id`);

SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'team_user' AND INDEX_NAME = 'idx_team_user_user') > 0, 'DROP INDEX `idx_team_user_user` ON `team_user`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
ALTER TABLE `team_user` ADD INDEX `idx_team_user_user` (`user_id`);

-- league_user
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'league_user' AND INDEX_NAME = 'idx_league_user_league') > 0, 'DROP INDEX `idx_league_user_league` ON `league_user`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
ALTER TABLE `league_user` ADD INDEX `idx_league_user_league` (`league_id`);

SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'league_user' AND INDEX_NAME = 'idx_league_user_user') > 0, 'DROP INDEX `idx_league_user_user` ON `league_user`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
ALTER TABLE `league_user` ADD INDEX `idx_league_user_user` (`user_id`);

-- league_player
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'league_player' AND INDEX_NAME = 'idx_league_player_league') > 0, 'DROP INDEX `idx_league_player_league` ON `league_player`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
ALTER TABLE `league_player` ADD INDEX `idx_league_player_league` (`league_id`);

SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'league_player' AND INDEX_NAME = 'idx_league_player_player') > 0, 'DROP INDEX `idx_league_player_player` ON `league_player`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
ALTER TABLE `league_player` ADD INDEX `idx_league_player_player` (`player_id`);

SELECT 'Pivot table indeksi obnovljeni ✓' AS status;

-- ===========================================
-- LEAGUES TABLE
-- ===========================================

SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'leagues' AND INDEX_NAME = 'idx_leagues_organization') > 0, 'DROP INDEX `idx_leagues_organization` ON `leagues`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
ALTER TABLE `leagues` ADD INDEX `idx_leagues_organization` (`organization_id`);

SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'leagues' AND INDEX_NAME = 'idx_leagues_sport') > 0, 'DROP INDEX `idx_leagues_sport` ON `leagues`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
ALTER TABLE `leagues` ADD INDEX `idx_leagues_sport` (`sport_id`);

SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'leagues' AND INDEX_NAME = 'idx_leagues_slug') > 0, 'DROP INDEX `idx_leagues_slug` ON `leagues`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
ALTER TABLE `leagues` ADD INDEX `idx_leagues_slug` (`slug`);

SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'leagues' AND INDEX_NAME = 'idx_leagues_status') > 0, 'DROP INDEX `idx_leagues_status` ON `leagues`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
ALTER TABLE `leagues` ADD INDEX `idx_leagues_status` (`status`);

SELECT 'Leagues indeksi obnovljeni ✓' AS status;

-- ===========================================
-- ORGANIZATIONS TABLE
-- ===========================================

SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'organizations' AND INDEX_NAME = 'idx_organizations_user') > 0, 'DROP INDEX `idx_organizations_user` ON `organizations`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
ALTER TABLE `organizations` ADD INDEX `idx_organizations_user` (`user_id`);

SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'organizations' AND INDEX_NAME = 'idx_organizations_slug') > 0, 'DROP INDEX `idx_organizations_slug` ON `organizations`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
ALTER TABLE `organizations` ADD INDEX `idx_organizations_slug` (`slug`);

SELECT 'Organizations indeksi obnovljeni ✓' AS status;

-- ===========================================
-- USERS TABLE
-- ===========================================

SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND INDEX_NAME = 'idx_users_email') > 0, 'DROP INDEX `idx_users_email` ON `users`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
ALTER TABLE `users` ADD INDEX `idx_users_email` (`email`);

SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND INDEX_NAME = 'idx_users_created_at') > 0, 'DROP INDEX `idx_users_created_at` ON `users`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
ALTER TABLE `users` ADD INDEX `idx_users_created_at` (`created_at`);

SELECT 'Users indeksi obnovljeni ✓' AS status;

-- ===========================================
-- ZAVRŠENO
-- ===========================================

SELECT '✅ SVI INDEKSI SU USPJEŠNO OBNOVLJENI!' AS status;
SELECT 'Baza podataka je optimizovana i svi indeksi su svježi.' AS info;
