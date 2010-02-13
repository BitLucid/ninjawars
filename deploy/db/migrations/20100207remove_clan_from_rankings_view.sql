drop view rankings;
create view rankings as SELECT player_rank.rank_id, players.player_id, player_rank.score, players.uname, players.class, players.level, 
        CASE
            WHEN players.health = 0 THEN 0
            ELSE 1
        END::boolean AS alive, players.days    FROM player_rank
   JOIN players ON players.player_id = player_rank._player_id
  WHERE players.confirmed = 1
  ORDER BY player_rank.rank_id;
