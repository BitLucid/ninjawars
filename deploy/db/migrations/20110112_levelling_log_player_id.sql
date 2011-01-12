alter table levelling_log add column _player_id int;
update levelling_log set _player_id = (select player_id FROM players WHERE players.uname = levelling_log.uname);
alter table levelling_log alter _player_id set not null;
alter table levelling_log add foreign key (_player_id) REFERENCES players(player_id) ON UPDATE CASCADE ON DELETE CASCADE;
alter table levelling_log drop COLUMN uname;
