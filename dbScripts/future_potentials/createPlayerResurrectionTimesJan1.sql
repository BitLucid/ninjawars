alter table players add column resurrection_time integer default (round(random()*7)*3) NOT NULL;
