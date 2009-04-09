ALTER TABLE players OWNER TO developers;
GRANT ALL ON TABLE players TO developers;
SELECT setval('players_player_id_seq', max(player_id)) FROM players;
