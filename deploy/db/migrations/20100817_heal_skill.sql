update skill set skill_internal_name = 'midnightheal' where skill_display_name = 'Midnight Heal';

insert into skill (skill_id, skill_level, skill_is_active, skill_display_name, skill_internal_name, skill_type) values (17, 1, true, 'Heal', 'heal', 'targeted');

insert into class_skill (_class_id, _skill_id) values (4, 17);
