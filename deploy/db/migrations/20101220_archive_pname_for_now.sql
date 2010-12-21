alter table players rename pname to pname_backup;
alter table players alter column pname_backup drop not null;
