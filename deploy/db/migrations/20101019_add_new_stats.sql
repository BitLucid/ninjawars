alter table players add column ki integer not null default 0;
alter table players add column stamina integer not null default 0;
alter table players add column speed integer not null default 0;
alter table players add column karma integer not null default 0;
alter table players add column kills_gained integer not null default 0;
alter table players add column kills_used integer not null default 0;

update players set stamina = strength, speed = strength;
update players set energy = 1000;

update players set kills_gained = (kills + ((level-1)*(level-1)+(level-1))/2*5);
update players set kills_used = ((level-1)*(level-1)+(level-1))/2*5;

alter table accounts add column karma_total integer not null default 0;
