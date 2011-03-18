create table login_attempts (attempt_id serial, username text, ua_string text, ip text, successful int, additional_info text, attempt_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
grant all on login_attempts_attempt_id_seq to developers;
grant all on login_attempts to developers;
