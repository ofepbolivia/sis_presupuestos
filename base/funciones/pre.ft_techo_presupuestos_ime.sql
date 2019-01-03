CREATE OR REPLACE FUNCTION pre.ft_techo_presupuestos_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Presupuesto
 FUNCION: 		pre.ft_techo_presupuestos_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'pre.ttecho_presupuestos'
 AUTOR: 		 (admin)
 FECHA:	        09-07-2018 18:45:47
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				09-07-2018 18:45:47								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'pre.ttecho_presupuestos'
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_techo_presupuesto	integer;

    v_id_presupuesto		integer;
    v_total_total			NUMERIC;


BEGIN

    v_nombre_funcion = 'pre.ft_techo_presupuestos_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'PRE_TECPRE_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		admin
 	#FECHA:		09-07-2018 18:45:47
	***********************************/

	if(p_transaccion='PRE_TECPRE_INS')then

    begin
        --para el estado
            update pre.ttecho_presupuestos  SET
            estado_techo_presupuesto = 'Inactivo'
            WHERE estado_techo_presupuesto = 'Activo'
            AND id_presupuesto = v_parametros.id_presupuesto;

        --controla que no sea un presupuesto menor si hay cambios
           SELECT
              sum(mc.importe_total)
            INTO
              v_total_total
            FROM pre.tmemoria_calculo mc
            JOIN pre.tpresupuesto pre on pre.id_presupuesto = mc.id_presupuesto
            WHERE pre.id_presupuesto = v_parametros.id_presupuesto;

          	 IF(v_total_total > v_parametros.importe_techo_presupuesto) THEN
             	RAISE EXCEPTION 'EL TECHO PRESUPUESTARIO % QUE INTENTA REGISTRAR ES MENOR AL TOTAL DEL PRESUPUESTO ACTUALMENTE REGISTRADO: %',v_parametros.importe_techo_presupuesto,v_total_total;
        	 END IF;


        	--Sentencia de la insercion
        	insert into pre.ttecho_presupuestos(
			estado_reg,
			importe_techo_presupuesto,
			observaciones,
			id_presupuesto,
			estado_techo_presupuesto,
			id_usuario_reg,
			fecha_reg,
			id_usuario_ai,
			usuario_ai,
			id_usuario_mod,
			fecha_mod
          	) values(
			'activo',
			v_parametros.importe_techo_presupuesto,
			v_parametros.observaciones,
			v_parametros.id_presupuesto,
			'Activo',
			p_id_usuario,
			now(),
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			null,
			null

			)RETURNING id_techo_presupuesto into v_id_techo_presupuesto;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Techo Presupuestario almacenado(a) con exito (id_techo_presupuesto'||v_id_techo_presupuesto||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_techo_presupuesto',v_id_techo_presupuesto::varchar);

            --Devuelve la respuesta
            return v_resp;
   		end;

	/*********************************
 	#TRANSACCION:  'PRE_TECPRE_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		admin
 	#FECHA:		09-07-2018 18:45:47
	***********************************/

	elsif(p_transaccion='PRE_TECPRE_MOD')then

		begin
			--Sentencia de la modificacion
			update pre.ttecho_presupuestos set
			importe_techo_presupuesto = v_parametros.importe_techo_presupuesto,
			observaciones = v_parametros.observaciones,
			id_presupuesto = v_parametros.id_presupuesto,
			estado_techo_presupuesto = v_parametros.estado_techo_presupuesto,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_techo_presupuesto=v_parametros.id_techo_presupuesto;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Techo Presupuestario modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_techo_presupuesto',v_parametros.id_techo_presupuesto::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'PRE_TECPRE_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		admin
 	#FECHA:		09-07-2018 18:45:47
	***********************************/

	elsif(p_transaccion='PRE_TECPRE_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from pre.ttecho_presupuestos
            where id_techo_presupuesto=v_parametros.id_techo_presupuesto;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Techo Presupuestario eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_techo_presupuesto',v_parametros.id_techo_presupuesto::varchar);

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