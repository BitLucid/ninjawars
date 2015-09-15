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
    "title" VARCHAR(100) DEFAULT '' NOT NULL,
    "content" TEXT DEFAULT '' NOT NULL,
    "created" TIMESTAMP DEFAULT now() NOT NULL,
    "updated" TIMESTAMP DEFAULT now() NOT NULL,
    "tags" TEXT DEFAULT '',
    PRIMARY KEY ("news_id")
);

CREATE INDEX "news_created_index" ON "news" ("created");

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

ALTER TABLE "item" ADD "traits" VARCHAR(250) DEFAULT '';

ALTER TABLE "login_attempts" DROP CONSTRAINT "login_attempts_pkey";

ALTER TABLE "login_attempts" ADD PRIMARY KEY ("attempt_id");

ALTER TABLE "messages" DROP CONSTRAINT "messages_pkey";

ALTER TABLE "messages" ADD PRIMARY KEY ("message_id");

ALTER TABLE "player_rank" DROP CONSTRAINT "player_rank_pkey";

ALTER TABLE "player_rank" ADD PRIMARY KEY ("rank_id");

DROP INDEX "players_last_started_attack_idx";

ALTER TABLE "players" ADD "description" TEXT DEFAULT '' NOT NULL;

ALTER TABLE "players" ADD "instincts" TEXT DEFAULT '' NOT NULL;

ALTER TABLE "players" ADD "beliefs" TEXT DEFAULT '' NOT NULL;

ALTER TABLE "players" ADD "goals" TEXT DEFAULT '' NOT NULL;

ALTER TABLE "players" ADD "traits" TEXT DEFAULT '' NOT NULL;

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