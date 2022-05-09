CREATE OR REPLACE FUNCTION pre.ft_presupuesto_partida_entidad_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Presupuesto
 FUNCION: 		pre.ft_presupuesto_partida_entidad_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'pre.tpresupuesto_partida_entidad'
 AUTOR: 		 (franklin.espinoza)
 FECHA:	        21-07-2017 12:58:43
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
	v_id_presupuesto_partida_entidad	integer;
    v_id_gestion						integer;
    v_datos 				record;
    v_valid					boolean;
    v_gestion				integer;
    v_record_m				record;

    v_partida_dos			integer;
    v_presupuesto_dos		integer;
    v_entidad_dos			integer;

BEGIN

    v_nombre_funcion = 'pre.ft_presupuesto_partida_entidad_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'PRE_P_P_ENT_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		franklin.espinoza
 	#FECHA:		21-07-2017 12:58:43
	***********************************/

	if(p_transaccion='PRE_P_P_ENT_INS')then

        begin
        	--RAISE EXCEPTION 'V_PARAMETROS %',v_parametros;
        	--Sentencia de la insercion
        	insert into pre.tpresupuesto_partida_entidad(
			id_partida,
			id_gestion,
			id_entidad_transferencia,
			estado_reg,
			id_presupuesto,
			id_usuario_ai,
			id_usuario_reg,
			usuario_ai,
			fecha_reg,
			id_usuario_mod,
			fecha_mod
          	) values(
			v_parametros.id_partida,
			v_parametros.id_gestion,
			v_parametros.id_entidad_transferencia,
			'activo',
			v_parametros.id_presupuesto,
			v_parametros._id_usuario_ai,
			p_id_usuario,
			v_parametros._nombre_usuario_ai,
			now(),
			null,
			null



			)RETURNING id_presupuesto_partida_entidad into v_id_presupuesto_partida_entidad;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','PresuPartidaEntidad almacenado(a) con exito (id_presupuesto_partida_entidad'||v_id_presupuesto_partida_entidad||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_presupuesto_partida_entidad',v_id_presupuesto_partida_entidad::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'PRE_P_P_ENT_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		franklin.espinoza
 	#FECHA:		21-07-2017 12:58:43
	***********************************/

	elsif(p_transaccion='PRE_P_P_ENT_MOD')then

		begin
			--Sentencia de la modificacion
			update pre.tpresupuesto_partida_entidad set
			id_partida = v_parametros.id_partida,
			id_gestion = v_parametros.id_gestion,
			id_entidad_transferencia = v_parametros.id_entidad_transferencia,
			id_presupuesto = v_parametros.id_presupuesto,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_presupuesto_partida_entidad=v_parametros.id_presupuesto_partida_entidad;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','PresuPartidaEntidad modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_presupuesto_partida_entidad',v_parametros.id_presupuesto_partida_entidad::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'PRE_P_P_ENT_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		franklin.espinoza
 	#FECHA:		21-07-2017 12:58:43
	***********************************/

	elsif(p_transaccion='PRE_P_P_ENT_ELI')then

		begin
			--Sentencia de la eliminacion
			/*delete from pre.tpresupuesto_partida_entidad
            where id_presupuesto_partida_entidad=v_parametros.id_presupuesto_partida_entidad;*/
            update pre.tpresupuesto_partida_entidad  set
            	estado_reg = 'inactivo'
            where id_presupuesto_partida_entidad=v_parametros.id_presupuesto_partida_entidad;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','PresuPartidaEntidad eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_presupuesto_partida_entidad',v_parametros.id_presupuesto_partida_entidad::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;
	/*********************************
 	#TRANSACCION:  'PRE_P_P_ENT_VAL'
 	#DESCRIPCION:  Validacion de Relación de Entidades.
 	#AUTOR:		franklin.espinoza
 	#FECHA:		21-07-2017 12:57:45
	***********************************/

	elsif(p_transaccion='PRE_P_P_ENT_VAL')then

		begin
			--Sentencia de la eliminacion
			select count(tppe.id_presupuesto_partida_entidad) AS contador,
            'La relación de Entidades ya fue registrada.' AS mensaje
            INTO v_datos
            FROM pre.tpresupuesto_partida_entidad tppe
            WHERE tppe.id_partida = v_parametros.id_partida  AND tppe.id_entidad_transferencia = v_parametros.id_entidad_transferencia AND tppe.id_presupuesto =  v_parametros.id_presupuesto;


            IF(v_datos.contador>=1)THEN
        		v_valid = true;
            ELSE
            	v_valid = false;
			END IF;


            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Validacion Exitosa.');
            v_resp = pxp.f_agrega_clave(v_resp,'v_valid',v_valid::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'v_mensaje',v_datos.mensaje::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

 /*********************************
     #TRANSACCION:  'PRE_CLOPREPAREN_REP'
     #DESCRIPCION:    Clonar registros registros
     #AUTOR:        maylee.perez
     #FECHA:        25-08-2017 19:34:27
    ***********************************/

    elsif(p_transaccion='PRE_CLOPREPAREN_REP')then

begin
  /*
            *    v_parametros.id_gestion_maestro: gestion de la que se quiere copiar (origen).
            *    v_parametros.id_gestion: gestion a la que se quiere copiar (destino)
            */
  select gestion + 1
  into v_gestion
  FROM param.tgestion
  WHERE id_gestion = v_parametros.id_gestion;

 select id_gestion
  into v_id_gestion
  FROM param.tgestion
  WHERE gestion = v_gestion;
  --raise exception 'gestiones %, %',v_gestion,v_id_gestion;

  FOR v_record_m IN (SELECT ppe. id_gestion, ppe.id_entidad_transferencia, ppe.id_presupuesto, ppe.id_partida
					  FROM pre.tpresupuesto_partida_entidad ppe
					  where ppe.id_gestion = v_parametros.id_gestion)loop



  /*Preguntamos si existe un dato con el codigo y gestion duplkicados en la tabla entidad transferencia
  donde se si existe el valor nos devolvera true pero si no hay no devolvera nada, para eso uso el exist
   ya que se encarga de verificar si existe un dato porlo menos*/


  Select p.id_partida_dos
  into v_partida_dos
  from pre.tpartida_ids p
  where p.id_partida_uno=v_record_m.id_partida;



  Select pr.id_presupuesto_dos
  into v_presupuesto_dos
  from pre.tpresupuesto_ids pr
  where pr.id_presupuesto_uno=v_record_m.id_presupuesto;

  Select ent.id_entidad_dos
  into v_entidad_dos
  from pre.tentidad_transferencia_ids ent
  where ent.id_entidad_uno=v_record_m.id_entidad_transferencia;

  --validamos que exista la entidad destino migrada
  if v_entidad_dos is null then
  	RAISE EXCEPTION 'No existe la entidad con ID %, en la nueva gestion.',v_gestion;
    end if;



   IF not  EXISTS ( select 1 from  pre.tpresupuesto_partida_entidad
                     where id_gestion=v_id_gestion
                     and id_partida=v_partida_dos
                     and id_presupuesto=v_presupuesto_dos
                     and id_entidad_transferencia=v_entidad_dos
     				) then

   INSERT INTO pre.tpresupuesto_partida_entidad(
			id_gestion,
            id_partida,
			id_entidad_transferencia,
			estado_reg,
			id_presupuesto,
			id_usuario_ai,
			id_usuario_reg,
			usuario_ai,
			fecha_reg,
			id_usuario_mod,
			fecha_mod
          	) values(

			v_id_gestion,
            v_partida_dos,
			v_entidad_dos,
			'activo',
			v_presupuesto_dos,
			v_parametros._id_usuario_ai,
			p_id_usuario,
			v_parametros._nombre_usuario_ai,
			now(),
			null,
			null);
  ELSE

   RAISE EXCEPTION 'ESTIMADO USUARIO: LAS ENTIDADES DE TRANSFERENCIA YA FUERON REGISTRADOS PARA LA GESTION % ANTERIORMENTE',v_gestion ;


   end if;


  END LOOP;

  --Definicion de la respuesta
  v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se ha clonado exitosamente');

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