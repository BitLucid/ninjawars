alter table accounts add column last_ip varchar(100);
alter table accounts add column confirmed int not null default 0;
alter table accounts add column verification_number varchar(100);

update accounts set last_ip = (select ip from players join account_players on player_id = _player_id where _account_id = account_id);
update accounts set confirmed = coalesce((select confirmed from players join account_players on player_id = _player_id where _account_id = account_id), 0);
