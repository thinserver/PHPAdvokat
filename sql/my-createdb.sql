-- script to create database
drop table if exists phpa_config ;
drop table if exists phpa_invoicepos ;
drop table if exists phpa_invoices ;
drop table if exists phpa_invoicetypes ;
drop table if exists phpa_expenditures ;
drop table if exists phpa_amounts ;
drop table if exists phpa_transactions ;
drop table if exists phpa_exp_categories ;
drop table if exists phpa_expendituretypes;
drop table if exists phpa_accounts;
drop table if exists phpa_events ;
drop table if exists phpa_dfiles ;
drop table if exists phpa_rvgcharges ;
drop table if exists phpa_pfiles ;
drop table if exists phpa_partner ;
drop table if exists phpa_partnertypes ;

-- insert into user (host,user,password)
-- values('localhost','burkhard',password('burkhard')); 

-- create database phpadvocat;

-- insert into db 
-- (host,db,user,Select_priv,Insert_priv,Update_priv,Delete_priv, 
-- Create_priv,Drop_priv) 
-- values 
-- ('localhost','phpadvocat','%','Y','Y','Y','Y','N','N'); 

-- insert into user values ('%', 'admin', password('admin'), 'Y', 'Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y');
-- update user set Select_priv='Y', insert_priv='Y', update_priv='Y', delete_priv='Y' where user='gast';

--  # don''t forget to run "mysqladmin reload" from shell! ---


create table phpa_partnertypes (
  type    varchar(10)
);


create table phpa_partner (
  number integer AUTO_INCREMENT PRIMARY KEY,
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
  email   varchar(40)
);

-- means *real* files (paper)
create table phpa_pfiles (
  number integer AUTO_INCREMENT PRIMARY KEY,
  processregister char(10),
  value numeric(17,2),
  subject varchar(50),
  partner integer,
  opposing integer,
  court integer,
  createdate date,
  enddate date,
  comment varchar(255)
);

create table phpa_events (
  number integer AUTO_INCREMENT PRIMARY KEY,
  pfile integer,
  eventstart timestamp,
  eventend timestamp,
  description varchar(50),
  location varchar(50),
  constraint foreign key(pfile) references phpa_pfiles(number) on delete cascade
);

-- means data files e.g. text documents
-- obsolete
-- create table phpa_dfiles (
--   number integer AUTO_INCREMENT PRIMARY KEY,
--   pfile integer,
--   createdate date,
--   dfilepos varchar(254),
--   constraint foreign key(pfile) references phpa_pfiles(number) on delete cascade
-- );

create table phpa_expendituretypes (
  number integer AUTO_INCREMENT PRIMARY KEY,
  description varchar(50),
  category integer,
  vat numeric(4,1),
  vat_category integer
);

create table phpa_accounts (
  number integer AUTO_INCREMENT PRIMARY KEY,
  description varchar(40)
);

create table phpa_exp_categories (
  number integer PRIMARY KEY,
  description varchar(40)
);

create table phpa_transactions (
  number integer PRIMARY KEY
);

create table phpa_amounts (
  number integer AUTO_INCREMENT PRIMARY KEY,
  createdate date,
  transaction integer,
  exp_category integer not null default 1,
  exp_account integer not null default 1,
  description varchar(100),
  incomingamount numeric(10,2) default 0.0,
  outgoingamount numeric(10,2) default 0.0
);


create table phpa_expenditures (
  number integer AUTO_INCREMENT PRIMARY KEY,
  pfile integer,
  createdate date,
  expendituretype integer,
  exp_category integer not null default 1,
  description varchar(100),
  amount integer,
  vatamount integer,
  outgoingamount integer,
  outgoingvat integer,
  constraint foreign key(pfile) references phpa_pfiles(number) on delete cascade,
  constraint foreign key(amount) references phpa_amounts(number),
  constraint foreign key(vatamount) references phpa_amounts(number)
);


create table phpa_invoices (
  number integer AUTO_INCREMENT PRIMARY KEY,
  pfile integer,
  address integer,
  pfilevalue numeric(10,2),
  charge numeric(10,2),
  invoicetext text,
  createdate date,
  paydate date,
  constraint foreign key(pfile) references phpa_pfiles(number) on delete cascade
);

create table phpa_invoicetypes (
  number integer AUTO_INCREMENT PRIMARY KEY,
  description varchar(60),
  charge numeric(17,2),
  amount_category integer,
  vat_percent numeric(10,2),
  vat_category integer
);


create table phpa_invoicepos (
  number integer AUTO_INCREMENT PRIMARY KEY,
  invoice integer,
  invoicetype integer,
  chargefactor numeric(10,2),
  amount numeric(10,2),
  vat numeric(10,2),
  constraint foreign key(invoice) references phpa_invoices(number) on delete cascade,
  constraint foreign key(invoicetype) references phpa_invoicetypes(number) on delete cascade
);

create table phpa_rvgcharges (
  rvgvalue numeric(12,2),
  rvgcharge numeric(12,2)
);


create table phpa_config (
  number integer AUTO_INCREMENT PRIMARY KEY,
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
  filebase      varchar(200)

);

-- grant all users access

-- insert into db 
-- (host,db,user,Select_priv,Insert_priv,Update_priv,Delete_priv, 
-- Create_priv,Drop_priv) 
-- values 
-- ('localhost','phpadvocat','%','Y','Y','Y','Y','N','N'); 

 

