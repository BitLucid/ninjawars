DROP TABLE player_rank CASCADE;
CREATE TABLE player_rank (
    rank_id serial NOT NULL,
    _player_id integer NOT NULL,
    score integer
);
ALTER TABLE player_rank OWNER TO developers;
GRANT ALL ON TABLE player_rank TO developers;


truncate player_rank;
SELECT setval('player_rank_rank_id_seq', 1, false);

insert into player_rank (_player_id, score) select player_id, ((level*800) + (kills*8) + ((1 + gold )/100) - (days*8)) AS score from players WHERE confirmed = 1 ORDER BY score DESC;

-- The Where clause of the insert needs to match the where clause of the rankings view.

DROP TABLE rankings;
CREATE VIEW rankings AS SELECT player_rank.rank_id, players.player_id, player_rank.score, uname, class, level, cast(CASE WHEN health = 0 THEN 0 ELSE 1 END AS bool) AS alive,days,CASE WHEN clan = '' THEN '-' WHEN clan_long_name <> '' THEN clan_long_name ELSE clan END AS clan FROM player_rank JOIN players ON players.player_id = player_rank._player_id WHERE confirmed = 1 ORDER BY player_rank.rank_id ASC; 

ALTER TABLE rankings OWNER TO developers;
GRANT ALL ON TABLE rankings TO developers;

select 'Result of ranking formula.';
select rank_id, uname, score, level, gold, kills, days from player_rank join players on _player_id=player_id order by rank_id limit 50;

alter table players owner to developers;
grant all on table players to developers;
