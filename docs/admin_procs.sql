create or replace function expense_load(p_account varchar, p_cursor refcursor) returns refcursor as $$
begin
    open p_cursor for
        select expense_account, expense_name from mst_expense where expense_account = p_account;
        
    return p_cursor;
end;
$$ language plpgsql;

create or replace function expense_load_all(p_cursor refcursor) returns refcursor as $$
begin
    open p_cursor for
        select expense_account, expense_name from mst_expense where expense_account not in ('5000000', '4100000') order by expense_name;
    
    return p_cursor;
end;
$$ language plpgsql;

-- ### --

create or replace function adm_expense_add(p_name varchar, p_detail varchar[], p_created_by varchar, p_created_date timestamp) returns void as $$
declare 
	idh integer;
	upbnd integer;
begin
    --- header
    insert into adm_expense_hdr (
        name, created_by, created_date
    ) values (
        p_name, p_created_by, p_created_date
    );

	--- get currval of hdr sequence to make a link of hdr and dtl
	select currval('adm_expense_hdr_seq') into idh;	
	
	---- detail
	-- loop through array element
	select array_upper(p_detail, 1) into upbnd;
	if upbnd is null then
	    upbnd = 0;
	end if;
	for idx in 1 .. upbnd loop
		--- insert detail ---
		if p_detail[idx] <> '' then
			insert into adm_expense_dtl (id_hdr, xpense_account) values (idh, p_detail[idx]);
		end if;	    	    
	end loop;
end;
$$ language plpgsql;

create or replace function adm_expense_load(p_id integer, p_cursor refcursor) returns refcursor as $$
begin
    open p_cursor for
        select name, is_active, created_by, created_date, last_user, last_update from adm_expense_hdr where id = p_id;
    
    return p_cursor;
end;
$$ language plpgsql;

create or replace function adm_expense_load_dtl(p_id integer, p_cursor refcursor) returns refcursor as $$
begin
    open p_cursor for
        select x.xpense_account, y.expense_name from adm_expense_dtl x inner join mst_expense y on y.expense_account = x.xpense_account 
        where x.id_hdr = p_id order by x.xpense_account;

    return p_cursor;
end;
$$ language plpgsql;


create or replace function adm_expense_load_all(p_cursor refcursor) returns refcursor as $$
begin
    open p_cursor for
        select name, is_active, created_by, created_date, last_user, last_update from adm_expense_hdr order by name;
    
    return p_cursor;
end;
$$ language plpgsql;

create or replace function adm_expense_remove(p_id integer) returns void as $$
begin
    delete from adm_expense_dtl where id_hdr = p_id;
    delete from adm_expense_hdr where id = p_id;
end;
$$ language plpgsql;

create or replace function adm_expense_update(p_id integer, p_name varchar, p_is_active integer, p_detail varchar[], p_last_user varchar, p_last_update timestamp) returns void as $$
declare 
	upbnd integer;
begin
    -- delete detail first
	delete from adm_expense_dtl where id_hdr = p_id;
	
	--- header       
    update adm_expense_hdr set name = p_name, is_active = p_is_active, last_user = p_last_user, last_update = p_last_update where id = p_id;
        
	---- detail
	-- loop through array element
	select array_upper(p_detail, 1) into upbnd;
	if upbnd is null then
	    upbnd = 0;
	end if;
	for idx in 1 .. upbnd loop
		--- insert detail ---
		if p_detail[idx] <> '' then
			insert into adm_expense_dtl (id_hdr, xpense_account) values (p_id, p_detail[idx]);
		end if;	    	    
	end loop;
end;
$$ language plpgsql;

-- ### --

create or replace function contact_add(p_name varchar, p_email varchar, p_position varchar, p_created_by varchar, p_created_date timestamp) returns void as $$
begin
    insert into adm_contact (
        name, email, position, created_by, created_date
    ) values (
        p_name, p_email, p_position, p_created_by, p_created_date
    );
end;
$$ language plpgsql;

create or replace function contact_load(p_id integer, p_cursor refcursor) returns refcursor as $$
begin
    open p_cursor for
        select name, email, position, created_by, created_date, last_user, last_update from adm_contact where id = p_id;
    
    return p_cursor;
end;
$$ language plpgsql;

create or replace function contact_load_all(p_cursor refcursor) returns refcursor as $$
begin
    open p_cursor for
        select id, name, email, position, created_by, created_date, last_user, last_update from adm_contact order by name;
    
    return p_cursor;
end;
$$ language plpgsql;

create or replace function contact_remove(p_id integer) returns void as $$
begin
    delete from adm_contact where id = p_id;
