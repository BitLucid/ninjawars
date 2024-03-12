update item set traits = traits||' food' where item_internal_name in('sushi', 'ginsengroot', 'sake', 'fugu', 'charcoal');
update item set traits = traits||' drink' where item_internal_name in('sake');
update item set traits = traits||' medicine' where item_internal_name in('ginsengroot');
update item set traits = traits||' poisonous' where item_internal_name in('fugu');
update item set traits = traits||' fuel' where item_internal_name in('charcoal', 'stick', 'phosphor', 'smokebomb');
update item set traits = traits||' explosive' where item_internal_name in('smokebomb');
update item set traits = traits||' totem' where item_internal_name in('mirror', 'lantern', 'prayerwheel', 'shell', 'mask');
