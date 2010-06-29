CREATE TYPE skill_type AS enum('combat','passive','self-only','targeted');
CREATE TABLE skill (
	skill_id serial not null primary key
	, skill_level int not null default 1
	, skill_is_active boolean default true
	, skill_display_name text not null unique
	, skill_internal_name text not null unique
	, skill_type skill_type not null
);
ALTER TABLE skill OWNER TO developers;

CREATE TABLE class_skill (
	_class_id int not null references class(class_id) on update cascade on delete cascade
	, _skill_id int not null references skill(skill_id) on update cascade on delete cascade
	, class_skill_level int
	, primary key(_class_id, _skill_id)
);
ALTER TABLE class_skill OWNER TO developers;

INSERT INTO skill VALUES
(default, 1, true, 'Ice Bolt', 'ice', 'targeted')
, (default, 6, true, 'Cold Steal', 'coldsteal', 'targeted')
, (default, 1, true, 'Speed', 'speed', 'passive')
, (default, 1, true, 'Chi', 'chi', 'passive')
, (default, 1, true, 'Midnight Heal', 'heal', 'passive')
, (default, 1, true, 'Fire Bolt', 'fire', 'targeted')
, (default, 1, true, 'Blaze', 'blaze', 'combat')
, (default, 2, true, 'Deflect', 'deflect', 'combat')
, (default, 1, true, 'Poison Touch', 'poison', 'targeted')
, (default, 1, true, 'Hidden Resurrect', 'stealthres', 'passive')
, (default, 1, true, 'Sight', 'sight', 'targeted')
, (default, 1, true, 'Stealth', 'stealth', 'self-only')
, (default, 1, true, 'Unstealth', 'unstealth', 'self-only')
, (default, 2, true, 'Steal', 'steal', 'targeted')
, (default, 2, true, 'Kampo', 'kampo', 'self-only')
, (default, 2, true, 'Evasion', 'evasion', 'combat');

INSERT INTO class_skill VALUES
((SELECT class_id FROM class WHERE class_name = 'Black'), (SELECT skill_id FROM skill WHERE skill_internal_name = 'poison'), null)
, ((SELECT class_id FROM class WHERE class_name = 'Black'), (SELECT skill_id FROM skill WHERE skill_internal_name = 'stealthres'), null)
, ((SELECT class_id FROM class WHERE class_name = 'Blue'), (SELECT skill_id FROM skill WHERE skill_internal_name = 'ice'), null)
, ((SELECT class_id FROM class WHERE class_name = 'Blue'), (SELECT skill_id FROM skill WHERE skill_internal_name = 'speed'), null)
, ((SELECT class_id FROM class WHERE class_name = 'White'), (SELECT skill_id FROM skill WHERE skill_internal_name = 'chi'), null)
, ((SELECT class_id FROM class WHERE class_name = 'White'), (SELECT skill_id FROM skill WHERE skill_internal_name = 'heal'), null)
, ((SELECT class_id FROM class WHERE class_name = 'Gray'), (SELECT skill_id FROM skill WHERE skill_internal_name = 'evasion'), null)
, ((SELECT class_id FROM class WHERE class_name = 'Gray'), (SELECT skill_id FROM skill WHERE skill_internal_name = 'kampo'), null)
, ((SELECT class_id FROM class WHERE class_name = 'Red'), (SELECT skill_id FROM skill WHERE skill_internal_name = 'fire'), null)
, ((SELECT class_id FROM class WHERE class_name = 'Red'), (SELECT skill_id FROM skill WHERE skill_internal_name = 'blaze'), null);
