drop table if exists account_players;
drop table if exists accounts;

CREATE TABLE accounts (
    account_id serial PRIMARY KEY NOT NULL, 
    account_identity text NOT NULL UNIQUE, 
    phash text,
    active_email text NOT NULL UNIQUE,
    type integer default 0::integer,
    active boolean default true,
    created_date timestamp without time zone NOT NULL default now(),
    last_login timestamp without time zone
);

CREATE TABLE account_players (
    _account_id integer NOT NULL REFERENCES accounts(account_id) ON DELETE CASCADE ON UPDATE CASCADE,
    _player_id integer NOT NULL REFERENCES players(player_id) ON DELETE CASCADE ON UPDATE CASCADE,
    last_login timestamp without time zone NOT NULL default now(),
	created_date timestamp without time zone NOT NULL default now()
);

INSERT INTO accounts (account_identity, active_email, phash, type) SELECT email AS email, email AS active_email, crypt(pname, gen_salt('bf')) AS phash, 0 FROM players;

--TODO - Copy over created date, - copy over last_login from days
INSERT INTO account_players (_account_id, _player_id) SELECT account_id, player_id FROM accounts join players ON account_identity = lower(email);
