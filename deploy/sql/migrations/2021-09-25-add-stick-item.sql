insert into item (
    item_internal_name, item_display_name, item_cost, image, for_sale,
    usage, ignore_stealth, covert, turn_cost, target_damage,
    turn_change, self_use, plural, other_usable, traits
)
 values 
(
    'stick', 'Stick', 1, '', default,
    'A piece of wood', default, default, default, default,
    default, default, 's', default, 'wooden'
);

update item set for_sale = false where item_internal_name = 'shell' or item_internal_name = 'sushi';


--
--  item_id            | integer                |           | not null | nextval('item_item_id_seq'::regclass)
--  item_internal_name | text                   |           | not null | 
--  item_display_name  | text                   |           | not null | 
--  item_cost          | numeric                |           | not null | 
--  image              | character varying(250) |           |          | 
--  for_sale           | boolean                |           |          | false
--  usage              | text                   |           |          | 
--  ignore_stealth     | boolean                |           |          | false
--  covert             | boolean                |           |          | false
--  turn_cost          | integer                |           |          | 1
--  target_damage      | integer                |           |          | 
--  turn_change        | integer                |           |          | 
--  self_use           | boolean                |           |          | false
--  plural             | character varying(20)  |           |          | 
--  other_usable       | boolean                |           |          | false
--  traits             | character varying(250) |           |          | ''::character varying


-- 14 | lantern            | Hooded Lantern    |        50 |               | f        | A lantern for light and flame | t              | t      |         1 |            20 |             | t        |        | t            | 
