<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1403731238.
 * Generated on 2014-06-25 17:20:38 by kzqai
 */
class PropelMigration_1403731238
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
ALTER TABLE "accounts" ADD "oauth_provider" VARCHAR(100);

ALTER TABLE "accounts" ADD "oauth_id" VARCHAR(100);

ALTER TABLE "item" ALTER COLUMN "traits" SET DEFAULT \'\';

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
ALTER TABLE "accounts" DROP COLUMN "oauth_provider";

ALTER TABLE "accounts" DROP COLUMN "oauth_id";

ALTER TABLE "item" ALTER COLUMN "traits" DROP DEFAULT;

ALTER TABLE "players" ALTER COLUMN "traits" DROP NOT NULL;
',
);
    }

}