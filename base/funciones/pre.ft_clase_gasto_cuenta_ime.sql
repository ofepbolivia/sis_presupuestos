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

    --clonar cuentas franklin.espinoza 10/01/2021
    v_registros				record;
    v_registros_ges			record;
    v_id_gestion_destino	integer;
    v_cuenta_dos			integer;

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

    /*********************************
 	#TRANSACCION:  'PRE_CLOCUE_IME'
 	#DESCRIPCION:	Clonacion de Cuentas Contables a partir de una clase de gasto
 	#AUTOR:		franklin.espinoza
 	#FECHA:		11-01-2021 10:00:23
	***********************************/

	elsif(p_transaccion='PRE_CLOCUE_IME')then
		begin
             --  definir id de la gestion siguiente
        	select ges.id_gestion, ges.gestion, ges.id_empresa
        	into v_registros_ges
           	from param.tgestion ges
           	where ges.id_gestion = v_parametros.id_gestion;

           	select ges.id_gestion
           	into v_id_gestion_destino
           	from param.tgestion ges
           	where ges.gestion = v_registros_ges.gestion + 1 and ges.id_empresa = v_registros_ges.id_empresa and ges.estado_reg = 'activo';

          	IF v_id_gestion_destino is null THEN
          		raise exception 'no se encontró una siguiente gestión preparada (primero cree  gestión siguiente)';
          	END IF;
          	--recuperar informacion de las partidas


          	for v_registros in  select cgc.id_cuenta, p.id_gestion, cgc.id_clase_gasto
                              	from pre.tclase_gasto_cuenta cgc
                              	inner join  conta.tcuenta p on p.id_cuenta = cgc.id_cuenta
                              	where  p.id_gestion = v_parametros.id_gestion and cgc.id_clase_gasto = v_parametros.id_clase_gasto loop

          		--buscamos si existe la relacion de cuentas para la siguiente gestion
               if exists (select 1
                          from conta.tcuenta_ids
                          where id_cuenta_uno = v_registros.id_cuenta) then

                          --encontramos el id_cuenta de la gestion siguiente
                          select cid.id_cuenta_dos
                          into v_cuenta_dos
                          from conta.tcuenta_ids cid
                          where cid.id_cuenta_uno = v_registros.id_cuenta;

                          --si no existe registrado la relacion de partidas con clases de gasto realizamos la insercion en la tabla
                          if not exists ( select 1
                                          from pre.tclase_gasto_cuenta cgc
                                          inner join  conta.tcuenta c on c.id_cuenta = cgc.id_cuenta
                                          where cgc.id_clase_gasto = v_registros.id_clase_gasto and cgc.id_cuenta =v_cuenta_dos )then

                          	insert into pre.tclase_gasto_cuenta(
                            	id_usuario_reg,
                                id_usuario_mod,
                                fecha_reg,
                                fecha_mod,
                                estado_reg,
                                id_usuario_ai,
                                usuario_ai,
                                id_clase_gasto,
                                id_cuenta
                            )values(
                            	p_id_usuario,
                                null,
                                now(),
                                null,
                                'activo',
                                null,
                                null,
                                v_registros.id_clase_gasto,
                                v_cuenta_dos
                            );
                          end if;
               end if;


          end loop;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','cuentas clonadas');
            v_resp = pxp.f_agrega_clave(v_resp,'gestion destino',v_id_gestion_destino::varchar);

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

ALTER FUNCTION pre.ft_clase_gasto_cuenta_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;