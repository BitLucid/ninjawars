update players set karma = level;
update accounts a set karma_total = karma from account_players, players where account_id = _account_id and _player_id = player_id;
-- Make karma_total match the level of the character, since karma defaults to that.
