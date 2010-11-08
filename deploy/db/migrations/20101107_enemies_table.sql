CREATE TABLE enemies (_player_id int not null references players(player_id) ON UPDATE CASCADE ON DELETE CASCADE, _enemy_id int not null references players(player_id) ON UPDATE CASCADE ON DELETE CASCADE, primary key (_player_id, _enemy_id));
SELECT 'YOU MUST RUN THE ENEMIES PHP MIGRATION SCRIPT AS WELL. /deploy/db/migrations/enemies_migration.php';
