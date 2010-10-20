alter table players add column ki integer not null default 0;
alter table players add column stamina integer not null default 0;
alter table players add column speed integer not null default 0;
alter table players add column karma integer not null default 0;

update players set stamina = strength, speed = strength;

alter table accounts add column karma_total integer not null default 0;
