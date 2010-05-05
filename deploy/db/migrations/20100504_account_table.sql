drop table if exists account_players;
drop table if exists accounts;
create table accounts (
    account_id serial primary key not null, 
    account_identity text not null unique, 
    phash text not null unique,
    active_email text default null,
    type integer default 0::integer,
    created_date timestamp without time zone NOT NULL default now()
);
create table account_players (
    _account_id serial not null references accounts(account_id) ON DELETE CASCADE ON UPDATE CASCADE,
    _player_id serial not null references players(player_id) ON DELETE CASCADE ON UPDATE CASCADE
);

update players set email = email || player_id where email ~* 'PAUSED';

-- Still a problem with duplicate accounts/identities here.
insert into accounts (account_identity, phash, type) select email as email, crypt(pname, gen_salt('bf')) as phash, 0 from players;
insert into account_players (_account_id, _player_id) select account_id, player_id from accounts join players on account_identity = email;
