CREATE OR REPLACE FUNCTION pre.ft_clase_gasto_cuenta_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Presupuesto
 FUNCION: 		pre.ft_clase_gasto_cuenta_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'pre.tclase_gasto_partida'
 AUTOR: 		Maylee Perez Pastor
 FECHA:	        22-08-2019 02:33:23
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
	v_id_clase_gasto_cuenta	integer;

BEGIN

    v_nombre_funcion = 'pre.ft_clase_gasto_cuenta_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'PRE_CGCU_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		admin
 	#FECHA:		26-02-2016 02:33:23
	***********************************/

	if(p_transaccion='PRE_CGCU_INS')then

        begin
        	--Sentencia de la insercion
        	insert into pre.tclase_gasto_cuenta(
			id_cuenta,
			estado_reg,
			id_clase_gasto,
			id_usuario_ai,
			usuario_ai,
			fecha_reg,
			id_usuario_reg,
			id_usuario_mod,
			fecha_mod
          	) values(
			v_parametros.id_cuenta,
			'activo',
			v_parametros.id_clase_gasto,
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			now(),
			p_id_usuario,
			null,
			null



			)RETURNING id_clase_gasto_cuenta into v_id_clase_gasto_cuenta;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','CGPR almacenado(a) con exito (id_clase_gasto_cuenta'||v_id_clase_gasto_cuenta||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_clase_gasto_cuenta',v_id_clase_gasto_cuenta::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'PRE_CGCU_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		admin
 	#FECHA:		26-02-2016 02:33:23
	***********************************/

	elsif(p_transaccion='PRE_CGCU_MOD')then

		begin
			--Sentencia de la modificacion
			update pre.tclase_gasto_cuenta set
			id_cuenta = v_parametros.id_cuenta,
			id_clase_gasto = v_parametros.id_clase_gasto,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_clase_gasto_cuenta=v_parametros.id_clase_gasto_cuenta;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','CGPR modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_clase_gasto_cuenta',v_parametros.id_clase_gasto_cuenta::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'PRE_CGCU_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		admin
 	#FECHA:		26-02-2016 02:33:23
	***********************************/

	elsif(p_transaccion='PRE_CGCU_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from pre.tclase_gasto_cuenta
            where id_clase_gasto_cuenta=v_parametros.id_clase_gasto_cuenta;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','CGPR eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_clase_gasto_cuenta',v_parametros.id_clase_gasto_cuenta::varchar);

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
