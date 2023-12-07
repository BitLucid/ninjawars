update players set created_date = now() where created_date is null;
alter table players alter column created_date set not null;
