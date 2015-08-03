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