end;
$$ language plpgsql;

create or replace function contact_update(p_id integer, p_name varchar, p_email varchar, p_position varchar, p_last_user varchar, p_last_update timestamp) returns void as $$
begin
    update adm_contact set name = p_name,  email = p_email, position = p_position, last_user = p_last_user, last_update = p_last_update where id = p_id;        
end;
$$ language plpgsql;

-- #### --

create or replace function menu_load_all(p_cursor refcursor) returns refcursor as $$
begin
    open p_cursor for
        select id, id_menu, title, created_by, created_date, last_user, last_update from adm_menu order by id_menu;
    
    return p_cursor;
end;
$$ language plpgsql;

create or replace function role_add(p_role_name varchar, p_description varchar, p_detail integer[], p_created_by varchar, p_created_date timestamp) returns void as $$
declare 
	idh integer;
	upbnd integer;
begin
    --- header
    insert into adm_role_hdr (
        role_name, description, created_by, created_date
    ) values (
        p_role_name, p_description, p_created_by, p_created_date
    );

	--- get currval of hdr sequence to make a link of hdr and dtl
	select currval('adm_role_hdr_seq') into idh;	
	
	---- detail
	-- loop through array element
	select array_upper(p_detail, 1) into upbnd;
	if upbnd is null then
	    upbnd = 0;
	end if;
	for idx in 1 .. upbnd loop
		--- insert detail ---
		if p_detail[idx] <> 0 then
			insert into adm_role_dtl (id_hdr, id_menu) values (idh, p_detail[idx]);
		end if;	    	    
	end loop;
end;
$$ language plpgsql;

create or replace function role_load(p_id integer, p_cursor refcursor) returns refcursor as $$
begin
    open p_cursor for
        select role_name, description, created_by, created_date, last_user, last_update from adm_role_hdr where id = p_id;
    
    return p_cursor;
end;
$$ language plpgsql;

create or replace function role_load_all(p_cursor refcursor) returns refcursor as $$
begin
    open p_cursor for
        select id, role_name, description, created_by, created_date, last_user, last_update from adm_role_hdr order by role_name;
    
    return p_cursor;
end;
$$ language plpgsql;

create or replace function role_load_dtl(p_id integer, p_cursor refcursor) returns refcursor as $$
begin
    open p_cursor for
        select a.id_menu, b.title from adm_role_dtl a inner join adm_menu b on a.id_menu = b.id_menu where a.id_hdr = p_id order by a.id_menu;

    return p_cursor;
end;
$$ language plpgsql;

create or replace function role_load_dtl_by_name(p_name varchar, p_cursor refcursor) returns refcursor as $$
begin
    open p_cursor for
        select a.id_menu, b.title from adm_role_dtl a inner join adm_role_hdr c on a.id_hdr = c.id inner join adm_menu b on a.id_menu = b.id_menu
		where c.role_name = p_name order by a.id_menu;

   return p_cursor;
end;
$$ language plpgsql;

create or replace function role_remove(p_id integer) returns void as $$
declare 
	rolename varchar(20);
begin
	select role_name into rolename from adm_role_hdr where id = p_id;
	
	delete from adm_role_dtl where id_hdr = p_id;
    delete from adm_role_hdr where id = p_id;

    update adm_user set role_name = 'n/a' where role_name = rolename;
end;
$$ language plpgsql;

create or replace function role_update(p_id integer, p_role_name varchar, p_description varchar, p_detail integer[], p_last_user varchar, p_last_update timestamp) returns void as $$
declare 
	upbnd integer;
begin
	-- delete detail first
	delete from adm_role_dtl where id_hdr = p_id;
	
	--- header       
    update adm_role_hdr set role_name = p_role_name,  description = p_description, last_user = p_last_user, last_update = p_last_update where id = p_id;
        
	---- detail
	-- loop through array element
	select array_upper(p_detail, 1) into upbnd;
	if upbnd is null then
	    upbnd = 0;
	end if;
	for idx in 1 .. upbnd loop
		--- insert detail ---
		if p_detail[idx] <> 0 then
			insert into adm_role_dtl (id_hdr, id_menu) values (p_id, p_detail[idx]);
		end if;	    	    
	end loop;
end;
$$ language plpgsql;

create or replace function user_add(p_user_id varchar, p_user_name varchar, p_passwd varchar, p_email varchar, p_branch_code varchar, p_departement varchar, p_role_name varchar, p_active integer, p_detail varchar[], p_created_by varchar, p_created_date timestamp) returns void as $$
declare 
	idh integer;
	upbnd integer;
