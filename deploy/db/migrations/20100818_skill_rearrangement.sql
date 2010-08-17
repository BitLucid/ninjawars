delete from class_skill where _skill_id in (select skill_id from skill where skill_internal_name = 'midnightheal');
update skill set skill_level = 20 where skill_internal_name = 'midnightheal';

