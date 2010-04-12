create table item (item_id serial primary key not null, item_internal_name text not null unique, item_display_name text not null unique, item_cost numeric not null);
insert into item (default, 'firescroll', 'Fire Scroll', 175), (default, 'icescroll', 'Ice Scroll', 125), (default, 'speedscroll', 'Speed Scroll', 225), (default, 'stealthscroll', 'Stealth Scroll', 150), (default, 'shuriken', 'Shuriken', 50), (default, 'dimmak', 'Dim Mak', 1000);
delete from inventory where item = '';
alter table inventory add foreign key (item) references item(item_display_name) on update cascade on delete cascade;
