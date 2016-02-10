SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

-----------------------------------------------------------------------
-- accounts
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "accounts" CASCADE;

CREATE TABLE "accounts"
(
    "account_id" serial NOT NULL,
    "account_identity" TEXT NOT NULL,
    "phash" TEXT,
    "oauth_provider" VARCHAR(100),
    "oauth_id" VARCHAR(100),
    "active_email" TEXT NOT NULL,
    "type" INTEGER DEFAULT 0,
    "operational" BOOLEAN DEFAULT 't',
    "created_date" TIMESTAMP DEFAULT now() NOT NULL,
    "last_login" TIMESTAMP,
    "last_login_failure" TIMESTAMP,
    "karma_total" INTEGER DEFAULT 0 NOT NULL,
    "last_ip" VARCHAR(100),
    "confirmed" INTEGER DEFAULT 0 NOT NULL,
    "verification_number" VARCHAR(100),
    PRIMARY KEY ("account_id"),
    CONSTRAINT "accounts_account_identity_key" UNIQUE ("account_identity"),
    CONSTRAINT "accounts_active_email_key" UNIQUE ("active_email")
);

-----------------------------------------------------------------------
-- players
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "players" CASCADE;

CREATE TABLE "players"
(
    "player_id" serial NOT NULL,
    "uname" VARCHAR(100) NOT NULL,
    "pname_backup" VARCHAR(100),
    "health" INTEGER DEFAULT 0 NOT NULL,
    "strength" INTEGER DEFAULT 0 NOT NULL,
    "gold" INTEGER DEFAULT 0 NOT NULL,
    "messages" TEXT DEFAULT '' NOT NULL,
    "description" TEXT DEFAULT '' NOT NULL,
    "instincts" TEXT DEFAULT '' NOT NULL,
    "beliefs" TEXT DEFAULT '' NOT NULL,
    "goals" TEXT DEFAULT '' NOT NULL,
    "traits" TEXT DEFAULT '' NOT NULL,
    "kills" INTEGER DEFAULT 0 NOT NULL,
    "turns" INTEGER DEFAULT 0 NOT NULL,
    "verification_number" INTEGER DEFAULT 0 NOT NULL,
    "active" INTEGER DEFAULT 0 NOT NULL,
    "level" INTEGER DEFAULT 0 NOT NULL,
    "status" INTEGER DEFAULT 0 NOT NULL,
    "member" INTEGER DEFAULT 0 NOT NULL,
    "days" INTEGER DEFAULT 0 NOT NULL,
    "bounty" INTEGER DEFAULT 0 NOT NULL,
    "created_date" TIMESTAMP DEFAULT now(),
    "resurrection_time" INTEGER DEFAULT (round((random() * (7)::double precision)) * (3)::double precision) NOT NULL,
    "last_started_attack" TIMESTAMP DEFAULT now(),
    "energy" INTEGER DEFAULT 0 NOT NULL,
    "avatar_type" INTEGER DEFAULT 1 NOT NULL,
    "_class_id" INTEGER NOT NULL,
    "ki" INTEGER DEFAULT 0 NOT NULL,
    "stamina" INTEGER DEFAULT 0 NOT NULL,
    "speed" INTEGER DEFAULT 0 NOT NULL,
    "karma" INTEGER DEFAULT 0 NOT NULL,
    "kills_gained" INTEGER DEFAULT 0 NOT NULL,
    "kills_used" INTEGER DEFAULT 0 NOT NULL,
    PRIMARY KEY ("player_id"),
    CONSTRAINT "players_uname_key" UNIQUE ("uname")
);

-----------------------------------------------------------------------
-- class
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "class" CASCADE;

CREATE TABLE "class"
(
    "class_id" serial NOT NULL,
    "class_name" TEXT NOT NULL,
    "class_active" BOOLEAN DEFAULT 't',
    "class_note" TEXT,
    "class_tier" INTEGER DEFAULT 1 NOT NULL,
    "class_desc" TEXT,
    "class_icon" TEXT,
    "theme" VARCHAR(255),
    "identity" VARCHAR(255),
    PRIMARY KEY ("class_id"),
    CONSTRAINT "class_class_name_key" UNIQUE ("class_name")
);

-----------------------------------------------------------------------
-- account_players
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "account_players" CASCADE;

