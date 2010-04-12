DELETE FROM messages WHERE send_to NOT IN (SELECT player_id FROM players);
DELETE FROM messages WHERE send_from NOT IN (SELECT player_id FROM players);
ALTER TABLE messages ADD FOREIGN KEY (send_from) REFERENCES players(player_id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE messages ADD FOREIGN KEY (send_to) REFERENCES players(player_id) ON UPDATE CASCADE ON DELETE CASCADE;
