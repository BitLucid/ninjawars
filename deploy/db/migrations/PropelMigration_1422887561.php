<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1422887561.
 * Generated on 2015-02-02 09:32:41 by kzqai
 */
class PropelMigration_1422887561
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
CREATE TABLE "account_news"
(
    "_account_id" INTEGER NOT NULL,
    "_news_id" INTEGER NOT NULL,
    "created_date" TIMESTAMP DEFAULT now() NOT NULL,
    PRIMARY KEY ("_account_id","_news_id")
);

CREATE TABLE "news"
(
    "news_id" serial NOT NULL,
    "title" VARCHAR(100) DEFAULT \'\' NOT NULL,
    "content" TEXT DEFAULT \'\' NOT NULL,
    "created" TIMESTAMP DEFAULT now() NOT NULL,
    "updated" TIMESTAMP DEFAULT now() NOT NULL,
    "tags" TEXT DEFAULT \'\',
    PRIMARY KEY ("news_id")
);

CREATE INDEX "news_created_index" ON "news" ("created");

CREATE TABLE "test"
(
    "id" TEXT DEFAULT \'encode(gen_random_bytes(32), hex\' NOT NULL,
    "value" TEXT,
    PRIMARY KEY ("id")
);

ALTER TABLE "account_players" DROP CONSTRAINT "account_players_pkey";

ALTER TABLE "account_players" ADD PRIMARY KEY ("_account_id","_player_id");

ALTER TABLE "accounts" ADD "oauth_provider" VARCHAR(100);

ALTER TABLE "accounts" ADD "oauth_id" VARCHAR(100);

ALTER TABLE "chat" DROP CONSTRAINT "chat_pkey";

ALTER TABLE "chat" ADD PRIMARY KEY ("chat_id");

ALTER TABLE "clan_player" DROP CONSTRAINT "clan_player_pkey";

ALTER TABLE "clan_player" ADD PRIMARY KEY ("_clan_id","_player_id");

DROP INDEX "dueling_log_attacker_idx";

DROP INDEX "dueling_log_defender_idx";

DROP INDEX "events_send_from_idx";

DROP INDEX "events_send_to_idx";

DROP INDEX "events_unread_idx";

ALTER TABLE "item" ADD "traits" VARCHAR(250) DEFAULT \'\';

ALTER TABLE "login_attempts" DROP CONSTRAINT "login_attempts_pkey";

ALTER TABLE "login_attempts" ADD PRIMARY KEY ("attempt_id");

ALTER TABLE "messages" DROP CONSTRAINT "messages_pkey";

ALTER TABLE "messages" ADD PRIMARY KEY ("message_id");

ALTER TABLE "player_rank" DROP CONSTRAINT "player_rank_pkey";

ALTER TABLE "player_rank" ADD PRIMARY KEY ("rank_id");

DROP INDEX "players_last_started_attack_idx";

ALTER TABLE "players" ALTER COLUMN "resurrection_time" SET DEFAULT (round((random() * (7)::double precision)) * (3)::double precision);

ALTER TABLE "players" ADD "description" TEXT DEFAULT \'\' NOT NULL;

ALTER TABLE "players" ADD "instincts" TEXT DEFAULT \'\' NOT NULL;

ALTER TABLE "players" ADD "beliefs" TEXT DEFAULT \'\' NOT NULL;

ALTER TABLE "players" ADD "goals" TEXT DEFAULT \'\' NOT NULL;

ALTER TABLE "players" ADD "traits" TEXT DEFAULT \'\' NOT NULL;

DROP INDEX "ppl_online_activity_idx";

DROP INDEX "ppl_online_ip_address_idx";

ALTER TABLE "settings" DROP CONSTRAINT "settings_pkey";

ALTER TABLE "settings" ADD PRIMARY KEY ("setting_id");

ALTER TABLE "time" DROP CONSTRAINT "time_pkey";

ALTER TABLE "time" ADD PRIMARY KEY ("time_id");

ALTER TABLE "account_news" ADD CONSTRAINT "account_news__account_id_fkey"
    FOREIGN KEY ("_account_id")
    REFERENCES "accounts" ("account_id")
    ON UPDATE CASCADE
    ON DELETE CASCADE;

ALTER TABLE "account_news" ADD CONSTRAINT "account_news__news_id_fkey"
    FOREIGN KEY ("_news_id")
    REFERENCES "news" ("news_id")
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
DROP TABLE IF EXISTS "account_news" CASCADE;

DROP TABLE IF EXISTS "news" CASCADE;

DROP TABLE IF EXISTS "test" CASCADE;

ALTER TABLE "account_players" DROP CONSTRAINT "account_players_pkey";

ALTER TABLE "account_players" ADD ;

ALTER TABLE "accounts" DROP COLUMN "oauth_provider";

ALTER TABLE "accounts" DROP COLUMN "oauth_id";

ALTER TABLE "chat" DROP CONSTRAINT "chat_pkey";

ALTER TABLE "chat" ADD ;

ALTER TABLE "clan_player" DROP CONSTRAINT "clan_player_pkey";

ALTER TABLE "clan_player" ADD ;

CREATE INDEX "dueling_log_attacker_idx" ON "dueling_log" ("attacker");

CREATE INDEX "dueling_log_defender_idx" ON "dueling_log" ("defender");

CREATE INDEX "events_send_from_idx" ON "events" ("send_from");

CREATE INDEX "events_send_to_idx" ON "events" ("send_to");

CREATE INDEX "events_unread_idx" ON "events" ("unread");

ALTER TABLE "item" DROP COLUMN "traits";

ALTER TABLE "login_attempts" DROP CONSTRAINT "login_attempts_pkey";

ALTER TABLE "login_attempts" ADD ;

ALTER TABLE "messages" DROP CONSTRAINT "messages_pkey";

ALTER TABLE "messages" ADD ;

ALTER TABLE "player_rank" DROP CONSTRAINT "player_rank_pkey";

ALTER TABLE "player_rank" ADD ;

ALTER TABLE "players" ALTER COLUMN "resurrection_time" SET DEFAULT (round((random() * (7)3);

ALTER TABLE "players" DROP COLUMN "description";

ALTER TABLE "players" DROP COLUMN "instincts";

ALTER TABLE "players" DROP COLUMN "beliefs";

ALTER TABLE "players" DROP COLUMN "goals";

ALTER TABLE "players" DROP COLUMN "traits";

CREATE INDEX "players_last_started_attack_idx" ON "players" ("last_started_attack");

CREATE INDEX "ppl_online_activity_idx" ON "ppl_online" ("activity");

CREATE INDEX "ppl_online_ip_address_idx" ON "ppl_online" ("ip_address");

ALTER TABLE "settings" DROP CONSTRAINT "settings_pkey";

ALTER TABLE "settings" ADD ;

ALTER TABLE "time" DROP CONSTRAINT "time_pkey";

ALTER TABLE "time" ADD ;
',
);
    }

}