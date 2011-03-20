alter table item add column other_usable boolean default false;
update item set other_usable = true where item_internal_name in('shuriken', 'amanita', 'smokebomb', 'caltrops', 'dimmak', 'phosphor', 'ginsengroot', 'tigersalve', 'lantern', 'kunai', 'tessen');
