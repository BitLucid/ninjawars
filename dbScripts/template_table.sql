DROP TABLE player_rank CASCADE;
CREATE TABLE player_rank (
    rank_id serial NOT NULL,
    _player_id integer NOT NULL,
    score integer
);
ALTER TABLE player_rank OWNER TO developers;
GRANT ALL ON TABLE player_rank TO developers;
