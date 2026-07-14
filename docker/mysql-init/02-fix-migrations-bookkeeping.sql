-- The production dump's `migrations` table is missing records for several migrations
-- whose schema changes are ALREADY present in the dumped tables (applied out-of-band at
-- some point, never recorded). Without this fixup, `php artisan migrate --force` tries to
-- re-run them on every fresh deploy and fails with "Duplicate column" / "Table already
-- exists" errors. This only backfills bookkeeping - it does not change any schema or data.
--
-- (2025_12_25_113100_create_team_coaches_table is intentionally NOT listed here: that one
-- is genuinely not yet applied in the dump, so `migrate` is left to run it for real.)
INSERT IGNORE INTO migrations (migration, batch) VALUES
('2025_10_30_194557_add_group_rounds_to_competitions_table', 17),
('2025_11_29_132758_add_manual_order_to_standings_table', 17),
('2025_12_23_173936_create_categories_table', 17),
('2025_12_23_173942_add_category_id_to_competitions_table', 17),
('2025_12_23_200734_update_teams_table_for_organizations', 17),
('2025_12_23_200735_create_team_matches_table', 17),
('2025_12_23_200735_create_team_player_table', 17),
('2025_12_23_200735_update_matches_table_for_team_matches', 17),
('2025_12_23_204029_add_is_double_round_to_competitions_table', 17),
('2025_12_25_112851_add_coach_to_teams_table', 17),
('2025_12_25_120749_add_captains_and_referee_to_matches_table', 17),
('2025_12_25_121301_add_captains_and_referee_to_team_matches_table', 17),
('2026_01_28_100000_create_organization_links_table', 17),
('2026_01_28_110000_add_logo_url_to_organizations_table', 17);
