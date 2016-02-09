--
-- Name: rankings; Type: VIEW; Schema: public; Owner: developers
--

CREATE VIEW rankings AS
    SELECT player_rank.rank_id, players.player_id, player_rank.score, players.uname, class.class_name, players.level, (CASE WHEN (players.health = 0) THEN 0 ELSE 1 END)::boolean AS alive, players.days FROM ((player_rank JOIN players ON ((players.player_id = player_rank._player_id))) JOIN class ON ((class.class_id = players._class_id))) WHERE (players.active = 1) ORDER BY player_rank.rank_id;
