<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1421296362.
 * Generated on 2015-01-14 23:32:42 by kzqai
 */
class PropelMigration_1421296362
{

    public function preUp($manager)
    {
        // add the pre-migration code here
    }

    public function postUp($manager)
    {
        // add the post-migration code here
    }

    public function preDown($manager)
    {
        // add the pre-migration code here
    }

    public function postDown($manager)
    {
        // add the post-migration code here
    }

    /**
     * Get the SQL statements for the Up migration
     *
     * @return array list of the SQL strings to execute for the Up migration
     *               the keys being the datasources
     */
    public function getUpSQL()
    {
        return array (
  'ninjawars' => '
Update "players" set traits = "" where traits is null;

ALTER TABLE "players" ALTER COLUMN "traits" SET NOT NULL;
',
);
    }

    /**
     * Get the SQL statements for the Down migration
     *
     * @return array list of the SQL strings to execute for the Down migration
     *               the keys being the datasources
     */
    public function getDownSQL()
    {
        return array (
  'ninjawars' => '
ALTER TABLE "players" ALTER COLUMN "traits" DROP NOT NULL;
',
);
    }

}