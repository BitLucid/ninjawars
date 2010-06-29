-- trim and lowercase all emails so we don't get fooled again!
UPDATE players SET email = lower(trim(email));

UPDATE players
	SET email = email || player_id
	WHERE email ~* 'paused' AND email !~* ('paused' || player_id);

-- ACCOUNT CLEAN UPS. THESE ACCOUNTS TO BE DELETED/PAUSED, PLEASE REVIEW (each tuple shares an email address (lowercase) not shown here)
-- player_id |  uname  | days | confirm | confirmed | level |        created_date        |    last_started_attack     
-----------+---------+------+---------+-----------+-------+----------------------------+----------------------------
--   135672 | Perseus |   55 |   55555 |         1 |     2 | 2010-03-01 17:49:29.497972 | 2010-03-01 13:53:24.362684
--   134957 | samurai |   60 |   55555 |         1 |     4 | 2010-02-19 17:48:12.939701 | 2010-02-22 07:04:04.032542

UPDATE players SET email = email||'paused'||player_id WHERE player_id = 134957 and email !~* 'paused';

-- player_id |     uname     | days | confirm | confirmed | level |        created_date        |    last_started_attack     
-----------+---------------+------+---------+-----------+-------+----------------------------+----------------------------
--    112152 | NinjaLord_45  |  456 |    3938 |         0 |    36 | 2008-09-17 20:43:28.521563 | 2008-11-07 03:26:02.113723
--    113948 | TheFinalBlade |  426 |    8404 |         0 |    13 | 2008-11-07 18:57:01.296793 | 2008-12-18 06:35:08.702628

UPDATE players SET email = email||'paused'||player_id WHERE player_id = 112152 and email !~* 'paused';

-- player_id |      uname      | days | confirm | confirmed | level |        created_date        |    last_started_attack     
-----------+-----------------+------+---------+-----------+-------+----------------------------+----------------------------
--    105756 | All-Seeing-EyeZ |  609 |    8531 |         0 |    11 | 2008-05-30 12:40:02.778696 | 2008-10-11 13:55:37.192898
--    105972 | Xena            |  541 |    9878 |         0 |    26 | 2008-06-02 14:41:26.302664 | 2008-10-11 13:55:37.192898

UPDATE players SET email = email||'paused'||player_id WHERE player_id = 105756 and email !~* 'paused';

-- player_id |   uname   | days | confirm | confirmed | level |        created_date        |    last_started_attack     
-----------+-----------+------+---------+-----------+-------+----------------------------+----------------------------
--    116753 | mateus    |  385 |    7620 |         0 |    18 | 2009-01-21 19:19:42.072924 | 2009-02-02 11:41:10.97293
--    125415 | maho      |  122 |    6905 |         0 |    31 | 2009-08-21 15:08:11.446455 | 2009-11-26 11:38:53.865341
--    108303 | voidninja |  467 |    5194 |         0 |    11 | 2008-07-06 15:18:03.705221 | 2008-10-11 13:55:37.192898

UPDATE players SET email = email||'paused'||player_id WHERE player_id = 108303 and email !~* 'paused';
UPDATE players SET email = email||'paused'||player_id WHERE player_id = 116753 and email !~* 'paused';

-- player_id |    uname    | days | confirm | confirmed | level |        created_date        |    last_started_attack     
-----------+-------------+------+---------+-----------+-------+----------------------------+----------------------------
--    132782 | BlackDagger |   85 |   55555 |         0 |     3 | 2010-01-20 11:00:17.825644 | 2010-01-20 07:18:14.308009
--    141275 | Dronge      |   10 |   55555 |         1 |     1 | 2010-05-19 08:40:36.370154 | 2010-05-19 04:48:27.040153

UPDATE players SET email = email||'paused'||player_id WHERE player_id = 132782 and email !~* 'paused';

-- player_id | uname  | days | confirm | confirmed | level |        created_date        |    last_started_attack     
-----------+--------+------+---------+-----------+-------+----------------------------+----------------------------
--     90123 | bLaze  |  692 |    4035 |         0 |    23 | 2007-12-02 08:35:29.636978 | 2008-10-11 13:55:37.192898
--     99687 | ScreaM |  281 |    6784 |         0 |    76 | 2008-03-08 10:00:46.088632 | 2009-06-03 14:20:49.888238

UPDATE players SET email = email||'paused'||player_id WHERE player_id = 90123 and email !~* 'paused';

-- player_id |   uname    | days | confirm | confirmed | level |        created_date        |    last_started_attack     
-----------+------------+------+---------+-----------+-------+----------------------------+----------------------------
--    132948 | scorpion   |   83 |   55555 |         0 |     3 | 2010-01-22 21:11:18.201839 | 2010-01-22 19:37:08.813573
--    132950 | scorpion17 |   82 |   55555 |         0 |     7 | 2010-01-22 21:21:12.906063 | 2010-01-23 08:55:55.204337

UPDATE players SET email = email||'paused'||player_id WHERE player_id = 132948 and email !~* 'paused';

-- player_id |     uname     |             email             | level | kills | days 
-------------+---------------+-------------------------------+-------+-------+------
--    139160 | warman123     | daytongoerlitz@rocketmail.com |     1 |     0 |   37
--    139586 | warriorboy555 | daytongoerlitz@rocketmail.com |     1 |     0 |   34

delete from players where player_id = 139160;

