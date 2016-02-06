<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1454741304.
 * Generated on 2016-02-06 01:48:24 by kzqai
 */
class PropelMigration_1454741304
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
CREATE TABLE "quests"
(
    "quest_id" serial NOT NULL,
    "title" VARCHAR(200) DEFAULT \'\' NOT NULL,
    "description" TEXT DEFAULT \'\' NOT NULL,
    "_player_id" INTEGER NOT NULL,
    "tags" TEXT DEFAULT \'\',
    "karma" INTEGER DEFAULT 0 NOT NULL,
    "rewards" TEXT DEFAULT \'\' NOT NULL,
    "obstacles" TEXT DEFAULT \'\' NOT NULL,
    "proof" TEXT DEFAULT \'\' NOT NULL,
    "expires_at" TIMESTAMP NOT NULL,
    "created_at" TIMESTAMP DEFAULT now() NOT NULL,
    "updated_at" TIMESTAMP DEFAULT now() NOT NULL,
    "type" INTEGER,
    "difficulty" INTEGER,
    PRIMARY KEY ("quest_id")
);

CREATE INDEX "quest_created_at_index" ON "quests" ("created_at");

CREATE INDEX "quest_title_index" ON "quests" ("title");

ALTER TABLE "quests" ADD CONSTRAINT "quests__player_id_fkey"
    FOREIGN KEY ("_player_id")
    REFERENCES "players" ("player_id")
    ON UPDATE CASCADE;
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
DROP TABLE IF EXISTS "quests" CASCADE;

',
);
    }

}