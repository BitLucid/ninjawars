--
-- PostgreSQL database dump
--

SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

--
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: postgres
--

COMMENT ON SCHEMA public IS 'Standard public schema';


SET search_path = public, pg_catalog;

--
-- Name: chat_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE chat_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.chat_id_seq OWNER TO developers;

--
-- Name: chat_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('chat_id_seq', 375505, true);


SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: chat; Type: TABLE; Schema: public; Owner: developers; Tablespace: 
--

CREATE TABLE chat (
    id integer DEFAULT nextval('chat_id_seq'::regclass) NOT NULL,
    send_from character varying(100) DEFAULT ''::character varying NOT NULL,
    send_to character varying(100) DEFAULT ''::character varying NOT NULL,
    "time" timestamp without time zone NOT NULL,
    message character varying(500) DEFAULT ''::character varying NOT NULL
);


ALTER TABLE public.chat OWNER TO developers;

--
-- Name: dueling_log_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE dueling_log_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.dueling_log_id_seq OWNER TO developers;

--
-- Name: dueling_log_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('dueling_log_id_seq', 529430, true);


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


ALTER TABLE public.dueling_log OWNER TO developers;

--
-- Name: flags_flag_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE flags_flag_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.flags_flag_id_seq OWNER TO developers;

--
-- Name: flags_flag_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('flags_flag_id_seq', 1, false);


--
-- Name: flags; Type: TABLE; Schema: public; Owner: developers; Tablespace: 
--

CREATE TABLE flags (
    flag_id integer DEFAULT nextval('flags_flag_id_seq'::regclass) NOT NULL,
    flag character varying(100) NOT NULL,
    flag_type integer NOT NULL
);


ALTER TABLE public.flags OWNER TO developers;

--
-- Name: inventory_item_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE inventory_item_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.inventory_item_id_seq OWNER TO developers;

--
-- Name: inventory_item_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('inventory_item_id_seq', 14244, true);


--
-- Name: inventory; Type: TABLE; Schema: public; Owner: developers; Tablespace: 
--

CREATE TABLE inventory (
    "owner" character varying(255) NOT NULL,
    item character varying(255) NOT NULL,
    item_id integer DEFAULT nextval('inventory_item_id_seq'::regclass) NOT NULL,
    amount integer DEFAULT 1
);


ALTER TABLE public.inventory OWNER TO developers;

--
-- Name: levelling_log_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE levelling_log_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.levelling_log_id_seq OWNER TO developers;

--
-- Name: levelling_log_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('levelling_log_id_seq', 1057694, true);


--
-- Name: levelling_log; Type: TABLE; Schema: public; Owner: developers; Tablespace: 
--

CREATE TABLE levelling_log (
    id integer DEFAULT nextval('levelling_log_id_seq'::regclass) NOT NULL,
    uname character varying(100) NOT NULL,
    killpoints integer DEFAULT 0 NOT NULL,
    levelling integer DEFAULT 0 NOT NULL,
    killsdate date NOT NULL
);


ALTER TABLE public.levelling_log OWNER TO developers;

--
-- Name: mail_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE mail_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.mail_id_seq OWNER TO developers;

--
-- Name: mail_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('mail_id_seq', 4210139, true);


--
-- Name: mail; Type: TABLE; Schema: public; Owner: developers; Tablespace: 
--

CREATE TABLE mail (
    id integer DEFAULT nextval('mail_id_seq'::regclass) NOT NULL,
    send_from character varying(255) DEFAULT ''::character varying NOT NULL,
    send_to character varying(255) DEFAULT ''::character varying NOT NULL,
    message character varying NOT NULL,
    date timestamp without time zone NOT NULL
);


ALTER TABLE public.mail OWNER TO developers;

--
-- Name: past_stats_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE past_stats_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.past_stats_id_seq OWNER TO developers;

--
-- Name: past_stats_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('past_stats_id_seq', 1, false);


--
-- Name: past_stats; Type: TABLE; Schema: public; Owner: developers; Tablespace: 
--

