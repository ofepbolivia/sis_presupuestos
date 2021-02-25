CREATE OR REPLACE FUNCTION pre.f_kp_get_id_presupuesto_por_empleado (
  pm_id_funcionario integer,
  pm_fecha_viaje varchar
)
RETURNS varchar AS
$body$
DECLARE
  g_id_presupuesto integer;
  g_gestion varchar;
  g_fecha_viaje_date date;
  g_codigo_prog  varchar;
  g_separador  varchar;
  g_codigo_presupuesto integer;

BEGIN
	g_separador = '#'; --Separador para mensajes devueltos por la función
  --Obtenemos la gestion a partir de la fecha
  	select to_date(pm_fecha_viaje, 'dd/MM/yyyy')INTO g_fecha_viaje_date;
	select extract(year from g_fecha_viaje_date) INTO g_gestion;


  RAISE NOTICE '%', 'g_fecha_viaje_date: '||g_fecha_viaje_date;
  RAISE NOTICE '%', 'g_gestion: '||g_gestion;

  	select carpres.id_centro_costo as presupuesto, cp.codigo_actividad, tc.codigo
    into g_id_presupuesto, g_codigo_prog, g_codigo_presupuesto
    from orga.tcargo_presupuesto carpres
    inner join param.tgestion g on g.id_gestion = carpres.id_gestion
    inner join orga.tuo_funcionario h on h.id_cargo = carpres.id_cargo AND h.tipo = 'oficial'
    inner join pre.tpresupuesto pres on pres.id_centro_costo = carpres.id_centro_costo
    inner join pre.vcategoria_programatica cp on cp.id_categoria_programatica = pres.id_categoria_prog
    inner join param.tcentro_costo cc on cc.id_centro_costo = pres.id_centro_costo
    inner join param.ttipo_cc tc on tc.id_tipo_cc = cc.id_tipo_cc
    where h.id_funcionario = pm_id_funcionario
    and h.fecha_asignacion <= g_fecha_viaje_date
    and (h.fecha_finalizacion >= g_fecha_viaje_date or h.fecha_finalizacion is null)
    and (carpres.fecha_fin >= g_fecha_viaje_date or carpres.fecha_fin is null)
    and g.gestion = g_gestion::NUMERIC
    and h.estado_reg = 'activo'
    and carpres.estado_reg = 'activo';

  	RETURN g_id_presupuesto|| g_separador || g_codigo_prog || g_separador || g_codigo_presupuesto;


END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;