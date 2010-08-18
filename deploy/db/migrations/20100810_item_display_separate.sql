

alter table inventory add column item_type int;

-- select * from inventory where item not in (select item_display_name from item);

delete from inventory where item not in (select item_display_name from item);

update inventory set item_type = (select item_id from item where item.item_display_name = inventory.item); 
alter table inventory add column item_type_string_backup varchar;
update inventory set item_type_string_backup = item;
