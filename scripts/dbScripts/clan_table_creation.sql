create table clan (clan_id serial not null primary key, clan_name varchar(255) unique not null, clan_created_date timestamp not null default now(), _creator_player_id int not null references players(player_id) on update cascade);

insert into clan (clan_name, _creator_player_id) select clan_long_name, player_id From players where uname = clan;

create table clan_player (_clan_id int not null references clan(clan_id) on update cascade on delete cascade, _player_id int not null references players(player_id) on update cascade on delete cascade, member_level int not null default 0);

insert into clan_player (_clan_id, _player_id, member_level) select clan_id, member.player_id AS member_id, CASE WHEN leader.player_id = member.player_id THEN 1 ELSE 0 END AS leader_id FROM clan JOIN players leader ON _creator_player_id = leader.player_id JOIN players member ON member.clan = leader.uname WHERE member.confirmed = 1 order by clan_id;

alter table players drop column clan;
alter table players drop column clan_long_name;
