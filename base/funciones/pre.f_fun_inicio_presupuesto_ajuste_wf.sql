CREATE OR REPLACE FUNCTION pre.f_fun_inicio_presupuesto_ajuste_wf (
  p_id_usuario integer,
  p_id_usuario_ai integer,
  p_usuario_ai varchar,
  p_id_estado_wf integer,
  p_id_proceso_wf integer,
  p_codigo_estado varchar
)
RETURNS boolean AS
$body$
/*
*
*  Autor:   maylee.perez
*  DESC:    funcion que actualiza los estados despues del registro del presupueto ajute
*  Fecha:   03/01/2021
*
*/

DECLARE

	v_nombre_funcion   	text;
    v_resp    			varchar;
    v_mensaje 			varchar;

    v_registros 		record;
    v_regitros_pp		record;
    v_monto_ejecutar_mo			numeric;
    v_id_uo						integer;
    v_id_usuario_excepcion		integer;
    v_resp_doc 					boolean;
    v_id_usuario_firma			integer;
    v_id_moneda_base			integer;
    v_fecha						date;
    v_importe_aprobado_total	numeric;
    v_importe_total				numeric;

    v_importe_total_ajuste		numeric;



BEGIN

	 v_nombre_funcion = 'pre.f_fun_inicio_presupuesto_ajuste_wf';


    -- actualiza estado en la solicitud
    update pre.tajuste   set
           id_estado_wf =  p_id_estado_wf,
           estado = p_codigo_estado,
           id_usuario_mod=p_id_usuario,
           id_usuario_ai = p_id_usuario_ai,
           usuario_ai = p_usuario_ai,
           fecha_mod=now()
    where id_proceso_wf = p_id_proceso_wf;


   --SIN LA INTER FACE ES DE VOBOPRE,  insertamos el resultado aprobado en la tabla partida ejecución

   IF p_codigo_estado = 'aprobado' THEN

          FOR v_registros in (  select a.id_ajuste,
                                     a.tipo_ajuste,
                                     a.importe_ajuste,
                                     a.nro_tramite,
                                     a.fecha,
                                     a.id_moneda,
                                     adet.id_presupuesto,
                                     a.id_gestion,
                                     adet.id_partida,
                                     adet.importe
                              from pre.tajuste a
                              inner join pre.tajuste_det adet on adet.id_ajuste = a.id_ajuste
                              where a.id_proceso_wf = p_id_proceso_wf ) LOOP


          				v_id_moneda_base = param.f_get_moneda_base();

                        SELECT sum(pp.importe),
                               sum(pp.importe_aprobado)
                        into v_importe_total,
                             v_importe_aprobado_total
                        FROM pre.tpresup_partida pp
                        where  pp.id_presupuesto =  v_registros.id_presupuesto
                        and pp.id_partida = v_registros.id_partida
                        and pp.estado_reg = 'activo';

                        v_importe_total_ajuste =  COALESCE(v_importe_aprobado_total,0) + (COALESCE(v_registros.importe,0)) ;


                         UPDATE pre.tpresup_partida pp SET
                           importe_aprobado = v_importe_total_ajuste
                         WHERE  pp.id_presupuesto =  v_registros.id_presupuesto
                         and pp.id_partida = v_registros.id_partida;

                         -- fecha de formulacion
                         v_fecha =now()::date;

                        SELECT pej.id_partida_ejecucion
                        INTO v_regitros_pp
                        FROM pre.tpartida_ejecucion pej
                        WHERE pej.id_presupuesto = v_registros.id_presupuesto
                        and pej.id_partida = v_registros.id_partida
                        and par.tipo_movimiento = 'formulado';


                       INSERT INTO  pre.tpartida_ejecucion
                                                  (
                                                    id_usuario_reg,
                                                    fecha_reg,
                                                    estado_reg,
                                                    nro_tramite,
                                                    monto,
                                                    monto_mb,
                                                    id_moneda,
                                                    id_presupuesto,
                                                    id_partida,
                                                    tipo_movimiento,
                                                    tipo_cambio,
                                                    fecha,
                                                    id_partida_ejecucion_fk
                                                  )
                                                  VALUES (
                                                    p_id_usuario,
                                                    now(),
                                                    'activo',
                                                    v_registros.nro_tramite,
                                                    v_registros.importe, --moneda de formualcion
                                                    v_registros.importe,   --moneda base
                                                    v_id_moneda_base,
                                                    v_registros.id_presupuesto,
                                                    v_registros.id_partida,
                                                    'formulado', --tipo_movimiento
                                                    1,  --tipo de cambios, moneda formulacion y moneda base (es la misma)
                                                    v_fecha,
                                                    v_regitros_pp.id_partida_ejecucion
                                                  );





          END LOOP;



   END IF;




RETURN   TRUE;



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