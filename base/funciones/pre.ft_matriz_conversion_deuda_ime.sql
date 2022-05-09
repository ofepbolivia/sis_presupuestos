CREATE OR REPLACE FUNCTION pre.ft_matriz_conversion_deuda_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Presupuesto
 FUNCION: 		pre.ft_matriz_conversion_deuda_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'pre.tmatriz_conversion_deuda'
 AUTOR: 		 (ismael.valdivia)
 FECHA:	        30-11-2021 18:07:32
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				30-11-2021 18:07:32								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'pre.tmatriz_conversion_deuda'
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_matriz_conversion	integer;

BEGIN

    v_nombre_funcion = 'pre.ft_matriz_conversion_deuda_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'PRE_macon_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		ismael.valdivia
 	#FECHA:		30-11-2021 18:07:32
	***********************************/

	if(p_transaccion='PRE_macon_INS')then

        begin
        	--Sentencia de la insercion
        	insert into pre.tmatriz_conversion_deuda(
			estado_reg,
			id_gestion_origen,
			id_partida_origen,
			id_partida_destino,
			id_gestion_destino,
			id_usuario_reg,
			fecha_reg,
			id_usuario_ai,
			usuario_ai,
			id_usuario_mod,
			fecha_mod
          	) values(
			'activo',
			v_parametros.id_gestion_origen,
			v_parametros.id_partida_origen,
			v_parametros.id_partida_destino,
			v_parametros.id_gestion_destino,
			p_id_usuario,
			now(),
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			null,
			null



			)RETURNING id_matriz_conversion into v_id_matriz_conversion;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Matriz de conversion deuda almacenado(a) con exito (id_matriz_conversion'||v_id_matriz_conversion||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_matriz_conversion',v_id_matriz_conversion::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'PRE_macon_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		ismael.valdivia
 	#FECHA:		30-11-2021 18:07:32
	***********************************/

	elsif(p_transaccion='PRE_macon_MOD')then

		begin
			--Sentencia de la modificacion
			update pre.tmatriz_conversion_deuda set
			id_gestion_origen = v_parametros.id_gestion_origen,
			id_partida_origen = v_parametros.id_partida_origen,
			id_partida_destino = v_parametros.id_partida_destino,
			id_gestion_destino = v_parametros.id_gestion_destino,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_matriz_conversion=v_parametros.id_matriz_conversion;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Matriz de conversion deuda modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_matriz_conversion',v_parametros.id_matriz_conversion::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'PRE_macon_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		ismael.valdivia
 	#FECHA:		30-11-2021 18:07:32
	***********************************/

	elsif(p_transaccion='PRE_macon_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from pre.tmatriz_conversion_deuda
            where id_matriz_conversion=v_parametros.id_matriz_conversion;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Matriz de conversion deuda eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_matriz_conversion',v_parametros.id_matriz_conversion::varchar);

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

ALTER FUNCTION pre.ft_matriz_conversion_deuda_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
