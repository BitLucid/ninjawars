DROP TABLE accounts;
CREATE TABLE accounts (
    account_id serial NOT NULL,
    _player_id integer NOT NULL, // Need to make this a foreign key.
    email varchar(255) NOT NULL,
    avatar_type integer NOT NULL DEFAULT 0,
    ip varchar(255) NOT NULL default ''::character varying,
    confirm integer NOT NULL default 0,
    confirmed integer NOT NULL default 0
);
ALTER TABLE accounts OWNER TO developers;
GRANT ALL ON TABLE accounts TO developers;