begin
    insert into adm_user (
        user_id, user_name, passwd, email, branch_code, departement, role_name, active, created_by, created_date
    ) values (
        p_user_id, p_user_name, p_passwd, p_email, p_branch_code, p_departement, p_role_name, p_active, p_created_by, p_created_date
    );
    
    --- get currval of hdr sequence to make a link of hdr and dtl
	select currval('adm_user_seq') into idh;
    
    ---- detail
	-- loop through array element
	select array_upper(p_detail, 1) into upbnd;
	if upbnd is null then
	    upbnd = 0;
	end if;
	for idx in 1 .. upbnd loop
		--- insert detail ---
		if p_detail[idx] is not null then
			insert into adm_user_store (id_hdr, branch_code) values (idh, p_detail[idx]);
		end if;	    	    
	end loop;
end;
$$ language plpgsql;
  
create or replace function user_load(p_id integer, p_cursor refcursor) returns refcursor as $$
begin
    open p_cursor for
        select user_id, user_name, passwd, email, branch_code, departement, role_name, active, created_by, created_date, last_user, last_update from adm_user where id = p_id;
    
    return p_cursor;
end;
$$ language plpgsql;

create or replace function user_load_stores(p_id integer, p_cursor refcursor) returns refcursor as $$
begin
    open p_cursor for
        select branch_code from adm_user_store where id_hdr = p_id order by branch_code;
    
    return p_cursor;
end;
$$ language plpgsql;

create or replace function user_load_all(p_cursor refcursor) returns refcursor as $$
begin
    open p_cursor for
        select id, user_id, user_name, passwd, email, branch_code, departement, role_name, active, created_by, created_date, last_user, last_update from adm_user order by user_id;
        
    return p_cursor;
end;
$$ language plpgsql;

create or replace function user_load_all_email(p_cursor refcursor) returns refcursor as $$
begin
    open p_cursor for
        select user_id, user_name, email from adm_user where email <> '' order by user_name, user_id;
        
    return p_cursor;
end;
$$ language plpgsql;

create or replace function user_load_by_active(p_active integer, p_cursor refcursor) returns refcursor as $$
begin
    open p_cursor for
        select id, user_id, user_name, passwd, email, branch_code, departement, role_name, active, created_by, created_date, last_user, last_update from adm_user
		where active = p_active order by user_id;
        
    return p_cursor;
end;
$$ language plpgsql;

create or replace function user_load_by_role_name(p_role_name varchar, p_cursor refcursor) returns refcursor as $$
begin
    open p_cursor for
        select id, user_id, user_name, passwd, email, branch_code, departement, role_name, active, created_by, created_date, last_user, last_update from adm_user
		where role_name = p_role_name order by user_id;
    
    return p_cursor;
end;
$$ language plpgsql;

create or replace function user_load_by_user_id(p_user_id varchar, p_cursor refcursor) returns refcursor as $$
begin
    open p_cursor for
        select id, user_id, user_name, passwd, email, branch_code, departement, role_name, active, created_by, created_date, last_user, last_update from adm_user
		where user_id = p_user_id;
        
    return p_cursor;
end;
$$ language plpgsql;

create or replace function user_load_stores_by_user_id(p_user_id varchar, p_cursor refcursor) returns refcursor as $$
declare
    idh integer;
begin

    select id into idh from adm_user where user_id = p_user_id;
    if not found then
        idh = -1;
    end if;
    open p_cursor for
        select distinct store_code, store_init, store_name from adm_user_store inner join mst_site on branch_code = store_init where id_hdr = idh order by store_init;
    
    return p_cursor;
end;
$$ language plpgsql;

create or replace function user_login(p_user_id varchar, p_logtime timestamp) returns void as $$
begin
	insert into adm_user_login (user_id, logtime) values (p_user_id, p_logtime);
end;
$$ language plpgsql;
  
create or replace function user_remove(p_id integer) returns void as $$
begin
	delete from adm_user where id = p_id;
end;
$$ language plpgsql;

create or replace function user_set_active(p_id integer, p_active integer, p_last_user varchar, p_last_update timestamp) returns void as $$
begin
	update adm_user set active = p_active, last_user = p_last_user, last_update = p_last_update where id = p_id;
end;
$$ language plpgsql;

create or replace function user_set_passwd(p_id integer, p_passwd varchar, p_last_user varchar, p_last_update timestamp) returns void as $$
begin
	update adm_user set passwd = p_passwd, last_user = p_last_user, last_update = p_last_update where id = p_id;
end;
$$ language plpgsql;

create or replace function user_set_passwd_by_user_id(p_user_id varchar, p_passwd varchar, p_last_user varchar, p_last_update timestamp) returns void as $$
begin
	update adm_user set passwd = p_passwd, last_user = p_last_user, last_update = p_last_update where user_id = p_user_id;
