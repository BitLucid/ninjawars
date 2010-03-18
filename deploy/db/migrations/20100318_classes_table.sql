create table class (class_id serial not null primary key, class_name text not null unique, class_active boolean default true);
insert into class values (default, 'Black', default), (default, 'Blue', default), (default, 'Red', default), (default, 'White', default), (default, 'Gray', false);
alter table players add column _class_id int references class(class_id) on update cascade;
update players set _class_id = (select class_id FROM class WHERE class_name = class);
alter table players alter column _class_id set not null;

drop view rankings;
create view rankings as SELECT player_rank.rank_id, players.player_id, player_rank.score, players.uname, class.class_name AS class, players.level,
        CASE
            WHEN players.health = 0 THEN 0
            ELSE 1
        END::boolean AS alive, players.days    FROM player_rank
   JOIN players ON players.player_id = player_rank._player_id
   JOIN class ON _class_id = class_id
  WHERE players.confirmed = 1
  ORDER BY player_rank.rank_id;

alter table players drop column class;
