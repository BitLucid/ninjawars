alter table messages drop column send_from;
alter table messages drop column send_to;
alter table messages add column send_to int;
alter table messages add column send_from int;
alter table messages add column unread int default 1;