end;
$$ language plpgsql;

create or replace function user_set_role(p_id integer, p_role_name varchar, p_last_user varchar, p_last_update timestamp) returns void as $$
begin
	update adm_user set role_name = p_role_name, active = 1, last_user = p_last_user, last_update = p_last_update where id = p_id;
end;
$$ language plpgsql;

create or replace function user_update(p_id integer, p_user_id varchar, p_user_name varchar, p_email varchar, p_branch_code varchar, p_departement varchar, p_role_name varchar, p_active integer, p_detail varchar[], p_last_user varchar, p_last_update timestamp) returns void as $$
declare 
	upbnd integer;
begin
	-- delete detail first
	delete from adm_user_store where id_hdr = p_id;
    
	update adm_user set user_id = p_user_id, user_name = p_user_name, email = p_email, branch_code = p_branch_code, departement = p_departement, role_name = p_role_name,
    active = p_active, last_user = p_last_user, last_update = p_last_update 
    where id = p_id;
    
    ---- detail
	-- loop through array element
	select array_upper(p_detail, 1) into upbnd;
	if upbnd is null then
	    upbnd = 0;
	end if;
	for idx in 1 .. upbnd loop
		--- insert detail ---
		if p_detail[idx] is not null then
			insert into adm_user_store (id_hdr, branch_code) values (p_id, p_detail[idx]);
		end if;	    	    
	end loop;
end;
$$ language plpgsql;

create or replace function user_update_with_modify_passwd(p_id integer, p_user_id varchar, p_user_name varchar, p_email varchar, p_branch_code varchar, p_departement varchar, p_role_name varchar, p_active integer, p_passwd varchar, p_detail varchar[], p_last_user varchar, p_last_update timestamp) returns void as $$
declare 
	upbnd integer;
begin
	-- delete detail first
	delete from adm_user_store where id_hdr = p_id;
    
	update adm_user set user_id = p_user_id, user_name = p_user_name, email = p_email, branch_code = p_branch_code, departement = p_departement, role_name = p_role_name,
    active = p_active, passwd = p_passwd, last_user = p_last_user, last_update = p_last_update 
    where id = p_id;
    
    ---- detail
	-- loop through array element
	select array_upper(p_detail, 1) into upbnd;
	if upbnd is null then
	    upbnd = 0;
	end if;
	for idx in 1 .. upbnd loop
		--- insert detail ---
		if p_detail[idx] is not null then
			insert into adm_user_store (id_hdr, branch_code) values (p_id, p_detail[idx]);
		end if;	    	    
	end loop;
end;
$$ language plpgsql;

create or replace function adm_expense_count(p_name varchar) returns integer as $$
declare 
	cnt integer;
begin
	if p_name is null then
        select count(id) into cnt from adm_expense_hdr;
    else
        select count(id) into cnt from adm_expense_hdr where name = p_name;
    end if;

    return cnt;
end;
$$ language plpgsql;

create or replace function contact_count(p_name varchar) returns integer as $$
declare 
	cnt integer;
begin
	if p_name is null then
        select count(id) into cnt from adm_contact;
    else
        select count(id) into cnt from adm_contact where name = p_name;
    end if;

    return cnt;
end;
$$ language plpgsql;

create or replace function role_count(p_role_name varchar) returns integer as $$
declare 
	cnt integer;
begin
	if p_role_name is null then
        select count(id) into cnt from adm_role_hdr;
    else
        select count(id) into cnt from adm_role_hdr where role_name = p_role_name;
    end if;

    return cnt;
end;
$$ language plpgsql;

create or replace function user_count(p_user_id varchar) returns integer as $$
declare 
	cnt integer;
begin
	if p_user_id is null then
        select count(id) into cnt from adm_user;
    else
        select count(id) into cnt from adm_user where user_id = p_user_id;
    end if;

    return cnt;
end;
$$ language plpgsql;

create or replace function user_stores_count(p_id integer) returns integer as $$
declare 
	cnt integer;
begin
	if p_id is null then
        select count(id) into cnt from adm_user_store;
    else
        select count(id) into cnt from adm_user_store where id_hdr = p_id;
    end if;

    return cnt;
end;
$$ language plpgsql;

create or replace function user_is_valid(p_user_id varchar, p_passwd varchar) returns integer as $$
declare 
	cnt integer;
begin
	select count(id) into cnt from adm_user where user_id = p_user_id and passwd = p_passwd and active = 1;

    return cnt;
end;
$$ language plpgsql;