CREATE TABLE "account_players"
(
    "_account_id" INTEGER NOT NULL,
    "_player_id" INTEGER NOT NULL,
    "last_login" TIMESTAMP DEFAULT now() NOT NULL,
    "created_date" TIMESTAMP DEFAULT now() NOT NULL,
    PRIMARY KEY ("_account_id","_player_id"),
    CONSTRAINT "account_players__player_id_key" UNIQUE ("_player_id")
);

-----------------------------------------------------------------------
-- players_flagged
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "players_flagged" CASCADE;

CREATE TABLE "players_flagged"
(
    "players_flagged_id" serial NOT NULL,
    "player_id" INTEGER,
    "flag_id" INTEGER,
    "timestamp" DATE DEFAULT now(),
    "originating_page" VARCHAR(50),
    "extra_notes" VARCHAR(100),
    PRIMARY KEY ("players_flagged_id")
);

-----------------------------------------------------------------------
-- ppl_online
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "ppl_online" CASCADE;

CREATE TABLE "ppl_online"
(
    "session_id" VARCHAR(255) NOT NULL,
    "activity" TIMESTAMP DEFAULT now() NOT NULL,
    "member" BOOLEAN DEFAULT 'f' NOT NULL,
    "ip_address" VARCHAR(255) DEFAULT '' NOT NULL,
    "refurl" VARCHAR(255) DEFAULT '' NOT NULL,
    "user_agent" VARCHAR(255),
    PRIMARY KEY ("session_id")
);

-----------------------------------------------------------------------
-- settings
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "settings" CASCADE;

CREATE TABLE "settings"
(
    "setting_id" serial NOT NULL,
    "player_id" INTEGER NOT NULL,
    "settings_store" TEXT,
    PRIMARY KEY ("setting_id")
);

-----------------------------------------------------------------------
-- skill
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "skill" CASCADE;

CREATE TABLE "skill"
(
    "skill_id" serial NOT NULL,
    "skill_level" INTEGER DEFAULT 1 NOT NULL,
    "skill_is_active" BOOLEAN DEFAULT 't',
    "skill_display_name" TEXT NOT NULL,
    "skill_internal_name" TEXT NOT NULL,
    "skill_type" VARCHAR NOT NULL,
    PRIMARY KEY ("skill_id"),
    CONSTRAINT "skill_skill_display_name_key" UNIQUE ("skill_display_name"),
    CONSTRAINT "skill_skill_internal_name_key" UNIQUE ("skill_internal_name")
);

-----------------------------------------------------------------------
-- class_skill
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "class_skill" CASCADE;

CREATE TABLE "class_skill"
(
    "_class_id" INTEGER NOT NULL,
    "_skill_id" INTEGER NOT NULL,
    "class_skill_level" INTEGER,
    PRIMARY KEY ("_class_id","_skill_id")
);

-----------------------------------------------------------------------
-- news
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "news" CASCADE;

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

-----------------------------------------------------------------------
-- password_reset_requests
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "password_reset_requests" CASCADE;

CREATE TABLE "password_reset_requests"
(
    "request_id" serial NOT NULL,
    "_account_id" INTEGER NOT NULL,
    "nonce" VARCHAR(130) NOT NULL,
    "created_at" TIMESTAMP DEFAULT now() NOT NULL,
    "used" BOOLEAN DEFAULT 'f' NOT NULL,
    PRIMARY KEY ("request_id")
);

-----------------------------------------------------------------------
-- account_news
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "account_news" CASCADE;

CREATE TABLE "account_news"
(
    "_account_id" INTEGER NOT NULL,
    "_news_id" INTEGER NOT NULL,
    "created_date" TIMESTAMP DEFAULT now() NOT NULL,
    PRIMARY KEY ("_account_id","_news_id")
);

-----------------------------------------------------------------------
-- chat
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "chat" CASCADE;

CREATE TABLE "chat"
(
    "chat_id" serial NOT NULL,
    "sender_id" INTEGER DEFAULT 0,
    "message" VARCHAR(255) NOT NULL,
    "date" TIMESTAMP DEFAULT now() NOT NULL,
    PRIMARY KEY ("chat_id")
);

CREATE INDEX "chat_date_index" ON "chat" ("date");

-----------------------------------------------------------------------
-- clan
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "clan" CASCADE;

CREATE TABLE "clan"
(
    "clan_id" serial NOT NULL,
    "clan_name" VARCHAR(255) NOT NULL,
    "clan_created_date" TIMESTAMP DEFAULT now() NOT NULL,
    "clan_founder" TEXT,
    "clan_avatar_url" TEXT,
    "description" TEXT,
    PRIMARY KEY ("clan_id"),
    CONSTRAINT "clan_clan_name_key" UNIQUE ("clan_name")
);