CREATE TABLE past_stats (
    id integer DEFAULT nextval('past_stats_id_seq'::regclass) NOT NULL,
    stat_type character varying(50) DEFAULT ''::character varying NOT NULL,
    stat_result character varying(50) DEFAULT ''::character varying NOT NULL
);


ALTER TABLE public.past_stats OWNER TO developers;

--
-- Name: player_rank_rank_id_seq1; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE player_rank_rank_id_seq1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.player_rank_rank_id_seq1 OWNER TO developers;

--
-- Name: player_rank_rank_id_seq1; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('player_rank_rank_id_seq1', 1066, true);


--
-- Name: player_rank; Type: TABLE; Schema: public; Owner: developers; Tablespace: 
--

CREATE TABLE player_rank (
    rank_id integer DEFAULT nextval('player_rank_rank_id_seq1'::regclass) NOT NULL,
    _player_id integer NOT NULL,
    score integer
);


ALTER TABLE public.player_rank OWNER TO developers;

--
-- Name: player_rank_rank_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE player_rank_rank_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;


ALTER TABLE public.player_rank_rank_id_seq OWNER TO developers;

--
-- Name: player_rank_rank_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('player_rank_rank_id_seq', 1, false);


--
-- Name: players_player_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE players_player_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.players_player_id_seq OWNER TO developers;

--
-- Name: players_player_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('players_player_id_seq', 113387, true);


--
-- Name: players; Type: TABLE; Schema: public; Owner: developers; Tablespace: 
--

CREATE TABLE players (
    player_id integer DEFAULT nextval('players_player_id_seq'::regclass) NOT NULL,
    uname character varying(100) NOT NULL,
    pname character varying(100) NOT NULL,
    health integer DEFAULT 0 NOT NULL,
    strength integer DEFAULT 0 NOT NULL,
    gold integer DEFAULT 0 NOT NULL,
    messages character varying(200) DEFAULT ''::character varying NOT NULL,
    kills integer DEFAULT 0 NOT NULL,
    turns integer DEFAULT 0 NOT NULL,
    confirm integer DEFAULT 0 NOT NULL,
    confirmed integer DEFAULT 0 NOT NULL,
    email character varying(100) DEFAULT ''::character varying NOT NULL,
    "class" character varying(100) DEFAULT ''::character varying NOT NULL,
    "level" integer DEFAULT 0 NOT NULL,
    status integer DEFAULT 0 NOT NULL,
    member integer DEFAULT 0 NOT NULL,
    days integer DEFAULT 0 NOT NULL,
    ip character varying(100) DEFAULT ''::character varying NOT NULL,
    bounty integer DEFAULT 0 NOT NULL,
    clan character varying(100),
    clan_long_name character varying(100),
    created_date timestamp without time zone DEFAULT now(),
    resurrection_time integer DEFAULT (round((random() * (7)::double precision)) * (3)::double precision) NOT NULL,
    last_started_attack timestamp without time zone DEFAULT now()
);


ALTER TABLE public.players OWNER TO developers;

--
-- Name: players_backup_player_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE players_backup_player_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.players_backup_player_id_seq OWNER TO developers;

--
-- Name: players_backup_player_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('players_backup_player_id_seq', 1, false);


--
-- Name: players_backup; Type: TABLE; Schema: public; Owner: developers; Tablespace: 
--

CREATE TABLE players_backup (
    player_id integer DEFAULT nextval('players_backup_player_id_seq'::regclass) NOT NULL,
    uname character varying(100) NOT NULL,
    pname character varying(100) NOT NULL,
    health integer DEFAULT 0 NOT NULL,
    strength integer DEFAULT 0 NOT NULL,
    gold integer DEFAULT 0 NOT NULL,
    messages character varying(200) DEFAULT ''::character varying NOT NULL,
    kills integer DEFAULT 0 NOT NULL,
    turns integer DEFAULT 0 NOT NULL,
    confirm integer DEFAULT 0 NOT NULL,
    confirmed integer DEFAULT 0 NOT NULL,
    email character varying(100) DEFAULT ''::character varying NOT NULL,
    "class" character varying(100) DEFAULT ''::character varying NOT NULL,
    "level" integer DEFAULT 0 NOT NULL,
    status integer DEFAULT 0 NOT NULL,
    member integer DEFAULT 0 NOT NULL,
    days integer DEFAULT 0 NOT NULL,
    ip character varying(100) DEFAULT ''::character varying NOT NULL,
    bounty integer DEFAULT 0 NOT NULL,
    clan character varying(100),
    clan_long_name character varying(100)
);


