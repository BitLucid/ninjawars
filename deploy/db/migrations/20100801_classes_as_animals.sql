alter table class add column theme varchar(255);
alter table class add column identity varchar(255);
update class set theme = class_name;
--Themes are colors
update class set class_name = 'Viper' where class_name = 'Black';
update class set class_name = 'Tiger' where class_name = 'Red';
update class set class_name = 'Mantis' where class_name = 'Gray';
update class set class_name = 'Dragon' where class_name = 'White';
update class set class_name = 'Crane' where class_name = 'Blue';
update class set identity = class_name;
--Identities are the animals.
--class_name is the totally mutable string.