-----------------------------------------------------------------------
-- clan_player
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "clan_player" CASCADE;

CREATE TABLE "clan_player"
(
    "_clan_id" INTEGER NOT NULL,
    "_player_id" INTEGER NOT NULL,
    "member_level" INTEGER DEFAULT 0 NOT NULL,
    PRIMARY KEY ("_clan_id","_player_id"),
    CONSTRAINT "clan_player__player_id_key" UNIQUE ("_player_id")
);

-----------------------------------------------------------------------
-- dueling_log
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "dueling_log" CASCADE;

CREATE TABLE "dueling_log"
(
    "id" serial NOT NULL,
    "attacker" VARCHAR(100) NOT NULL,
    "defender" VARCHAR(100) NOT NULL,
    "won" BOOLEAN DEFAULT 'f' NOT NULL,
    "killpoints" INTEGER DEFAULT 0 NOT NULL,
    "date" DATE DEFAULT now() NOT NULL,
    PRIMARY KEY ("id")
);

CREATE INDEX "dueling_log_date_index" ON "dueling_log" ("date");

-----------------------------------------------------------------------
-- duped_unames
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "duped_unames" CASCADE;

CREATE TABLE "duped_unames"
(
    "uname" TEXT NOT NULL,
    "email" TEXT NOT NULL,
    "created_date" TIMESTAMP NOT NULL,
    "relative_age" INT2 NOT NULL,
    "player_id" INTEGER NOT NULL,
    "locked" BOOLEAN DEFAULT 'f' NOT NULL,
    PRIMARY KEY ("player_id")
);

CREATE INDEX "duped_unames_email_key" ON "duped_unames" ("email");

CREATE INDEX "duped_unames_uname_key" ON "duped_unames" ("uname");

-----------------------------------------------------------------------
-- effects
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "effects" CASCADE;

CREATE TABLE "effects"
(
    "effect_id" serial NOT NULL,
    "effect_identity" VARCHAR(500) NOT NULL,
    "effect_name" TEXT NOT NULL,
    "effect_verb" TEXT NOT NULL,
    "effect_self" BOOLEAN,
    PRIMARY KEY ("effect_id"),
    CONSTRAINT "effects_effect_identity_key" UNIQUE ("effect_identity"),
    CONSTRAINT "effects_effect_name_key" UNIQUE ("effect_name")
);

-----------------------------------------------------------------------
-- enemies
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "enemies" CASCADE;

CREATE TABLE "enemies"
(
    "_player_id" INTEGER NOT NULL,
    "_enemy_id" INTEGER NOT NULL,
    PRIMARY KEY ("_player_id","_enemy_id")
);

-----------------------------------------------------------------------
-- events
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "events" CASCADE;

CREATE TABLE "events"
(
    "event_id" serial NOT NULL,
    "send_to" INTEGER DEFAULT 0,
    "send_from" INTEGER DEFAULT 0,
    "message" TEXT NOT NULL,
    "unread" INTEGER DEFAULT 1 NOT NULL,
    "date" TIMESTAMP DEFAULT now() NOT NULL,
    PRIMARY KEY ("event_id")
);

CREATE INDEX "events_date_index" ON "events" ("date");

-----------------------------------------------------------------------
-- flags
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "flags" CASCADE;

CREATE TABLE "flags"
(
    "flag_id" serial NOT NULL,
    "flag" VARCHAR(100) NOT NULL,
    "flag_type" INTEGER NOT NULL,
    PRIMARY KEY ("flag_id"),
    CONSTRAINT "flags_flag_key" UNIQUE ("flag")
);

-----------------------------------------------------------------------
-- inventory
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "inventory" CASCADE;

CREATE TABLE "inventory"
(
    "item_id" serial NOT NULL,
    "amount" INTEGER DEFAULT 1,
    "owner" INTEGER NOT NULL,
    "item_type" INTEGER,
    "item_type_string_backup" VARCHAR,
    PRIMARY KEY ("item_id")
);

-----------------------------------------------------------------------
-- item
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "item" CASCADE;

