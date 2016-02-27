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

alter table password_reset_requests add column updated_at timestamp with time zone default now();
alter table flags add column created_at timestamp with time zone default now();
alter table clan_player add column created_at timestamp with time zone default now();
alter table levelling_log alter column killsdate set default now();


alter table players alter column resurrection_time set default round(random() * (23)::double precision);
alter table players_flagged rename column timestamp to date;