-- player_id |  uname   |       email        | level | kills | days 
-------------+----------+--------------------+-------+-------+------
--    138738 | Jonathan | seanwinner@aol.com |     1 |     0 |   42
--    138737 | Sean     | seanwinner@aol.com |     1 |     0 |   42

delete from players where player_id = 138738;

-- player_id |   uname    |      email       | level | kills | days 
-------------+------------+------------------+-------+-------+------
--    138005 | ast371     | lyc1987@live.com |     1 |     0 |   40
--    138006 | DarkPoison | lyc1987@live.com |     1 |     0 |   47

delete from players where player_id = 138006;

-- player_id |       uname        |          email          | level | kills | days 
-------------+--------------------+-------------------------+-------+-------+------
--    143046 | YourWorstNightMare | guymi@loreto.vic.edu.au |     1 |     0 |    3
--    141044 | WickedNightMare    | guymi@loreto.vic.edu.au |     1 |    12 |   25

UPDATE players SET email = email||'paused'||player_id WHERE player_id = 141044 and email !~* 'paused';

-- player_id |  uname   |          email          | level | kills | days 
-------------+----------+-------------------------+-------+-------+------
--    141773 | shreeder | e.g.free2ryme@yahoo.com |     1 |     0 |   17
--    141395 | shredder | e.g.free2ryme@yahoo.com |     1 |     1 |   21

UPDATE players SET email = email||'paused'||player_id WHERE player_id = 141395 and email !~* 'paused';

-- player_id |  uname  |        email        | level | kills | days 
-------------+---------+---------------------+-------+-------+------
--    143108 | anthony | im@fandinoyahoo.com |     1 |     0 |    2
--    143113 | Anthony | im@fandinoyahoo.com |     1 |     0 |    2

delete from players where player_id = 143108;

-- player_id |  uname  |        email        | level | kills | days 
-------------+----------+---------------------+-------+-------+------
--    137077 | cowpie12 | mephits@comcast.net |     1 |     1 |   55
--    137125 | Cowpee   | mephits@comcast.net |     1 |     1 |   54

UPDATE players SET email = email||'paused'||player_id WHERE player_id = 137077 and email !~* 'paused';

-- player_id |  uname  |         email          | level | kills | days |        created_date        
-------------+---------+------------------------+-------+-------+------+----------------------------
--    143583 | Alex747 | ag.s232009@hotmail.com |     1 |     0 |    5 | 2010-06-14 15:00:08.192945
--    143581 | Alex002 | ag.s232009@hotmail.com |     1 |     0 |    5 | 2010-06-14 14:58:44.066892

UPDATE players SET email = email||'paused'||player_id WHERE player_id = 143581 and email !~* 'paused';

-- player_id |   uname   |        email         | level | kills | days 
-------------+-----------+----------------------+-------+-------+------
--    142757 | rikimarro | osimpsonjr@gmail.com |     1 |     0 |    6
--    142688 | Tessu     | osimpsonjr@gmail.com |     1 |     0 |    7

DELETE FROM players WHERE player_id = 142757;

-- player_id |   uname    |         email         | level | kills | days 
-------------+------------+-----------------------+-------+-------+------
--    139756 | SnakeBite  | texasrwarrior@aol.com |     1 |     0 |   32
--    139757 | SnakeBlade | texasrwarrior@aol.com |     1 |     1 |   32

DELETE FROM players WHERE player_id = 139756;

-- player_id |   uname   |          email           | level | kills | days 
-------------+-----------+--------------------------+-------+-------+------
--    143197 | Zyeal     | gustavo_roge@hotmail.com |     1 |     0 |    1
--    138342 | Mezorykan | gustavo_roge@hotmail.com |     1 |     0 |   45

DELETE FROM players WHERE player_id = 138342;

-- Fuck all of these guys
-- player_id |   uname   |         email         | level | kills | days 
-------------+-----------+-----------------------+-------+-------+------
--    138572 | mitch     | noreply@ninjawars.net |     1 |     0 |   44
--    139466 | GOKU      | noreply@ninjawars.net |     1 |     0 |   35
--    139311 | sand0     | noreply@ninjawars.net |     1 |     0 |   35
--    138522 | darkflame | noreply@ninjawars.net |     1 |     0 |   44
--    137785 | Katsu     | noreply@ninjawars.net |     1 |     0 |   49

DELETE FROM players WHERE email = 'noreply@ninjawars.net';

SELECT
	email, COUNT(email) AS NumOccurrences
	FROM players
	GROUP BY email
	HAVING COUNT(email) > 1;
-- email case duplicates

SELECT
	lower(uname), count(lower(uname)) AS NumOccurences
	FROM players
	GROUP BY lower(uname)
	HAVING count(lower(uname)) > 1;

--UPDATE players SET
--	email = email || 'paused' || player_id
--	WHERE ((level < 5 AND days > 7) OR days > 30)
--	AND email IN
--		(SELECT email
--			FROM players
--			GROUP BY email
--			HAVING COUNT(email) > 1);
--
--SELECT
--	uname, email, level, days
--	FROM players
--	WHERE email IN
--		(SELECT email
--			FROM players
--			GROUP BY email
--			HAVING COUNT(email) > 1)
--	ORDER BY email;
--
--SELECT
--	uname, email, level, days
--	FROM players
--	WHERE ((level < 5 AND days > 7) OR days > 30)
--	AND email IN
--		(SELECT email
--			FROM players
--			GROUP BY email
--			HAVING COUNT(email) > 1)
--	ORDER BY email;
