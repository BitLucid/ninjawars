SELECT lower(email), 
 COUNT(lower(email)) AS NumOccurrences
FROM players
GROUP BY lower(email)
HAVING ( COUNT(lower(email)) > 1 );
-- email case duplicates

update players set email = email || player_id where email ~* 'PAUSED';


delete from players where ((level < 5 and days>7) or days > 30) and lower(email) in (SELECT lower(email)
FROM players
GROUP BY lower(email)
HAVING ( COUNT(lower(email)) > 1 ));

select uname, email, level, days from players where lower(email) in (SELECT lower(email)
FROM players
GROUP BY lower(email)
HAVING ( COUNT(lower(email)) > 1 )) order by email;

select uname, email, level, days from players where 
((level < 5 and days>7) or days > 30) and
lower(email) in (SELECT lower(email)
FROM players
GROUP BY lower(email)
HAVING ( COUNT(lower(email)) > 1 )) order by email;
