alter table item add column image varchar(250);
alter table item add column for_sale boolean default FALSE;
alter table item add column usage text;
alter table item add column ignore_stealth boolean default FALSE;
alter table item add column covert boolean default FALSE;
alter table item add column turn_cost int;
alter table item add column target_damage int;
alter table item add column turn_change int;
alter table item add column self_use boolean default FALSE;
alter table item add column plural varchar(20);

--	protected $m_name;
--	protected $m_ignoresStealth;
--	protected $m_targetDamage;
--	protected $m_turnCost;
--	protected $m_turnChange;
--	protected $m_covert;
--	protected $m_type;

-- Set the defaults to where they have to be.
update item set for_sale = TRUE;
update item set image = 'scroll.png';
update item set for_sale = FALSE where item_internal_name = 'dimmak' or item_internal_name = 'tigersalve' or item_internal_name = 'ginsengroot';
update item set self_use = TRUE where item_internal_name in ('tigersalve', 'ginsengroot', 'amanita', 'smokebomb');
update item set plural = 's' where item_internal_name not in ('shuriken', 'caltrops', 'dimmak');
update item set usage = 'Reduces health' where item_internal_name = 'phosphor';
update item set usage = 'Increases Turns' where item_internal_name = 'amanita';
update item set usage = 'Stealths a Ninja' where item_internal_name = 'smokebomb';
update item set usage = 'Reduces Turns', image='caltrops.png' where item_internal_name = 'caltrops';
update item set usage = 'Reduces health', image='mini_star.png' where item_internal_name = 'shuriken';
update item set turn_change = 6 where item_internal_name = 'amanita';
update item set turn_change = -6 where item_internal_name = 'caltrops';
update item set ignore_stealth = TRUE where item_internal_name = 'amanita' or item_internal_name = 'ginsengroot' or item_internal_name = 'tigersalve' or item_internal_name = 'dimmak';
update item set covert = TRUE where item_internal_name = 'amanita' or item_internal_name = 'ginsengroot' or item_internal_name = 'tigersalve' or item_internal_name = 'dimmak';

-- alter table item drop column image;
-- alter table item drop column for_sale;
-- alter table item drop column uses;
-- alter table item drop column ignore_stealth;
-- alter table item drop column turn_cost;
-- alter table item drop column target_damage;
-- alter table item drop column turn_change;
-- alter table item drop column self_use;
-- alter table item drop column plural;
