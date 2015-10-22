create table password_reset_requests(
	request_id serial,
	_account_id int not null,
	nonce varchar(130),
	created_at timestamp with time zone default CURRENT_DATE,
	used boolean,
	CONSTRAINT fk__account_id FOREIGN KEY (_account_id)
      REFERENCES accounts (account_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);
grant select, update, insert, delete on password_reset_requests to developers;
-- Sample data
insert into password_reset_requests (_account_id, nonce, requested_on_datetime, used) values ((select account_id from accounts order by account_id asc limit 1), '41ee0ebcef9cca3226ba4cd93ffb22b4a3ee34bc5433cdf0b327f5a66e5fdeeb002b8f633629d1ed5c440c1c201367c03a4f3b026c59ba81af5c47adb1eedf98', now(), false);

