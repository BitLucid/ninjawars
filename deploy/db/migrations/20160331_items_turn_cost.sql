update item set turn_cost = 1 where turn_cost is null;
alter table item alter turn_cost set default 1;