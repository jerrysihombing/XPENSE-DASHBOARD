drop table if exists adm_user_login;
drop table if exists adm_menu;
drop table if exists adm_role_hdr;
drop table if exists adm_role_dtl;
drop table if exists adm_user;

create table adm_user_login (
  id serial,
  user_id varchar(20),
  logtime timestamp
);
alter table adm_user_login add primary key (id);

create table adm_menu (
  id serial not null,
  id_menu integer,
  title varchar(100),
  created_by varchar(20),
  created_date timestamp,
  last_user varchar(20),
  last_update timestamp
);
alter table adm_menu add primary key (id);
create index adm_menu_idx on adm_menu (id_menu);

create sequence adm_role_hdr_seq;
create table adm_role_hdr (
  id integer not null default nextval('adm_role_hdr_seq'),
  role_name varchar(20),
  description varchar(100),
  created_by varchar(20),
  created_date timestamp,
  last_user varchar(20),
  last_update timestamp
);
alter table adm_role_hdr add primary key (id);
alter sequence adm_role_hdr_seq owned by adm_role_hdr.id;
create index adm_role_hdr_idx on adm_role_hdr (role_name);

create table adm_role_dtl (
  id serial not null,
  id_hdr integer not null,
  id_menu integer not null
);
alter table adm_role_dtl add primary key (id);
create index adm_role_dtl_idx on adm_role_dtl (id_hdr);
create index adm_role_dtl_idx2 on adm_role_dtl (id_menu);

create sequence adm_user_seq;
create table adm_user (
  id integer not null default nextval('adm_user_seq'),
  user_id varchar(20),
  user_name varchar(40),
  passwd varchar(40),
  email varchar(100),
  branch_code varchar(10),
  departement varchar(30),
  role_name varchar(20) default 'N/A',
  active integer default 0, -- 0: inactive, otherwise active
  created_by varchar(20),
  created_date timestamp,
  last_user varchar(20),
  last_update timestamp
);
alter sequence adm_user_seq owned by adm_user.id;
alter table adm_user add primary key (id);
create index adm_user_idx on adm_user (user_id);
create index adm_user_idx2 on adm_user (branch_code);
create index adm_user_idx3 on adm_user (role_name);
create index adm_user_idx4 on adm_user (user_name);

create table adm_user_store (
  id serial not null,
  id_hdr integer not null,
  branch_code varchar(10)
);
alter table adm_user_store add primary key (id);
create index adm_user_store_idx on adm_user_store (id_hdr);

-- master, start with 1 --
    
-- transaction, start with 3 --
    
-- report, start with 7 --
insert into adm_menu (id_menu, title, created_by, created_date, last_user, last_update) values 
    (711, 'Expense Report', 'system', current_timestamp, 'system', current_timestamp);
    
-- admin, start with 9 --
insert into adm_menu (id_menu, title, created_by, created_date, last_user, last_update) values 
    (999, 'System Administration', 'system', current_timestamp, 'system', current_timestamp);

insert into adm_role_hdr (role_name, description, created_by, created_date, last_user, last_update) values 
    ('Administrator', 'Super user role', 'system', current_timestamp, 'system', current_timestamp);

insert into adm_role_dtl (id_hdr, id_menu) values 
    (1, 711), (1, 999);

insert into adm_user (user_id, user_name, passwd, email, branch_code, departement, role_name, active, created_by, created_date, last_user, last_update) values
    ('admin', 'admin', '2d9efa6b87dd1ca99d355486817a98aa696ac5a1', '', '', '', 'Administrator', 1, 'system', current_timestamp, 'system', current_timestamp);
