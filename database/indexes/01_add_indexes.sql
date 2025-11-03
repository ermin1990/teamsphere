-- =====================================================
-- TeamSphere - Dodavanje indeksa za optimizaciju baze
-- =====================================================
-- Skripta SIGURNO dodaje indekse samo ako ne postoje.
-- Možeš pokrenuti više puta bez problema.
-- 
-- Pokretanje: mysql -u username -p database_name < 01_add_indexes.sql
-- =====================================================

SET @db = DATABASE();

-- ===========================================
-- MATCHES TABLE - najvažnija tabela za upite
-- ===========================================

-- Composite index za league + scheduled_at (listing mečeva po ligi i datumu)
SELECT COUNT(*) INTO @cnt FROM information_schema.statistics WHERE table_schema=@db AND table_name='matches' AND index_name='idx_matches_league_scheduled';
SET @sql = IF(@cnt=0,'ALTER TABLE `matches` ADD INDEX `idx_matches_league_scheduled` (`league_id`,`scheduled_at`)','SELECT "idx_matches_league_scheduled već postoji" AS status');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Index za status (filtriranje live/completed/scheduled mečeva)
SELECT COUNT(*) INTO @cnt FROM information_schema.statistics WHERE table_schema=@db AND table_name='matches' AND index_name='idx_matches_status';
SET @sql = IF(@cnt=0,'ALTER TABLE `matches` ADD INDEX `idx_matches_status` (`status`)','SELECT "idx_matches_status već postoji" AS status');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Composite za league + status (najčešća kombinacija u upitima)
SELECT COUNT(*) INTO @cnt FROM information_schema.statistics WHERE table_schema=@db AND table_name='matches' AND index_name='idx_matches_league_status';
SET @sql = IF(@cnt=0,'ALTER TABLE `matches` ADD INDEX `idx_matches_league_status` (`league_id`,`status`)','SELECT "idx_matches_league_status već postoji" AS status');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Player-based matches (single player view)
SELECT COUNT(*) INTO @cnt FROM information_schema.statistics WHERE table_schema=@db AND table_name='matches' AND index_name='idx_matches_home_player';
SET @sql = IF(@cnt=0,'ALTER TABLE `matches` ADD INDEX `idx_matches_home_player` (`home_player_id`)','SELECT "idx_matches_home_player već postoji" AS status');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT COUNT(*) INTO @cnt FROM information_schema.statistics WHERE table_schema=@db AND table_name='matches' AND index_name='idx_matches_away_player';
SET @sql = IF(@cnt=0,'ALTER TABLE `matches` ADD INDEX `idx_matches_away_player` (`away_player_id`)','SELECT "idx_matches_away_player već postoji" AS status');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Team-based matches
SELECT COUNT(*) INTO @cnt FROM information_schema.statistics WHERE table_schema=@db AND table_name='matches' AND index_name='idx_matches_home_team';
SET @sql = IF(@cnt=0,'ALTER TABLE `matches` ADD INDEX `idx_matches_home_team` (`home_team_id`)','SELECT "idx_matches_home_team već postoji" AS status');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT COUNT(*) INTO @cnt FROM information_schema.statistics WHERE table_schema=@db AND table_name='matches' AND index_name='idx_matches_away_team';
SET @sql = IF(@cnt=0,'ALTER TABLE `matches` ADD INDEX `idx_matches_away_team` (`away_team_id`)','SELECT "idx_matches_away_team već postoji" AS status');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Round-based queries (kola u ligi/turniru)
SELECT COUNT(*) INTO @cnt FROM information_schema.statistics WHERE table_schema=@db AND table_name='matches' AND index_name='idx_matches_round';
SET @sql = IF(@cnt=0,'ALTER TABLE `matches` ADD INDEX `idx_matches_round` (`round`)','SELECT "idx_matches_round već postoji" AS status');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Created_at za sortiranje (najnoviji mečevi)
SELECT COUNT(*) INTO @cnt FROM information_schema.statistics WHERE table_schema=@db AND table_name='matches' AND index_name='idx_matches_created_at';
SET @sql = IF(@cnt=0,'ALTER TABLE `matches` ADD INDEX `idx_matches_created_at` (`created_at`)','SELECT "idx_matches_created_at već postoji" AS status');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ===========================================
-- STANDINGS TABLE
-- ===========================================

