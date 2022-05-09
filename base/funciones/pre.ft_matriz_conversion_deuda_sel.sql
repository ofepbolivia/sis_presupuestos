CREATE OR REPLACE FUNCTION pre.ft_matriz_conversion_deuda_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Presupuesto
 FUNCION: 		pre.ft_matriz_conversion_deuda_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'pre.tmatriz_conversion_deuda'
 AUTOR: 		 (ismael.valdivia)
 FECHA:	        30-11-2021 18:07:32
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				30-11-2021 18:07:32								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'pre.tmatriz_conversion_deuda'
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

BEGIN

	v_nombre_funcion = 'pre.ft_matriz_conversion_deuda_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'PRE_macon_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		ismael.valdivia
 	#FECHA:		30-11-2021 18:07:32
	***********************************/

	if(p_transaccion='PRE_macon_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						macon.id_matriz_conversion,
						macon.estado_reg,
						macon.id_gestion_origen,
						macon.id_partida_origen,
						macon.id_partida_destino,
						macon.id_gestion_destino,
						macon.id_usuario_reg,
						macon.fecha_reg,
						macon.id_usuario_ai,
						macon.usuario_ai,
						macon.id_usuario_mod,
						macon.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
						ges.gestion::varchar as gestion_destino,
                        ges2.gestion::varchar as gestion_origen,
                        (''(''||par.codigo||'') - ''||par.nombre_partida)::varchar as desc_partida_origen,
                        (''(''||par2.codigo||'') - ''||par2.nombre_partida)::varchar as desc_partida_destino
                        from pre.tmatriz_conversion_deuda macon
                        inner join segu.tusuario usu1 on usu1.id_usuario = macon.id_usuario_reg
                        left join segu.tusuario usu2 on usu2.id_usuario = macon.id_usuario_mod
                        inner join param.tgestion ges on ges.id_gestion = macon.id_gestion_destino
                        inner join param.tgestion ges2 on ges2.id_gestion = macon.id_gestion_origen
                        inner join pre.tpartida par on par.id_partida = macon.id_partida_origen
                        inner join pre.tpartida par2 on par2.id_partida = macon.id_partida_destino
				        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'PRE_macon_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		ismael.valdivia
 	#FECHA:		30-11-2021 18:07:32
	***********************************/

	elsif(p_transaccion='PRE_macon_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_matriz_conversion)
					    from pre.tmatriz_conversion_deuda macon
                        inner join segu.tusuario usu1 on usu1.id_usuario = macon.id_usuario_reg
                        left join segu.tusuario usu2 on usu2.id_usuario = macon.id_usuario_mod
                        inner join param.tgestion ges on ges.id_gestion = macon.id_gestion_destino
                        inner join param.tgestion ges2 on ges2.id_gestion = macon.id_gestion_origen
                        inner join pre.tpartida par on par.id_partida = macon.id_partida_origen
                        inner join pre.tpartida par2 on par2.id_partida = macon.id_partida_destino
				        where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
 	#TRANSACCION:  'LIST_PARTIDAS_SEL'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		ismael.valdivia
 	#FECHA:		30-11-2021 18:07:32
	***********************************/

	elsif(p_transaccion='LIST_PARTIDAS_SEL')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select parti.codigo,
                                 parti.nombre_partida,
                                 (''(''||parti.codigo || '') - '' || parti.nombre_partida)::varchar as desc_partida,
                                 parti.id_partida
                          from pre.tpartida parti
                          where parti.id_partida_fk = (select par.id_partida
                                                    from pre.tpartida par
                                                    where par.id_partida_fk is null
                                                    and par.tipo = ''gasto''
                                                    and par.id_gestion = '||v_parametros.id_gestion||') and ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
 	#TRANSACCION:  'LIST_PARTIDAS_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		ismael.valdivia
 	#FECHA:		30-11-2021 18:07:32
	***********************************/

	elsif(p_transaccion='LIST_PARTIDAS_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(parti.id_partida)
                          from pre.tpartida parti
                          where parti.id_partida_fk = (select par.id_partida
                                                    from pre.tpartida par
                                                    where par.id_partida_fk is null
                                                    and par.tipo = ''gasto''
                                                    and par.id_gestion = '||v_parametros.id_gestion||') and ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;

        /*********************************
        #TRANSACCION:  'LIST_PARTID_DEST_SEL'
        #DESCRIPCION:	Conteo de registros
        #AUTOR:		ismael.valdivia
        #FECHA:		30-11-2021 18:07:32
        ***********************************/

        elsif(p_transaccion='LIST_PARTID_DEST_SEL')then

            begin
                --Sentencia de la consulta de conteo de registros
                v_consulta:='select  parti.codigo,
                                     parti.nombre_partida,
                                     (''(''||parti.codigo || '') - '' || parti.nombre_partida)::varchar as desc_partida,
                                     parti.id_partida
                              from pre.tpartida parti
                              where parti.id_gestion = '||v_parametros.id_gestion_destino||'
                              and parti.sw_transaccional = ''movimiento''
                              and parti.tipo = ''gasto''
                              and parti.sw_movimiento = ''presupuestaria'' and';

                --Definicion de la respuesta
                v_consulta:=v_consulta||v_parametros.filtro;
                v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

                --Devuelve la respuesta
                return v_consulta;

            end;

        /*********************************
        #TRANSACCION:  'LIST_PARTID_DEST_CONT'
        #DESCRIPCION:	Conteo de registros
        #AUTOR:		ismael.valdivia
        #FECHA:		30-11-2021 18:07:32
        ***********************************/

        elsif(p_transaccion='LIST_PARTID_DEST_CONT')then

            begin
                --Sentencia de la consulta de conteo de registros
                v_consulta:='select  count(parti.id_partida)
                              from pre.tpartida parti
                              where parti.id_gestion = '||v_parametros.id_gestion_destino||'
                              and parti.sw_transaccional = ''movimiento''
                              and parti.tipo = ''gasto''
                              and parti.sw_movimiento = ''presupuestaria'' and ';

                --Definicion de la respuesta
                v_consulta:=v_consulta||v_parametros.filtro;

                --Devuelve la respuesta
                return v_consulta;

            end;

	else

		raise exception 'Transaccion inexistente';

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

ALTER FUNCTION pre.ft_matriz_conversion_deuda_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
