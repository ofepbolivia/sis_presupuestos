CREATE OR REPLACE FUNCTION pre.f_clonar_memoria (
  p_id_presupuesto_dos integer
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
    v_id_partida_18			integer;


BEGIN
	v_nombre_funcion = 'pre.f_clonar_memoria';

select p.id_presupuesto_uno
		into
        v_id_presupuesto
from pre.tpresupuesto_ids p
where p.id_presupuesto_dos = p_id_presupuesto_dos;


 select
              cc.id_gestion,
              pre.estado,
              ges.gestion
            into
              v_id_gestion,
              v_estado,
              v_gestion
            from pre.tpresupuesto pre
            inner join param.tcentro_costo cc on cc.id_centro_costo = pre.id_centro_costo
            inner join param.tgestion ges on ges.id_gestion = cc.id_gestion
            where pre.id_presupuesto = p_id_presupuesto_dos;





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
                            m.id_partida
                           -- m.id_objetivo
                from pre.tmemoria_calculo m
                where m.id_presupuesto = v_id_presupuesto)
LOOP

SELECT
par.id_partida
into
v_id_partida
           FROM pre.tpresupuesto pre
               JOIN param.tcentro_costo cc ON cc.id_centro_costo = pre.id_centro_costo
               JOIN param.tgestion ges ON ges.id_gestion = cc.id_gestion
               JOIN pre.tpartida par ON par.id_gestion = cc.id_gestion
               JOIN pre.tconcepto_partida cp ON cp.id_partida = par.id_partida
              JOIN param.tconcepto_ingas cig ON cig.id_concepto_ingas = cp.id_concepto_ingas
           where pre.id_presupuesto = v_id_presupuesto and
                cig.id_concepto_ingas = v_record.id_concepto_ingas;

                select i.id_partida_dos
                into
                v_id_partida_18
                from pre.tpartida_ids i
           		where i.id_partida_uno = v_id_partida;



           IF NOT EXISTS (	select 1
           					 from pre.tpresup_partida
           					where id_partida = v_id_partida_18 and id_presupuesto = p_id_presupuesto_dos) THEN
           		INSERT INTO pre.tpresup_partida
                (id_presupuesto,
                 id_partida,
                 id_centro_costo,
                 id_usuario_reg
                 )VALUES
                (p_id_presupuesto_dos,
                 v_id_partida_18,
                  p_id_presupuesto_dos,
                  1);

           END IF;




		insert into pre.tmemoria_calculo(
              id_concepto_ingas,
              importe_total,
              obs,
              id_presupuesto,
              estado_reg,
              id_usuario_ai,
              fecha_reg,
              usuario_ai,
              id_usuario_reg,
              fecha_mod,
              id_usuario_mod,
              id_partida
              --id_objetivo
          	) values(
              v_record.id_concepto_ingas,
              0,
              v_record.obs,
              p_id_presupuesto_dos,
              'activo',
              null,
              now(),
              null,
              1,
              null,
              null,
              v_id_partida_18
              /*v_record.id_objetivo*/)RETURNING id_memoria_calculo into v_id_memoria_calculo;

               FOR v_registros in (select
                                   per.id_periodo
                                from param.tperiodo per
                                where per.id_gestion = v_id_gestion
                                      and per.estado_reg = 'activo'
                                order by per.fecha_ini) LOOP

                            insert into pre.tmemoria_det(
                                importe,
                                estado_reg,
                                id_periodo,
                                id_memoria_calculo,
                                usuario_ai,
                                fecha_reg,
                                id_usuario_reg,
                                id_usuario_ai
                              )
                              values
                              ( 0,
                                'activo',
                                v_registros.id_periodo,
                                v_id_memoria_calculo,
                                null,
                                now(),
                                1,
                                null);

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