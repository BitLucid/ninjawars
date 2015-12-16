<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1442675610.
 * Generated on 2015-09-19 11:13:30 by kzqai
 */
class PropelMigration_1442675610
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
CREATE TABLE "password_reset_requests"
(
    "request_id" serial NOT NULL,
    "_account_id" INTEGER NOT NULL,
    "nonce" VARCHAR(130) NOT NULL,
    "created_at" TIMESTAMP DEFAULT CURRENT_DATE NOT NULL,
    "used" BOOLEAN DEFAULT \'f\',
    PRIMARY KEY ("request_id")
);

ALTER TABLE "password_reset_requests" ADD CONSTRAINT "account__account_id_fkey"
    FOREIGN KEY ("_account_id")
    REFERENCES "accounts" ("account_id")
    ON UPDATE CASCADE
    ON DELETE CASCADE;
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
DROP TABLE IF EXISTS "password_reset_requests" CASCADE;

',
);
    }

}