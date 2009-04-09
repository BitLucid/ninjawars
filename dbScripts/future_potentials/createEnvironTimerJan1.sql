DROP TABLE time CASCADE;
CREATE TABLE time (
    time_id serial NOT NULL,
	time_label varchar NOT NULL,
	amount integer NOT NULL
);
ALTER TABLE time OWNER TO developers;
GRANT ALL ON TABLE time TO developers;

INSERT into time (time_id, time_label, amount) VALUES (default, 'hours', 0);

