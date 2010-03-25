alter table class add column class_note text;
update class set class_note = 'Poison' WHERE class_name = 'Black';
update class set class_note = 'Smoke' WHERE class_name = 'Gray';
update class set class_note = 'Strength' WHERE class_name = 'Red';
update class set class_note = 'Speed' WHERE class_name = 'Blue';
update class set class_note = 'Healing' WHERE class_name = 'White';
