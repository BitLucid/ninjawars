BEGIN TRANSACTION;
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

INSERT INTO accounts (account_identity, active_email, phash, type, last_login, created_date) SELECT email AS email, email AS active_email, crypt(pname, gen_salt('bf')) AS phash, 0, now() - 'internal '||days||' days', created_date FROM players;

INSERT INTO account_players (_account_id, _player_id, last_login, created_date) SELECT account_id, player_id, now() - 'internal '||days||' days', created_date FROM accounts join players ON account_identity = lower(email);

COMMIT;
