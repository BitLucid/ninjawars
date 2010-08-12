DELETE FROM clan WHERE clan_id in (select clan_id FROM CLAN left join clan_player ON clan_id = _clan_id AND member_level = 1 WHERE (select count(*) from clan_player WHERE clan_id = _clan_id) = 0);