-- Competition standings lookup
SELECT COUNT(*) INTO @cnt FROM information_schema.statistics WHERE table_schema=@db AND table_name='standings' AND index_name='idx_standings_competition';
SET @sql = IF(@cnt=0,'ALTER TABLE `standings` ADD INDEX `idx_standings_competition` (`competition_id`)','SELECT "idx_standings_competition već postoji" AS status');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Composite za brže pronalaženje standinsa po učesniku
SELECT COUNT(*) INTO @cnt FROM information_schema.statistics WHERE table_schema=@db AND table_name='standings' AND index_name='idx_standings_team_player';
SET @sql = IF(@cnt=0,'ALTER TABLE `standings` ADD INDEX `idx_standings_team_player` (`team_id`,`player_id`)','SELECT "idx_standings_team_player već postoji" AS status');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Position index za sortiranje
SELECT COUNT(*) INTO @cnt FROM information_schema.statistics WHERE table_schema=@db AND table_name='standings' AND index_name='idx_standings_position';
SET @sql = IF(@cnt=0,'ALTER TABLE `standings` ADD INDEX `idx_standings_position` (`position`)','SELECT "idx_standings_position već postoji" AS status');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ===========================================
-- PLAYERS TABLE
-- ===========================================

-- Organization players lookup
SELECT COUNT(*) INTO @cnt FROM information_schema.statistics WHERE table_schema=@db AND table_name='players' AND index_name='idx_players_organization';
SET @sql = IF(@cnt=0,'ALTER TABLE `players` ADD INDEX `idx_players_organization` (`organization_id`)','SELECT "idx_players_organization već postoji" AS status');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- User's player profile
SELECT COUNT(*) INTO @cnt FROM information_schema.statistics WHERE table_schema=@db AND table_name='players' AND index_name='idx_players_user';
SET @sql = IF(@cnt=0,'ALTER TABLE `players` ADD INDEX `idx_players_user` (`user_id`)','SELECT "idx_players_user već postoji" AS status');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Name search
SELECT COUNT(*) INTO @cnt FROM information_schema.statistics WHERE table_schema=@db AND table_name='players' AND index_name='idx_players_name';
SET @sql = IF(@cnt=0,'ALTER TABLE `players` ADD INDEX `idx_players_name` (`name`(255))','SELECT "idx_players_name već postoji" AS status');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Email search (ako se koristi)
SELECT COUNT(*) INTO @cnt FROM information_schema.statistics WHERE table_schema=@db AND table_name='players' AND index_name='idx_players_email';
SET @sql = IF(@cnt=0,'ALTER TABLE `players` ADD INDEX `idx_players_email` (`email`)','SELECT "idx_players_email već postoji" AS status');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Created_at za sortiranje
SELECT COUNT(*) INTO @cnt FROM information_schema.statistics WHERE table_schema=@db AND table_name='players' AND index_name='idx_players_created_at';
SET @sql = IF(@cnt=0,'ALTER TABLE `players` ADD INDEX `idx_players_created_at` (`created_at`)','SELECT "idx_players_created_at već postoji" AS status');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ===========================================
-- TEAMS TABLE
-- ===========================================

-- Competition teams lookup
SELECT COUNT(*) INTO @cnt FROM information_schema.statistics WHERE table_schema=@db AND table_name='teams' AND index_name='idx_teams_competition';
SET @sql = IF(@cnt=0,'ALTER TABLE `teams` ADD INDEX `idx_teams_competition` (`competition_id`)','SELECT "idx_teams_competition već postoji" AS status');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Captain lookup
SELECT COUNT(*) INTO @cnt FROM information_schema.statistics WHERE table_schema=@db AND table_name='teams' AND index_name='idx_teams_captain';
SET @sql = IF(@cnt=0,'ALTER TABLE `teams` ADD INDEX `idx_teams_captain` (`captain_id`)','SELECT "idx_teams_captain već postoji" AS status');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ===========================================
-- PIVOT TABLES
-- ===========================================