ALTER TABLE public.players_backup OWNER TO developers;

--
-- Name: players_flagged_players_flagged_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE players_flagged_players_flagged_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.players_flagged_players_flagged_id_seq OWNER TO developers;

--
-- Name: players_flagged_players_flagged_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('players_flagged_players_flagged_id_seq', 1, false);


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


ALTER TABLE public.players_flagged OWNER TO developers;

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


ALTER TABLE public.ppl_online OWNER TO developers;

--
-- Name: rankings; Type: VIEW; Schema: public; Owner: developers
--

CREATE VIEW rankings AS
    SELECT player_rank.rank_id, players.player_id, player_rank.score, players.uname, players."class", players."level", (CASE WHEN (players.health = 0) THEN 0 ELSE 1 END)::boolean AS alive, players.days, CASE WHEN ((players.clan)::text = ''::text) THEN '-'::character varying WHEN ((players.clan_long_name)::text <> ''::text) THEN players.clan_long_name ELSE players.clan END AS clan FROM (player_rank JOIN players ON ((players.player_id = player_rank._player_id))) WHERE (players.confirmed = 1) ORDER BY player_rank.rank_id;


ALTER TABLE public.rankings OWNER TO developers;

--
-- Name: time; Type: TABLE; Schema: public; Owner: developers; Tablespace: 
--

CREATE TABLE "time" (
    time_id integer NOT NULL,
    time_label character varying NOT NULL,
    amount integer NOT NULL
);


ALTER TABLE public."time" OWNER TO developers;

--
-- Name: time_time_id_seq; Type: SEQUENCE; Schema: public; Owner: developers
--

CREATE SEQUENCE time_time_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.time_time_id_seq OWNER TO developers;

--
-- Name: time_time_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: developers
--

ALTER SEQUENCE time_time_id_seq OWNED BY "time".time_id;


--
-- Name: time_time_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('time_time_id_seq', 1, true);


--
-- Name: time_id; Type: DEFAULT; Schema: public; Owner: developers
--

ALTER TABLE "time" ALTER COLUMN time_id SET DEFAULT nextval('time_time_id_seq'::regclass);



--
-- Data for Name: dueling_log; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY dueling_log (id, attacker, defender, won, killpoints, date) FROM stdin;
\.


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
-- Data for Name: mail; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY mail (id, send_from, send_to, message, date) FROM stdin;
\.


--
-- Data for Name: past_stats; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY past_stats (id, stat_type, stat_result) FROM stdin;
2	Most Kills Last Month	0
3	Total Kills Last Month	0
5	Previous Month's Vicious Killer	no-one
6	Total Kills Yesterday	0
1	Most Kills Yesterday	0
4	Yesterday's Vicious Killer	None
\.



--
-- Data for Name: players; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY players (player_id, uname, pname, health, strength, gold, messages, kills, turns, confirm, confirmed, email, "class", "level", status, member, days, ip, bounty, clan, clan_long_name, created_date, resurrection_time, last_started_attack) FROM stdin;
86984	exampleninja	exampleninja	175	6	180		0	100	3679	1	example@example.com	Blue	1	0	0	331		0			2007-12-02 00:24:12.858532	18	2008-10-11 17:55:37.192898
80178	example2	example2	175	6	180		0	100	9110	1	example2@example2.com	White	1	0	0	331		0			2007-12-02 00:24:12.858532	12	2008-10-11 17:55:37.192898
87798	example3	example3	175	6	180		0	100	4962	1	example3@example3.com	White	10	0	0	331		0			2007-12-02 00:24:12.858532	9	2008-10-11 17:55:37.192898
89452	example4	example4	0	6	2		2	100	1968	1	example4@example4.com	Black	100	0	0	331		0			2007-12-02 00:24:12.858532	15	2008-10-11 17:55:37.192898
\.

