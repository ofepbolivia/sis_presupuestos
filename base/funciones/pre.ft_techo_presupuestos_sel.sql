CREATE OR REPLACE FUNCTION pre.ft_techo_presupuestos_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Presupuesto
 FUNCION: 		pre.ft_techo_presupuestos_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'pre.ttecho_presupuestos'
 AUTOR: 		 (admin)
 FECHA:	        09-07-2018 18:45:47
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				09-07-2018 18:45:47								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'pre.ttecho_presupuestos'
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

BEGIN

	v_nombre_funcion = 'pre.ft_techo_presupuestos_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'PRE_TECPRE_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		admin
 	#FECHA:		09-07-2018 18:45:47
	***********************************/

	if(p_transaccion='PRE_TECPRE_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						tecpre.id_techo_presupuesto,
						tecpre.estado_reg,
						tecpre.importe_techo_presupuesto,
						tecpre.observaciones,
						tecpre.id_presupuesto,
						tecpre.estado_techo_presupuesto,
						tecpre.id_usuario_reg,
						tecpre.fecha_reg,
						tecpre.id_usuario_ai,
						tecpre.usuario_ai,
						tecpre.id_usuario_mod,
						tecpre.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod
						from pre.ttecho_presupuestos tecpre
						inner join segu.tusuario usu1 on usu1.id_usuario = tecpre.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = tecpre.id_usuario_mod
				        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
--raise NOTICE 'errorrrrr %', v_consulta;
--raise EXCEPTION 'errorrrrr %', v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'PRE_TECPRE_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin
 	#FECHA:		09-07-2018 18:45:47
	***********************************/

	elsif(p_transaccion='PRE_TECPRE_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_techo_presupuesto)
					    from pre.ttecho_presupuestos tecpre
					    inner join segu.tusuario usu1 on usu1.id_usuario = tecpre.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = tecpre.id_usuario_mod
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