-- team_user pivot
SELECT COUNT(*) INTO @cnt FROM information_schema.statistics WHERE table_schema=@db AND table_name='team_user' AND index_name='idx_team_user_team';
SET @sql = IF(@cnt=0,'ALTER TABLE `team_user` ADD INDEX `idx_team_user_team` (`team_id`)','SELECT "idx_team_user_team već postoji" AS status');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT COUNT(*) INTO @cnt FROM information_schema.statistics WHERE table_schema=@db AND table_name='team_user' AND index_name='idx_team_user_user';
SET @sql = IF(@cnt=0,'ALTER TABLE `team_user` ADD INDEX `idx_team_user_user` (`user_id`)','SELECT "idx_team_user_user već postoji" AS status');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- competition_user pivot
SELECT COUNT(*) INTO @cnt FROM information_schema.statistics WHERE table_schema=@db AND table_name='competition_user' AND index_name='idx_competition_user_competition';
SET @sql = IF(@cnt=0,'ALTER TABLE `competition_user` ADD INDEX `idx_competition_user_competition` (`competition_id`)','SELECT "idx_competition_user_competition već postoji" AS status');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT COUNT(*) INTO @cnt FROM information_schema.statistics WHERE table_schema=@db AND table_name='competition_user' AND index_name='idx_competition_user_user';
SET @sql = IF(@cnt=0,'ALTER TABLE `competition_user` ADD INDEX `idx_competition_user_user` (`user_id`)','SELECT "idx_competition_user_user već postoji" AS status');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- competition_player pivot
SELECT COUNT(*) INTO @cnt FROM information_schema.statistics WHERE table_schema=@db AND table_name='competition_player' AND index_name='idx_competition_player_competition';
SET @sql = IF(@cnt=0,'ALTER TABLE `competition_player` ADD INDEX `idx_competition_player_competition` (`competition_id`)','SELECT "idx_competition_player_competition već postoji" AS status');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT COUNT(*) INTO @cnt FROM information_schema.statistics WHERE table_schema=@db AND table_name='competition_player' AND index_name='idx_competition_player_player';
SET @sql = IF(@cnt=0,'ALTER TABLE `competition_player` ADD INDEX `idx_competition_player_player` (`player_id`)','SELECT "idx_competition_player_player već postoji" AS status');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ===========================================
-- COMPETITIONS TABLE
-- ===========================================

-- Organization's competitions
SELECT COUNT(*) INTO @cnt FROM information_schema.statistics WHERE table_schema=@db AND table_name='competitions' AND index_name='idx_competitions_organization';
SET @sql = IF(@cnt=0,'ALTER TABLE `competitions` ADD INDEX `idx_competitions_organization` (`organization_id`)','SELECT "idx_competitions_organization već postoji" AS status');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Sport filter
SELECT COUNT(*) INTO @cnt FROM information_schema.statistics WHERE table_schema=@db AND table_name='competitions' AND index_name='idx_competitions_sport';
SET @sql = IF(@cnt=0,'ALTER TABLE `competitions` ADD INDEX `idx_competitions_sport` (`sport_id`)','SELECT "idx_competitions_sport već postoji" AS status');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Slug lookup (public URLs)
SELECT COUNT(*) INTO @cnt FROM information_schema.statistics WHERE table_schema=@db AND table_name='competitions' AND index_name='idx_competitions_slug';
SET @sql = IF(@cnt=0,'ALTER TABLE `competitions` ADD INDEX `idx_competitions_slug` (`slug`)','SELECT "idx_competitions_slug već postoji" AS status');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Status filter (active/completed competitions)
SELECT COUNT(*) INTO @cnt FROM information_schema.statistics WHERE table_schema=@db AND table_name='competitions' AND index_name='idx_competitions_status';
SET @sql = IF(@cnt=0,'ALTER TABLE `competitions` ADD INDEX `idx_competitions_status` (`status`)','SELECT "idx_competitions_status već postoji" AS status');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ===========================================
-- ORGANIZATIONS TABLE
-- ===========================================

-- User's organizations
SELECT COUNT(*) INTO @cnt FROM information_schema.statistics WHERE table_schema=@db AND table_name='organizations' AND index_name='idx_organizations_user';
SET @sql = IF(@cnt=0,'ALTER TABLE `organizations` ADD INDEX `idx_organizations_user` (`user_id`)','SELECT "idx_organizations_user već postoji" AS status');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Slug lookup
SELECT COUNT(*) INTO @cnt FROM information_schema.statistics WHERE table_schema=@db AND table_name='organizations' AND index_name='idx_organizations_slug';
SET @sql = IF(@cnt=0,'ALTER TABLE `organizations` ADD INDEX `idx_organizations_slug` (`slug`)','SELECT "idx_organizations_slug već postoji" AS status');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ===========================================
-- USERS TABLE
-- ===========================================

-- Email (obično već postoji unique, ali dodajemo za sigurnost)
SELECT COUNT(*) INTO @cnt FROM information_schema.statistics WHERE table_schema=@db AND table_name='users' AND index_name='idx_users_email';
SET @sql = IF(@cnt=0,'ALTER TABLE `users` ADD INDEX `idx_users_email` (`email`(255))','SELECT "idx_users_email već postoji (ili je unique)" AS status');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Created_at za sortiranje novih korisnika
SELECT COUNT(*) INTO @cnt FROM information_schema.statistics WHERE table_schema=@db AND table_name='users' AND index_name='idx_users_created_at';
SET @sql = IF(@cnt=0,'ALTER TABLE `users` ADD INDEX `idx_users_created_at` (`created_at`)','SELECT "idx_users_created_at već postoji" AS status');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ===========================================
-- ZAVRŠENO
-- ===========================================

SELECT '✅ SVI INDEKSI SU USPJEŠNO DODANI!' AS status;
SELECT 'Baza podataka je optimizovana za brže izvršavanje upita.' AS info;
