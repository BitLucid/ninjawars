<?php
/*
 * Value object for holding a skills's data from the database.
 * Fields have to be added here and in the SkillDAO.
 * Essentially this acts as the container for the model's data.
 * @var database_fields
 */
class SkillVO {
	public $skill_id, $skill_level, $skill_is_active, $skill_display_name, $skill_internal_name, $skill_type;
}
