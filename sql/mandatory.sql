insert into phpa_partnertypes values('Person');
insert into phpa_partnertypes values('Firma');
insert into phpa_partnertypes values('Gericht');

insert into phpa_expendituretypes (description, category, vat, vat_category) values ('Auslagen',1370,0,0);
insert into phpa_expendituretypes (description, category, vat, vat_category) values ('Fremdgeld',1374,0,0);
insert into phpa_expendituretypes (description, category, vat, vat_category) values ('Gebuehren',1200,19,3806);
insert into phpa_expendituretypes (description, category, vat, vat_category) values ('Gebuehren',1200,16,3805);

insert into phpa_accounts (description) values('Betriebskonto');
insert into phpa_accounts (description) values('Barkasse');

insert into phpa_invoicetypes (description, charge, amount_category, vat_percent, vat_category) values ('Honorar', NULL, 1200, 19, 3806);
insert into phpa_invoicetypes (description, charge, amount_category, vat_percent, vat_category) values ('Prozessgebuehr/Gebuehr fuer Mahnbescheid', NULL, 1370, 0, 0);
insert into phpa_invoicetypes (description, charge, amount_category, vat_percent, vat_category) values ('Erhoehungsgebuehr', NULL, 1200, 19, 3806);
insert into phpa_invoicetypes (description, charge, amount_category, vat_percent, vat_category) values ('Verhandlungsgebuehr/Gebuehr fuer Vollstreckungsbescheid', NULL, 1370, 0, 0);
insert into phpa_invoicetypes (description, charge, amount_category, vat_percent, vat_category) values ('Verhandlungsgebuehr, nichtstreitig', NULL, 1370, 0, 0);
insert into phpa_invoicetypes (description, charge, amount_category, vat_percent, vat_category) values ('Verhandlungsgebuehr bei Einspruch', NULL, 1370, 0, 0);
insert into phpa_invoicetypes (description, charge, amount_category, vat_percent, vat_category) values ('Eroerterungsgebuehr', NULL, 1370, 0, 0);
insert into phpa_invoicetypes (description, charge, amount_category, vat_percent, vat_category) values ('Beweisgebuehr', NULL, 1200, 19, 3806);
insert into phpa_invoicetypes (description, charge, amount_category, vat_percent, vat_category) values ('Vergleichsgebuehr', NULL, 1370, 0, 0);
insert into phpa_invoicetypes (description, charge, amount_category, vat_percent, vat_category) values ('Zwangsvollstreckungsgebuehr', NULL, 1370, 0, 0);
insert into phpa_invoicetypes (description, charge, amount_category, vat_percent, vat_category) values ('Gebuehr fuer eidesstattliche Versicherung', NULL, 1370, 0, 0);
insert into phpa_invoicetypes (description, charge, amount_category, vat_percent, vat_category) values ('Tage- und Abwesenheitsgeld', NULL, 1200, 19, 3806);
insert into phpa_invoicetypes (description, charge, amount_category, vat_percent, vat_category) values ('Schreibauslagen - Fotokopien', NULL, 1200, 19, 3806);
insert into phpa_invoicetypes (description, charge, amount_category, vat_percent, vat_category) values ('Porto, Telefon-, Telefax und BTX-Auslagen -Pauschale-', NULL, 1200, 19, 3809);

insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (300,	25);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (600,	45);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (900,	65);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (1200,	85);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (1500,	105);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (2000,	133);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (2500,	161);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (3000,	189);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (3500,	217);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (4000,	245);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (4500,	273);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (5000,	301);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (6000,	338);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (7000,	375);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (8000,	412);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (9000,	449);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (10000, 486);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (13000, 526);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (16000, 566);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (19000, 606);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (22000, 646);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (25000, 686);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (30000, 758);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (35000, 830);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (40000, 902);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (45000, 974);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (50000, 1046);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (65000, 1123);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (80000, 1200);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (95000, 1277);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (110000, 1354);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (125000, 1431);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (140000, 1508);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (155000, 1585);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (170000, 1662);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (185000, 1739);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (200000, 1816);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (230000, 1934);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (260000, 2052);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (290000, 2170);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (320000, 2288);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (350000, 2406);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (380000, 2524);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (410000, 2642);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (440000, 2760);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (470000, 2878);
insert into phpa_rvgcharges  (rvgvalue, rvgcharge) values (500000, 2996);

-- Demo Configuration; cannot live without ;-)
insert into  phpa_config (title, name, prename, organization, street, zip, city, language) 
  values ('Frau', 'Duck', 'Daisy', 'Rechtsanwaltskanzlei', 'Bahnhofstr. 12', '12344', 'Entenhausen', 'DE');