CREATE TABLE "item"
(
    "item_id" serial NOT NULL,
    "item_internal_name" TEXT NOT NULL,
    "item_display_name" TEXT NOT NULL,
    "item_cost" DECIMAL NOT NULL,
    "image" VARCHAR(250),
    "for_sale" BOOLEAN DEFAULT 'f',
    "usage" TEXT,
    "ignore_stealth" BOOLEAN DEFAULT 'f',
    "covert" BOOLEAN DEFAULT 'f',
    "turn_cost" INTEGER,
    "target_damage" INTEGER,
    "turn_change" INTEGER,
    "self_use" BOOLEAN DEFAULT 'f',
    "plural" VARCHAR(20),
    "other_usable" BOOLEAN DEFAULT 'f',
    "traits" VARCHAR(250) DEFAULT '',
    PRIMARY KEY ("item_id"),
    CONSTRAINT "item_item_display_name_key" UNIQUE ("item_display_name"),
    CONSTRAINT "item_item_internal_name_key" UNIQUE ("item_internal_name")
);

-----------------------------------------------------------------------
-- item_effects
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "item_effects" CASCADE;

CREATE TABLE "item_effects"
(
    "_item_id" INTEGER NOT NULL,
    "_effect_id" INTEGER NOT NULL,
    PRIMARY KEY ("_item_id","_effect_id")
);

-----------------------------------------------------------------------
-- levelling_log
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "levelling_log" CASCADE;

CREATE TABLE "levelling_log"
(
    "id" serial NOT NULL,
    "killpoints" INTEGER DEFAULT 0 NOT NULL,
    "levelling" INTEGER DEFAULT 0 NOT NULL,
    "killsdate" DATE NOT NULL,
    "_player_id" INTEGER NOT NULL,
    PRIMARY KEY ("id")
);

-----------------------------------------------------------------------
-- login_attempts
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "login_attempts" CASCADE;

CREATE TABLE "login_attempts"
(
    "attempt_id" serial NOT NULL,
    "username" TEXT,
    "ua_string" TEXT,
    "ip" TEXT,
    "successful" INTEGER,
    "additional_info" TEXT,
    "attempt_date" TIMESTAMP DEFAULT now(),
    PRIMARY KEY ("attempt_id")
);

-----------------------------------------------------------------------
-- messages
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "messages" CASCADE;

CREATE TABLE "messages"
(
    "message_id" serial NOT NULL,
    "message" TEXT NOT NULL,
    "date" TIMESTAMP DEFAULT now() NOT NULL,
    "send_to" INTEGER,
    "send_from" INTEGER,
    "unread" INTEGER DEFAULT 1,
    "type" INTEGER DEFAULT 0,
    PRIMARY KEY ("message_id")
);

CREATE INDEX "messages_date_index" ON "messages" ("date");

-----------------------------------------------------------------------
-- quests
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "quests" CASCADE;

CREATE TABLE "quests"
(
    "quest_id" serial NOT NULL,
    "title" VARCHAR(200) DEFAULT '' NOT NULL,
    "_player_id" INTEGER NOT NULL,
    "description" TEXT DEFAULT '' NOT NULL,
    "tags" TEXT DEFAULT '' NOT NULL,
    "karma" INTEGER DEFAULT 0 NOT NULL,
    "rewards" TEXT DEFAULT '' NOT NULL,
    "obstacles" TEXT DEFAULT '' NOT NULL,
    "proof" TEXT DEFAULT '' NOT NULL,
    "created_at" TIMESTAMP DEFAULT now() NOT NULL,
    "updated_at" TIMESTAMP DEFAULT now() NOT NULL,
    "expires_at" TIMESTAMP DEFAULT now() + interval ' 1 mon ' NOT NULL,
    "type" INTEGER,
    "difficulty" INTEGER,
    PRIMARY KEY ("quest_id")
);

CREATE INDEX "quest_created_at_index" ON "quests" ("created_at");

CREATE INDEX "quest_title_index" ON "quests" ("title");

-----------------------------------------------------------------------
-- past_stats
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "past_stats" CASCADE;

CREATE TABLE "past_stats"
(
    "id" serial NOT NULL,
    "stat_type" VARCHAR(50) DEFAULT '' NOT NULL,
    "stat_result" VARCHAR(50) DEFAULT '' NOT NULL,
    PRIMARY KEY ("id")
);

-----------------------------------------------------------------------
-- player_rank
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "player_rank" CASCADE;

CREATE TABLE "player_rank"
(
    "rank_id" serial NOT NULL,
    "_player_id" INTEGER NOT NULL,
    "score" INTEGER,
    PRIMARY KEY ("rank_id")
);

-----------------------------------------------------------------------
-- time
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "time" CASCADE;

