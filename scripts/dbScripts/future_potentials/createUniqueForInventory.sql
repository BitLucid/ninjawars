alter table inventory add constraint unique_name_to_item unique (owner, item);
