CREATE TABLE duped_unames (
	uname text not null
	, email text not null
	, created_date timestamp not null
	, relative_age smallint not null
	, player_id int not null primary key
	, locked boolean default false not null
);

CREATE INDEX duped_unames_uname_key ON duped_unames (uname);
CREATE INDEX duped_unames_email_key ON duped_unames (email);

INSERT INTO duped_unames SELECT
	p1.uname
	, p1.email
	, p1.created_date
	, CASE 
		WHEN p1.created_date = (SELECT max(p2.created_date) FROM players AS p2 WHERE lower(p2.uname) = lower(p1.uname))
		THEN (SELECT count(*) FROM players AS p4 WHERE lower(p4.uname) = lower(p1.uname))
		ELSE 1 END AS ord
	, p1.player_id
	, false
	FROM players AS p1
	WHERE lower(p1.uname) in (
		SELECT lower(p3.uname) FROM players AS p3 GROUP BY lower(p3.uname) HAVING count(lower(p3.uname)) > 1
	)
	ORDER BY lower(uname);

UPDATE duped_unames AS p1
	SET relative_age = 2
	WHERE lower(uname) in (
		SELECT lower(uname) FROM duped_unames WHERE relative_age = 1 GROUP BY lower(uname) HAVING count(lower(uname)) > 1
		)
	AND relative_age = 1
	AND created_date = (SELECT max(created_date) FROM duped_unames WHERE lower(uname) = lower(p1.uname) AND relative_age = 1);
