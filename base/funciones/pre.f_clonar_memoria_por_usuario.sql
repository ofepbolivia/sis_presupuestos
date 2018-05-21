CREATE OR REPLACE FUNCTION pre.f_clonar_memoria_por_usuario (
  p_id_ussuario integer,
  p_gestion_actual integer,
  p_gestion_siguiente integer
)
RETURNS void AS
$body$
DECLARE
    v_nombre_funcion   	text;
    v_resp    			varchar;
    v_mensaje 			varchar;

    v_id_presupuesto	integer;
    v_record			record;
    v_registros			record;
    v_id_memoria_calculo		integer;
     v_estado				varchar;
    v_gestion				integer;
    v_id_partida			integer;
    v_id_gestion			integer;

    v_id_partida_2018			integer;
    v_id_gestion_actual			integer;
    v_id_gestion_siguiente		integer;
    v_id_presuesto_2018			integer;
    v_record_memoria_det		record;
    v_importe_detalle			numeric;
    v_id_registro					integer;
    va_id_periodo 				integer[];
    va_id_periodo_an 				integer[];

BEGIN
v_nombre_funcion = 'pre.f_clonar_memoria_por_usuario';

--- gestion actual
select g.id_gestion
into
v_id_gestion_actual
from param.tgestion g
where g.gestion = p_gestion_actual;

---gestion siguiente
select e.id_gestion
into
v_id_gestion_siguiente
from param.tgestion e
where e.gestion = p_gestion_siguiente;

FOR v_record in (select	 	m. id_concepto_ingas,
							m. importe_total,
                            m. obs,
                            m. id_presupuesto,
                            m. estado_reg,
                            m.id_usuario_ai,
                            m.fecha_reg,
                            m.usuario_ai,
                            m.id_usuario_reg,
                            m.fecha_mod,
                            m.id_usuario_mod,
                            m.id_partida,
                           -- m.id_objetivo,
                            m.id_memoria_calculo
                from pre.tmemoria_calculo m
                inner join pre.tpresupuesto p on p.id_presupuesto = m.id_presupuesto
                inner join pre.tcategoria_programatica ct on ct.id_categoria_programatica = p.id_categoria_prog
                where m.id_usuario_reg = p_id_ussuario and ct.id_gestion = v_id_gestion_actual)
LOOP

