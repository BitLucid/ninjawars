<?php

namespace NinjaWars\core\data;

/*
 * Value object for holding a skills's data from the database.
 * Fields have to be added here and in the SkillDAO.
 * Essentially this acts as the container for the model's data.
 * @var database_fields
 */
class SkillVO
{
    public $skill_id;
    public $skill_level;
    public $skill_is_active;
    public $skill_display_name;
    public $skill_internal_name;
    public $skill_type;
}
