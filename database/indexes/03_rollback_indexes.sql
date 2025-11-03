-- =====================================================
-- TeamSphere - ROLLBACK - Brisanje svih dodanih indeksa
-- =====================================================
-- Skripta BRIŠE sve custom indekse koje smo dodali.
-- Vraća bazu na početno stanje (ostavlja samo:
-- - PRIMARY KEY indekse
-- - FOREIGN KEY indekse koje Laravel kreira automatski
-- - UNIQUE indekse)
--
-- Koristi ovo ako:
-- - Indeksi uzrokuju probleme
-- - Želiš testirati performanse BEZ indeksa
-- - Nešto nije u redu i želiš vratiti na početno stanje
-- 
-- NAPOMENA: Nakon brisanja indeksa, upiti će biti sporiji!
-- Ova skripta je SIGURNA - neće obrisati FK indekse ni
-- primarni ključ. Možeš je pokrenuti bez bojazni.
--
-- Pokretanje: mysql -u username -p database_name < 03_rollback_indexes.sql
-- =====================================================

SELECT 'ZAPOČINJEM BRISANJE CUSTOM INDEKSA...' AS status;
SELECT 'Ova skripta briše SAMO custom indekse, ne dira PRIMARY/FOREIGN ključeve.' AS info;

-- ===========================================
-- MATCHES TABLE
-- ===========================================

DROP INDEX IF EXISTS `idx_matches_league_scheduled` ON `matches`;
DROP INDEX IF EXISTS `idx_matches_status` ON `matches`;
DROP INDEX IF EXISTS `idx_matches_league_status` ON `matches`;
DROP INDEX IF EXISTS `idx_matches_home_player` ON `matches`;
DROP INDEX IF EXISTS `idx_matches_away_player` ON `matches`;
DROP INDEX IF EXISTS `idx_matches_home_team` ON `matches`;
DROP INDEX IF EXISTS `idx_matches_away_team` ON `matches`;
DROP INDEX IF EXISTS `idx_matches_round` ON `matches`;
DROP INDEX IF EXISTS `idx_matches_created_at` ON `matches`;

SELECT 'Matches indeksi obrisani ✓' AS status;

-- ===========================================
-- STANDINGS TABLE
-- ===========================================

DROP INDEX IF EXISTS `idx_standings_league` ON `standings`;
DROP INDEX IF EXISTS `idx_standings_team_player` ON `standings`;
DROP INDEX IF EXISTS `idx_standings_position` ON `standings`;

SELECT 'Standings indeksi obrisani ✓' AS status;

-- ===========================================
-- PLAYERS TABLE
-- ===========================================

DROP INDEX IF EXISTS `idx_players_organization` ON `players`;
DROP INDEX IF EXISTS `idx_players_user` ON `players`;
DROP INDEX IF EXISTS `idx_players_name` ON `players`;
DROP INDEX IF EXISTS `idx_players_email` ON `players`;
DROP INDEX IF EXISTS `idx_players_created_at` ON `players`;

SELECT 'Players indeksi obrisani ✓' AS status;

-- ===========================================
-- TEAMS TABLE
-- ===========================================

DROP INDEX IF EXISTS `idx_teams_league` ON `teams`;
DROP INDEX IF EXISTS `idx_teams_captain` ON `teams`;

SELECT 'Teams indeksi obrisani ✓' AS status;

-- ===========================================
-- PIVOT TABLES
-- ===========================================

DROP INDEX IF EXISTS `idx_team_user_team` ON `team_user`;
DROP INDEX IF EXISTS `idx_team_user_user` ON `team_user`;
DROP INDEX IF EXISTS `idx_league_user_league` ON `league_user`;
DROP INDEX IF EXISTS `idx_league_user_user` ON `league_user`;
DROP INDEX IF EXISTS `idx_league_player_league` ON `league_player`;
DROP INDEX IF EXISTS `idx_league_player_player` ON `league_player`;

SELECT 'Pivot table indeksi obrisani ✓' AS status;

-- ===========================================
-- LEAGUES TABLE
-- ===========================================

DROP INDEX IF EXISTS `idx_leagues_organization` ON `leagues`;
DROP INDEX IF EXISTS `idx_leagues_sport` ON `leagues`;
DROP INDEX IF EXISTS `idx_leagues_slug` ON `leagues`;
DROP INDEX IF EXISTS `idx_leagues_status` ON `leagues`;

SELECT 'Leagues indeksi obrisani ✓' AS status;

-- ===========================================
-- ORGANIZATIONS TABLE
-- ===========================================

DROP INDEX IF EXISTS `idx_organizations_user` ON `organizations`;
DROP INDEX IF EXISTS `idx_organizations_slug` ON `organizations`;

SELECT 'Organizations indeksi obrisani ✓' AS status;

-- ===========================================
-- USERS TABLE
-- ===========================================

DROP INDEX IF EXISTS `idx_users_email` ON `users`;
DROP INDEX IF EXISTS `idx_users_created_at` ON `users`;

SELECT 'Users indeksi obrisani ✓' AS status;

-- ===========================================
-- ZAVRŠENO - ROLLBACK USPJEŠAN
-- ===========================================

SELECT '✅ SVI CUSTOM INDEKSI SU USPJEŠNO OBRISANI!' AS status;
SELECT 'Baza je vraćena na početno stanje (samo PK i FK indeksi).' AS info;
SELECT '⚠️  NAPOMENA: Upiti će sada biti sporiji bez custom indeksa.' AS warning;
