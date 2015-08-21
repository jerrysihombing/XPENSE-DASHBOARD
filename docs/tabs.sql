drop table mst_dc;
create table mst_dc (
    dc_code varchar(5) not null,
    dc_init varchar(5),
    dc_name varchar(60)
);
alter table mst_dc add primary key (dc_code);
create index mst_dc_idx on mst_dc(dc_init);

insert into mst_dc values 
('189', 'DCM', 'DC KAYUMANIS'),
('303', 'DCGMS', 'DCGMS'),
('101', 'DCL', 'DC BUAHBATU'),
('160', 'RM2', 'RM2'),
('126', 'DCP', 'DCP'),
('196', 'CTA', 'CTA'),
('127', 'DCC', 'DC CIKONENG'),
('144', 'DCT', 'DC TEGAL'),
('125', 'DCS', 'DC SUMBER'),
('198', 'DCG', 'DCG');

create table mst_regional (
    reg_code varchar(3),
    store_init varchar(5),
    store_name varchar(60)
);
create index mst_regional_idx on mst_regional(reg_code);
create index mst_regional_idx2 on mst_regional(store_init);

create table mst_cluster (
    site varchar(5) not null,
    site_name varchar(10),
    store_code varchar(3),
    store_init varchar(5),
    store_name varchar(60),
    cluster varchar(10)
);
alter table mst_cluster add primary key (site);
create index mst_cluster_idx on mst_cluster(store_code);
create index mst_cluster_idx2 on mst_cluster(store_init);
create index mst_cluster_idx3 on mst_cluster(cluster);

create table mst_site (
    site varchar(5) not null,
    store_code varchar(3),
    store_init varchar(5),
    store_name varchar(60),
    regional_code varchar(3),
    regional_init varchar(5),
    regional_name varchar(60)
);
alter table mst_site add primary key (site);
create index mst_site_idx on mst_site(store_code);
create index mst_site_idx2 on mst_site(store_init);

create sequence xpense_seq;

create table xpense_info (
    id integer not null default nextval('xpense_seq'),
    file_form varchar(8),
    file_name varchar(20),
    inserted_date timestamp
);
alter table xpense_info add primary key (id);
create index xpense_info_idx on xpense_info (file_form);

alter sequence xpense_seq owned by xpense_info.id;

create table xpense (
    pid integer not null,
    pmonth integer,
    pyear integer,
    store_code varchar(5),
    xpense_name varchar(50),
    xpense_pc real
);

create index xpense_idx on xpense (pid);
create index xpense_idx2 on xpense (pmonth);
create index xpense_idx3 on xpense (pyear);
create index xpense_idx4 on xpense (store_code);

alter table xpense add amount numeric(18, 2);
alter table xpense add xpense_account varchar(16);
create index xpense_idx5 on xpense (xpense_account);
