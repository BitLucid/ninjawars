--
-- Name: rankings; Type: VIEW; Schema: public; Owner: developers
--

CREATE VIEW rankings AS
    SELECT player_rank.rank_id, players.player_id, player_rank.score, players.uname, class.class_name, players.level, (CASE WHEN (players.health = 0) THEN 0 ELSE 1 END)::boolean AS alive, players.days FROM ((player_rank JOIN players ON ((players.player_id = player_rank._player_id))) JOIN class ON ((class.class_id = players._class_id))) WHERE (players.active = 1) ORDER BY player_rank.rank_id;

ALTER VIEW rankings owner to developers;

alter table players alter column resurrection_time set default round(random() * (23)::double precision);

alter table players alter column created_date type timestamp with time zone;
alter table players alter column last_started_attack type timestamp with time zone;
alter table chat alter column date type timestamp with time zone;
alter table account_news alter column created_date type timestamp with time zone;
alter table account_players alter column created_date type timestamp with time zone;
alter table account_players alter column last_login type timestamp with time zone;
alter table accounts alter column created_date type timestamp with time zone;
alter table accounts alter column last_login type timestamp with time zone;
alter table accounts alter column last_login_failure type timestamp with time zone;
alter table clan alter column clan_created_date type timestamp with time zone;
alter table events alter column date type timestamp with time zone;
alter table login_attempts alter column attempt_date type timestamp with time zone;
alter table messages alter column date type timestamp with time zone;
alter table news alter column created type timestamp with time zone;
alter table news alter column updated type timestamp with time zone;
alter table password_reset_requests alter column created_at type timestamp with time zone;
alter table ppl_online alter column activity type timestamp with time zone;

#Have to custom alter these ones as well, created by schema.xml
alter table password_reset_requests alter column updated_at type timestamp with time zone;
alter table flags alter column created_at type timestamp with time zone;
alter table clan_player alter column created_at type timestamp with time zone;