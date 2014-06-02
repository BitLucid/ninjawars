--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

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


SET search_path = public, pg_catalog;

--
-- Name: skill_type; Type: TYPE; Schema: public; Owner: kzqai
--

CREATE TYPE skill_type AS ENUM (
    'combat',
    'passive',
    'self-only',
    'targeted'
);


--
-- Name: array_accum(anyelement); Type: AGGREGATE; Schema: public; Owner: kzqai
--

CREATE AGGREGATE array_accum(anyelement) (
    SFUNC = array_append,
    STYPE = anyarray,
    INITCOND = '{}'
);


SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: account_players; Type: TABLE; Schema: public; Owner: developers; Tablespace: 
--

CREATE TABLE account_players (
    _account_id integer NOT NULL,
    _player_id integer NOT NULL,
    last_login timestamp without time zone DEFAULT now() NOT NULL,
    created_date timestamp without time zone DEFAULT now() NOT NULL
);


--
-- Name: accounts; Type: TABLE; Schema: public; Owner: developers; Tablespace: 
--

CREATE TABLE accounts (
    account_id integer NOT NULL,
    account_identity text NOT NULL,
    phash text,
    active_email text NOT NULL,
    type integer DEFAULT 0,
    operational boolean DEFAULT true,
    created_date timestamp without time zone DEFAULT now() NOT NULL,
    last_login timestamp without time zone,
    last_login_failure timestamp without time zone,
    karma_total integer DEFAULT 0 NOT NULL,
    last_ip character varying(100),
    confirmed integer DEFAULT 0 NOT NULL,
    verification_number character varying(100)
);


--
-- Name: accounts_account_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE accounts_account_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: accounts_account_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: developers
--

ALTER SEQUENCE accounts_account_id_seq OWNED BY accounts.account_id;


--
-- Name: chat; Type: TABLE; Schema: public; Owner: developers; Tablespace: 
--

CREATE TABLE chat (
    chat_id integer NOT NULL,
    sender_id integer DEFAULT 0,
    message character varying(255) NOT NULL,
    date timestamp without time zone DEFAULT now() NOT NULL
);


--
-- Name: chat_chat_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE chat_chat_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: chat_chat_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: developers
--

ALTER SEQUENCE chat_chat_id_seq OWNED BY chat.chat_id;


--
-- Name: chat_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE chat_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: clan; Type: TABLE; Schema: public; Owner: developers; Tablespace: 
--

CREATE TABLE clan (
    clan_id integer NOT NULL,
    clan_name character varying(255) NOT NULL,
    clan_created_date timestamp without time zone DEFAULT now() NOT NULL,
    clan_founder text,
    clan_avatar_url text,
    description text
);


--
-- Name: clan_clan_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE clan_clan_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: clan_clan_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: developers
--

ALTER SEQUENCE clan_clan_id_seq OWNED BY clan.clan_id;


--
-- Name: clan_player; Type: TABLE; Schema: public; Owner: developers; Tablespace: 
--

CREATE TABLE clan_player (
    _clan_id integer NOT NULL,
    _player_id integer NOT NULL,
    member_level integer DEFAULT 0 NOT NULL
);


--
-- Name: class; Type: TABLE; Schema: public; Owner: developers; Tablespace: 
--

