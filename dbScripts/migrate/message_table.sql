DROP TABLE messages;
CREATE TABLE messages (
    message_id serial NOT NULL,
    send_to character varying(255) not null default ''::character varying,
    send_from character varying(255) not null default ''::character varying,
    message varchar(255) NOT NULL,
    date timestamp without time zone NOT NULL
);
ALTER TABLE messages OWNER TO developers;
GRANT ALL ON TABLE messages TO developers;
