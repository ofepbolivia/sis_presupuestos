--------------- SQL ---------------

CREATE OR REPLACE FUNCTION pre.ft_clase_gasto_partida_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Presupuesto
 FUNCION: 		pre.ft_clase_gasto_partida_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'pre.tclase_gasto_partida'
 AUTOR: 		 (admin)
 FECHA:	        26-02-2016 02:33:23
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
	v_id_clase_gasto_partida	integer;

	   --------27/12/2019
    v_registros		record;
    v_registros_ges	record;
    v_id_gestion_destino	integer;
    v_partida_dos	integer;

			    
BEGIN

    v_nombre_funcion = 'pre.ft_clase_gasto_partida_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'PRE_CGP_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		admin	
 	#FECHA:		26-02-2016 02:33:23
	***********************************/

	if(p_transaccion='PRE_CGP_INS')then
					
        begin
        	--Sentencia de la insercion
        	insert into pre.tclase_gasto_partida(
			id_partida,
			estado_reg,
			id_clase_gasto,
			id_usuario_ai,
			usuario_ai,
			fecha_reg,
			id_usuario_reg,
			id_usuario_mod,
			fecha_mod
          	) values(
			v_parametros.id_partida,
			'activo',
			v_parametros.id_clase_gasto,
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			now(),
			p_id_usuario,
			null,
			null
							
			
			
			)RETURNING id_clase_gasto_partida into v_id_clase_gasto_partida;
			
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','CGPR almacenado(a) con exito (id_clase_gasto_partida'||v_id_clase_gasto_partida||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_clase_gasto_partida',v_id_clase_gasto_partida::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************    
 	#TRANSACCION:  'PRE_CGP_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		admin	
 	#FECHA:		26-02-2016 02:33:23
	***********************************/

	elsif(p_transaccion='PRE_CGP_MOD')then

		begin
			--Sentencia de la modificacion
			update pre.tclase_gasto_partida set
			id_partida = v_parametros.id_partida,
			id_clase_gasto = v_parametros.id_clase_gasto,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_clase_gasto_partida=v_parametros.id_clase_gasto_partida;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','CGPR modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_clase_gasto_partida',v_parametros.id_clase_gasto_partida::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;

	/*********************************    
 	#TRANSACCION:  'PRE_CGP_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		admin	
 	#FECHA:		26-02-2016 02:33:23
	***********************************/

	elsif(p_transaccion='PRE_CGP_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from pre.tclase_gasto_partida
            where id_clase_gasto_partida=v_parametros.id_clase_gasto_partida;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','CGPR eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_clase_gasto_partida',v_parametros.id_clase_gasto_partida::varchar);
              
            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'PRE_CLOPAR_IME'
 	#DESCRIPCION:	Clonacion de Partidas a partir de una clase de gasto
 	#AUTOR:		Alan Felipez
 	#FECHA:		27-12-2019 02:33:23
	***********************************/

	elsif(p_transaccion='PRE_CLOPAR_IME')then

		begin

             --  definir id de la gestion siguiente
              select
              ges.id_gestion,
              ges.gestion,
              ges.id_empresa
           into
              v_registros_ges
           from
           param.tgestion ges
           where ges.id_gestion = v_parametros.id_gestion;

            select
              ges.id_gestion
           into
              v_id_gestion_destino
           from
           param.tgestion ges
           where       ges.gestion = v_registros_ges.gestion + 1
                   and ges.id_empresa = v_registros_ges.id_empresa
                   and ges.estado_reg = 'activo';
            IF v_id_gestion_destino is null THEN
                   raise exception 'no se encontró una siguiente gestión preparada (primero cree  gestión siguiente)';
          END IF;
          --recuperar informacion de las partidas


          for v_registros in (select *
                              /*cgp.id_clase_gasto_partida,
                              cgp.id_partida,
                              p.id_gestion*/
                              from pre.tclase_gasto_partida cgp
                              inner join segu.tusuario usu1 on usu1.id_usuario = cgp.id_usuario_reg
                              inner join  pre.tpartida p on p.id_partida = cgp.id_partida
                              left join segu.tusuario usu2 on usu2.id_usuario = cgp.id_usuario_mod
                              where  p.id_gestion = v_parametros.id_gestion)loop

          		--buscamos si existe la relacion de partidas para la siguiente gestion
               if exists (select 1
                          from pre.tpartida_ids
                          where id_partida_uno = v_registros.id_partida) then
                          --encontramos el id_partida de la gestion siguiente
                          select pid.id_partida_dos
                          into v_partida_dos
                          from pre.tpartida_ids pid
                          where pid.id_partida_uno = v_registros.id_partida;
                          --si no existe registrado la relacion de partidas con clases de gasto realizamos la insercion en la tabla
                          if not exists ( select 1
                                          from pre.tclase_gasto_partida cgp
                                          inner join segu.tusuario usu1 on usu1.id_usuario = cgp.id_usuario_reg
                                          inner join  pre.tpartida p on p.id_partida = cgp.id_partida
                                          left join segu.tusuario usu2 on usu2.id_usuario = cgp.id_usuario_mod
                                          where cgp.id_clase_gasto = v_registros.id_clase_gasto and cgp.id_partida =v_partida_dos )then
                          	insert into pre.tclase_gasto_partida(
                            	id_usuario_reg,
                                id_usuario_mod,
                                fecha_reg,
                                fecha_mod,
                                estado_reg,
                                id_usuario_ai,
                                usuario_ai,
                                id_clase_gasto,
                                id_partida
                            )values(
                            	p_id_usuario,
                                null,
                                now(),
                                null,
                                'activo',
                                null,
                                null,
                                v_registros.id_clase_gasto,
                                v_partida_dos
                            );
                          end if;
               end if;


          end loop;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','partidas clonadas');
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