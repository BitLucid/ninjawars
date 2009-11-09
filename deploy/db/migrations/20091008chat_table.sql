DROP TABLE IF EXISTS chat;
CREATE TABLE chat (
    chat_id serial NOT NULL,
    sender_id int default 0,
    message varchar(255) NOT NULL,
    date timestamp without time zone NOT NULL default now()
);
ALTER TABLE chat OWNER TO developers;
GRANT ALL ON TABLE chat TO developers;
