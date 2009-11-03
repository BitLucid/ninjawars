DROP TABLE IF EXISTS events;
CREATE TABLE events (
    event_id serial NOT NULL,
    send_to int default 0,
    send_from int default 0,
    message varchar(255) NOT NULL,
    unread int NOT NULL default 1,
    date timestamp without time zone NOT NULL default now()
);
ALTER TABLE events OWNER TO developers;
GRANT ALL ON TABLE events TO developers;