CREATE TABLE class (
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


--
-- Name: class_class_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE class_class_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: class_class_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: developers
--

ALTER SEQUENCE class_class_id_seq OWNED BY class.class_id;


--
-- Name: class_skill; Type: TABLE; Schema: public; Owner: developers; Tablespace: 
--

CREATE TABLE class_skill (
    _class_id integer NOT NULL,
    _skill_id integer NOT NULL,
    class_skill_level integer
);


--
-- Name: dueling_log_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE dueling_log_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: dueling_log; Type: TABLE; Schema: public; Owner: developers; Tablespace: 
--

CREATE TABLE dueling_log (
    id integer DEFAULT nextval('dueling_log_id_seq'::regclass) NOT NULL,
    attacker character varying(100) NOT NULL,
    defender character varying(100) NOT NULL,
    won boolean DEFAULT false NOT NULL,
    killpoints integer DEFAULT 0 NOT NULL,
    date date DEFAULT now() NOT NULL
);


--
-- Name: duped_unames; Type: TABLE; Schema: public; Owner: developers; Tablespace: 
--

CREATE TABLE duped_unames (
    uname text NOT NULL,
    email text NOT NULL,
    created_date timestamp without time zone NOT NULL,
    relative_age smallint NOT NULL,
    player_id integer NOT NULL,
    locked boolean DEFAULT false NOT NULL
);


--
-- Name: effects; Type: TABLE; Schema: public; Owner: developers; Tablespace: 
--

CREATE TABLE effects (
    effect_id integer NOT NULL,
    effect_identity character varying(500) NOT NULL,
    effect_name text NOT NULL,
    effect_verb text NOT NULL,
    effect_self boolean
);


--
-- Name: effects_effect_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE effects_effect_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: effects_effect_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: developers
--

ALTER SEQUENCE effects_effect_id_seq OWNED BY effects.effect_id;


--
-- Name: enemies; Type: TABLE; Schema: public; Owner: developers; Tablespace: 
--

CREATE TABLE enemies (
    _player_id integer NOT NULL,
    _enemy_id integer NOT NULL
);


--
-- Name: events; Type: TABLE; Schema: public; Owner: developers; Tablespace: 
--

CREATE TABLE events (
    event_id integer NOT NULL,
    send_to integer DEFAULT 0,
    send_from integer DEFAULT 0,
    message text NOT NULL,
    unread integer DEFAULT 1 NOT NULL,
    date timestamp without time zone DEFAULT now() NOT NULL
);


--
-- Name: events_event_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE events_event_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;



--
-- Name: events_event_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: developers
--

ALTER SEQUENCE events_event_id_seq OWNED BY events.event_id;


--
-- Name: flags_flag_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE flags_flag_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;



--
-- Name: flags; Type: TABLE; Schema: public; Owner: developers; Tablespace: 
--

CREATE TABLE flags (
    flag_id integer DEFAULT nextval('flags_flag_id_seq'::regclass) NOT NULL,
    flag character varying(100) NOT NULL,
    flag_type integer NOT NULL
);



--
-- Name: inventory_item_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE inventory_item_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;



--
-- Name: inventory; Type: TABLE; Schema: public; Owner: developers; Tablespace: 
--

CREATE TABLE inventory (
    item_id integer DEFAULT nextval('inventory_item_id_seq'::regclass) NOT NULL,
    amount integer DEFAULT 1,
    owner integer NOT NULL,
    item_type integer,
    item_type_string_backup character varying
);



--
-- Name: item; Type: TABLE; Schema: public; Owner: developers; Tablespace: 
--

CREATE TABLE item (
    item_id integer NOT NULL,
    item_internal_name text NOT NULL,
    item_display_name text NOT NULL,
    item_cost numeric NOT NULL,
    image character varying(250),
    for_sale boolean DEFAULT false,
    usage text,
    ignore_stealth boolean DEFAULT false,
    covert boolean DEFAULT false,
    turn_cost integer,
    target_damage integer,
    turn_change integer,
    self_use boolean DEFAULT false,
    plural character varying(20),
    other_usable boolean DEFAULT false,
    traits character varying(250)
);



--
-- Name: item_effects; Type: TABLE; Schema: public; Owner: developers; Tablespace: 
--

CREATE TABLE item_effects (
    _item_id integer NOT NULL,
    _effect_id integer NOT NULL
);



--
-- Name: item_item_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE item_item_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;



--
-- Name: item_item_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: developers
--

ALTER SEQUENCE item_item_id_seq OWNED BY item.item_id;


--
-- Name: levelling_log_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE levelling_log_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;



--
-- Name: levelling_log; Type: TABLE; Schema: public; Owner: developers; Tablespace: 
--

CREATE TABLE levelling_log (
    id integer DEFAULT nextval('levelling_log_id_seq'::regclass) NOT NULL,
    killpoints integer DEFAULT 0 NOT NULL,
    levelling integer DEFAULT 0 NOT NULL,
    killsdate date NOT NULL,
    _player_id integer NOT NULL
);



--
-- Name: login_attempts; Type: TABLE; Schema: public; Owner: kzqai; Tablespace: 
--

CREATE TABLE login_attempts (
    attempt_id integer NOT NULL,
    username text,
    ua_string text,
    ip text,
    successful integer,
    additional_info text,
    attempt_date timestamp without time zone DEFAULT now()
);



--
-- Name: login_attempts_attempt_id_seq; Type: SEQUENCE; Schema: public; Owner: kzqai
--

CREATE SEQUENCE login_attempts_attempt_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;



--
-- Name: login_attempts_attempt_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: kzqai
--

ALTER SEQUENCE login_attempts_attempt_id_seq OWNED BY login_attempts.attempt_id;


--
-- Name: messages; Type: TABLE; Schema: public; Owner: developers; Tablespace: 
--

CREATE TABLE messages (
    message_id integer NOT NULL,
    message text NOT NULL,
    date timestamp without time zone DEFAULT now() NOT NULL,
    send_to integer,
    send_from integer,
    unread integer DEFAULT 1,
    type integer DEFAULT 0
);



--
-- Name: messages_message_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE messages_message_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;



--
-- Name: messages_message_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: developers
--

ALTER SEQUENCE messages_message_id_seq OWNED BY messages.message_id;


--
-- Name: past_stats_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE past_stats_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;



--
-- Name: past_stats; Type: TABLE; Schema: public; Owner: developers; Tablespace: 
--

CREATE TABLE past_stats (
    id integer DEFAULT nextval('past_stats_id_seq'::regclass) NOT NULL,
    stat_type character varying(50) DEFAULT ''::character varying NOT NULL,
    stat_result character varying(50) DEFAULT ''::character varying NOT NULL
);



--
-- Name: player_rank_rank_id_seq1; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE player_rank_rank_id_seq1
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;



--
-- Name: player_rank; Type: TABLE; Schema: public; Owner: developers; Tablespace: 
--

CREATE TABLE player_rank (
    rank_id integer DEFAULT nextval('player_rank_rank_id_seq1'::regclass) NOT NULL,
    _player_id integer NOT NULL,
    score integer
);



--
-- Name: player_rank_rank_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE player_rank_rank_id_seq
    START WITH 1
    INCREMENT BY 1
    MINVALUE 0
    NO MAXVALUE
    CACHE 1;



--
-- Name: players_player_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE players_player_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;



--
-- Name: players; Type: TABLE; Schema: public; Owner: developers; Tablespace: 
--

CREATE TABLE players (
    player_id integer DEFAULT nextval('players_player_id_seq'::regclass) NOT NULL,
    uname character varying(100) NOT NULL,
    pname_backup character varying(100),
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
    created_date timestamp without time zone DEFAULT now(),
    resurrection_time integer DEFAULT (round((random() * (7)::double precision)) * (3)::double precision) NOT NULL,
    last_started_attack timestamp without time zone DEFAULT now(),
    energy integer DEFAULT 0 NOT NULL,
    avatar_type integer DEFAULT 1 NOT NULL,
    _class_id integer NOT NULL,
    ki integer DEFAULT 0 NOT NULL,
    stamina integer DEFAULT 0 NOT NULL,
    speed integer DEFAULT 0 NOT NULL,
    karma integer DEFAULT 0 NOT NULL,
    kills_gained integer DEFAULT 0 NOT NULL,
    kills_used integer DEFAULT 0 NOT NULL
);



--
-- Name: players_flagged_players_flagged_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE players_flagged_players_flagged_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;



--
-- Name: players_flagged; Type: TABLE; Schema: public; Owner: developers; Tablespace: 
--

CREATE TABLE players_flagged (
    players_flagged_id integer DEFAULT nextval('players_flagged_players_flagged_id_seq'::regclass) NOT NULL,
    player_id integer,
    flag_id integer,
    "timestamp" date DEFAULT now(),
    originating_page character varying(50),
    extra_notes character varying(100)
);



--
-- Name: ppl_online; Type: TABLE; Schema: public; Owner: developers; Tablespace: 
--

CREATE TABLE ppl_online (
    session_id character varying(255) NOT NULL,
    activity timestamp without time zone DEFAULT now() NOT NULL,
    member boolean DEFAULT false NOT NULL,
    ip_address character varying(255) DEFAULT ''::character varying NOT NULL,
    refurl character varying(255) DEFAULT ''::character varying NOT NULL,
    user_agent character varying(255)
);



--
-- Name: rankings; Type: VIEW; Schema: public; Owner: kzqai
--

CREATE VIEW rankings AS
    SELECT player_rank.rank_id, players.player_id, player_rank.score, players.uname, class.class_name, players.level, (CASE WHEN (players.health = 0) THEN 0 ELSE 1 END)::boolean AS alive, players.days FROM ((player_rank JOIN players ON ((players.player_id = player_rank._player_id))) JOIN class ON ((class.class_id = players._class_id))) WHERE (players.active = 1) ORDER BY player_rank.rank_id;



--
-- Name: settings; Type: TABLE; Schema: public; Owner: developers; Tablespace: 
--

CREATE TABLE settings (
    setting_id integer NOT NULL,
    player_id integer NOT NULL,
    settings_store text
);



--
-- Name: settings_setting_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE settings_setting_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;



--
-- Name: settings_setting_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: developers
--

ALTER SEQUENCE settings_setting_id_seq OWNED BY settings.setting_id;


--
-- Name: skill; Type: TABLE; Schema: public; Owner: developers; Tablespace: 
--

CREATE TABLE skill (
    skill_id integer NOT NULL,
    skill_level integer DEFAULT 1 NOT NULL,
    skill_is_active boolean DEFAULT true,
    skill_display_name text NOT NULL,
    skill_internal_name text NOT NULL,
    skill_type skill_type NOT NULL
);



--
-- Name: skill_skill_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE skill_skill_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;



--
-- Name: skill_skill_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: developers
--

ALTER SEQUENCE skill_skill_id_seq OWNED BY skill.skill_id;


--
-- Name: test; Type: TABLE; Schema: public; Owner: kzqai; Tablespace: 
--

CREATE TABLE test (
    id text DEFAULT encode(gen_random_bytes(32), 'hex'::text) NOT NULL,
    value text
);



--
-- Name: time; Type: TABLE; Schema: public; Owner: developers; Tablespace: 
--

CREATE TABLE "time" (
    time_id integer NOT NULL,
    time_label character varying NOT NULL,
    amount integer NOT NULL
);



--
-- Name: time_time_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE time_time_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;



--
-- Name: time_time_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: developers
--

ALTER SEQUENCE time_time_id_seq OWNED BY "time".time_id;


--
-- Name: account_id; Type: DEFAULT; Schema: public; Owner: developers
--

ALTER TABLE ONLY accounts ALTER COLUMN account_id SET DEFAULT nextval('accounts_account_id_seq'::regclass);


--
-- Name: chat_id; Type: DEFAULT; Schema: public; Owner: developers
--

ALTER TABLE ONLY chat ALTER COLUMN chat_id SET DEFAULT nextval('chat_chat_id_seq'::regclass);


--
-- Name: clan_id; Type: DEFAULT; Schema: public; Owner: developers
--

ALTER TABLE ONLY clan ALTER COLUMN clan_id SET DEFAULT nextval('clan_clan_id_seq'::regclass);


--
-- Name: class_id; Type: DEFAULT; Schema: public; Owner: developers
--

ALTER TABLE ONLY class ALTER COLUMN class_id SET DEFAULT nextval('class_class_id_seq'::regclass);


--
-- Name: effect_id; Type: DEFAULT; Schema: public; Owner: developers
--

ALTER TABLE ONLY effects ALTER COLUMN effect_id SET DEFAULT nextval('effects_effect_id_seq'::regclass);


--
-- Name: event_id; Type: DEFAULT; Schema: public; Owner: developers
--

ALTER TABLE ONLY events ALTER COLUMN event_id SET DEFAULT nextval('events_event_id_seq'::regclass);


--
-- Name: item_id; Type: DEFAULT; Schema: public; Owner: developers
--

ALTER TABLE ONLY item ALTER COLUMN item_id SET DEFAULT nextval('item_item_id_seq'::regclass);


--
-- Name: attempt_id; Type: DEFAULT; Schema: public; Owner: kzqai
--

ALTER TABLE ONLY login_attempts ALTER COLUMN attempt_id SET DEFAULT nextval('login_attempts_attempt_id_seq'::regclass);


--
-- Name: message_id; Type: DEFAULT; Schema: public; Owner: developers
--

ALTER TABLE ONLY messages ALTER COLUMN message_id SET DEFAULT nextval('messages_message_id_seq'::regclass);


--
-- Name: setting_id; Type: DEFAULT; Schema: public; Owner: developers
--

ALTER TABLE ONLY settings ALTER COLUMN setting_id SET DEFAULT nextval('settings_setting_id_seq'::regclass);


--
-- Name: skill_id; Type: DEFAULT; Schema: public; Owner: developers
--

ALTER TABLE ONLY skill ALTER COLUMN skill_id SET DEFAULT nextval('skill_skill_id_seq'::regclass);


--
-- Name: time_id; Type: DEFAULT; Schema: public; Owner: developers
--

ALTER TABLE ONLY "time" ALTER COLUMN time_id SET DEFAULT nextval('time_time_id_seq'::regclass);


--
-- Data for Name: account_players; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY account_players (_account_id, _player_id, last_login, created_date) FROM stdin;
5442	1000	2013-05-10 18:07:54.362302	2013-05-10 18:07:54.362302
\.


--
-- Data for Name: accounts; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY accounts (account_id, account_identity, phash, active_email, type, operational, created_date, last_login, last_login_failure, karma_total, last_ip, confirmed, verification_number) FROM stdin;
5442	tchalvakspam@gmail.com	$2a$08$y/vyPiyTwR23tPQcLm4gKuqtvjbk/oepXjhQXGCgwCMxSesdEnkZW	tchalvakspam@gmail.com	0	t	2009-11-03 12:04:53.223615	2013-05-20 12:57:26.758901	2013-05-20 12:57:21.901384	7	127.0.0.1	1	14141
\.


--
-- Name: accounts_account_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('accounts_account_id_seq', 24904, true);


--
-- Name: clan_clan_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('clan_clan_id_seq', 225, true);


--
-- Data for Name: class; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY class (class_id, class_name, class_active, class_note, class_tier, class_desc, class_icon, theme, identity) FROM stdin;
1	Viper	t	Poison	1	\N	\N	Black	viper
2	Crane	t	Speed	1	\N	\N	Blue	crane
4	Dragon	t	Healing	1	\N	\N	White	dragon
3	Tiger	t	Strength	1	\N	\N	Red	tiger
5	Mantis	f	Smoke	1	\N	\N	Gray	mantis
\.


--
-- Name: class_class_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('class_class_id_seq', 6, true);


--
-- Data for Name: class_skill; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY class_skill (_class_id, _skill_id, class_skill_level) FROM stdin;
1	9	\N
1	10	\N
2	1	\N
2	3	\N
4	4	\N
3	6	\N
3	7	\N
4	17	\N
2	15	\N
4	16	\N
\.

--
-- Name: dueling_log_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('dueling_log_id_seq', 1266177, true);

-- Data for Name: effects; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY effects (effect_id, effect_identity, effect_name, effect_verb, effect_self) FROM stdin;
1	wound	Wound	Wounds	f
2	fire	Fire	Burns	f
3	ice	Ice	Freezes	f
4	shock	Shock	Shocks	f
5	acid	Acid	Dissolves	f
6	void	Void	Taints	f
7	flare	Flare	Blinds	f
8	poison	Poison	Poisons	f
9	paralysis	Paralysis	Paralyzes	f
10	slice	Slice	Slices	f
11	bash	Bash	Bashes	f
12	pierce	Pierce	Pierces	f
13	slow	Slow	Slows down	f
14	speed	Speed	Speeds up	t
15	stealth	Stealthed	Hides	t
16	vigor	Vigor	Energizes	t
17	strength	Strength	Strengthens	t
18	weaken	Weaken	Weakens	f
19	heal	Heal	Heals	t
20	healing	Healing	Healed	t
21	regen	Regenerate	Regenerating	t
22	death	Death	Dying	f
\.


--
-- Name: effects_effect_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('effects_effect_id_seq', 22, true);


--
-- Name: events_event_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('events_event_id_seq', 7059596, true);


--
-- Data for Name: flags; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY flags (flag_id, flag, flag_type) FROM stdin;
1	bugabuse	2
2	multiplaying	3
3	spamming	4
4	paused	10
5	moderator	21
6	bugfinder	22
\.


--
-- Name: flags_flag_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('flags_flag_id_seq', 1, false);


-- Name: inventory_item_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('inventory_item_id_seq', 49240, true);


--
-- Data for Name: item; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY item (item_id, item_internal_name, item_display_name, item_cost, image, for_sale, usage, ignore_stealth, covert, turn_cost, target_damage, turn_change, self_use, plural, other_usable, traits) FROM stdin;
9	charcoal	Charcoal	10	\N	f	Purges Poisons, Burns Merrily	t	t	\N	20	\N	t	\N	f	\N
10	sake	Sake	30	\N	f	Warms the Soul	t	t	\N	1	\N	t	\N	f	\N
11	mirror	Mirror Shard	120	\N	f	Reflects Light	t	t	\N	5	\N	t	\N	f	\N
12	shell	Shell Fragment	700	\N	f	Insulates against Flame	t	t	\N	0	\N	t	\N	f	\N
13	prayerwheel	Prayer Wheel	150	\N	f	Lifts Curses	t	t	\N	0	\N	t	\N	f	\N
15	sushi	Sushi	50	\N	f	For immediate consumption	t	t	\N	20	\N	t	\N	f	\N
16	fugu	Fugu Blowfish	50	\N	f	Delicious, or Deadly	t	t	\N	20	\N	t	\N	f	\N
17	oyoroi	O-yoroi Great Armor	3000	\N	f	Woven Armor with a Metal Breastplate against piercing and slashing	t	t	\N	3000	\N	t	s	f	\N
18	kozando	Kozan-do Scale Armor	1600	\N	f	Scale Plated Armor against slashing	t	t	\N	0	\N	t	s	f	\N
19	domaru	Do-Maru Woven Armor	1000	\N	f	Woven Armor against piercing and slashing	t	t	\N	0	\N	t	s	f	\N
20	tanko	Tanko Scale Armor	900	\N	f	Lamellate Armor against Crushing Blows	t	t	\N	0	\N	t	s	f	\N
21	tatamido	Tatami-do Folding Armor	1500	\N	f	Laced Squares of Flexible Leather for easy movement	t	t	\N	0	\N	t	s	f	\N
22	keikogi	Keiko-Gi Suit	70	\N	f	Thick Cloth Uniform for unfettered movement	t	t	\N	0	\N	t	s	f	\N
24	hakama	Hakama Garb	30	\N	f	Pleated, Loose Pants and Shirt for unfettered movement	t	t	\N	0	\N	t	s	f	\N
25	mask	Menpo Mask	600	\N	f	For Disguise or Intimidation	t	t	\N	0	\N	t	s	f	\N
27	meito	Meito Named Katana	3000	\N	f	Folded-Steel Named Sword for Slashing	t	f	\N	3000	\N	t	\N	f	\N
28	naginata	Naginata Spear	750	\N	f	Long Reached, Curved Spear for Piercing and Slashing	f	f	\N	750	\N	t	s	f	\N
30	kusarigama	Kusarigama Chain Sickle	500	\N	f	For Swinging Slashes and Entanglement	t	f	\N	500	\N	t	s	f	\N
32	tetsubo	Tetsubo Club	140	\N	f	For Piercing, Crushing Blows	f	f	\N	140	\N	t	\N	f	\N
33	nunchaku	Nunchaku	180	\N	f	Thrashing Blows with a long reach	t	f	\N	180	\N	t	\N	f	\N
34	zanbato	Zanbato Long Sword	660	\N	f	For Heavy Slashing Blows with a long reach	t	f	\N	660	\N	t	\N	f	\N
35	eku	Eku Wooden Oar	30	\N	f	For Slow, Wide-Arcing Blows	t	f	\N	130	\N	t	s	f	\N
36	ono	Ono Axe	10	\N	f	Great for Beheadings and De-limbing	f	f	\N	110	\N	t	s	f	\N
37	nekote	Neko-Te Claws	450	\N	f	For Poisoned slashing or climbing	f	t	\N	30	\N	t	\N	f	\N
23	kimono	Kimono	170	\N	f	Light Silk Clothing for formal wear	t	t	\N	0	\N	t	s	f	\N
26	katana	Katana	1800	\N	f	Crafted Sword for Slashing	f	f	\N	1800	\N	t	\N	f	\N
31	kama	Kama Sickle	55	\N	f	For Reaping Rice	f	f	\N	55	\N	t	\N	f	\N
38	hamagari	Hamagari Saw	77	\N	f	For Poisoned slashing or climbing	f	t	\N	177	\N	t	s	f	\N
39	bo	Bo Staff	70	\N	f	For Ease of Walking	t	t	\N	170	\N	t	s	f	\N
14	lantern	Hooded Lantern	50	\N	f	A lantern for light and flame	t	t	\N	20	\N	t	\N	t	\N
5	shuriken	Shuriken	50	mini_star.png	t	Reduces health	f	f	\N	\N	\N	f	\N	t	\N
3	amanita	Amanita Mushroom	225	mushroom.png	t	Increases Turns	t	t	\N	\N	6	t	s	t	\N
2	caltrops	Caltrops	125	caltrops.png	t	Reduces Turns	f	f	\N	\N	-6	f	\N	t	\N
4	smokebomb	Smoke Bomb	150	smoke_bomb.gif	t	Stealths a Ninja	f	f	\N	\N	\N	t	s	t	\N
6	dimmak	Dim Mak	1000	scroll.png	f	\N	t	t	\N	\N	\N	f	\N	t	\N
1	phosphor	Phosphor Powder	175	\N	t	Reduces health	f	f	\N	\N	\N	f	s	t	\N
7	ginsengroot	Ginseng Root	1000	\N	f	\N	t	t	\N	\N	\N	t	s	t	\N
8	tigersalve	Tiger Salve	3000	\N	f	\N	t	t	\N	\N	\N	t	s	t	\N
40	tessen	Tessen Fan	150	\N	t	For Cooling Air	f	t	\N	20	\N	t	s	t	\N
29	kunai	Kunai	50	\N	t	For Digging and Planting	t	t	\N	50	\N	t	\N	t	\N
\.


--
-- Data for Name: item_effects; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY item_effects (_item_id, _effect_id) FROM stdin;
1	2
1	7
1	1
2	13
2	12
2	1
7	16
8	17
3	14
6	22
6	1
5	10
5	1
4	15
29	1
29	12
40	1
\.


--
-- Name: item_item_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('item_item_id_seq', 40, true);


--
-- Name: levelling_log_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('levelling_log_id_seq', 4168545, true);


--
-- Name: messages_message_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('messages_message_id_seq', 336595, true);


--
-- Data for Name: past_stats; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY past_stats (id, stat_type, stat_result) FROM stdin;
2	Most Kills Last Month	0
3	Total Kills Last Month	0
5	Previous Month's Vicious Killer	no-one
6	Total Kills Yesterday	0
1	Most Kills Yesterday	0
4	Yesterday's Vicious Killer	lightning_dust
\.


--
-- Name: past_stats_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('past_stats_id_seq', 1, false);


--
-- Data for Name: player_rank; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY player_rank (rank_id, _player_id, score) FROM stdin;
1	5442	9999999
\.


--
-- Name: player_rank_rank_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('player_rank_rank_id_seq', 1, false);


--
-- Name: player_rank_rank_id_seq1; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('player_rank_rank_id_seq1', 921, true);


--
-- Data for Name: players; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY players (player_id, uname, pname_backup, health, strength, gold, messages, kills, turns, verification_number, active, email, level, status, member, days, ip, bounty, created_date, resurrection_time, last_started_attack, energy, avatar_type, _class_id, ki, stamina, speed, karma, kills_gained, kills_used) FROM stdin;
1000	Tchalvak	foobar	3896171	75	40634	I am staff!  It's true, check the staff page.\r\n\r\nEmail me there if you want a prompt response.  Or the official email, ninjawarslivebythesword@gmail.com\r\n\r\nIn the depths of my black little coal of a heart, there is a dying sun that burns on the fuel of my hatred for you.	72	33332247	1414	1	tchalvakspam@gmail.com	15	131074	0	0	127.0.0.1	5000	2009-11-03 12:04:53.223615	12	2013-05-17 12:39:56.863946	1000	1	1	463	75	75	23	126	105
\.


--
-- Data for Name: players_flagged; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY players_flagged (players_flagged_id, player_id, flag_id, "timestamp", originating_page, extra_notes) FROM stdin;
\.


--
-- Name: players_flagged_players_flagged_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('players_flagged_players_flagged_id_seq', 1, false);


--
-- Name: players_player_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('players_player_id_seq', 166507, true);



--
-- Data for Name: skill; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY skill (skill_id, skill_level, skill_is_active, skill_display_name, skill_internal_name, skill_type) FROM stdin;
1	1	t	Ice Bolt	ice	targeted
2	6	t	Cold Steal	coldsteal	targeted
3	1	t	Speed	speed	passive
4	1	t	Chi	chi	passive
6	1	t	Fire Bolt	fire	targeted
7	1	t	Blaze	blaze	combat
8	2	t	Deflect	deflect	combat
9	1	t	Poison Touch	poison	targeted
10	1	t	Hidden Resurrect	stealthres	passive
11	1	t	Sight	sight	targeted
12	1	t	Stealth	stealth	self-only
13	1	t	Unstealth	unstealth	self-only
14	2	t	Steal	steal	targeted
15	2	t	Kampo	kampo	self-only
16	2	t	Evasion	evasion	combat
5	20	t	Midnight Heal	midnightheal	passive
17	1	t	Heal	heal	targeted
\.


--
-- Name: skill_skill_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('skill_skill_id_seq', 16, true);


--
-- Data for Name: test; Type: TABLE DATA; Schema: public; Owner: kzqai
--

COPY test (id, value) FROM stdin;
1f9b35a4f87292a8888b2c68d7de86302d433419bd979afdf023358dbffe884e	scoobydoo
\.


--
-- Data for Name: time; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY "time" (time_id, time_label, amount) FROM stdin;
1	hours	6
\.


--
-- Name: time_time_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('time_time_id_seq', 1, true);


--
-- Name: account_players__player_id_key; Type: CONSTRAINT; Schema: public; Owner: developers; Tablespace: 
--

ALTER TABLE ONLY account_players
    ADD CONSTRAINT account_players__player_id_key UNIQUE (_player_id);


--
-- Name: account_players__player_id_key1; Type: CONSTRAINT; Schema: public; Owner: developers; Tablespace: 
--

ALTER TABLE ONLY account_players
    ADD CONSTRAINT account_players__player_id_key1 UNIQUE (_player_id);


--
-- Name: accounts_account_identity_key; Type: CONSTRAINT; Schema: public; Owner: developers; Tablespace: 
--

ALTER TABLE ONLY accounts
    ADD CONSTRAINT accounts_account_identity_key UNIQUE (account_identity);


--
-- Name: accounts_active_email_key; Type: CONSTRAINT; Schema: public; Owner: developers; Tablespace: 
--

ALTER TABLE ONLY accounts
    ADD CONSTRAINT accounts_active_email_key UNIQUE (active_email);


--
-- Name: accounts_pkey; Type: CONSTRAINT; Schema: public; Owner: developers; Tablespace: 
--

ALTER TABLE ONLY accounts
    ADD CONSTRAINT accounts_pkey PRIMARY KEY (account_id);


--
-- Name: clan_clan_name_key; Type: CONSTRAINT; Schema: public; Owner: developers; Tablespace: 
--

ALTER TABLE ONLY clan
    ADD CONSTRAINT clan_clan_name_key UNIQUE (clan_name);


--
-- Name: clan_pkey; Type: CONSTRAINT; Schema: public; Owner: developers; Tablespace: 
--

ALTER TABLE ONLY clan
    ADD CONSTRAINT clan_pkey PRIMARY KEY (clan_id);


--
-- Name: clan_player__player_id_key; Type: CONSTRAINT; Schema: public; Owner: developers; Tablespace: 
--

ALTER TABLE ONLY clan_player
    ADD CONSTRAINT clan_player__player_id_key UNIQUE (_player_id);


--
-- Name: clan_player__player_id_key1; Type: CONSTRAINT; Schema: public; Owner: developers; Tablespace: 
--

ALTER TABLE ONLY clan_player
    ADD CONSTRAINT clan_player__player_id_key1 UNIQUE (_player_id);


--
-- Name: class_class_name_key; Type: CONSTRAINT; Schema: public; Owner: developers; Tablespace: 
--

ALTER TABLE ONLY class
    ADD CONSTRAINT class_class_name_key UNIQUE (class_name);


--
-- Name: class_pkey; Type: CONSTRAINT; Schema: public; Owner: developers; Tablespace: 
--

ALTER TABLE ONLY class
    ADD CONSTRAINT class_pkey PRIMARY KEY (class_id);


--
-- Name: class_skill_pkey; Type: CONSTRAINT; Schema: public; Owner: developers; Tablespace: 
--

ALTER TABLE ONLY class_skill
    ADD CONSTRAINT class_skill_pkey PRIMARY KEY (_class_id, _skill_id);


--
-- Name: dueling_log_pkey; Type: CONSTRAINT; Schema: public; Owner: developers; Tablespace: 
--

ALTER TABLE ONLY dueling_log
    ADD CONSTRAINT dueling_log_pkey PRIMARY KEY (id);


--
-- Name: duped_unames_pkey; Type: CONSTRAINT; Schema: public; Owner: developers; Tablespace: 
--

ALTER TABLE ONLY duped_unames
    ADD CONSTRAINT duped_unames_pkey PRIMARY KEY (player_id);


--
-- Name: effects_effect_identity_key; Type: CONSTRAINT; Schema: public; Owner: developers; Tablespace: 
--

ALTER TABLE ONLY effects
    ADD CONSTRAINT effects_effect_identity_key UNIQUE (effect_identity);


--
-- Name: effects_effect_name_key; Type: CONSTRAINT; Schema: public; Owner: developers; Tablespace: 
--

ALTER TABLE ONLY effects
    ADD CONSTRAINT effects_effect_name_key UNIQUE (effect_name);


--
-- Name: effects_pkey; Type: CONSTRAINT; Schema: public; Owner: developers; Tablespace: 
--

ALTER TABLE ONLY effects
    ADD CONSTRAINT effects_pkey PRIMARY KEY (effect_id);


--
-- Name: enemies_pkey; Type: CONSTRAINT; Schema: public; Owner: developers; Tablespace: 
--

ALTER TABLE ONLY enemies
    ADD CONSTRAINT enemies_pkey PRIMARY KEY (_player_id, _enemy_id);


--
-- Name: flags_flag_key; Type: CONSTRAINT; Schema: public; Owner: developers; Tablespace: 
--

ALTER TABLE ONLY flags
    ADD CONSTRAINT flags_flag_key UNIQUE (flag);


--
-- Name: flags_pkey; Type: CONSTRAINT; Schema: public; Owner: developers; Tablespace: 
--

ALTER TABLE ONLY flags
    ADD CONSTRAINT flags_pkey PRIMARY KEY (flag_id);


--
-- Name: inventory_pkey; Type: CONSTRAINT; Schema: public; Owner: developers; Tablespace: 
--

ALTER TABLE ONLY inventory
    ADD CONSTRAINT inventory_pkey PRIMARY KEY (item_id);


--
-- Name: item_effects_pkey; Type: CONSTRAINT; Schema: public; Owner: developers; Tablespace: 
--

ALTER TABLE ONLY item_effects
    ADD CONSTRAINT item_effects_pkey PRIMARY KEY (_item_id, _effect_id);


--
-- Name: item_item_display_name_key; Type: CONSTRAINT; Schema: public; Owner: developers; Tablespace: 
--

ALTER TABLE ONLY item
    ADD CONSTRAINT item_item_display_name_key UNIQUE (item_display_name);


--
-- Name: item_item_internal_name_key; Type: CONSTRAINT; Schema: public; Owner: developers; Tablespace: 
--

ALTER TABLE ONLY item
    ADD CONSTRAINT item_item_internal_name_key UNIQUE (item_internal_name);


--
-- Name: item_pkey; Type: CONSTRAINT; Schema: public; Owner: developers; Tablespace: 
--

ALTER TABLE ONLY item
    ADD CONSTRAINT item_pkey PRIMARY KEY (item_id);


--
-- Name: levelling_log_pkey; Type: CONSTRAINT; Schema: public; Owner: developers; Tablespace: 
--

ALTER TABLE ONLY levelling_log
    ADD CONSTRAINT levelling_log_pkey PRIMARY KEY (id);


--
-- Name: past_stats_pkey; Type: CONSTRAINT; Schema: public; Owner: developers; Tablespace: 
--

ALTER TABLE ONLY past_stats
    ADD CONSTRAINT past_stats_pkey PRIMARY KEY (id);


--
-- Name: players_flagged_pkey; Type: CONSTRAINT; Schema: public; Owner: developers; Tablespace: 
--

ALTER TABLE ONLY players_flagged
    ADD CONSTRAINT players_flagged_pkey PRIMARY KEY (players_flagged_id);


--
-- Name: players_pkey; Type: CONSTRAINT; Schema: public; Owner: developers; Tablespace: 
--

ALTER TABLE ONLY players
    ADD CONSTRAINT players_pkey PRIMARY KEY (player_id);


--
-- Name: players_uname_key; Type: CONSTRAINT; Schema: public; Owner: developers; Tablespace: 
--

ALTER TABLE ONLY players
    ADD CONSTRAINT players_uname_key UNIQUE (uname);


--
-- Name: ppl_online_pkey; Type: CONSTRAINT; Schema: public; Owner: developers; Tablespace: 
--

ALTER TABLE ONLY ppl_online
    ADD CONSTRAINT ppl_online_pkey PRIMARY KEY (session_id);


--
-- Name: skill_pkey; Type: CONSTRAINT; Schema: public; Owner: developers; Tablespace: 
--

ALTER TABLE ONLY skill
    ADD CONSTRAINT skill_pkey PRIMARY KEY (skill_id);


--
-- Name: skill_skill_display_name_key; Type: CONSTRAINT; Schema: public; Owner: developers; Tablespace: 
--

ALTER TABLE ONLY skill
    ADD CONSTRAINT skill_skill_display_name_key UNIQUE (skill_display_name);


--
-- Name: skill_skill_internal_name_key; Type: CONSTRAINT; Schema: public; Owner: developers; Tablespace: 
--

ALTER TABLE ONLY skill
    ADD CONSTRAINT skill_skill_internal_name_key UNIQUE (skill_internal_name);


--
-- Name: test_pkey; Type: CONSTRAINT; Schema: public; Owner: kzqai; Tablespace: 
--

ALTER TABLE ONLY test
    ADD CONSTRAINT test_pkey PRIMARY KEY (id);


--
-- Name: chat_date_index; Type: INDEX; Schema: public; Owner: developers; Tablespace: 
--

CREATE INDEX chat_date_index ON chat USING btree (date);


--
-- Name: dueling_log_date_index; Type: INDEX; Schema: public; Owner: developers; Tablespace: 
--

CREATE INDEX dueling_log_date_index ON dueling_log USING btree (date);


--
-- Name: duped_unames_email_key; Type: INDEX; Schema: public; Owner: developers; Tablespace: 
--

CREATE INDEX duped_unames_email_key ON duped_unames USING btree (email);


--
-- Name: duped_unames_uname_key; Type: INDEX; Schema: public; Owner: developers; Tablespace: 
--

CREATE INDEX duped_unames_uname_key ON duped_unames USING btree (uname);


--
-- Name: events_date_index; Type: INDEX; Schema: public; Owner: developers; Tablespace: 
--

CREATE INDEX events_date_index ON events USING btree (date);


--
-- Name: messages_date_index; Type: INDEX; Schema: public; Owner: developers; Tablespace: 
--

CREATE INDEX messages_date_index ON messages USING btree (date);


--
-- Name: account_players__account_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY account_players
    ADD CONSTRAINT account_players__account_id_fkey FOREIGN KEY (_account_id) REFERENCES accounts(account_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: account_players__player_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY account_players
    ADD CONSTRAINT account_players__player_id_fkey FOREIGN KEY (_player_id) REFERENCES players(player_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: clan_player__clan_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY clan_player
    ADD CONSTRAINT clan_player__clan_id_fkey FOREIGN KEY (_clan_id) REFERENCES clan(clan_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: clan_player__player_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY clan_player
    ADD CONSTRAINT clan_player__player_id_fkey FOREIGN KEY (_player_id) REFERENCES players(player_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: class_skill__class_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY class_skill
    ADD CONSTRAINT class_skill__class_id_fkey FOREIGN KEY (_class_id) REFERENCES class(class_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: class_skill__skill_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY class_skill
    ADD CONSTRAINT class_skill__skill_id_fkey FOREIGN KEY (_skill_id) REFERENCES skill(skill_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: effects_effect_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY item_effects
    ADD CONSTRAINT effects_effect_id_fkey FOREIGN KEY (_effect_id) REFERENCES effects(effect_id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: enemies__enemy_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY enemies
    ADD CONSTRAINT enemies__enemy_id_fkey FOREIGN KEY (_enemy_id) REFERENCES players(player_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: enemies__player_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY enemies
    ADD CONSTRAINT enemies__player_id_fkey FOREIGN KEY (_player_id) REFERENCES players(player_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: inventory_owner_fkey; Type: FK CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY inventory
    ADD CONSTRAINT inventory_owner_fkey FOREIGN KEY (owner) REFERENCES players(player_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: item_item_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY item_effects
    ADD CONSTRAINT item_item_id_fkey FOREIGN KEY (_item_id) REFERENCES item(item_id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: levelling_log__player_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY levelling_log
    ADD CONSTRAINT levelling_log__player_id_fkey FOREIGN KEY (_player_id) REFERENCES players(player_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: messages_send_from_fkey; Type: FK CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY messages
    ADD CONSTRAINT messages_send_from_fkey FOREIGN KEY (send_from) REFERENCES players(player_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: messages_send_to_fkey; Type: FK CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY messages
    ADD CONSTRAINT messages_send_to_fkey FOREIGN KEY (send_to) REFERENCES players(player_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: players__class_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: developers
--

ALTER TABLE ONLY players
    ADD CONSTRAINT players__class_id_fkey FOREIGN KEY (_class_id) REFERENCES class(class_id) ON UPDATE CASCADE;


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- Name: chat; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON TABLE chat FROM PUBLIC;


--
-- Name: chat_id_seq; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON SEQUENCE chat_id_seq FROM PUBLIC;


--
-- Name: clan; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON TABLE clan FROM PUBLIC;


--
-- Name: clan_player; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON TABLE clan_player FROM PUBLIC;


--
-- Name: class; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON TABLE class FROM PUBLIC;


--
-- Name: dueling_log_id_seq; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON SEQUENCE dueling_log_id_seq FROM PUBLIC;


--
-- Name: dueling_log; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON TABLE dueling_log FROM PUBLIC;


--
-- Name: effects; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON TABLE effects FROM PUBLIC;


--
-- Name: enemies; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON TABLE enemies FROM PUBLIC;


--
-- Name: events; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON TABLE events FROM PUBLIC;


--
-- Name: flags_flag_id_seq; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON SEQUENCE flags_flag_id_seq FROM PUBLIC;


--
-- Name: flags; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON TABLE flags FROM PUBLIC;


--
-- Name: inventory_item_id_seq; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON SEQUENCE inventory_item_id_seq FROM PUBLIC;


--
-- Name: inventory; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON TABLE inventory FROM PUBLIC;


--
-- Name: item; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON TABLE item FROM PUBLIC;


--
-- Name: item_effects; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON TABLE item_effects FROM PUBLIC;


--
-- Name: levelling_log_id_seq; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON SEQUENCE levelling_log_id_seq FROM PUBLIC;


--
-- Name: levelling_log; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON TABLE levelling_log FROM PUBLIC;


--
-- Name: login_attempts; Type: ACL; Schema: public; Owner: kzqai
--

REVOKE ALL ON TABLE login_attempts FROM PUBLIC;


--
-- Name: login_attempts_attempt_id_seq; Type: ACL; Schema: public; Owner: kzqai
--

REVOKE ALL ON SEQUENCE login_attempts_attempt_id_seq FROM PUBLIC;


--
-- Name: messages; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON TABLE messages FROM PUBLIC;


--
-- Name: past_stats_id_seq; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON SEQUENCE past_stats_id_seq FROM PUBLIC;


--
-- Name: past_stats; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON TABLE past_stats FROM PUBLIC;


--
-- Name: player_rank; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON TABLE player_rank FROM PUBLIC;


--
-- Name: player_rank_rank_id_seq; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON SEQUENCE player_rank_rank_id_seq FROM PUBLIC;


--
-- Name: players_player_id_seq; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON SEQUENCE players_player_id_seq FROM PUBLIC;


--
-- Name: players; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON TABLE players FROM PUBLIC;


--
-- Name: players_flagged_players_flagged_id_seq; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON SEQUENCE players_flagged_players_flagged_id_seq FROM PUBLIC;


--
-- Name: players_flagged; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON TABLE players_flagged FROM PUBLIC;


--
-- Name: ppl_online; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON TABLE ppl_online FROM PUBLIC;


--
-- Name: rankings; Type: ACL; Schema: public; Owner: kzqai
--

REVOKE ALL ON TABLE rankings FROM PUBLIC;


--
-- Name: settings; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON TABLE settings FROM PUBLIC;


--
-- Name: time; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON TABLE "time" FROM PUBLIC;


--
-- PostgreSQL database dump complete
--