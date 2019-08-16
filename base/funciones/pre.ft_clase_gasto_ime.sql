CREATE OR REPLACE FUNCTION pre.ft_clase_gasto_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Presupuesto
 FUNCION: 		pre.ft_clase_gasto_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'pre.tclase_gasto'
 AUTOR: 		 (admin)
 FECHA:	        26-02-2016 01:22:22
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
	v_id_clase_gasto	integer;

    v_codigo				varchar;
    v_tipo_clase			varchar;

BEGIN

    v_nombre_funcion = 'pre.ft_clase_gasto_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'PRE_CLG_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		admin
 	#FECHA:		26-02-2016 01:22:22
	***********************************/

	if(p_transaccion='PRE_CLG_INS')then

        begin
        	--Sentencia de la insercion
        	insert into pre.tclase_gasto(
			estado_reg,
			nombre,
			codigo,
			id_usuario_reg,
			usuario_ai,
			fecha_reg,
			id_usuario_ai,
			id_usuario_mod,
			fecha_mod,
            tipo_clase
          	) values(
			'Activo',
			upper(v_parametros.nombre),
			v_parametros.codigo,
			p_id_usuario,
			v_parametros._nombre_usuario_ai,
			now(),
			v_parametros._id_usuario_ai,
			null,
			null,
            v_parametros.tipo_clase

			)RETURNING id_clase_gasto into v_id_clase_gasto;

            --control para no repetir codigo y el tipo de clase de gasto



              IF exists (SELECT 1
                        FROM pre.tclase_gasto a
                        WHERE a.nombre <> upper(v_parametros.nombre)
                        and a.tipo_clase = v_parametros.tipo_clase
                        and a.codigo = v_parametros.codigo
                      ) THEN

                  raise exception 'Ya existe el Código % con el Tipo de Clase de Gasto %', v_parametros.codigo, v_parametros.tipo_clase ;

              END IF;


			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','CLASE almacenado(a) con exito (id_clase_gasto'||v_id_clase_gasto||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_clase_gasto',v_id_clase_gasto::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'PRE_CLG_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		admin
 	#FECHA:		26-02-2016 01:22:22
	***********************************/

	elsif(p_transaccion='PRE_CLG_MOD')then

		begin
			--Sentencia de la modificacion
			update pre.tclase_gasto set
			nombre = v_parametros.nombre,
			codigo = v_parametros.codigo,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai,
            tipo_clase = v_parametros.tipo_clase
			where id_clase_gasto=v_parametros.id_clase_gasto;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','CLASE modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_clase_gasto',v_parametros.id_clase_gasto::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'PRE_CLG_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		admin
 	#FECHA:		26-02-2016 01:22:22
	***********************************/

	elsif(p_transaccion='PRE_CLG_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from pre.tclase_gasto
            where id_clase_gasto=v_parametros.id_clase_gasto;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','CLASE eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_clase_gasto',v_parametros.id_clase_gasto::varchar);

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
