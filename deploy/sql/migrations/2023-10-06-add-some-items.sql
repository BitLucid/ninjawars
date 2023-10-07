insert into item (
item_internal_name,
item_display_name,
item_cost,
image,
for_sale,
    usage, ignore_stealth, covert, turn_cost, target_damage,
    turn_change, self_use, plural, other_usable, traits
)
values 
(
'egg',
'Egg',
1,
'',
default,
    'The egg of some creature', default, default, default, default,
    default, default, 's', default, 'food'
);
