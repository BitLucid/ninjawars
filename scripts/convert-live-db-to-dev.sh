#!/bin/sh
ssh tchalvak@old.ninjawars.net 'pg_dump ninjawarsLive > ~/latest_ninjawars_live.sql'
sleep 10
scp tchalvak@old.ninjawars.net:~/latest_ninjawars_live.sql /srv/backups/latest_ninjawars_live.sql
sed 's/beagle/developers/g' < /srv/backups/latest_ninjawars_live.sql > /srv/backups/nw_debug_temp.sql
sed 's/TO tchalvak/TO developers/g' /srv/backups/nw_debug_temp.sql -i
sed 's/FROM tchalvak/FROM developers/g' /srv/backups/nw_debug_temp.sql -i
sed 's/Owner: tchalvak/Owner: developers/g' /srv/backups/nw_debug_temp.sql > /srv/backups/latest_ninjawarsLive.sql
dropdb nw;createdb nw --owner=ninjamaster --template=template0 --encoding=UTF8;
echo "create extension pgcrypto" | psql nw
echo "drop role if exists ninjamaster" | psql nw
echo "create role ninjamaster with login" | psql nw
echo "grant developers to ninjamaster" | psql nw
psql nw < /srv/backups/latest_ninjawarsLive.sql > allgood
echo "update accounts set phash = crypt('spamspam', gen_salt('bf', 8)) where account_identity = 'tchalvak@gmail.com' or account_identity = 'tchalvakspam@gmail.com'" | psql nw

