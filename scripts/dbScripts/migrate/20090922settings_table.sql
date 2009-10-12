DROP TABLE IF EXISTS settings;
CREATE TABLE settings (
    setting_id serial NOT NULL,
    player_id int NOT NULL,
    settings_store text
);
ALTER TABLE settings OWNER TO developers;
GRANT ALL ON TABLE settings TO developers;
