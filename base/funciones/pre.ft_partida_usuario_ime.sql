CREATE OR REPLACE FUNCTION pre.ft_partida_usuario_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Presupuesto
 FUNCION: 		pre.ft_partida_usuario_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'pre.tpartida_usuario'
 AUTOR: 		 (admin)
 FECHA:	        24-07-2018 20:34:48
 COMENTARIOS:	
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				24-07-2018 20:34:48								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'pre.tpartida_usuario'	
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_partida_usuario	integer;
    
    v_list_partida          integer;
    v_nombre_partida		varchar;
    v_desc_funcionario		varchar;
			    
BEGIN

    v_nombre_funcion = 'pre.ft_partida_usuario_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'PRE_PARUSU_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		admin	
 	#FECHA:		24-07-2018 20:34:48
	***********************************/

	if(p_transaccion='PRE_PARUSU_INS')then
					
        begin
        
        	--control de fechas
            IF v_parametros.fecha_inicio_partida_usuario > v_parametros.fecha_fin_partida_usuario THEN
              raise exception 'LA FECHA INICIO ES MAYOR A LA FECHA FIN';
            END IF;
            
            IF v_parametros.fecha_fin_partida_usuario < now()::date THEN
            	raise exception 'LA FECHA FIN ES MENOR A LA FECHA ACTUAL, EL ESTADO SERÁ INACTIVO';
            END IF;
            
            --control si se repite un usuario en una partida
            SELECT pu.id_partida
            INTO v_list_partida
            FROM pre.tpartida_usuario pu
            WHERE 
            pu.id_funcionario_resp = v_parametros.id_funcionario_resp AND
            estado_partida_usuario = 'Activo' AND
            pu.id_partida = v_parametros.id_partida;
            
            SELECT fun.desc_funcionario2, par.codigo||' - '||par.nombre_partida
            INTO v_desc_funcionario, v_nombre_partida
            FROM pre.tpartida_usuario pus
            inner join orga.vfuncionario fun on fun.id_funcionario = pus.id_funcionario_resp
            inner join pre.tpartida par on par.id_partida = pus.id_partida
            WHERE pus.id_funcionario_resp = v_parametros.id_funcionario_resp;
            
            IF v_list_partida = v_parametros.id_partida THEN
            	raise exception ' EL FUNCIONARIO % YA TIENE PERMISOS SOBRE LA PARTIDA %',v_desc_funcionario, v_nombre_partida;
            END IF;
            	        
        	--Sentencia de la insercion
        	insert into pre.tpartida_usuario(
			estado_reg,
			fecha_inicio_partida_usuario,
			fecha_fin_partida_usuario,
			estado_partida_usuario,
			observaciones,
			id_partida,
			id_usuario_reg,
			fecha_reg,
			id_usuario_ai,
			usuario_ai,
			id_usuario_mod,
			fecha_mod,
            id_funcionario_resp,
            id_gestion
          	) values(
			'activo',
			v_parametros.fecha_inicio_partida_usuario,
			v_parametros.fecha_fin_partida_usuario,
			'Activo',
			v_parametros.observaciones,
			v_parametros.id_partida,
			p_id_usuario,
			now(),
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			null,
			null,
            v_parametros.id_funcionario_resp,
            v_parametros.id_gestion
							
			
			
			)RETURNING id_partida_usuario into v_id_partida_usuario;
			
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Partida Usuario almacenado(a) con exito (id_partida_usuario'||v_id_partida_usuario||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_partida_usuario',v_id_partida_usuario::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************    
 	#TRANSACCION:  'PRE_PARUSU_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		admin	
 	#FECHA:		24-07-2018 20:34:48
	***********************************/

	elsif(p_transaccion='PRE_PARUSU_MOD')then

		begin
            --control de fechas
            IF v_parametros.fecha_inicio_partida_usuario > v_parametros.fecha_fin_partida_usuario THEN
              raise exception 'LA FECHA INICIO ES MAYOR A LA FECHA FIN';
            END IF;
                       
           
        
			--Sentencia de la modificacion
			update pre.tpartida_usuario set
			fecha_inicio_partida_usuario = v_parametros.fecha_inicio_partida_usuario,
			fecha_fin_partida_usuario = v_parametros.fecha_fin_partida_usuario,
			estado_partida_usuario = 'Activo',
			observaciones = v_parametros.observaciones,
			id_partida = v_parametros.id_partida,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai,
            id_funcionario_resp = v_parametros.id_funcionario_resp,
            id_gestion = v_parametros.id_gestion
			where id_partida_usuario=v_parametros.id_partida_usuario;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Partida Usuario modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_partida_usuario',v_parametros.id_partida_usuario::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;

	/*********************************    
 	#TRANSACCION:  'PRE_PARUSU_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		admin	
 	#FECHA:		24-07-2018 20:34:48
	***********************************/

	elsif(p_transaccion='PRE_PARUSU_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from pre.tpartida_usuario
            where id_partida_usuario=v_parametros.id_partida_usuario;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Partida Usuario eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_partida_usuario',v_parametros.id_partida_usuario::varchar);
              
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