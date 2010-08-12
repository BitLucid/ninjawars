UPDATE clan SET clan_founder = (SELECT uname FROM players WHERE player_id = clan_founder::integer) WHERE clan_founder like '1%';
