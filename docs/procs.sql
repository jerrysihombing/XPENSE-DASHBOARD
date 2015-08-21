create or replace function load_expense_v2(p_year integer, p_month integer, p_store varchar, p_cursor refcursor) returns refcursor as $$
begin
    open p_cursor for
        select z.pyear, z.pmonth, z.store_code, x.name xpense_name, sum(z.amount) amount, is_dc(z.store_code) is_dc,
        get_total_netsales(z.pyear, z.pmonth, z.store_code) total_netsales, get_total_expense(z.pyear, z.pmonth, z.store_code) total_expense  
        from adm_expense_hdr x
        inner join adm_expense_dtl y on x.id = y.id_hdr
        inner join xpense z on z.xpense_account = y.xpense_account
        where z.pyear = p_year and z.pmonth = p_month and store_code = p_store and x.is_active = 1 
        group by z.pyear, z.pmonth, z.store_code, x.name
        order by x.name;
        
    return p_cursor;
end;
$$ language plpgsql;

create or replace function is_dc(p_store varchar) returns integer as $$
declare
    cnt integer;
begin

    select count(dc_code) into cnt from mst_dc where dc_code = p_store;
        
    return cnt;
end
$$ language plpgsql;

create or replace function get_total_expense(p_year integer, p_month integer, p_store varchar) returns numeric as $$
declare
    total numeric(18, 2);
begin

    select amount into total from xpense where pyear = p_year and pmonth = p_month and store_code = p_store and xpense_account = '5000000';
    if not found then
        total = 0;
    end if;
    
    return total;
end
$$ language plpgsql;

create or replace function get_total_netsales(p_year integer, p_month integer, p_store varchar) returns numeric as $$
declare
    total numeric(18, 2);
begin

    select amount into total from xpense where pyear = p_year and pmonth = p_month and store_code = p_store and xpense_account = '4100000';
    if not found then
        total = 0;
    end if;
    
    return total;
end
$$ language plpgsql;

-- ### --

create or replace function load_regional_members(p_regional varchar, p_cursor refcursor) returns refcursor as $$
begin
    open p_cursor for
        select (select y.store_code from mst_site y where y.store_init = x.store_init limit 1) store_code, x.store_init, x.store_name,
        (select y.store_name from mst_site y where y.store_init = x.store_init limit 1) store_name_2
        from mst_regional x where x.reg_code = p_regional order by x.store_init;
        
    return p_cursor;
end;
$$ language plpgsql;

create or replace function load_all_regional(p_cursor refcursor) returns refcursor as $$
begin
    open p_cursor for
        select distinct reg_code, length(reg_code) l_reg_code from mst_regional where reg_code <> '' order by l_reg_code, reg_code;
        
    return p_cursor;
end;
$$ language plpgsql;

create or replace function load_cluster_members(p_cluster varchar, p_cursor refcursor) returns refcursor as $$
begin
    open p_cursor for
        select x.store_code, x.store_init, x.store_name,
        (select y.store_name from mst_site y where y.store_init = x.store_init limit 1) store_name_2
        from mst_cluster x where x.cluster = p_cluster order by x.store_init;
        
    return p_cursor;
end;
$$ language plpgsql;

create or replace function load_all_cluster(p_cursor refcursor) returns refcursor as $$
begin
    open p_cursor for
        select distinct cluster, length(cluster) l_cluster from mst_cluster where cluster <> '' order by l_cluster, cluster;
        
    return p_cursor;
end;
$$ language plpgsql;

create or replace function load_cluster_expense(p_year integer, p_month integer, p_cluster varchar, p_cursor refcursor) returns refcursor as $$
begin
    open p_cursor for
        select pid, pmonth, pyear, store_code, xpense_name, xpense_pc, amount from xpense where pyear = p_year and pmonth = p_month
        and store_code in (select store_code from mst_cluster where cluster = p_cluster)
        order by xpense_name;
        
    return p_cursor;
end;
$$ language plpgsql;

create or replace function load_expense(p_year integer, p_month integer, p_store varchar, p_cursor refcursor) returns refcursor as $$
begin
    open p_cursor for
        select pid, pmonth, pyear, store_code, xpense_name, xpense_pc, amount from xpense where pyear = p_year and pmonth = p_month and store_code = p_store order by xpense_name;
        
    return p_cursor;
end;
$$ language plpgsql;

create or replace function load_store(p_store varchar, p_cursor refcursor) returns refcursor as $$
begin
    open p_cursor for
        select store_code, store_init, store_name, regional_code, regional_init, regional_name from mst_site where store_code = p_store limit 1;
        
    return p_cursor;
end;
$$ language plpgsql;

create or replace function load_all_store(p_cursor refcursor) returns refcursor as $$
begin
    open p_cursor for
        select distinct store_code, store_init, store_name from mst_site where store_init is not null order by store_init;
        
    return p_cursor;
end;
$$ language plpgsql;

create or replace function load_site(p_site varchar, p_cursor refcursor) returns refcursor as $$
begin
    open p_cursor for
        select site, store_code, store_init, store_name, regional_code, regional_init, regional_name from mst_site where site = p_site;
        
    return p_cursor;
end;
$$ language plpgsql;

create or replace function site_count(p_site varchar) returns integer as $$
declare
    cnt integer;
begin

    if p_site is null then
        select count(site) into cnt from mst_site;
    else
        select count(site) into cnt from mst_site where site = p_site;
    end if;
    
    return cnt;
end
$$ language plpgsql;