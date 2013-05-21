<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1369148748.
 * Generated on 2013-05-21 22:05:48 by toopay
 */
class PropelMigration_1369148748
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
CREATE UNIQUE INDEX "account_players__player_id_key1" ON "account_players" ("_player_id");

ALTER TABLE "chat" DROP CONSTRAINT "chat_pkey";

ALTER TABLE "chat" ADD PRIMARY KEY ("chat_id");

CREATE UNIQUE INDEX "clan_player__player_id_key1" ON "clan_player" ("_player_id");

ALTER TABLE "events" DROP CONSTRAINT "events_pkey";

ALTER TABLE "events" ADD PRIMARY KEY ("event_id");

ALTER TABLE "login_attempts" DROP CONSTRAINT "login_attempts_pkey";

ALTER TABLE "login_attempts" ADD PRIMARY KEY ("attempt_id");

ALTER TABLE "player_rank" DROP CONSTRAINT "player_rank_pkey";

ALTER TABLE "player_rank" ADD PRIMARY KEY ("rank_id","_player_id");

ALTER TABLE "settings" DROP CONSTRAINT "settings_pkey";

ALTER TABLE "settings" ADD PRIMARY KEY ("setting_id","player_id");
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
    ALTER TABLE "account_players" DROP CONSTRAINT "account_players__player_id_key1";
    
ALTER TABLE "chat" DROP CONSTRAINT "chat_pkey";

ALTER TABLE "chat" ADD ;

    ALTER TABLE "clan_player" DROP CONSTRAINT "clan_player__player_id_key1";
    
ALTER TABLE "events" DROP CONSTRAINT "events_pkey";

ALTER TABLE "events" ADD ;

ALTER TABLE "login_attempts" DROP CONSTRAINT "login_attempts_pkey";

ALTER TABLE "login_attempts" ADD ;

ALTER TABLE "player_rank" DROP CONSTRAINT "player_rank_pkey";

ALTER TABLE "player_rank" ADD ;

ALTER TABLE "settings" DROP CONSTRAINT "settings_pkey";

ALTER TABLE "settings" ADD ;
',
);
    }

}