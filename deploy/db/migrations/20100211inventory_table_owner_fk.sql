ALTER TABLE inventory RENAME COLUMN owner TO owner_name;
ALTER TABLE inventory ADD COLUMN owner int references players(player_id) ON DELETE CASCADE ON UPDATE CASCADE;
UPDATE inventory SET owner = (SELECT player_id FROM players WHERE owner_name = uname);
ALTER TABLE inventory ALTER COLUMN owner SET NOT NULL;
ALTER TABLE inventory DROP COLUMN owner_name;
ALTER TABLE inventory ADD PRIMARY KEY(item_id);
ALTER TABLE inventory ADD unique(owner, item);
