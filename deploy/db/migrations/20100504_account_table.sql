drop table if exists account_players;
drop table if exists accounts;
create table accounts (
    account_id serial primary key not null, 
    account_identity text not null unique, 
    phash text,
    active_email text not null unique,
    type integer default 0::integer,
    active integer default 1::integer,
    created_date timestamp without time zone NOT NULL default now(),
    last_login timestamp without time zone
);
create table account_players (
    _account_id integer not null references accounts(account_id) ON DELETE CASCADE ON UPDATE CASCADE,
    _player_id integer not null references players(player_id) ON DELETE CASCADE ON UPDATE CASCADE,
    last_login timestamp without time zone NOT NULL default now()
);

-- Still a problem with duplicate accounts/identities here.
insert into accounts (account_identity, active_email, phash, type) select lower(email) as email, lower(email) as active_email, crypt(pname, gen_salt('bf')) as phash, 0 from players;
-- Just a simple hack to deal with lowercasing of emails.
insert into account_players (_account_id, _player_id) select account_id, player_id from accounts join players on account_identity = lower(email);

