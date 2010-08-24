alter table inventory rename column item to item_type_string_backup;
alter table inventory drop constraint inventory_item_fkey;
alter table inventory ALTER COLUMN item_type_string_backup DROP NOT NULL;
