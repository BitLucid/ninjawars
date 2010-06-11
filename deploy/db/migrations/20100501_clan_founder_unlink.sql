alter table clan add column original_creator int;
update clan set original_creator = _creator_player_id;