SELECT par.id_partida
		into
		v_id_partida
           FROM pre.tpresupuesto pre
           JOIN param.tcentro_costo cc ON cc.id_centro_costo = pre.id_centro_costo
           JOIN param.tgestion ges ON ges.id_gestion = cc.id_gestion
           JOIN pre.tpartida par ON par.id_gestion = cc.id_gestion
           JOIN pre.tconcepto_partida cp ON cp.id_partida = par.id_partida
           JOIN param.tconcepto_ingas cig ON cig.id_concepto_ingas = cp.id_concepto_ingas
           WHERE pre.id_presupuesto = v_record.id_presupuesto and
                 cig.id_concepto_ingas = v_record.id_concepto_ingas;

           ---recuperar id presupuesto gestion 2018

    	select p.id_presupuesto_dos
		into
        v_id_presuesto_2018
        from pre.tpresupuesto_ids p
        where p.id_presupuesto_uno = v_record.id_presupuesto;

        ----recuperar la partida actual gestion 2018
        select i.id_partida_dos
        into
        v_id_partida_2018
        from pre.tpartida_ids i
        where i.id_partida_uno = v_id_partida;
       IF NOT EXISTS (	select 1
           					 from pre.tpresup_partida
           					where id_partida = v_id_partida_2018 and id_presupuesto = v_id_presuesto_2018) THEN
           		INSERT INTO pre.tpresup_partida
                (id_presupuesto,
                 id_partida,
                 id_centro_costo,
                 id_usuario_reg
                 )VALUES
                (v_id_presuesto_2018,
                 v_id_partida_2018,
                 v_id_presuesto_2018,
                  1);

           END IF;

		insert into pre.tmemoria_calculo(
              id_concepto_ingas,
              importe_total,
              obs,
              id_presupuesto,
              estado_reg,--
              id_usuario_ai,--
              fecha_reg,
              usuario_ai,
              id_usuario_reg,
              fecha_mod,
              id_usuario_mod,
              id_partida
          	) values(
              v_record.id_concepto_ingas,
              round((v_record.importe_total*0.2)+v_record.importe_total),
              v_record.obs,
              v_id_presuesto_2018,
              'activo',
              null,
              now(),
              null,
              p_id_ussuario,
              null,
              null,
              v_id_partida_2018)RETURNING id_memoria_calculo into v_id_memoria_calculo;

              ---recuperar monto de memoria detalle
              FOR v_record_memoria_det in (SELECT md.importe,
                                                  md.unidad_medida,
                                                  md.cantidad_mem,
                                                  md.importe_unitario,
                                                  md.id_memoria_calculo,
                                                  md.id_periodo
                                                FROM
                                                  pre.tmemoria_det md
                                                 WHERE md.id_memoria_calculo = v_record.id_memoria_calculo )LOOP
              select
              pxp.aggarray(id_periodo)
         into
             va_id_periodo_an
         from  (   select
                      per.id_periodo
                    from param.tperiodo per
                    where per.id_gestion =  v_id_gestion_actual
                    order by per.periodo asc) periodo;

              select
              pxp.aggarray(id_periodo)
         into
             va_id_periodo
         from  (   select
                      per.id_periodo
                    from param.tperiodo per
                    where per.id_gestion =  v_id_gestion_siguiente
                    order by per.periodo asc) periodo;



                             insert into pre.tmemoria_det(
                                importe,
                                estado_reg,
                                id_periodo,
                                id_memoria_calculo,
                                usuario_ai,
                                fecha_reg,
                                id_usuario_reg,
                                id_usuario_ai,
                                cantidad_mem,
                                importe_unitario
                              )
                              values
                              ( round((v_record_memoria_det.importe*0.2)+v_record_memoria_det.importe),
                                'activo',
                                (CASE
                                WHEN v_record_memoria_det.id_periodo = va_id_periodo_an[1] then
                                va_id_periodo[1]
                                WHEN v_record_memoria_det.id_periodo = va_id_periodo_an[2] then
                                va_id_periodo[2]
                                WHEN v_record_memoria_det.id_periodo = va_id_periodo_an[3] then
                                va_id_periodo[3]
                                WHEN v_record_memoria_det.id_periodo = va_id_periodo_an[4] then
                                va_id_periodo[4]
                                WHEN v_record_memoria_det.id_periodo = va_id_periodo_an[5] then
                                va_id_periodo[5]
                                WHEN v_record_memoria_det.id_periodo = va_id_periodo_an[6] then
                                va_id_periodo[6]
                                WHEN v_record_memoria_det.id_periodo = va_id_periodo_an[7] then
                                va_id_periodo[7]
                                WHEN v_record_memoria_det.id_periodo = va_id_periodo_an[8] then
                                va_id_periodo[8]
                                WHEN v_record_memoria_det.id_periodo = va_id_periodo_an[9] then
                                va_id_periodo[9]
                                WHEN v_record_memoria_det.id_periodo = va_id_periodo_an[10] then
                                va_id_periodo[10]
                                WHEN v_record_memoria_det.id_periodo = va_id_periodo_an[11] then
                                va_id_periodo[11]
                                WHEN v_record_memoria_det.id_periodo = va_id_periodo_an[12] then
                                va_id_periodo[12]
                                END ),
                                v_id_memoria_calculo,
                                null,
                                now(),
                                p_id_ussuario,
                                null,
                                v_record_memoria_det.cantidad_mem,
                                round((v_record_memoria_det.importe_unitario * 0.2)+v_record_memoria_det.importe_unitario) );

            END LOOP;


 END LOOP;

EXCEPTION
	WHEN OTHERS THEN
			v_resp='';
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje',SQLERRM);
			v_resp = pxp.f_agrega_clave(v_resp,'codigo_error',SQLSTATE);
			v_resp = pxp.f_agrega_clave(v_resp,'procedimientos',v_nombre_funcion);
			raise exception '%',v_resp;
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;