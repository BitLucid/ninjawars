CREATE TABLE class (
	class_id serial not null primary key
	, class_name text not null unique
	, class_active boolean default true
);

INSERT INTO class VALUES
	(default, 'Black', default)
	, (default, 'Blue', default)
	, (default, 'Red', default)
	, (default, 'White', default)
	, (default, 'Gray', default);

ALTER TABLE players ADD COLUMN _class_id int REFERENCES class(class_id) ON UPDATE CASCADE;

UPDATE players SET _class_id = (SELECT class_id FROM class WHERE class_name = class);

ALTER TABLE players ALTER COLUMN _class_id SET NOT NULL;

DROP VIEW rankings;

CREATE VIEW rankings AS SELECT player_rank.rank_id, players.player_id, player_rank.score, players.uname, class.class_name AS class, players.level,
        CASE
            WHEN players.health = 0 THEN 0
            ELSE 1
        END::boolean AS alive, players.days    FROM player_rank
   JOIN players ON players.player_id = player_rank._player_id
   JOIN class ON _class_id = class_id
  WHERE players.confirmed = 1
  ORDER BY player_rank.rank_id;

ALTER TABLE players DROP COLUMN class;
