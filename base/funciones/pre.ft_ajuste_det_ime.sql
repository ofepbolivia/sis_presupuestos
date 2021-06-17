CREATE OR REPLACE FUNCTION pre.ft_ajuste_det_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Presupuesto
 FUNCION: 		pre.ft_ajuste_det_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'pre.tajuste_det'
 AUTOR: 		 (admin)
 FECHA:	        13-04-2016 13:51:41
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
	v_id_ajuste_det	integer;
	--(franklin.espinoza)variables total detalle
    v_importe_total			numeric;

    --11-06-2021(may)
    v_registros_cig			varchar;
    v_ajuste				record;
    v_tipo_obligacion		varchar;
    v_relacion				varchar;
    v_id_partida			integer;
    v_id_centro_costo		integer;

BEGIN

    v_nombre_funcion = 'pre.ft_ajuste_det_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'PRE_AJD_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		admin
 	#FECHA:		13-04-2016 13:51:41
	***********************************/

	if(p_transaccion='PRE_AJD_INS')then

        begin

			--16-06-2021 (may) para tipo AJUSTE estaran con incremento y decremento
			IF v_parametros.tipo_ajuste != 'ajuste' THEN
                IF v_parametros.tipo_ajuste = 'incremento' THEN
                  IF v_parametros.importe <= 0 THEN
                       RAISE EXCEPTION 'en incrementos el importe tiene que ser mayor a cero';
                  END IF;

                ELSE
                  IF v_parametros.importe >= 0 THEN
                       RAISE EXCEPTION 'en decrementos el importe tiene que ser menor a cero';
                  END IF;
                END IF;
            END IF;

            v_id_partida = v_parametros.id_partida;

            IF v_parametros.descripcion = 'REGISTRO AUTOMATICO POR PRESUPUESTO' THEN
                --11-06-2021 (may) registro PARTIDA segun el concepto de gasto
                select cig.desc_ingas
                into v_registros_cig
                from param.tconcepto_ingas cig
                where cig.id_concepto_ingas = v_parametros.id_concepto_ingas;

                --tipo_obligacion
                select a.nro_tramite, a.id_gestion
                into v_ajuste
                from pre.tajuste a
                where a.id_ajuste = v_parametros.id_ajuste;

                select op.tipo_obligacion
                into v_tipo_obligacion
                from tes.tobligacion_pago op
                where op.num_tramite = v_ajuste.nro_tramite;

                --centro de costo
                select pcc.id_centro_costo
                into v_id_centro_costo
                from pre.vpresupuesto_cc pcc
                where pcc.id_presupuesto = v_parametros.id_presupuesto;

                --(may) el tipo de obligacion pago_especial_spi son para las estaciones internacionales SIP
                IF v_tipo_obligacion = 'pago_especial' or v_tipo_obligacion = 'pago_especial_spi' THEN
                    v_relacion = 'PAGOES';
                ELSE
                    v_relacion = 'CUECOMP';
                END IF;

                SELECT ps_id_partida
                into v_id_partida
                FROM conta.f_get_config_relacion_contable(v_relacion, v_ajuste.id_gestion, v_parametros.id_concepto_ingas, v_id_centro_costo, 'No se encontro relación contable para el concepto de gasto: '||v_registros_cig||'. <br> Mensaje: ');


                IF v_id_partida is NULL THEN
                    raise exception 'no se tiene una parametrización de partida  para este concepto de gasto en la relación contable de código  (%,%,%,%)','CUECOMP', v_relacion, v_parametros.id_concepto_ingas, v_id_centro_costo;
                END IF;
           END IF;
            --

        	--Sentencia de la insercion
        	insert into pre.tajuste_det(
                id_presupuesto,
    			importe,
                id_partida,
                estado_reg,
                tipo_ajuste,
                id_usuario_ai,
                fecha_reg,
                usuario_ai,
                id_usuario_reg,
                fecha_mod,
                id_usuario_mod,
                id_ajuste,
                --11-06-2021 (may) se aumenta campos de registro
                id_orden_trabajo,
                id_concepto_ingas,
                descripcion

          	) values(
                v_parametros.id_presupuesto,
                v_parametros.importe,
                v_id_partida,
                'activo',
                v_parametros.tipo_ajuste,
                v_parametros._id_usuario_ai,
                now(),
                v_parametros._nombre_usuario_ai,
                p_id_usuario,
                null,
                null,
                v_parametros.id_ajuste,
                --11-06-2021 (may) se aumenta campos de registro
                v_parametros.id_orden_trabajo,
                v_parametros.id_concepto_ingas,
                v_parametros.descripcion

		  )RETURNING id_ajuste_det into v_id_ajuste_det;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Detalle del Ajuste almacenado(a) con exito (id_ajuste_det'||v_id_ajuste_det||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_ajuste_det',v_id_ajuste_det::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'PRE_AJD_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		admin
 	#FECHA:		13-04-2016 13:51:41
	***********************************/

	elsif(p_transaccion='PRE_AJD_MOD')then

		begin
			--Sentencia de la modificacion
			update pre.tajuste_det set
			id_presupuesto = v_parametros.id_presupuesto,
			importe = v_parametros.importe,
			id_partida = v_parametros.id_partida,
			tipo_ajuste = v_parametros.tipo_ajuste,
			fecha_mod = now(),
			id_usuario_mod = p_id_usuario,
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
            where id_ajuste_det=v_parametros.id_ajuste_det;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Detalle del Ajuste modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_ajuste_det',v_parametros.id_ajuste_det::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'PRE_AJD_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		admin
 	#FECHA:		13-04-2016 13:51:41
	***********************************/

	elsif(p_transaccion='PRE_AJD_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from pre.tajuste_det
            where id_ajuste_det=v_parametros.id_ajuste_det;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Detalle del Ajuste eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_ajuste_det',v_parametros.id_ajuste_det::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;
    /*********************************
 	#TRANSACCION:  'PRE_AJD_IMP_TOT_IME'
 	#DESCRIPCION:	Listado del importe total de los detalles
 	#AUTOR:		franklin.espinoza
 	#FECHA:		13-04-2016 13:21:12
	***********************************/

	elsif(p_transaccion='PRE_AJD_IMP_TOT_IME')then

		begin
            select sum(tad.importe)
            into v_importe_total
            from pre.tajuste ta
            inner join pre.tajuste_det tad on tad.id_ajuste = ta.id_ajuste
            where ta.id_ajuste = v_parametros.id_ajuste;

			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se recupera con exito el importe del detalle)');
            v_resp = pxp.f_agrega_clave(v_resp,'importe_total',v_importe_total::varchar);

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