CREATE OR REPLACE FUNCTION pre.f_ins_centro_costo (
  p_id_gestion integer,
  p_tipo_pres varchar,
  p_fecha_ini date,
  p_fecha_fin date
)
RETURNS void AS
$body$
DECLARE

BEGIN

    insert into temp_prog_costo(codigo,descripcion
                ,id_categoria,importe,importe_aprobado,modificado,
                vigente,comprometido,ejecutado,pagado,porc_ejecucion)
    SELECT 
            vcc.codigo_tcc,
            vcc.descripcion_tcc,
            pr.id_categoria_prog,
            sum(COALESCE(prpa.importe, 0::numeric)) AS importe,
            sum(prpa.importe_aprobado) as importe_aprobado,
            ((sum(pre.f_get_estado_presupuesto_mb_x_fechas(prpa.id_presupuesto, prpa.id_partida,'formulado',p_fecha_ini,p_fecha_fin)))- sum(prpa.importe_aprobado)) as modificado,
            sum(pre.f_get_estado_presupuesto_mb_x_fechas(prpa.id_presupuesto, prpa.id_partida,'formulado',p_fecha_ini,p_fecha_fin)) as vigente,
            sum(pre.f_get_estado_presupuesto_mb_x_fechas(prpa.id_presupuesto, prpa.id_partida,'comprometido',p_fecha_ini,p_fecha_fin)) AS comprometido,
            sum(pre.f_get_estado_presupuesto_mb_x_fechas(prpa.id_presupuesto, prpa.id_partida,'ejecutado',p_fecha_ini,p_fecha_fin)) AS ejecutado,
            sum(pre.f_get_estado_presupuesto_mb_x_fechas(prpa.id_presupuesto, prpa.id_partida, 'pagado',p_fecha_ini,p_fecha_fin)) AS pagado,
            case when sum(prpa.importe_aprobado) = 0 then
              0::numeric
            else 
            round((sum(pre.f_get_estado_presupuesto_mb_x_fechas(prpa.id_presupuesto, prpa.id_partida,'ejecutado',p_fecha_ini,p_fecha_fin))/sum(prpa.importe_aprobado))*100,2)
            end  as porc_ejecucion

    FROM pre.vpresup_partida prpa
    inner join param.vcentro_costo vcc on vcc.id_centro_costo=prpa.id_centro_costo
    inner join pre.tpresupuesto pr on pr.id_presupuesto = vcc.id_centro_costo
    WHERE vcc.id_gestion = p_id_gestion
    and prpa.sw_transaccional = 'movimiento'
    and prpa.id_presupuesto IN (SELECT p.id_presupuesto
                        FROM pre.tpresupuesto p
                        where p.id_categoria_prog in (
                              SELECT 
                                cpr.id_categoria_programatica
                              FROM 
                                pre.vcategoria_programatica cpr 
                              WHERE  cpr.id_gestion = p_id_gestion)                       
                         and p.tipo_pres = ANY(string_to_array(p_tipo_pres::text,'')))
    group by 
    vcc.codigo_tcc,
    vcc.descripcion_tcc,
	pr.id_categoria_prog;
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
PARALLEL UNSAFE
COST 100;