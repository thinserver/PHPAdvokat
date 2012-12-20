-- Demo Data

-- already in mandatory.sql
--insert into  phpa_config (title, name, prename, organization, street, zip, city, language) 
--  values ('Frau', 'Duck', 'Daisy', 'Rechtsanwaltskanzlei', 'Bahnhofstr. 12', '12344', 'Entenhausen', 'DE');

insert into  phpa_partner (type, title, name, prename, organization) values ('Person', 'Herr', 'Duck', 'Donald', 'Universitaet Entenhausen');
insert into  phpa_partner (type, title, name, prename) values ('Person', 'Herr', 'Mustermann', 'Manfred');
insert into  phpa_partner (type, title, name,  organization) values ('Gericht', NULL, 'LG Entenhausen', 'Landgericht Entenhausen');
insert into  phpa_pfiles (processregister, partner, court, createdate, subject) values ('2005-001', 1, 3, '2005-01-01', 'Duck ./. Land NRW');
insert into  phpa_pfiles (processregister, partner, createdate, subject) values ('2004-001', 2, '2004-01-11', 'Mustermann ./. Mustermann');
