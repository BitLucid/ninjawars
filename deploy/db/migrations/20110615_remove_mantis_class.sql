update class set class_active = false where identity = 'mantis';
update players set _class_id = (select class_id from class where identity = 'dragon') where _class_id = (select class_id from class where identity = 'mantis');
update class_skill set _class_id = (select class_id from class where identity = 'crane') where _skill_id = (select skill_id from skill where skill_internal_name = 'kampo');
update class_skill set _class_id = (select class_id from class where identity = 'dragon') where _skill_id = (select skill_id from skill where skill_internal_name = 'evasion');