--
-- Data for Name: players_backup; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY players_backup (player_id, uname, pname, health, strength, gold, messages, kills, turns, confirm, confirmed, email, "class", "level", status, member, days, ip, bounty, clan, clan_long_name) FROM stdin;
\.


--
-- Data for Name: players_flagged; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY players_flagged (players_flagged_id, player_id, flag_id, "timestamp", originating_page, extra_notes) FROM stdin;
\.


--
-- Data for Name: ppl_online; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY ppl_online (session_id, activity, member, ip_address, refurl, user_agent) FROM stdin;
\.


--
-- Data for Name: time; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY "time" (time_id, time_label, amount) FROM stdin;
1	hours	15
\.


--
-- Name: chat_pkey; Type: CONSTRAINT; Schema: public; Owner: developers; Tablespace: 
--

ALTER TABLE ONLY chat
    ADD CONSTRAINT chat_pkey PRIMARY KEY (id);


--
-- Name: dueling_log_pkey; Type: CONSTRAINT; Schema: public; Owner: developers; Tablespace: 
--

ALTER TABLE ONLY dueling_log
    ADD CONSTRAINT dueling_log_pkey PRIMARY KEY (id);


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
-- Name: levelling_log_pkey; Type: CONSTRAINT; Schema: public; Owner: developers; Tablespace: 
--

ALTER TABLE ONLY levelling_log
    ADD CONSTRAINT levelling_log_pkey PRIMARY KEY (id);


--
-- Name: mail_pkey; Type: CONSTRAINT; Schema: public; Owner: developers; Tablespace: 
--

ALTER TABLE ONLY mail
    ADD CONSTRAINT mail_pkey PRIMARY KEY (id);


--
-- Name: past_stats_pkey; Type: CONSTRAINT; Schema: public; Owner: developers; Tablespace: 
--

ALTER TABLE ONLY past_stats
    ADD CONSTRAINT past_stats_pkey PRIMARY KEY (id);


--
-- Name: players_backup_pkey; Type: CONSTRAINT; Schema: public; Owner: developers; Tablespace: 
--

ALTER TABLE ONLY players_backup
    ADD CONSTRAINT players_backup_pkey PRIMARY KEY (player_id);


--
-- Name: players_backup_uname_key; Type: CONSTRAINT; Schema: public; Owner: developers; Tablespace: 
--

ALTER TABLE ONLY players_backup
    ADD CONSTRAINT players_backup_uname_key UNIQUE (uname);


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
-- Name: unique_name_to_item; Type: CONSTRAINT; Schema: public; Owner: developers; Tablespace: 
--

ALTER TABLE ONLY inventory
    ADD CONSTRAINT unique_name_to_item UNIQUE ("owner", item);


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- Name: chat_id_seq; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON SEQUENCE chat_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE chat_id_seq FROM developers;
GRANT ALL ON SEQUENCE chat_id_seq TO developers;


--
-- Name: chat; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON TABLE chat FROM PUBLIC;
REVOKE ALL ON TABLE chat FROM developers;
GRANT ALL ON TABLE chat TO developers;


--
-- Name: dueling_log_id_seq; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON SEQUENCE dueling_log_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE dueling_log_id_seq FROM developers;
GRANT ALL ON SEQUENCE dueling_log_id_seq TO developers;


--
-- Name: dueling_log; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON TABLE dueling_log FROM PUBLIC;
REVOKE ALL ON TABLE dueling_log FROM developers;
GRANT ALL ON TABLE dueling_log TO developers;


--
-- Name: flags_flag_id_seq; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON SEQUENCE flags_flag_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE flags_flag_id_seq FROM developers;
GRANT ALL ON SEQUENCE flags_flag_id_seq TO developers;


--
-- Name: flags; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON TABLE flags FROM PUBLIC;
REVOKE ALL ON TABLE flags FROM developers;
GRANT ALL ON TABLE flags TO developers;


