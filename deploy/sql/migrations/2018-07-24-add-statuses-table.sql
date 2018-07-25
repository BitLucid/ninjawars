-- Wildcard statuses entered at time of creation by various skills
CREATE TABLE public.statuses (
    status_id serial,
    name character varying(500) NOT NULL,
    expiry_datetime timestamp with time zone not null default CURRENT_TIMESTAMP,
    _player_id integer REFERENCES players(player_id) on delete cascade on update cascade,
    CONSTRAINT effect_per_player UNIQUE (name, _player_id)
);


ALTER TABLE public.statuses OWNER TO developers;