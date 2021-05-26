--
-- PostgreSQL database dump
--

-- Dumped from database version 10.4 (Ubuntu 10.4-2.pgdg16.04+1)
-- Dumped by pg_dump version 10.4 (Ubuntu 10.4-2.pgdg16.04+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


--
-- Name: pgcrypto; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS pgcrypto WITH SCHEMA public;


--
-- Name: EXTENSION pgcrypto; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION pgcrypto IS 'cryptographic functions';


--
-- Name: skill_type; Type: TYPE; Schema: public; Owner: ninjamaster
--

CREATE TYPE public.skill_type AS ENUM (
    'combat',
    'passive',
    'self-only',
    'targeted'
);


ALTER TYPE public.skill_type OWNER TO ninjamaster;

--
-- Name: array_accum(anyelement); Type: AGGREGATE; Schema: public; Owner: ninjamaster
--

CREATE AGGREGATE public.array_accum(anyelement) (
    SFUNC = array_append,
    STYPE = anyarray,
    INITCOND = '{}'
);


ALTER AGGREGATE public.array_accum(anyelement) OWNER TO ninjamaster;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: account_news; Type: TABLE; Schema: public; Owner: developers
--

CREATE TABLE public.account_news (
    _account_id integer NOT NULL,
    _news_id integer NOT NULL,
    created_date timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.account_news OWNER TO developers;

--
-- Name: account_players; Type: TABLE; Schema: public; Owner: developers
--

CREATE TABLE public.account_players (
    _account_id integer NOT NULL,
    _player_id integer NOT NULL,
    last_login timestamp with time zone DEFAULT now() NOT NULL,
    created_date timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.account_players OWNER TO developers;

--
-- Name: accounts; Type: TABLE; Schema: public; Owner: developers
--

CREATE TABLE public.accounts (
    account_id integer NOT NULL,
    account_identity text NOT NULL,
    phash text,
    active_email text NOT NULL,
    type integer DEFAULT 0,
    operational boolean DEFAULT true,
    created_date timestamp with time zone DEFAULT now() NOT NULL,
    last_login timestamp with time zone,
    last_login_failure timestamp with time zone,
    karma_total integer DEFAULT 0 NOT NULL,
    last_ip character varying(100),
    confirmed integer DEFAULT 0 NOT NULL,
    verification_number character varying(100),
    oauth_provider character varying(100),
    oauth_id character varying(100)
);


ALTER TABLE public.accounts OWNER TO developers;

--
-- Name: accounts_account_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE public.accounts_account_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.accounts_account_id_seq OWNER TO developers;

--
-- Name: accounts_account_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: developers
--

ALTER SEQUENCE public.accounts_account_id_seq OWNED BY public.accounts.account_id;


--
-- Name: chat; Type: TABLE; Schema: public; Owner: developers
--

CREATE TABLE public.chat (
    chat_id integer NOT NULL,
    sender_id integer DEFAULT 0,
    message character varying(255) NOT NULL,
    date timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.chat OWNER TO developers;

--
-- Name: chat_chat_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE public.chat_chat_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.chat_chat_id_seq OWNER TO developers;

--
-- Name: chat_chat_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: developers
--

ALTER SEQUENCE public.chat_chat_id_seq OWNED BY public.chat.chat_id;


--
-- Name: chat_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE public.chat_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.chat_id_seq OWNER TO developers;

--
-- Name: clan; Type: TABLE; Schema: public; Owner: developers
--

CREATE TABLE public.clan (
    clan_id integer NOT NULL,
    clan_name character varying(255) NOT NULL,
    clan_created_date timestamp with time zone DEFAULT now() NOT NULL,
    clan_founder text,
    clan_avatar_url text,
    description text
);


ALTER TABLE public.clan OWNER TO developers;

--
-- Name: clan_clan_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE public.clan_clan_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.clan_clan_id_seq OWNER TO developers;

--
-- Name: clan_clan_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: developers
--

ALTER SEQUENCE public.clan_clan_id_seq OWNED BY public.clan.clan_id;


--
-- Name: clan_player; Type: TABLE; Schema: public; Owner: developers
--

CREATE TABLE public.clan_player (
    _clan_id integer NOT NULL,
    _player_id integer NOT NULL,
    member_level integer DEFAULT 0 NOT NULL,
    created_at timestamp with time zone DEFAULT now()
);


ALTER TABLE public.clan_player OWNER TO developers;

--
-- Name: class; Type: TABLE; Schema: public; Owner: developers
--

CREATE TABLE public.class (
    class_id integer NOT NULL,
    class_name text NOT NULL,
    class_active boolean DEFAULT true,
    class_note text,
    class_tier integer DEFAULT 1 NOT NULL,
    class_desc text,
    class_icon text,
    theme character varying(255),
    identity character varying(255)
);


ALTER TABLE public.class OWNER TO developers;

--
-- Name: class_class_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE public.class_class_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.class_class_id_seq OWNER TO developers;

--
-- Name: class_class_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: developers
--

ALTER SEQUENCE public.class_class_id_seq OWNED BY public.class.class_id;


--
-- Name: class_skill; Type: TABLE; Schema: public; Owner: developers
--

CREATE TABLE public.class_skill (
    _class_id integer NOT NULL,
    _skill_id integer NOT NULL,
    class_skill_level integer
);


ALTER TABLE public.class_skill OWNER TO developers;

--
-- Name: dueling_log_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE public.dueling_log_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dueling_log_id_seq OWNER TO developers;

--
-- Name: dueling_log; Type: TABLE; Schema: public; Owner: developers
--

CREATE TABLE public.dueling_log (
    id integer DEFAULT nextval('public.dueling_log_id_seq'::regclass) NOT NULL,
    attacker character varying(100) NOT NULL,
    defender character varying(100) NOT NULL,
    won boolean DEFAULT false NOT NULL,
    killpoints integer DEFAULT 0 NOT NULL,
    date date DEFAULT now() NOT NULL
);


ALTER TABLE public.dueling_log OWNER TO developers;

--
-- Name: effects; Type: TABLE; Schema: public; Owner: developers
--

CREATE TABLE public.effects (
    effect_id integer NOT NULL,
    effect_identity character varying(500) NOT NULL,
    effect_name text NOT NULL,
    effect_verb text NOT NULL,
    effect_self boolean
);


ALTER TABLE public.effects OWNER TO developers;

--
-- Name: effects_effect_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE public.effects_effect_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.effects_effect_id_seq OWNER TO developers;

--
-- Name: effects_effect_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: developers
--

ALTER SEQUENCE public.effects_effect_id_seq OWNED BY public.effects.effect_id;


--
-- Name: enemies; Type: TABLE; Schema: public; Owner: developers
--

CREATE TABLE public.enemies (
    _player_id integer NOT NULL,
    _enemy_id integer NOT NULL
);


ALTER TABLE public.enemies OWNER TO developers;

--
-- Name: events; Type: TABLE; Schema: public; Owner: developers
--

CREATE TABLE public.events (
    event_id integer NOT NULL,
    send_to integer DEFAULT 0,
    send_from integer DEFAULT 0,
    message text NOT NULL,
    unread integer DEFAULT 1 NOT NULL,
    date timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.events OWNER TO developers;

--
-- Name: events_event_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE public.events_event_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.events_event_id_seq OWNER TO developers;

--
-- Name: events_event_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: developers
--

ALTER SEQUENCE public.events_event_id_seq OWNED BY public.events.event_id;


--
-- Name: flags_flag_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE public.flags_flag_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.flags_flag_id_seq OWNER TO developers;

--
-- Name: flags; Type: TABLE; Schema: public; Owner: developers
--

CREATE TABLE public.flags (
    flag_id integer DEFAULT nextval('public.flags_flag_id_seq'::regclass) NOT NULL,
    flag character varying(100) NOT NULL,
    flag_type integer NOT NULL,
    created_at timestamp with time zone DEFAULT now()
);


ALTER TABLE public.flags OWNER TO developers;

--
-- Name: inventory_item_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE public.inventory_item_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.inventory_item_id_seq OWNER TO developers;

--
-- Name: inventory; Type: TABLE; Schema: public; Owner: developers
--

CREATE TABLE public.inventory (
    item_id integer DEFAULT nextval('public.inventory_item_id_seq'::regclass) NOT NULL,
    amount integer DEFAULT 1,
    owner integer NOT NULL,
    item_type integer
);


ALTER TABLE public.inventory OWNER TO developers;

--
-- Name: item; Type: TABLE; Schema: public; Owner: developers
--

CREATE TABLE public.item (
    item_id integer NOT NULL,
    item_internal_name text NOT NULL,
    item_display_name text NOT NULL,
    item_cost numeric NOT NULL,
    image character varying(250),
    for_sale boolean DEFAULT false,
    usage text,
    ignore_stealth boolean DEFAULT false,
    covert boolean DEFAULT false,
    turn_cost integer DEFAULT 1,
    target_damage integer,
    turn_change integer,
    self_use boolean DEFAULT false,
    plural character varying(20),
    other_usable boolean DEFAULT false,
    traits character varying(250) DEFAULT ''::character varying
);


ALTER TABLE public.item OWNER TO developers;

--
-- Name: item_effects; Type: TABLE; Schema: public; Owner: developers
--

CREATE TABLE public.item_effects (
    _item_id integer NOT NULL,
    _effect_id integer NOT NULL
);


ALTER TABLE public.item_effects OWNER TO developers;

--
-- Name: item_item_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE public.item_item_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.item_item_id_seq OWNER TO developers;

--
-- Name: item_item_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: developers
--

ALTER SEQUENCE public.item_item_id_seq OWNED BY public.item.item_id;


--
-- Name: levelling_log_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE public.levelling_log_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.levelling_log_id_seq OWNER TO developers;

--
-- Name: levelling_log; Type: TABLE; Schema: public; Owner: developers
--

CREATE TABLE public.levelling_log (
    id integer DEFAULT nextval('public.levelling_log_id_seq'::regclass) NOT NULL,
    killpoints integer DEFAULT 0 NOT NULL,
    levelling integer DEFAULT 0 NOT NULL,
    killsdate date DEFAULT now() NOT NULL,
    _player_id integer NOT NULL
);


ALTER TABLE public.levelling_log OWNER TO developers;

--
-- Name: login_attempts; Type: TABLE; Schema: public; Owner: developers
--

CREATE TABLE public.login_attempts (
    attempt_id integer NOT NULL,
    username text,
    ua_string text,
    ip text,
    successful integer,
    additional_info text,
    attempt_date timestamp with time zone DEFAULT now()
);


ALTER TABLE public.login_attempts OWNER TO developers;

--
-- Name: login_attempts_attempt_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE public.login_attempts_attempt_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.login_attempts_attempt_id_seq OWNER TO developers;

--
-- Name: login_attempts_attempt_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: developers
--

ALTER SEQUENCE public.login_attempts_attempt_id_seq OWNED BY public.login_attempts.attempt_id;


--
-- Name: messages; Type: TABLE; Schema: public; Owner: developers
--

CREATE TABLE public.messages (
    message_id integer NOT NULL,
    message text NOT NULL,
    date timestamp with time zone DEFAULT now() NOT NULL,
    send_to integer,
    send_from integer,
    unread integer DEFAULT 1,
    type integer DEFAULT 0
);


ALTER TABLE public.messages OWNER TO developers;

--
-- Name: messages_message_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE public.messages_message_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.messages_message_id_seq OWNER TO developers;

--
-- Name: messages_message_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: developers
--

ALTER SEQUENCE public.messages_message_id_seq OWNED BY public.messages.message_id;


-----------------------------------------------------------------------
-- quests
-----------------------------------------------------------------------

CREATE TABLE public.quests
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
    "created_at" TIMESTAMP WITH TIME ZONE DEFAULT now() NOT NULL,
    "updated_at" TIMESTAMP WITH TIME ZONE DEFAULT now() NOT NULL,
    "deleted_at" TIMESTAMP WITH TIME ZONE DEFAULT NULL,
    "expires_at" TIMESTAMP WITH TIME ZONE DEFAULT now() + interval ' 1 mon ' NOT NULL,
    "type" INTEGER,
    "difficulty" INTEGER,
    PRIMARY KEY ("quest_id")
);

CREATE INDEX "quest_created_at_index" ON "quests" ("created_at");

CREATE INDEX "quest_title_index" ON "quests" ("title");

--
-- Name: news; Type: TABLE; Schema: public; Owner: developers
--

CREATE TABLE public.news (
    news_id integer NOT NULL,
    title character varying(100) DEFAULT ''::character varying NOT NULL,
    content text DEFAULT ''::text NOT NULL,
    created timestamp with time zone DEFAULT now() NOT NULL,
    updated timestamp with time zone DEFAULT now() NOT NULL,
    tags text DEFAULT ''::text
);


ALTER TABLE public.news OWNER TO developers;

--
-- Name: news_news_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE public.news_news_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.news_news_id_seq OWNER TO developers;

--
-- Name: news_news_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: developers
--

ALTER SEQUENCE public.news_news_id_seq OWNED BY public.news.news_id;


--
-- Name: password_reset_requests; Type: TABLE; Schema: public; Owner: developers
--

CREATE TABLE public.password_reset_requests (
    request_id integer NOT NULL,
    _account_id integer NOT NULL,
    nonce character varying(130) NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL,
    used boolean DEFAULT false NOT NULL,
    updated_at timestamp with time zone DEFAULT now()
);


ALTER TABLE public.password_reset_requests OWNER TO developers;

--
-- Name: password_reset_requests_request_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE public.password_reset_requests_request_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.password_reset_requests_request_id_seq OWNER TO developers;

--
-- Name: password_reset_requests_request_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: developers
--

ALTER SEQUENCE public.password_reset_requests_request_id_seq OWNED BY public.password_reset_requests.request_id;


--
-- Name: past_stats_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE public.past_stats_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.past_stats_id_seq OWNER TO developers;

--
-- Name: past_stats; Type: TABLE; Schema: public; Owner: developers
--

CREATE TABLE public.past_stats (
    id integer DEFAULT nextval('public.past_stats_id_seq'::regclass) NOT NULL,
    stat_type character varying(50) DEFAULT ''::character varying NOT NULL,
    stat_result character varying(50) DEFAULT ''::character varying NOT NULL
);


ALTER TABLE public.past_stats OWNER TO developers;

--
-- Name: player_rank_rank_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE public.player_rank_rank_id_seq
    START WITH 1
    INCREMENT BY 1
    MINVALUE 0
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.player_rank_rank_id_seq OWNER TO developers;

--
-- Name: player_rank; Type: TABLE; Schema: public; Owner: developers
--

CREATE TABLE public.player_rank (
    rank_id integer DEFAULT nextval('public.player_rank_rank_id_seq'::regclass) NOT NULL,
    _player_id integer NOT NULL,
    score integer
);


ALTER TABLE public.player_rank OWNER TO developers;

--
-- Name: players_player_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE public.players_player_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.players_player_id_seq OWNER TO developers;

--
-- Name: players; Type: TABLE; Schema: public; Owner: developers
--

CREATE TABLE public.players (
    player_id integer DEFAULT nextval('public.players_player_id_seq'::regclass) NOT NULL,
    uname character varying(100) NOT NULL,
    health integer DEFAULT 0 NOT NULL,
    strength integer DEFAULT 0 NOT NULL,
    gold integer DEFAULT 0 NOT NULL,
    messages text DEFAULT ''::character varying NOT NULL,
    kills integer DEFAULT 0 NOT NULL,
    turns integer DEFAULT 0 NOT NULL,
    verification_number integer DEFAULT 0 NOT NULL,
    active integer DEFAULT 0 NOT NULL,
    email character varying(100) DEFAULT ''::character varying NOT NULL,
    level integer DEFAULT 0 NOT NULL,
    status integer DEFAULT 0 NOT NULL,
    member integer DEFAULT 0 NOT NULL,
    days integer DEFAULT 0 NOT NULL,
    ip character varying(100) DEFAULT ''::character varying NOT NULL,
    bounty integer DEFAULT 0 NOT NULL,
    created_date timestamp with time zone DEFAULT now(),
    resurrection_time integer DEFAULT round((random() * (23)::double precision)) NOT NULL,
    last_started_attack timestamp with time zone DEFAULT now(),
    energy integer DEFAULT 0 NOT NULL,
    avatar_type integer DEFAULT 1 NOT NULL,
    _class_id integer NOT NULL,
    ki integer DEFAULT 0 NOT NULL,
    stamina integer DEFAULT 0 NOT NULL,
    speed integer DEFAULT 0 NOT NULL,
    karma integer DEFAULT 0 NOT NULL,
    kills_gained integer DEFAULT 0 NOT NULL,
    kills_used integer DEFAULT 0 NOT NULL,
    description text DEFAULT ''::text NOT NULL,
    instincts text DEFAULT ''::text NOT NULL,
    beliefs text DEFAULT ''::text NOT NULL,
    goals text DEFAULT ''::text NOT NULL,
    traits text DEFAULT ''::text NOT NULL
);


ALTER TABLE public.players OWNER TO developers;

--
-- Name: players_flagged_players_flagged_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE public.players_flagged_players_flagged_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.players_flagged_players_flagged_id_seq OWNER TO developers;

--
-- Name: players_flagged; Type: TABLE; Schema: public; Owner: developers
--

CREATE TABLE public.players_flagged (
    players_flagged_id integer DEFAULT nextval('public.players_flagged_players_flagged_id_seq'::regclass) NOT NULL,
    player_id integer,
    flag_id integer,
    "timestamp" date DEFAULT now(),
    originating_page character varying(50),
    extra_notes character varying(100)
);


ALTER TABLE public.players_flagged OWNER TO developers;

--
-- Name: rankings; Type: VIEW; Schema: public; Owner: developers
--

CREATE VIEW public.rankings AS
 SELECT player_rank.rank_id,
    players.player_id,
    player_rank.score,
    players.uname,
    class.class_name,
    players.level,
    (
        CASE
            WHEN (players.health = 0) THEN 0
            ELSE 1
        END)::boolean AS alive,
    players.days
   FROM ((public.player_rank
     JOIN public.players ON ((players.player_id = player_rank._player_id)))
     JOIN public.class ON ((class.class_id = players._class_id)))
  WHERE (players.active = 1)
  ORDER BY player_rank.rank_id;


ALTER TABLE public.rankings OWNER TO developers;

--
-- Name: settings; Type: TABLE; Schema: public; Owner: developers
--

CREATE TABLE public.settings (
    setting_id integer NOT NULL,
    player_id integer NOT NULL,
    settings_store text
);


ALTER TABLE public.settings OWNER TO developers;

--
-- Name: settings_setting_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE public.settings_setting_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.settings_setting_id_seq OWNER TO developers;

--
-- Name: settings_setting_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: developers
--

ALTER SEQUENCE public.settings_setting_id_seq OWNED BY public.settings.setting_id;


--
-- Name: skill; Type: TABLE; Schema: public; Owner: developers
--

CREATE TABLE public.skill (
    skill_id integer NOT NULL,
    skill_level integer DEFAULT 1 NOT NULL,
    skill_is_active boolean DEFAULT true,
    skill_display_name text NOT NULL,
    skill_internal_name text NOT NULL,
    skill_type public.skill_type NOT NULL
);


ALTER TABLE public.skill OWNER TO developers;

--
-- Name: skill_skill_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE public.skill_skill_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.skill_skill_id_seq OWNER TO developers;

--
-- Name: skill_skill_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: developers
--

ALTER SEQUENCE public.skill_skill_id_seq OWNED BY public.skill.skill_id;


--
-- Name: time; Type: TABLE; Schema: public; Owner: developers
--

CREATE TABLE public."time" (
    time_id integer NOT NULL,
    time_label character varying NOT NULL,
    amount integer NOT NULL
);


ALTER TABLE public."time" OWNER TO developers;

--
-- Name: time_time_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE public.time_time_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.time_time_id_seq OWNER TO developers;

--
-- Name: time_time_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: developers
--

ALTER SEQUENCE public.time_time_id_seq OWNED BY public."time".time_id;


--
-- Name: accounts account_id; Type: DEFAULT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.accounts ALTER COLUMN account_id SET DEFAULT nextval('public.accounts_account_id_seq'::regclass);


--
-- Name: chat chat_id; Type: DEFAULT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.chat ALTER COLUMN chat_id SET DEFAULT nextval('public.chat_chat_id_seq'::regclass);


--
-- Name: clan clan_id; Type: DEFAULT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.clan ALTER COLUMN clan_id SET DEFAULT nextval('public.clan_clan_id_seq'::regclass);


--
-- Name: class class_id; Type: DEFAULT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.class ALTER COLUMN class_id SET DEFAULT nextval('public.class_class_id_seq'::regclass);


--
-- Name: effects effect_id; Type: DEFAULT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.effects ALTER COLUMN effect_id SET DEFAULT nextval('public.effects_effect_id_seq'::regclass);


--
-- Name: events event_id; Type: DEFAULT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.events ALTER COLUMN event_id SET DEFAULT nextval('public.events_event_id_seq'::regclass);


--
-- Name: item item_id; Type: DEFAULT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.item ALTER COLUMN item_id SET DEFAULT nextval('public.item_item_id_seq'::regclass);


--
-- Name: login_attempts attempt_id; Type: DEFAULT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.login_attempts ALTER COLUMN attempt_id SET DEFAULT nextval('public.login_attempts_attempt_id_seq'::regclass);


--
-- Name: messages message_id; Type: DEFAULT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.messages ALTER COLUMN message_id SET DEFAULT nextval('public.messages_message_id_seq'::regclass);


--
-- Name: news news_id; Type: DEFAULT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.news ALTER COLUMN news_id SET DEFAULT nextval('public.news_news_id_seq'::regclass);


--
-- Name: password_reset_requests request_id; Type: DEFAULT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.password_reset_requests ALTER COLUMN request_id SET DEFAULT nextval('public.password_reset_requests_request_id_seq'::regclass);


--
-- Name: settings setting_id; Type: DEFAULT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.settings ALTER COLUMN setting_id SET DEFAULT nextval('public.settings_setting_id_seq'::regclass);


--
-- Name: skill skill_id; Type: DEFAULT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.skill ALTER COLUMN skill_id SET DEFAULT nextval('public.skill_skill_id_seq'::regclass);


--
-- Name: time time_id; Type: DEFAULT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public."time" ALTER COLUMN time_id SET DEFAULT nextval('public.time_time_id_seq'::regclass);


--
-- Name: account_news account_news_pkey; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.account_news
    ADD CONSTRAINT account_news_pkey PRIMARY KEY (_account_id, _news_id);


--
-- Name: account_players account_players__player_id_key; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.account_players
    ADD CONSTRAINT account_players__player_id_key UNIQUE (_player_id);


--
-- Name: account_players account_players__player_id_key1; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.account_players
    ADD CONSTRAINT account_players__player_id_key1 UNIQUE (_player_id);


--
-- Name: account_players account_players_pkey; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.account_players
    ADD CONSTRAINT account_players_pkey PRIMARY KEY (_account_id, _player_id);


--
-- Name: accounts accounts_account_identity_key; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.accounts
    ADD CONSTRAINT accounts_account_identity_key UNIQUE (account_identity);


--
-- Name: accounts accounts_active_email_key; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.accounts
    ADD CONSTRAINT accounts_active_email_key UNIQUE (active_email);


--
-- Name: accounts accounts_pkey; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.accounts
    ADD CONSTRAINT accounts_pkey PRIMARY KEY (account_id);


--
-- Name: chat chat_pkey; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.chat
    ADD CONSTRAINT chat_pkey PRIMARY KEY (chat_id);


--
-- Name: clan clan_clan_name_key; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.clan
    ADD CONSTRAINT clan_clan_name_key UNIQUE (clan_name);


--
-- Name: clan clan_pkey; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.clan
    ADD CONSTRAINT clan_pkey PRIMARY KEY (clan_id);


--
-- Name: clan_player clan_player__player_id_key; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.clan_player
    ADD CONSTRAINT clan_player__player_id_key UNIQUE (_player_id);


--
-- Name: clan_player clan_player__player_id_key1; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.clan_player
    ADD CONSTRAINT clan_player__player_id_key1 UNIQUE (_player_id);


--
-- Name: clan_player clan_player_pkey; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.clan_player
    ADD CONSTRAINT clan_player_pkey PRIMARY KEY (_clan_id, _player_id);


--
-- Name: class class_class_name_key; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.class
    ADD CONSTRAINT class_class_name_key UNIQUE (class_name);


--
-- Name: class class_pkey; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.class
    ADD CONSTRAINT class_pkey PRIMARY KEY (class_id);


--
-- Name: class_skill class_skill_pkey; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.class_skill
    ADD CONSTRAINT class_skill_pkey PRIMARY KEY (_class_id, _skill_id);


--
-- Name: dueling_log dueling_log_pkey; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.dueling_log
    ADD CONSTRAINT dueling_log_pkey PRIMARY KEY (id);


--
-- Name: effects effects_effect_identity_key; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.effects
    ADD CONSTRAINT effects_effect_identity_key UNIQUE (effect_identity);


--
-- Name: effects effects_effect_name_key; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.effects
    ADD CONSTRAINT effects_effect_name_key UNIQUE (effect_name);


--
-- Name: effects effects_pkey; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.effects
    ADD CONSTRAINT effects_pkey PRIMARY KEY (effect_id);


--
-- Name: enemies enemies_pkey; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.enemies
    ADD CONSTRAINT enemies_pkey PRIMARY KEY (_player_id, _enemy_id);


--
-- Name: events events_pkey; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.events
    ADD CONSTRAINT events_pkey PRIMARY KEY (event_id);


--
-- Name: flags flags_flag_key; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.flags
    ADD CONSTRAINT flags_flag_key UNIQUE (flag);


--
-- Name: flags flags_pkey; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.flags
    ADD CONSTRAINT flags_pkey PRIMARY KEY (flag_id);


--
-- Name: inventory inventory_pkey; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.inventory
    ADD CONSTRAINT inventory_pkey PRIMARY KEY (item_id);


--
-- Name: item_effects item_effects_pkey; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.item_effects
    ADD CONSTRAINT item_effects_pkey PRIMARY KEY (_item_id, _effect_id);


--
-- Name: item item_item_display_name_key; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.item
    ADD CONSTRAINT item_item_display_name_key UNIQUE (item_display_name);


--
-- Name: item item_item_internal_name_key; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.item
    ADD CONSTRAINT item_item_internal_name_key UNIQUE (item_internal_name);


--
-- Name: item item_pkey; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.item
    ADD CONSTRAINT item_pkey PRIMARY KEY (item_id);


--
-- Name: levelling_log levelling_log_pkey; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.levelling_log
    ADD CONSTRAINT levelling_log_pkey PRIMARY KEY (id);


--
-- Name: login_attempts login_attempts_pkey; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.login_attempts
    ADD CONSTRAINT login_attempts_pkey PRIMARY KEY (attempt_id);


--
-- Name: messages messages_pkey; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.messages
    ADD CONSTRAINT messages_pkey PRIMARY KEY (message_id);


--
-- Name: news news_pkey; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.news
    ADD CONSTRAINT news_pkey PRIMARY KEY (news_id);


--
-- Name: password_reset_requests password_reset_requests_pkey; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.password_reset_requests
    ADD CONSTRAINT password_reset_requests_pkey PRIMARY KEY (request_id);


--
-- Name: past_stats past_stats_pkey; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.past_stats
    ADD CONSTRAINT past_stats_pkey PRIMARY KEY (id);


--
-- Name: player_rank player_rank_pkey; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.player_rank
    ADD CONSTRAINT player_rank_pkey PRIMARY KEY (rank_id);


--
-- Name: players_flagged players_flagged_pkey; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.players_flagged
    ADD CONSTRAINT players_flagged_pkey PRIMARY KEY (players_flagged_id);


--
-- Name: players players_pkey; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.players
    ADD CONSTRAINT players_pkey PRIMARY KEY (player_id);


--
-- Name: players players_uname_key; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.players
    ADD CONSTRAINT players_uname_key UNIQUE (uname);


--
-- Name: settings settings_pkey; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.settings
    ADD CONSTRAINT settings_pkey PRIMARY KEY (setting_id);


--
-- Name: skill skill_pkey; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.skill
    ADD CONSTRAINT skill_pkey PRIMARY KEY (skill_id);


--
-- Name: skill skill_skill_display_name_key; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.skill
    ADD CONSTRAINT skill_skill_display_name_key UNIQUE (skill_display_name);


--
-- Name: skill skill_skill_internal_name_key; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.skill
    ADD CONSTRAINT skill_skill_internal_name_key UNIQUE (skill_internal_name);


--
-- Name: time time_pkey; Type: CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public."time"
    ADD CONSTRAINT time_pkey PRIMARY KEY (time_id);


--
-- Name: active_pc_health; Type: INDEX; Schema: public; Owner: developers
--

CREATE INDEX active_pc_health ON public.players USING btree (active, health);


--
-- Name: chat_date_index; Type: INDEX; Schema: public; Owner: developers
--

CREATE INDEX chat_date_index ON public.chat USING btree (date);


--
-- Name: dueling_log_date_index; Type: INDEX; Schema: public; Owner: developers
--

CREATE INDEX dueling_log_date_index ON public.dueling_log USING btree (date);


--
-- Name: events_date_idx; Type: INDEX; Schema: public; Owner: developers
--

CREATE INDEX events_date_idx ON public.events USING btree (date);


--
-- Name: messages_date_index; Type: INDEX; Schema: public; Owner: developers
--

CREATE INDEX messages_date_index ON public.messages USING btree (date);


--
-- Name: news_created_index; Type: INDEX; Schema: public; Owner: developers
--

CREATE INDEX news_created_index ON public.news USING btree (created);


--
-- Name: account_news account_news__account_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.account_news
    ADD CONSTRAINT account_news__account_id_fkey FOREIGN KEY (_account_id) REFERENCES public.accounts(account_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: account_news account_news__news_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.account_news
    ADD CONSTRAINT account_news__news_id_fkey FOREIGN KEY (_news_id) REFERENCES public.news(news_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: account_players account_players__account_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.account_players
    ADD CONSTRAINT account_players__account_id_fkey FOREIGN KEY (_account_id) REFERENCES public.accounts(account_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: account_players account_players__player_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.account_players
    ADD CONSTRAINT account_players__player_id_fkey FOREIGN KEY (_player_id) REFERENCES public.players(player_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: clan_player clan_player__clan_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.clan_player
    ADD CONSTRAINT clan_player__clan_id_fkey FOREIGN KEY (_clan_id) REFERENCES public.clan(clan_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: clan_player clan_player__player_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.clan_player
    ADD CONSTRAINT clan_player__player_id_fkey FOREIGN KEY (_player_id) REFERENCES public.players(player_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: class_skill class_skill__class_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.class_skill
    ADD CONSTRAINT class_skill__class_id_fkey FOREIGN KEY (_class_id) REFERENCES public.class(class_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: class_skill class_skill__skill_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.class_skill
    ADD CONSTRAINT class_skill__skill_id_fkey FOREIGN KEY (_skill_id) REFERENCES public.skill(skill_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: item_effects effects_effect_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.item_effects
    ADD CONSTRAINT effects_effect_id_fkey FOREIGN KEY (_effect_id) REFERENCES public.effects(effect_id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: enemies enemies__enemy_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.enemies
    ADD CONSTRAINT enemies__enemy_id_fkey FOREIGN KEY (_enemy_id) REFERENCES public.players(player_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: enemies enemies__player_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.enemies
    ADD CONSTRAINT enemies__player_id_fkey FOREIGN KEY (_player_id) REFERENCES public.players(player_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: inventory inventory_owner_fkey; Type: FK CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.inventory
    ADD CONSTRAINT inventory_owner_fkey FOREIGN KEY (owner) REFERENCES public.players(player_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: item_effects item_item_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.item_effects
    ADD CONSTRAINT item_item_id_fkey FOREIGN KEY (_item_id) REFERENCES public.item(item_id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: levelling_log levelling_log__player_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.levelling_log
    ADD CONSTRAINT levelling_log__player_id_fkey FOREIGN KEY (_player_id) REFERENCES public.players(player_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: messages messages_send_from_fkey; Type: FK CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.messages
    ADD CONSTRAINT messages_send_from_fkey FOREIGN KEY (send_from) REFERENCES public.players(player_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: messages messages_send_to_fkey; Type: FK CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.messages
    ADD CONSTRAINT messages_send_to_fkey FOREIGN KEY (send_to) REFERENCES public.players(player_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: players players__class_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY public.players
    ADD CONSTRAINT players__class_id_fkey FOREIGN KEY (_class_id) REFERENCES public.class(class_id) ON UPDATE CASCADE;


--
-- Name: SEQUENCE accounts_account_id_seq; Type: ACL; Schema: public; Owner: developers
--

GRANT SELECT,USAGE ON SEQUENCE public.accounts_account_id_seq TO ninjamaster;


--
-- Name: SEQUENCE chat_chat_id_seq; Type: ACL; Schema: public; Owner: developers
--

GRANT SELECT,USAGE ON SEQUENCE public.chat_chat_id_seq TO ninjamaster;


--
-- Name: SEQUENCE chat_id_seq; Type: ACL; Schema: public; Owner: developers
--

GRANT SELECT,USAGE ON SEQUENCE public.chat_id_seq TO ninjamaster;


--
-- Name: SEQUENCE clan_clan_id_seq; Type: ACL; Schema: public; Owner: developers
--

GRANT SELECT,USAGE ON SEQUENCE public.clan_clan_id_seq TO ninjamaster;


--
-- Name: SEQUENCE class_class_id_seq; Type: ACL; Schema: public; Owner: developers
--

GRANT SELECT,USAGE ON SEQUENCE public.class_class_id_seq TO ninjamaster;


--
-- Name: SEQUENCE dueling_log_id_seq; Type: ACL; Schema: public; Owner: developers
--

GRANT SELECT,USAGE ON SEQUENCE public.dueling_log_id_seq TO ninjamaster;


--
-- Name: SEQUENCE effects_effect_id_seq; Type: ACL; Schema: public; Owner: developers
--

GRANT SELECT,USAGE ON SEQUENCE public.effects_effect_id_seq TO ninjamaster;


--
-- Name: SEQUENCE events_event_id_seq; Type: ACL; Schema: public; Owner: developers
--

GRANT SELECT,USAGE ON SEQUENCE public.events_event_id_seq TO ninjamaster;


--
-- Name: SEQUENCE flags_flag_id_seq; Type: ACL; Schema: public; Owner: developers
--

GRANT SELECT,USAGE ON SEQUENCE public.flags_flag_id_seq TO ninjamaster;


--
-- Name: SEQUENCE inventory_item_id_seq; Type: ACL; Schema: public; Owner: developers
--

GRANT SELECT,USAGE ON SEQUENCE public.inventory_item_id_seq TO ninjamaster;


--
-- Name: SEQUENCE item_item_id_seq; Type: ACL; Schema: public; Owner: developers
--

GRANT SELECT,USAGE ON SEQUENCE public.item_item_id_seq TO ninjamaster;


--
-- Name: SEQUENCE levelling_log_id_seq; Type: ACL; Schema: public; Owner: developers
--

GRANT SELECT,USAGE ON SEQUENCE public.levelling_log_id_seq TO ninjamaster;


--
-- Name: SEQUENCE login_attempts_attempt_id_seq; Type: ACL; Schema: public; Owner: developers
--

GRANT SELECT,USAGE ON SEQUENCE public.login_attempts_attempt_id_seq TO ninjamaster;


--
-- Name: SEQUENCE messages_message_id_seq; Type: ACL; Schema: public; Owner: developers
--

GRANT SELECT,USAGE ON SEQUENCE public.messages_message_id_seq TO ninjamaster;


--
-- Name: SEQUENCE news_news_id_seq; Type: ACL; Schema: public; Owner: developers
--

GRANT SELECT,USAGE ON SEQUENCE public.news_news_id_seq TO ninjamaster;


--
-- Name: SEQUENCE past_stats_id_seq; Type: ACL; Schema: public; Owner: developers
--

GRANT SELECT,USAGE ON SEQUENCE public.past_stats_id_seq TO ninjamaster;


--
-- Name: SEQUENCE player_rank_rank_id_seq; Type: ACL; Schema: public; Owner: developers
--

GRANT SELECT,USAGE ON SEQUENCE public.player_rank_rank_id_seq TO ninjamaster;


--
-- Name: SEQUENCE players_player_id_seq; Type: ACL; Schema: public; Owner: developers
--

GRANT SELECT,USAGE ON SEQUENCE public.players_player_id_seq TO ninjamaster;


--
-- Name: SEQUENCE players_flagged_players_flagged_id_seq; Type: ACL; Schema: public; Owner: developers
--

GRANT SELECT,USAGE ON SEQUENCE public.players_flagged_players_flagged_id_seq TO ninjamaster;


--
-- Name: SEQUENCE settings_setting_id_seq; Type: ACL; Schema: public; Owner: developers
--

GRANT SELECT,USAGE ON SEQUENCE public.settings_setting_id_seq TO ninjamaster;


--
-- Name: SEQUENCE skill_skill_id_seq; Type: ACL; Schema: public; Owner: developers
--

GRANT SELECT,USAGE ON SEQUENCE public.skill_skill_id_seq TO ninjamaster;


--
-- Name: SEQUENCE time_time_id_seq; Type: ACL; Schema: public; Owner: developers
--

GRANT SELECT,USAGE ON SEQUENCE public.time_time_id_seq TO ninjamaster;


--
-- PostgreSQL database dump complete
--