--
-- Name: inventory_item_id_seq; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON SEQUENCE inventory_item_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE inventory_item_id_seq FROM developers;
GRANT ALL ON SEQUENCE inventory_item_id_seq TO developers;


--
-- Name: inventory; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON TABLE inventory FROM PUBLIC;
REVOKE ALL ON TABLE inventory FROM developers;
GRANT ALL ON TABLE inventory TO developers;


--
-- Name: levelling_log_id_seq; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON SEQUENCE levelling_log_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE levelling_log_id_seq FROM developers;
GRANT ALL ON SEQUENCE levelling_log_id_seq TO developers;


--
-- Name: levelling_log; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON TABLE levelling_log FROM PUBLIC;
REVOKE ALL ON TABLE levelling_log FROM developers;
GRANT ALL ON TABLE levelling_log TO developers;


--
-- Name: mail_id_seq; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON SEQUENCE mail_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE mail_id_seq FROM developers;
GRANT ALL ON SEQUENCE mail_id_seq TO developers;


--
-- Name: mail; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON TABLE mail FROM PUBLIC;
REVOKE ALL ON TABLE mail FROM developers;
GRANT ALL ON TABLE mail TO developers;


--
-- Name: past_stats_id_seq; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON SEQUENCE past_stats_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE past_stats_id_seq FROM developers;
GRANT ALL ON SEQUENCE past_stats_id_seq TO developers;


--
-- Name: past_stats; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON TABLE past_stats FROM PUBLIC;
REVOKE ALL ON TABLE past_stats FROM developers;
GRANT ALL ON TABLE past_stats TO developers;


--
-- Name: player_rank; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON TABLE player_rank FROM PUBLIC;
REVOKE ALL ON TABLE player_rank FROM developers;
GRANT ALL ON TABLE player_rank TO developers;


--
-- Name: player_rank_rank_id_seq; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON SEQUENCE player_rank_rank_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE player_rank_rank_id_seq FROM developers;
GRANT ALL ON SEQUENCE player_rank_rank_id_seq TO developers;


--
-- Name: players_player_id_seq; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON SEQUENCE players_player_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE players_player_id_seq FROM developers;
GRANT ALL ON SEQUENCE players_player_id_seq TO developers;


--
-- Name: players; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON TABLE players FROM PUBLIC;
REVOKE ALL ON TABLE players FROM developers;
GRANT ALL ON TABLE players TO developers;


--
-- Name: players_backup_player_id_seq; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON SEQUENCE players_backup_player_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE players_backup_player_id_seq FROM developers;
GRANT ALL ON SEQUENCE players_backup_player_id_seq TO developers;


--
-- Name: players_backup; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON TABLE players_backup FROM PUBLIC;
REVOKE ALL ON TABLE players_backup FROM developers;
GRANT ALL ON TABLE players_backup TO developers;


--
-- Name: players_flagged_players_flagged_id_seq; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON SEQUENCE players_flagged_players_flagged_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE players_flagged_players_flagged_id_seq FROM developers;
GRANT ALL ON SEQUENCE players_flagged_players_flagged_id_seq TO developers;


--
-- Name: players_flagged; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON TABLE players_flagged FROM PUBLIC;
REVOKE ALL ON TABLE players_flagged FROM developers;
GRANT ALL ON TABLE players_flagged TO developers;


--
-- Name: ppl_online; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON TABLE ppl_online FROM PUBLIC;
REVOKE ALL ON TABLE ppl_online FROM developers;
GRANT ALL ON TABLE ppl_online TO developers;


--
-- Name: rankings; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON TABLE rankings FROM PUBLIC;
REVOKE ALL ON TABLE rankings FROM developers;
GRANT ALL ON TABLE rankings TO developers;


--
-- Name: time; Type: ACL; Schema: public; Owner: developers
--

REVOKE ALL ON TABLE "time" FROM PUBLIC;
REVOKE ALL ON TABLE "time" FROM developers;
GRANT ALL ON TABLE "time" TO developers;


--
-- PostgreSQL database dump complete
--

