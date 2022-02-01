CREATE OR REPLACE FUNCTION pre.f_fun_inicio_ajuste_wf (
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
*  Autor:   RAC
*  DESC:    funcion que actualiza los estados de los ajsutes presupeustarios
*  Fecha:   10/06/2016
***************************************************************************
  HISTORIAL DE MODIFICACIONES:

 ISSUE            FECHA:		      AUTOR       DESCRIPCION
 0				12/10/2017			RAC			Validacion para comprometer o revertir presupuesto
***************************************************************************/


DECLARE

	v_nombre_funcion   	text;
    v_resp    			varchar;
    v_mensaje 			varchar;

    v_registros 		record;
    v_registros_det		record;
    v_monto_ejecutar_mo			numeric;
    v_id_uo						integer;
    v_id_usuario_excepcion		integer;
    v_resp_doc 					boolean;
    v_id_usuario_firma			integer;
    v_id_moneda_base			integer;
    v_fecha						date;
    sw_incremento				boolean;
    sw_decremento				boolean;
    v_resultado_ges				numeric[];
    v_mensaje_error 			varchar;
    v_sw_error 					boolean;
    sw_revertir 				boolean;
    sw_comprometer				boolean;

	--(franklin.espinoza)[25/6/2019]
	v_tipo_proceso				varchar;
    v_modificacion				record;
    v_id_partida_ejecucion 		integer;
    v_codigo					varchar;
    v_id_moneda 				integer;
    v_oficial					numeric;

    v_monto_mb					numeric;
    v_columna_origen			varchar;

    --(may) 11-06-2021
    sw_ajuste 					boolean;
    v_id_obligacion_pag			integer;
    v_id_centro_costo			integer;
    v_id_obligacion_det			integer;
	v_tipo_cambio_op			numeric;
    v_id_partida_ejecu			integer;
    v_id_sol_origen				integer;
    v_tabla_origen				varchar;


BEGIN

	  v_nombre_funcion = 'pre.f_fun_inicio_ajuste_wf';

       ----------------------------------------
       -- actualiza estado en la solicitud
       ---------------------------------------

       update pre.tajuste   set
           id_estado_wf =  p_id_estado_wf,
           estado = p_codigo_estado,
           id_usuario_mod=p_id_usuario,
           id_usuario_ai = p_id_usuario_ai,
           usuario_ai = p_usuario_ai,
           fecha_mod=now()
       where id_proceso_wf = p_id_proceso_wf;

      -------------------------------------------------------------------------
      --  si pasa al estado aprobado registramos el ajuste presupuestario
      ------------------------------------------------------------------------

      IF p_codigo_estado = 'aprobado' THEN

              select
                 a.id_ajuste,
                 a.tipo_ajuste,
                 a.importe_ajuste,
                 a.nro_tramite,
                 a.fecha,
                 a.id_moneda
              into
                v_registros
              from pre.tajuste a
              where a.id_proceso_wf = p_id_proceso_wf;

            v_id_moneda_base = param.f_get_moneda_base();
            sw_incremento = false;
            sw_decremento = false;
            v_sw_error = false;
            v_mensaje_error = '';

             -- valida


             IF v_registros.tipo_ajuste in ('traspaso','reformulacion') THEN
                 sw_incremento = true;
                 sw_decremento = true;
                 sw_comprometer = false;
                 sw_revertir = false;
                 sw_ajuste = false;
             ELSEIF  v_registros.tipo_ajuste = 'decremento' THEN
                 sw_incremento = false;
                 sw_decremento = true;
                 sw_comprometer = false;
                 sw_revertir = false;
                 sw_ajuste = false;
             ELSEIF  v_registros.tipo_ajuste = 'incremento' THEN
                 sw_incremento = true;
                 sw_decremento = false;
                 sw_comprometer = false;
                 sw_revertir = false;
                 sw_ajuste = false;

             ELSEIF  v_registros.tipo_ajuste in ( 'inc_comprometido') THEN
                 sw_incremento = false;
                 sw_decremento = false;
                 sw_comprometer = true;
                 sw_revertir = false;
                 sw_ajuste = false;
            ELSEIF  v_registros.tipo_ajuste in ('rev_comprometido') THEN
                 sw_incremento = false;
                 sw_decremento = false;
                 sw_comprometer = false;
                 sw_revertir = true;
                 sw_ajuste = false;

            ELSEIF  v_registros.tipo_ajuste in ( 'ajuste_comprometido') THEN --16-06-2021 (may) Ajuste
                 sw_incremento = false;
                 sw_decremento = false;
                 sw_comprometer = false;
                 sw_revertir = false;
                 sw_ajuste = true;


             ELSE
                 raise exception 'no se reconoce el tipo de ajuste %', v_registros.tipo_ajuste;
             END IF;


             IF sw_decremento  THEN
                 -- listar decrementos
                 FOR v_registros_det in (
                                        select
                                          ad.id_ajuste_det,
                                          ad.id_presupuesto,
                                          ad.id_partida,
                                          ad.importe ,
                                          par.codigo as codigo_partida,
                                          pre.estado as estado_presupuesto
                                        from pre.tajuste_det ad
                                        inner join pre.tpresupuesto pre on pre.id_presupuesto = ad.id_presupuesto
                                        inner join pre.tpartida par on par.id_partida = ad.id_partida
                                        where ad.id_ajuste = v_registros.id_ajuste
                                              and ad.tipo_ajuste = 'decremento'
                                              and ad.estado_reg = 'activo'
                                       ) LOOP

                        -- valida estado del presupuesto
                        IF v_registros_det.estado_presupuesto != 'aprobado'  THEN
                          raise exception 'el pesupuesto id %, no esta aprobado', v_registros_det.id_presupuesto;
                        END IF;

                        -- registras decrementos
                        v_resultado_ges = pre.f_gestionar_presupuesto_individual(
                                                p_id_usuario,
                                                NULL,
                                                v_registros_det.id_presupuesto,
                                                v_registros_det.id_partida,
                                                v_id_moneda_base,
                                                v_registros_det.importe,
                                                v_registros.fecha,
                                                'formulado', --traducido a varchar
                                                NULL, --p_id_partida_ejecucion
                                                'id_ajuste_det',
                                                v_registros_det.id_ajuste_det,
                                                v_registros.nro_tramite,
                                                NULL,
                                                v_registros_det.importe);



                         ------------------------
                         --  ACUMULAR ERRORES
                         -----------------------

                         --  analizamos respuesta y retornamos error
                         IF v_resultado_ges[1] = 0 THEN

                                 --  recuperamos datos del presupuesto
                                 v_mensaje_error = v_mensaje_error|| conta.f_armar_error_presupuesto(v_resultado_ges,
                                                                               v_registros_det.id_presupuesto,
                                                                               v_registros_det.codigo_partida,
                                                                               v_id_moneda_base,
                                                                               v_id_moneda_base,
                                                                               'reformulado',
                                                                               v_registros_det.importe);
                                 v_sw_error = true;

                          ELSE
                                   -- sino se tiene error almacenamos el id de la aprtida ejecucion
                                   update pre.tajuste_det a set
                                           id_partida_ejecucion = v_resultado_ges[2],
                                           fecha_mod = now(),
                                           id_usuario_mod = p_id_usuario
                                   where a.id_ajuste_det  =  v_registros_det.id_ajuste_det;


                          END IF; --fin id de error

                 END LOOP;

                  IF v_sw_error THEN
                    if v_columna_origen in ('id_solicitud_compra','id_obligacion_pago') and v_id_partida_ejecucion is not null then
                      raise exception 'Estimado Usuario:<br> La disminución del proceso % supera al formulado del presupuesto %',v_registros.nro_tramite, v_mensaje_error;
                    else
                      raise exception 'Error al reformular presupuesto: %', v_mensaje_error;
                    end if;
                  END IF;

             END IF;

             IF sw_incremento  and (not v_sw_error) THEN


                 --listar incrementos
                 FOR v_registros_det in (
                                        select
                                          ad.id_ajuste_det,
                                          ad.id_presupuesto,
                                          ad.id_partida,
                                          ad.importe ,
                                          par.codigo as codigo_partida,
                                          pre.estado as estado_presupuesto
                                        from pre.tajuste_det ad
                                        inner join pre.tpresupuesto pre on pre.id_presupuesto = ad.id_presupuesto
                                        inner join pre.tpartida par on par.id_partida = ad.id_partida
                                        where ad.id_ajuste = v_registros.id_ajuste
                                              and ad.tipo_ajuste = 'incremento'
                                              and ad.estado_reg = 'activo'
                                       ) LOOP



                        -- valida estado del presupuesto
                        IF v_registros_det.estado_presupuesto != 'aprobado'  THEN
                          raise exception 'el pesupuesto id %, no esta aprobado', v_registros_det.id_presupuesto;
                        END IF;

                        -- registras decrementos
                        v_resultado_ges = pre.f_gestionar_presupuesto_individual(
                                                p_id_usuario,
                                                NULL,
                                                v_registros_det.id_presupuesto,
                                                v_registros_det.id_partida,
                                                v_id_moneda_base,
                                                v_registros_det.importe,
                                                v_registros.fecha,
                                                'formulado', --traducido a varchar
                                                NULL, --p_id_partida_ejecucion
                                                'id_ajuste_det',
                                                v_registros_det.id_ajuste_det,
                                                v_registros.nro_tramite,
                                                NULL,
                                                v_registros_det.importe);



                         ------------------------
                         --  ACUMULAR ERRORES
                         ------------------------

                         --  analizamos respuesta y retornamos error
                         IF v_resultado_ges[1] = 0 THEN

                                 --  recuperamos datos del presupuesto
                                 v_mensaje_error = v_mensaje_error|| conta.f_armar_error_presupuesto(v_resultado_ges,
                                                                               v_registros_det.id_presupuesto,
                                                                               v_registros_det.codigo_partida,
                                                                               v_registros.id_moneda,
                                                                               v_id_moneda_base,
                                                                               'reformulado',
                                                                               v_registros_det.importe);
                                 v_sw_error = true;

                          ELSE
                                   -- sino se tiene error almacenamos el id de la aprtida ejecucion
                                   update pre.tajuste_det a set
                                           id_partida_ejecucion = v_resultado_ges[2],
                                           fecha_mod = now(),
                                           id_usuario_mod = p_id_usuario
                                   where a.id_ajuste_det  =  v_registros_det.id_ajuste_det;

                         END IF; --fin id de error

                 END LOOP;

                  IF v_sw_error THEN
                    if v_columna_origen in ('id_solicitud_compra','id_obligacion_pago') and v_id_partida_ejecucion is not null then
                      raise exception 'Estimado Usuario:<br> El Incremento del proceso % supera al formulado del presupuesto %',v_registros.nro_tramite, v_mensaje_error;
                    else
                      raise exception 'Error al reformular presupuesto: %', v_mensaje_error;
                    end if;
                  END IF;

             END IF;

             ----------------------------------------
             -- Manejo de compromisos de presupuesto
             -----------------------------------------

              IF sw_comprometer or sw_revertir  THEN

                 --listar incrementos
                 FOR v_registros_det in (
                                        select
                                          ad.id_ajuste_det,
                                          ad.id_presupuesto,
                                          ad.id_partida,
                                          ad.importe ,
                                          par.codigo as codigo_partida,
                                          pre.estado as estado_presupuesto,
                                          ad.id_sol_origen
                                        from pre.tajuste_det ad
                                        inner join pre.tpresupuesto pre on pre.id_presupuesto = ad.id_presupuesto
                                        inner join pre.tpartida par on par.id_partida = ad.id_partida
                                        where ad.id_ajuste = v_registros.id_ajuste and
                                              case when sw_revertir then
                                                       ad.tipo_ajuste = 'decremento'
                                                   else
                                                       ad.tipo_ajuste = 'incremento'
                                                   end

                                              and ad.estado_reg = 'activo'
                                       ) LOOP


                        -- valida estado del presupuesto
                        IF v_registros_det.estado_presupuesto != 'aprobado'  THEN
                          raise exception 'el pesupuesto id %, no esta aprobado', v_registros_det.id_presupuesto;
                        END IF;

                        --Inicio->(franklin.espinoza)[25/6/2019]obtener partida_ejecucion
                          select tm.tipo_moneda, ta.id_moneda
                          into v_codigo, v_id_moneda
                          from pre.tajuste ta
                          inner join param.tmoneda tm on tm.id_moneda = ta.id_moneda
                          where ta.id_proceso_wf = p_id_proceso_wf;

                          if v_codigo = 'ref' or v_codigo = 'base' then

                          /*  select tpe.id_partida_ejecucion, tpe.columna_origen
                            into v_id_partida_ejecucion, v_columna_origen
                            from pre.tpartida_ejecucion tpe
                            where tpe.nro_tramite = v_registros.nro_tramite and tpe.id_presupuesto = v_registros_det.id_presupuesto and
                            	    tpe.id_partida = v_registros_det.id_partida and tpe.columna_origen in ('id_solicitud_compra','id_obligacion_pago') and
                                  tpe.id_partida_ejecucion_fk is null;*/

                            --adicion breydi.vasquez 30/11/2021 optener la id_partida_ejecucion segun el detalle de origen
                              select pe.id_partida_ejecucion, pe.columna_origen
                                  into v_id_partida_ejecucion, v_columna_origen
                              from tes.tobligacion_det odet
                              inner join pre.tpartida_ejecucion pe on pe.id_partida_ejecucion = odet.id_partida_ejecucion_com
                              where odet.id_obligacion_det = v_registros_det.id_sol_origen
                              and pe.id_partida_ejecucion_fk is null;

                            if v_id_partida_ejecucion is null then
                                    raise exception 'El proceso aun no fue certificado en la Unidad de Presupuestos.';
                            end if;

                            if v_id_partida_ejecucion is not null and  v_codigo = 'ref' then

                            	select tc.oficial
                                into v_oficial
                                from param.ttipo_cambio tc
                                where tc.id_moneda = v_id_moneda and tc.fecha = current_date;

                              v_monto_mb =  param.f_convertir_moneda (

                                 v_id_moneda,
                                 v_id_moneda_base,
                                 v_registros_det.importe,
                                 v_registros.fecha,
                                 'CUS',50,
                                 v_oficial, 'no');
                            end if;
                          end if;
                        --Fin->(franklin.espinoza)[25/6/2019]

						            if v_registros_det.importe != 0 then
                          -- registras decrementos
                          v_resultado_ges = pre.f_gestionar_presupuesto_individual(
                                                  p_id_usuario,
                                                  case when v_oficial is null then 1 else v_oficial end,
                                                  v_registros_det.id_presupuesto,
                                                  v_registros_det.id_partida,
                                                  v_registros.id_moneda,  --> moneda del ajuste
                                                  v_registros_det.importe,
                                                  v_registros.fecha,
                                                  'comprometido', --traducido a varchar
                                                  case when v_id_partida_ejecucion is null then NULL else v_id_partida_ejecucion end, --p_id_partida_ejecucion
                                                  'id_ajuste_det',
                                                  v_registros_det.id_ajuste_det,
                                                  v_registros.nro_tramite,
                                                  NULL,
                                                  case when v_id_partida_ejecucion is not null and  v_codigo = 'ref' then v_monto_mb else v_registros_det.importe end);


                           ------------------------
                           --  ACUMULAR ERRORES
                           -----------------------

                           --  analizamos respuesta y retornamos error
                           IF v_resultado_ges[1] = 0 THEN

                                   --  recuperamos datos del presupuesto
                                   v_mensaje_error = v_mensaje_error|| conta.f_armar_error_presupuesto(v_resultado_ges,
                                                                                 v_registros_det.id_presupuesto,
                                                                                 v_registros_det.codigo_partida,
                                                                                 case when v_codigo = 'ref' then v_registros.id_moneda else v_id_moneda_base end,--v_id_moneda_base,
                                                                                 v_id_moneda_base,
                                                                                 'Comprometer',
                                                                                 case when v_codigo = 'ref' then v_monto_mb else v_registros_det.importe end--v_registros_det.importe
                                                                                 );
                                   v_sw_error = true;

                            ELSE
                                     -- sino se tiene error almacenamos el id de la aprtida ejecucion
                                     update pre.tajuste_det a set
                                             id_partida_ejecucion = v_resultado_ges[2],
                                             fecha_mod = now(),
                                             id_usuario_mod = p_id_usuario
                                     where a.id_ajuste_det  =  v_registros_det.id_ajuste_det;

                           END IF; --fin id de error
                        end if;

                 END LOOP;

                  IF v_sw_error THEN
                    if v_columna_origen in ('id_solicitud_compra','id_obligacion_pago') and v_id_partida_ejecucion is not null then
                      raise exception 'Estimado Usuario:<br> El Incremento y/o Disminución del proceso % supera al formulado del presupuesto %',v_registros.nro_tramite, v_mensaje_error;
                    else
                      raise exception 'Error al reformular presupuesto: %', v_mensaje_error;
                    end if;
                  END IF;

             END IF;

             ----------------------------------------------
             --16-06-2021 (may)
             -- Manejo de compromisos de presupuesto AJUSTE
             ----------------------------------------------

              IF sw_ajuste  THEN

                 --registro previo de detalle obligacion de pago, para acualizar id_sol_origen, antes de finalizacion 
                 FOR v_registros_det in (
                                        select
                                          ad.id_ajuste_det,
                                          ad.id_presupuesto,
                                          ad.id_partida,
                                          ad.importe ,
                                          par.codigo as codigo_partida,
                                          pre.estado as estado_presupuesto,

                                          ad.descripcion,
                                          ad.id_concepto_ingas,
                                          ad.id_orden_trabajo,
                                          ad.id_sol_origen,
                                          ad.tabla_origen

                                        from pre.tajuste_det ad
                                        inner join pre.tpresupuesto pre on pre.id_presupuesto = ad.id_presupuesto
                                        inner join pre.tpartida par on par.id_partida = ad.id_partida
                                        where ad.id_ajuste = v_registros.id_ajuste and
                                        ad.tipo_ajuste = 'ajuste'
                                        and ad.estado_reg = 'activo'
                                       ) LOOP

						--
                        IF v_registros_det.descripcion = 'REGISTRO AUTOMATICO POR PRESUPUESTO' and v_registros_det.importe !=0 THEN

                        	--recupera id_obligacion_pago
                            SELECT op.id_obligacion_pago, op.tipo_cambio_conv
                            INTO v_id_obligacion_pag, v_tipo_cambio_op
                            FROM tes.tobligacion_pago op
                            WHERE op.num_tramite = v_registros.nro_tramite
                            and op.estado_reg = 'activo';

                            --tipo de cambio, calcula monto en moneda base
          					v_monto_mb = v_registros_det.importe * v_tipo_cambio_op;


                            --centro de costo
                            select pcc.id_centro_costo
                            into v_id_centro_costo
                            from pre.vpresupuesto_cc pcc
                            where pcc.id_presupuesto = v_registros_det.id_presupuesto;

                        	--Sentencia de la insercion PARTIDA_EJECUCION
                              insert into pre.tpartida_ejecucion(
                              id_int_comprobante,
                              id_moneda,
                              id_presupuesto,
                              id_partida,
                              nro_tramite,
                              tipo_cambio,
                              columna_origen,
                              tipo_movimiento,
                              id_partida_ejecucion_fk,
                              estado_reg,
                              fecha,
                              monto_mb,
                              monto,
                              valor_id_origen,
                              id_usuario_reg,
                              fecha_reg,
                              usuario_ai,
                              id_usuario_ai,
                              fecha_mod,
                              id_usuario_mod

                              ) values(

                              NULL,
                              v_registros.id_moneda,
                              v_registros_det.id_presupuesto,
                              v_registros_det.id_partida,
                              v_registros.nro_tramite,
                              NULL, --tipo_cambio,
                              'id_obligacion_pago', --v_parametros.columna_origen, ------
                              'comprometido', --tipo_movimiento,
                              NULL,--id_partida_ejecucion_fk,
                              'activo',
                              now(), --fecha,
                              0,
                              0,
                              v_id_obligacion_pag, --valor_id_origen,
                              p_id_usuario,
                              now(),
                              NULL,
                              NULL,
                              null,
                              null

                              )RETURNING id_partida_ejecucion into v_id_partida_ejecu;

                            --Sentencia de la insercion
                            insert into tes.tobligacion_det(
                              estado_reg,
                              --id_cuenta,
                              id_partida,
                              --id_auxiliar,
                              id_concepto_ingas,
                              monto_pago_mo,
                              id_obligacion_pago,
                              id_centro_costo,
                              monto_pago_mb,
                              descripcion,
                              fecha_reg,
                              id_usuario_reg,
                              fecha_mod,
                              id_usuario_mod,
                              id_orden_trabajo,
            				  id_partida_ejecucion_com

                            )values(
                              'activo',
                              --v_parametros.id_cuenta,
                              v_registros_det.id_partida,
                              --v_parametros.id_auxiliar,
                              v_registros_det.id_concepto_ingas,
                              0,
                              v_id_obligacion_pag,
                              v_id_centro_costo,
                              0,
                              v_registros_det.descripcion,
                              now(),
                              p_id_usuario,
                              null,
                              null,
                              v_registros_det.id_orden_trabajo,
            				  v_id_partida_ejecu

                              )RETURNING id_obligacion_det into v_id_obligacion_det;

							                update pre.tajuste_det a set
                              id_sol_origen = v_id_obligacion_det
                              where a.id_ajuste_det  =  v_registros_det.id_ajuste_det;


                        END IF;
                        ---
                      END LOOP;

                      --listar incrementos
                      FOR v_registros_det in (
                                        select
                                          ad.id_ajuste_det,
                                          ad.id_presupuesto,
                                          ad.id_partida,
                                          ad.importe ,
                                          par.codigo as codigo_partida,
                                          pre.estado as estado_presupuesto,                                          
                                          ad.descripcion,
                                          ad.id_concepto_ingas,
                                          ad.id_orden_trabajo,
                                          ad.id_sol_origen,
                                          ad.tabla_origen                                          
                                          
                                        from pre.tajuste_det ad
                                        inner join pre.tpresupuesto pre on pre.id_presupuesto = ad.id_presupuesto
                                        inner join pre.tpartida par on par.id_partida = ad.id_partida
                                        where ad.id_ajuste = v_registros.id_ajuste and
                                        ad.tipo_ajuste = 'ajuste'
                                        and ad.estado_reg = 'activo'
                                       ) LOOP
                        -- valida estado del presupuesto
                        IF v_registros_det.estado_presupuesto != 'aprobado'  THEN
                          raise exception 'el pesupuesto id %, no esta aprobado', v_registros_det.id_presupuesto;
                        END IF;

                        --Inicio->(franklin.espinoza)[25/6/2019]obtener partida_ejecucion
                          select tm.tipo_moneda, ta.id_moneda
                          into v_codigo, v_id_moneda
                          from pre.tajuste ta
                          inner join param.tmoneda tm on tm.id_moneda = ta.id_moneda
                          where ta.id_proceso_wf = p_id_proceso_wf;

                          if v_codigo = 'ref' or v_codigo = 'base' then

                            /*select tpe.id_partida_ejecucion, tpe.columna_origen
                            into v_id_partida_ejecucion, v_columna_origen
                            from pre.tpartida_ejecucion tpe
                            where tpe.nro_tramite = v_registros.nro_tramite and tpe.id_presupuesto = v_registros_det.id_presupuesto and
                            	    tpe.id_partida = v_registros_det.id_partida and tpe.columna_origen in ('id_solicitud_compra','id_obligacion_pago') and
                                  tpe.id_partida_ejecucion_fk is null;*/

							              --20-12-2021 (may) se aumenta para lo procesos de FA, ahora se distingue segun la columna tabla_origen
                            --para FA tabla_origen= cd.tcuenta_doc

                            IF (v_registros_det.tabla_origen= 'cd.tcuenta_doc') THEN

                              		select pe.id_partida_ejecucion, pe.columna_origen
                                    into v_id_partida_ejecucion, v_columna_origen
                                    from cd.tcuenta_doc_det cdet
                                    inner join pre.tpartida_ejecucion pe on pe.id_partida_ejecucion = cdet.id_partida_ejecucion
                                    where cdet.id_cuenta_doc_det = v_registros_det.id_sol_origen
                                    and pe.id_partida_ejecucion_fk is null;

                            ELSE IF (v_registros_det.tabla_origen= 'adq.tsolicitud') THEN

                              		select pe.id_partida_ejecucion, pe.columna_origen
                                    into v_id_partida_ejecucion, v_columna_origen
                                    from adq.tsolicitud_det sold
                                    inner join pre.tpartida_ejecucion pe on pe.id_partida_ejecucion = sold.id_partida_ejecucion
                                    where sold.id_solicitud_det = v_registros_det.id_sol_origen
                                    and pe.id_partida_ejecucion_fk is null;


                            ELSE

                                  /*  select tpe.id_partida_ejecucion, tpe.columna_origen
                                    into v_id_partida_ejecucion, v_columna_origen
                                    from pre.tpartida_ejecucion tpe
                                    where tpe.nro_tramite = v_registros.nro_tramite and tpe.id_presupuesto = v_registros_det.id_presupuesto and
                            	    tpe.id_partida = v_registros_det.id_partida and tpe.columna_origen in ('id_solicitud_compra','id_obligacion_pago') and
                                    tpe.id_partida_ejecucion_fk is null;*/

                            --adicion breydi.vasquez 03/12/2021 optener la id_partida_ejecucion segun el detalle de origen
                                    select pe.id_partida_ejecucion, pe.columna_origen
                                    into v_id_partida_ejecucion, v_columna_origen
                                    from tes.tobligacion_det odet
                                    inner join pre.tpartida_ejecucion pe on pe.id_partida_ejecucion = odet.id_partida_ejecucion_com
                                    where odet.id_obligacion_det = v_registros_det.id_sol_origen
                                    and pe.id_partida_ejecucion_fk is null;

                             END IF;
                            END IF;


                            if v_id_partida_ejecucion is null then
                                    raise exception 'El proceso aun no fue certificado en la Unidad de Presupuestos.';
                            end if;

                            if v_id_partida_ejecucion is not null and  v_codigo = 'ref' then

                            	select tc.oficial
                                into v_oficial
                                from param.ttipo_cambio tc
                                where tc.id_moneda = v_id_moneda and tc.fecha = current_date;

                              v_monto_mb =  param.f_convertir_moneda (

                                 v_id_moneda,
                                 v_id_moneda_base,
                                 v_registros_det.importe,
                                 v_registros.fecha,
                                 'CUS',50,
                                 v_oficial, 'no');
                            end if;
                          end if;
                        --Fin->(franklin.espinoza)[25/6/2019]

						if v_registros_det.importe != 0 then
                          -- registras decrementos
                          v_resultado_ges = pre.f_gestionar_presupuesto_individual(
                                                  p_id_usuario,
                                                  case when v_oficial is null then 1 else v_oficial end,
                                                  v_registros_det.id_presupuesto,
                                                  v_registros_det.id_partida,
                                                  v_registros.id_moneda,  --> moneda del ajuste
                                                  v_registros_det.importe,
                                                  v_registros.fecha,
                                                  'comprometido', --traducido a varchar
                                                  case when v_id_partida_ejecucion is null then NULL else v_id_partida_ejecucion end, --p_id_partida_ejecucion
                                                  'id_ajuste_det',
                                                  v_registros_det.id_ajuste_det,
                                                  v_registros.nro_tramite,
                                                  NULL,
                                                  case when v_id_partida_ejecucion is not null and  v_codigo = 'ref' then v_monto_mb else v_registros_det.importe end);


                           ------------------------
                           --  ACUMULAR ERRORES
                           -----------------------

                           --  analizamos respuesta y retornamos error
                           IF v_resultado_ges[1] = 0 THEN

                                   --  recuperamos datos del presupuesto
                                   v_mensaje_error = v_mensaje_error|| conta.f_armar_error_presupuesto(v_resultado_ges,
                                                                                 v_registros_det.id_presupuesto,
                                                                                 v_registros_det.codigo_partida,
                                                                                 case when v_codigo = 'ref' then v_registros.id_moneda else v_id_moneda_base end,--v_id_moneda_base,
                                                                                 v_id_moneda_base,
                                                                                 'Comprometer',
                                                                                 case when v_codigo = 'ref' then v_monto_mb else v_registros_det.importe end--v_registros_det.importe
                                                                                 );
                                   v_sw_error = true;

                            ELSE
                                     -- sino se tiene error almacenamos el id de la aprtida ejecucion
                                     update pre.tajuste_det a set
                                             id_partida_ejecucion = v_resultado_ges[2],
                                             fecha_mod = now(),
                                             id_usuario_mod = p_id_usuario
                                     where a.id_ajuste_det  =  v_registros_det.id_ajuste_det;

                           END IF; --fin id de error
                        end if;

                 END LOOP;

				IF v_sw_error THEN
                  if v_columna_origen in ('id_solicitud_compra','id_obligacion_pago', 'id_cuenta_doc') and v_id_partida_ejecucion is not null then
                    raise exception 'Estimado Usuario:<br> El Incremento y Disminucion del proceso % supera al formulado del presupuesto %',v_registros.nro_tramite, v_mensaje_error;
                  else
                    raise exception 'Error al reformular presupuesto: %', v_mensaje_error;
                  end if;
                END IF;
             END IF;


			/* 17-06-2021 (may) mensaje se separa para los distintos tipos
            IF v_sw_error THEN
              if v_columna_origen in ('id_solicitud_compra','id_obligacion_pago') and v_id_partida_ejecucion is not null then
                raise exception 'Estimado Usuario:<br> El incremento del proceso % supera al formulado del presupuesto %',v_registros.nro_tramite, v_mensaje_error;
              else
                raise exception 'Error al reformular presupuesto: %', v_mensaje_error;
              end if;
            END IF;*/
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