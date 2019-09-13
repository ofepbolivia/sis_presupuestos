CREATE OR REPLACE FUNCTION pre.ft_memoria_det_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Presupuesto
 FUNCION: 		pre.ft_memoria_det_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'pre.tmemoria_det'
 AUTOR: 		 (admin)
 FECHA:	        01-03-2016 14:23:08
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:
 AUTOR:
 FECHA:
***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_memoria_det		integer;
    v_registros				record;
    v_registros_mem			record;
    v_importe				numeric;
    v_estado		        varchar;

    v_techo_importe			numeric;
    v_total_total			numeric;
    v_suma					numeric;
    v_resta					numeric;
    v_exceso				numeric;

BEGIN

    v_nombre_funcion = 'pre.ft_memoria_det_ime';
    v_parametros = pxp.f_get_record(p_tabla);



	/*********************************
 	#TRANSACCION:  'PRE_MDT_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		admin
 	#FECHA:		01-03-2016 14:23:08
	***********************************/

	if(p_transaccion='PRE_MDT_MOD')then

		begin
			--raise exception 'hola: %',v_parametros;
            select
              md.id_memoria_det,
              md.importe
            into
              v_registros
            from pre.tmemoria_det md
            where md.id_memoria_det = v_parametros.id_memoria_det;

            select
              mc.id_memoria_calculo,
              mc.importe_total,
              mc.id_presupuesto
            into
              v_registros_mem
            from pre.tmemoria_calculo mc
            where mc.id_memoria_calculo = v_parametros.id_memoria_calculo;
        --raise exception 'importe  %',v_registros_mem.importe_total;

            select
              pre.estado
            into
              v_estado
            from pre.tpresupuesto pre
            inner join param.tcentro_costo cc on cc.id_centro_costo = pre.id_centro_costo
            inner join param.tgestion ges on ges.id_gestion = cc.id_gestion
            where pre.id_presupuesto = v_registros_mem.id_presupuesto;

            --raise exception 'v_gestion %', v_gestion;

            IF v_estado = 'aprobado' THEN
               raise exception 'No puede agregar conceptos a la memoria de calculo de un presupuesto aprobado';
            END IF;


            --Sentencia de la modificacion
			update pre.tmemoria_det set
              importe = v_parametros.importe,
              id_periodo = v_parametros.id_periodo,
              id_memoria_calculo = v_parametros.id_memoria_calculo,
              fecha_mod = now(),
              id_usuario_mod = p_id_usuario,
              id_usuario_ai = v_parametros._id_usuario_ai,
              usuario_ai = v_parametros._nombre_usuario_ai,
              cantidad_mem = v_parametros.cantidad_mem,
              unidad_medida = v_parametros.unidad_medida,
              importe_unitario = v_parametros.importe_unitario
			where id_memoria_det = v_parametros.id_memoria_det;


            --v_importe = v_registros_mem.importe_total - v_registros.importe  + v_parametros.importe;
            select sum(mdet.importe)
            	into v_importe
            from pre.tmemoria_det mdet
            where mdet.id_memoria_calculo = v_parametros.id_memoria_calculo;
        --raise exception '%, %, %, %', v_importe,v_registros_mem.importe_total, v_registros.importe, v_parametros.importe ;

         --control de techo presupuestario
           SELECT
             tecpre.importe_techo_presupuesto
           INTO
             v_techo_importe
           FROM pre.ttecho_presupuestos tecpre
           join pre.tpresupuesto pres on pres.id_presupuesto = tecpre.id_presupuesto
           join pre.tmemoria_calculo mem on mem.id_presupuesto = pres.id_presupuesto
           WHERE tecpre.estado_techo_presupuesto = 'Activo'
           and mem.id_memoria_calculo = v_parametros.id_memoria_calculo;


            SELECT
              sum(mc.importe_total)
            INTO
              v_total_total
            FROM pre.tmemoria_calculo mc
            JOIN pre.tpresupuesto pre on pre.id_presupuesto = mc.id_presupuesto
            WHERE pre.id_presupuesto = v_registros_mem.id_presupuesto;
	--raise exception 'importe Total %',v_total_total;

    	--v_total_resta = v_total_total-(v_importe_tot - v_total_memoria_uni);
        --v_importe = v_registros_mem.importe_total - v_registros.importe  + v_parametros.importe;
            v_resta = v_importe - v_registros_mem.importe_total;
            v_suma = v_total_total + v_resta;

    v_exceso = v_suma - v_techo_importe;
    --raise exception '%, %, %, %, %',v_resta,v_suma, v_importe, v_registros_mem.importe_total, v_total_total;
    	 IF (v_techo_importe < v_suma )THEN
          	raise exception 'YA ESTA AL TOPE DE SU TECHO PRESUPUESTARIO,TOTAL IMPORTE:% , TECHO PRESUPUESTARIO: % , EXCESO: %', v_suma, v_techo_importe, v_exceso;
         ELSE

         -- raise exception '%, %, %',v_importe, v_parametros.id_memoria_calculo, v_registros_mem.id_presupuesto;

             update pre.tmemoria_calculo set
                  importe_total = v_importe
             where  id_memoria_calculo = v_parametros.id_memoria_calculo;
         end if;


			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Detalle de Memoria modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_memoria_det',v_parametros.id_memoria_det::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	else

    	raise exception 'Transaccion inexistente: %',p_transaccion;

	end if;

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