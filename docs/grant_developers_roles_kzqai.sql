create role developers;
GRANT ALL ON *.* TO GROUP developers;
GRANT ALL ON DATABASE ninjawars to developers;

create role kzqai SUPERUSER LOGIN in group developers;
