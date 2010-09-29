-- Comment these out once the prototyping phase is over for them.
drop table item_effects;
drop table effects;

create table effects (
    effect_id serial primary key not null, 
    effect_identity varchar(500) not null unique, 
    effect_name text not null unique,
    effect_verb text not null,
    effect_self boolean
    );
    
    
-- These are somewhat like statuses, but they occur in an active, ongoing context, e.g.
-- The Item <description> Bob.
insert into effects values 
    (default, 'wound', 'Wound', 'Wounds', FALSE),
    (default, 'fire', 'Fire', 'Burns', FALSE),
    (default, 'ice', 'Ice', 'Freezes', FALSE),
    (default, 'shock', 'Shock', 'Shocks', FALSE),
    (default, 'acid', 'Acid', 'Dissolves', FALSE),    
    (default, 'void', 'Void', 'Taints', FALSE),
    (default, 'flare', 'Flare', 'Blinds', FALSE),
    (default, 'poison', 'Poison', 'Poisons', FALSE),
    (default, 'paralysis', 'Paralysis', 'Paralyzes', FALSE), -- The "frozen" effect can use paralysis.
    (default, 'slice', 'Slice', 'Slices', FALSE),
    (default, 'bash', 'Bash', 'Bashes', FALSE),
    (default, 'pierce', 'Pierce', 'Pierces', FALSE),
    (default, 'slow', 'Slow', 'Slows down', FALSE),
    (default, 'speed', 'Speed', 'Speeds up', TRUE),
    (default, 'stealth', 'Stealthed', 'Hides', TRUE),
    (default, 'vigor', 'Vigor', 'Energizes', TRUE),
    (default, 'strength', 'Strength', 'Strengthens', TRUE),
    (default, 'weaken', 'Weaken', 'Weakens', FALSE),
    (default, 'heal', 'Heal', 'Heals', TRUE),
    (default, 'healing', 'Healing', 'Healed', TRUE), -- As in, was healed, and can't be healed quite yet again.
    (default, 'regen', 'Regenerate', 'Regenerating', TRUE), -- As in, constantly gaining health.
    (default, 'death', 'Death', 'Dying', FALSE) -- As in, an ongoing countdown to a death/dim mak effect.
;

create table item_effects (
    _item_id int NOT NULL,
    _effect_id int NOT NULL,
    PRIMARY KEY(_item_id, _effect_id)
);


ALTER TABLE item_effects
ADD CONSTRAINT effects_effect_id_fkey
FOREIGN KEY (_effect_id)
REFERENCES effects(effect_id)
ON UPDATE CASCADE
ON DELETE RESTRICT;

ALTER TABLE item_effects
ADD CONSTRAINT item_item_id_fkey
FOREIGN KEY (_item_id)
REFERENCES item(item_id)
ON UPDATE CASCADE
ON DELETE RESTRICT;

insert into item_effects values ((select item_id from item where item_internal_name = 'phosphor'), (select effect_id from effects where effect_identity = 'fire'));
insert into item_effects values ((select item_id from item where item_internal_name = 'phosphor'), (select effect_id from effects where effect_identity = 'flare'));
insert into item_effects values ((select item_id from item where item_internal_name = 'phosphor'), (select effect_id from effects where effect_identity = 'wound'));


insert into item_effects values ((select item_id from item where item_internal_name = 'caltrops'), (select effect_id from effects where effect_identity = 'slow'));
insert into item_effects values ((select item_id from item where item_internal_name = 'caltrops'), (select effect_id from effects where effect_identity = 'pierce'));
insert into item_effects values ((select item_id from item where item_internal_name = 'caltrops'), (select effect_id from effects where effect_identity = 'wound'));

insert into item_effects values ((select item_id from item where item_internal_name = 'ginsengroot'), (select effect_id from effects where effect_identity = 'vigor'));

insert into item_effects values ((select item_id from item where item_internal_name = 'tigersalve'), (select effect_id from effects where effect_identity = 'strength'));

insert into item_effects values ((select item_id from item where item_internal_name = 'amanita'), (select effect_id from effects where effect_identity = 'speed'));

insert into item_effects values ((select item_id from item where item_internal_name = 'dimmak'), (select effect_id from effects where effect_identity = 'death'));
insert into item_effects values ((select item_id from item where item_internal_name = 'dimmak'), (select effect_id from effects where effect_identity = 'wound'));


insert into item_effects values ((select item_id from item where item_internal_name = 'shuriken'), (select effect_id from effects where effect_identity = 'slice'));
insert into item_effects values ((select item_id from item where item_internal_name = 'shuriken'), (select effect_id from effects where effect_identity = 'wound'));

insert into item_effects values ((select item_id from item where item_internal_name = 'smokebomb'), (select effect_id from effects where effect_identity = 'stealth'));

grant all on item_effects to developers;
alter table item_effects owner to developers;
grant all on effects to developers;
alter table effects owner to developers;