CREATE TABLE "time"
(
    "time_id" serial NOT NULL,
    "time_label" VARCHAR NOT NULL,
    "amount" INTEGER NOT NULL,
    PRIMARY KEY ("time_id")
);

ALTER TABLE "players" ADD CONSTRAINT "players__class_id_fkey"
    FOREIGN KEY ("_class_id")
    REFERENCES "class" ("class_id")
    ON UPDATE CASCADE;

ALTER TABLE "account_players" ADD CONSTRAINT "account_players__account_id_fkey"
    FOREIGN KEY ("_account_id")
    REFERENCES "accounts" ("account_id")
    ON UPDATE CASCADE
    ON DELETE CASCADE;

ALTER TABLE "account_players" ADD CONSTRAINT "account_players__player_id_fkey"
    FOREIGN KEY ("_player_id")
    REFERENCES "players" ("player_id")
    ON UPDATE CASCADE
    ON DELETE CASCADE;

ALTER TABLE "class_skill" ADD CONSTRAINT "class_skill__class_id_fkey"
    FOREIGN KEY ("_class_id")
    REFERENCES "class" ("class_id")
    ON UPDATE CASCADE
    ON DELETE CASCADE;

ALTER TABLE "class_skill" ADD CONSTRAINT "class_skill__skill_id_fkey"
    FOREIGN KEY ("_skill_id")
    REFERENCES "skill" ("skill_id")
    ON UPDATE CASCADE
    ON DELETE CASCADE;

ALTER TABLE "password_reset_requests" ADD CONSTRAINT "pwrr__account_id_fkey"
    FOREIGN KEY ("_account_id")
    REFERENCES "accounts" ("account_id")
    ON UPDATE CASCADE
    ON DELETE CASCADE;

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

ALTER TABLE "clan_player" ADD CONSTRAINT "clan_player__clan_id_fkey"
    FOREIGN KEY ("_clan_id")
    REFERENCES "clan" ("clan_id")
    ON UPDATE CASCADE
    ON DELETE CASCADE;

ALTER TABLE "clan_player" ADD CONSTRAINT "clan_player__player_id_fkey"
    FOREIGN KEY ("_player_id")
    REFERENCES "players" ("player_id")
    ON UPDATE CASCADE
    ON DELETE CASCADE;

ALTER TABLE "enemies" ADD CONSTRAINT "enemies__enemy_id_fkey"
    FOREIGN KEY ("_enemy_id")
    REFERENCES "players" ("player_id")
    ON UPDATE CASCADE
    ON DELETE CASCADE;

ALTER TABLE "enemies" ADD CONSTRAINT "enemies__player_id_fkey"
    FOREIGN KEY ("_player_id")
    REFERENCES "players" ("player_id")
    ON UPDATE CASCADE
    ON DELETE CASCADE;

ALTER TABLE "inventory" ADD CONSTRAINT "inventory_owner_fkey"
    FOREIGN KEY ("owner")
    REFERENCES "players" ("player_id")
    ON UPDATE CASCADE
    ON DELETE CASCADE;

ALTER TABLE "item_effects" ADD CONSTRAINT "effects_effect_id_fkey"
    FOREIGN KEY ("_effect_id")
    REFERENCES "effects" ("effect_id")
    ON UPDATE CASCADE
    ON DELETE RESTRICT;

ALTER TABLE "item_effects" ADD CONSTRAINT "item_item_id_fkey"
    FOREIGN KEY ("_item_id")
    REFERENCES "item" ("item_id")
    ON UPDATE CASCADE
    ON DELETE RESTRICT;

ALTER TABLE "levelling_log" ADD CONSTRAINT "levelling_log__player_id_fkey"
    FOREIGN KEY ("_player_id")
    REFERENCES "players" ("player_id")
    ON UPDATE CASCADE
    ON DELETE CASCADE;

ALTER TABLE "messages" ADD CONSTRAINT "messages_send_from_fkey"
    FOREIGN KEY ("send_from")
    REFERENCES "players" ("player_id")
    ON UPDATE CASCADE
    ON DELETE CASCADE;

ALTER TABLE "messages" ADD CONSTRAINT "messages_send_to_fkey"
    FOREIGN KEY ("send_to")
    REFERENCES "players" ("player_id")
    ON UPDATE CASCADE
    ON DELETE CASCADE;

ALTER TABLE "quests" ADD CONSTRAINT "quests__player_id_fkey"
    FOREIGN KEY ("_player_id")
    REFERENCES "players" ("player_id")
    ON UPDATE CASCADE;
