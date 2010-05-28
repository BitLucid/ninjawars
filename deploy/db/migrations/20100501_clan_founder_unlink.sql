ALTER TABLE clan ADD COLUMN clan_founder text;
UPDATE clan SET clan_founder = (SELECT uname FROM players WHERE player_id = _creator_player_id);
ALTER TABLE clan DROP COLUMN _creator_player_id;
