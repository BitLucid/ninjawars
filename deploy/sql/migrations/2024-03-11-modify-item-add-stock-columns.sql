alter table item add column stock int not null default 100;
alter table item add column stock_refresh_rate int not null default 5;
alter table item add column stock_refresh_amount int not null default 1;

-- Adds in ways for the stocking of the store to occur
