CREATE OR REPLACE FUNCTION pre.ft_partida_usuario_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Presupuesto
 FUNCION: 		pre.ft_partida_usuario_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'pre.tpartida_usuario'
 AUTOR: 		 (admin)
 FECHA:	        24-07-2018 20:34:48
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				24-07-2018 20:34:48								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'pre.tpartida_usuario'
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

BEGIN

	v_nombre_funcion = 'pre.ft_partida_usuario_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'PRE_PARUSU_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		admin
 	#FECHA:		24-07-2018 20:34:48
	***********************************/

	if(p_transaccion='PRE_PARUSU_SEL')then

    	begin


        --para el estado
            update pre.tpartida_usuario  SET
            estado_partida_usuario = 'Inactivo'
            WHERE
            fecha_inicio_partida_usuario < fecha_fin_partida_usuario AND
            fecha_fin_partida_usuario <= now()::date - integer '1';

    		--Sentencia de la consulta
			v_consulta:='select
						parusu.id_partida_usuario,
						parusu.estado_reg,
						parusu.fecha_inicio_partida_usuario,
						parusu.fecha_fin_partida_usuario,
						parusu.estado_partida_usuario,
						parusu.observaciones,
						parusu.id_partida,
						parusu.id_usuario_reg,
						parusu.fecha_reg,
						parusu.id_usuario_ai,
						parusu.usuario_ai,
						parusu.id_usuario_mod,
						parusu.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        par.codigo,
                        par.nombre_partida,
                        parusu.id_funcionario_resp,
                        fun.desc_funcionario1 as desc_funcionario,
                        parusu.id_gestion,
                        ges.gestion

						from pre.tpartida_usuario parusu
                        inner join pre.tpartida par on par.id_partida = parusu.id_partida
                        inner join orga.vfuncionario fun on fun.id_funcionario = parusu.id_funcionario_resp
                        inner join param.tgestion ges on ges.id_gestion = parusu.id_gestion
                        inner join segu.tusuario usu1 on usu1.id_usuario = parusu.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = parusu.id_usuario_mod
				        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'PRE_PARUSU_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin
 	#FECHA:		24-07-2018 20:34:48
	***********************************/

	elsif(p_transaccion='PRE_PARUSU_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_partida_usuario)
					    from pre.tpartida_usuario parusu
                        inner join pre.tpartida par on par.id_partida = parusu.id_partida
                        inner join orga.vfuncionario fun on fun.id_funcionario = parusu.id_funcionario_resp
                        inner join param.tgestion ges on ges.id_gestion = parusu.id_gestion
                        inner join segu.tusuario usu1 on usu1.id_usuario = parusu.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = parusu.id_usuario_mod
				        where ';

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