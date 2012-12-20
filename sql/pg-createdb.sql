SET client_encoding = 'LATIN9';
-- script to create database
drop table phpa_config ;
drop table phpa_invoicepos ;
drop table phpa_invoices ;
drop table phpa_invoicetypes ;
drop table phpa_expenditures ;
drop table phpa_exp_categories ;
drop table phpa_amounts ;
drop table phpa_transactions ;
drop table phpa_expendituretypes;
drop table phpa_accounts;
drop table phpa_events ;
-- drop table phpa_dfiles ;
drop table phpa_rvgcharges ;
drop table phpa_pfiles ;
drop table phpa_partner ;
drop table phpa_partnertypes ;

create table phpa_partnertypes (
  type    varchar(10)
);

create table phpa_partner (
  number serial,
  type    varchar(10),
  title   varchar(10),
  name    varchar(30),
  prename varchar(30),
  organization  varchar(30),
  street  varchar(30),
  zip     varchar(7),
  city    varchar(30),
  phone   varchar(30),
  fax     varchar(30),
  email   varchar(40),
  constraint pri_phpa_partner primary key(number)
);

-- means *real* files (paper)
create table phpa_pfiles (
  number serial,
  processregister char(10),
  value numeric(17,2),
  subject varchar(50),
  partner integer,
  opposing integer,
  court integer,
  createdate date,
  enddate date,
  comment varchar(512),
  constraint pri_phpa_pfiles primary key(number)
);

create table phpa_events (
  number serial,
  pfile integer,
  eventstart timestamp,
  eventend timestamp,
  description varchar(50),
  location varchar(50),
  constraint pri_phpa_events primary key(number),
  foreign key(pfile) references phpa_pfiles(number) on delete cascade
);

-- means data files e.g. text documents
-- obsolete because alle documents are administrated in filesystem
-- create table phpa_dfiles (
--   number serial,
--   pfile integer,
--   createdate date,
--   dfilepos varchar(254),
--   constraint pri_phpa_dfiles primary key(number),
--   foreign key(pfile) references phpa_pfiles(number) on delete cascade
-- );

create table phpa_expendituretypes (
  number serial,
  description varchar(50),
  category integer,
  vat numeric(4,1),
  vat_category integer
);

create table phpa_accounts (
  number serial,
  description varchar(40)
);

create table phpa_exp_categories (
  number integer,
  description varchar(40),
  constraint pri_phpa_categories primary key(number)
);

create table phpa_transactions (
  number integer,
  constraint pri_transactions primary key(number)
);

create table phpa_amounts (
  number serial,
  createdate date,
  transaction integer,
  exp_category integer not null default 1,
  exp_account integer not null default 1,
  description varchar(100),
  incomingamount numeric(10,2) default 0.0,
  outgoingamount numeric(10,2) default 0.0,
  constraint pri_phpa_amounts primary key(number)
);


create table phpa_expenditures (
  number serial,
  pfile integer,
  createdate date,
  expendituretype integer,
  exp_category integer not null default 1,
  description varchar(100),
  amount integer,
  vatamount integer,
  constraint pri_phpa_expenditures primary key(number),
  foreign key(pfile) references phpa_pfiles(number) on delete cascade
--  foreign key(amount) references phpa_amounts(number),
--  foreign key(vatamount) references phpa_amounts(number)
);


create table phpa_invoices (
  number serial,
  pfile integer,
  address integer,
  pfilevalue numeric(10,2),
  charge numeric(10,2),
  invoicetext text,
  createdate date,
  paydate date,
  constraint pri_phpa_invoices primary key(number),
  foreign key(pfile) references phpa_pfiles(number) on delete cascade
);

create table phpa_invoicetypes (
  number serial,
  description varchar(60),
  charge numeric(17,2),
  amount_category integer,
  vat_percent numeric(10,2),
  vat_category integer,
  constraint pri_phpa_invoicetypes primary key(number)
);


create table phpa_invoicepos (
  number serial,
  invoice integer,
  invoicetype integer,
  chargefactor numeric(10,2),
  amount numeric(10,2),
  vat numeric(10,2),
  constraint pri_phpa_invoicespos primary key(number),
  foreign key(invoice) references phpa_invoices(number) on delete cascade,
  foreign key(invoicetype) references phpa_invoicetypes(number) on delete cascade
);

create table phpa_rvgcharges (
  rvgvalue numeric(12,2),
  rvgcharge numeric(12,2)
);


create table phpa_config (
  number serial,
  title         varchar(10),
  name          varchar(30),
  prename       varchar(30),
  organization  varchar(30),
  street        varchar(30),
  zip           varchar(7),
  city          varchar(30),
  phone         varchar(30),
  fax           varchar(30),
  email         varchar(40),
  language      varchar(5),
  bank          varchar(100),
  bank_id       varchar(15),
  account       varchar(20),
  vat_id        varchar(40),
  vat_percent   numeric(10,2),
  filebase      varchar(200),
  constraint pri_phpa_config primary key(number)

);

-- grant all users access
grant all on phpa_config to public;
grant all on phpa_invoicepos to public;
grant all on phpa_invoices to public;
grant all on phpa_invoicetypes to public;
grant all on phpa_accounts to public;
grant all on phpa_amounts to public;
grant all on phpa_transactions to public;
grant all on phpa_expenditures to public;
grant all on phpa_expendituretypes to public;
grant all on phpa_exp_categories to public;
grant all on phpa_events to public;
-- grant all on phpa_dfiles to public;
grant all on phpa_rvgcharges to public;
grant all on phpa_pfiles to public;
grant all on phpa_partner to public;
grant all on phpa_partnertypes to public;
grant all on phpa_config_number_seq to public;
-- grant all on phpa_dfiles_number_seq to public;
grant all on phpa_pfiles_number_seq to public;
grant all on phpa_events_number_seq to public;
grant all on phpa_amounts_number_seq to public;
grant all on phpa_expenditures_number_seq to public;
grant all on phpa_accounts_number_seq to public;
grant all on phpa_invoicepos_number_seq to public;
grant all on phpa_invoices_number_seq to public;
grant all on phpa_partner_number_seq to public;
grant all on phpa_invoicetypes_number_seq to public;


