CREATE OR REPLACE FUNCTION pre.ft_clase_gasto_cuenta_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Presupuesto
 FUNCION: 		pre.ft_clase_gasto_cuenta_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'pre.tclase_gasto_cuenta'
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

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

BEGIN

	v_nombre_funcion = 'pre.ft_clase_gasto_cuenta_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'PRE_CGCU_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		admin
 	#FECHA:		26-02-2016 02:33:23
	***********************************/

	if(p_transaccion='PRE_CGCU_SEL')then

    	begin

    		--Sentencia de la consulta
			v_consulta:='select
                            cgc.id_clase_gasto_cuenta,
                            cgc.id_cuenta,
                            cgc.estado_reg,
                            cgc.id_clase_gasto,
                            cgc.id_usuario_ai,
                            cgc.usuario_ai,
                            cgc.fecha_reg,
                            cgc.id_usuario_reg,
                            cgc.id_usuario_mod,
                            cgc.fecha_mod,
                            usu1.cuenta as usr_reg,
                            usu2.cuenta as usr_mod,
                            (cuen.nro_cuenta||'' ''||cuen.nombre_cuenta)::varchar as desc_cuenta,
                           cuen.id_gestion
						from pre.tclase_gasto_cuenta cgc
						inner join segu.tusuario usu1 on usu1.id_usuario = cgc.id_usuario_reg
                        inner join conta.tcuenta cuen on cuen.id_cuenta = cgc.id_cuenta
                        left join segu.tusuario usu2 on usu2.id_usuario = cgc.id_usuario_mod
				        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'PRE_CGCU_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin
 	#FECHA:		26-02-2016 02:33:23
	***********************************/

	elsif(p_transaccion='PRE_CGCU_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_clase_gasto_cuenta)
					    from pre.tclase_gasto_cuenta cgc
						inner join segu.tusuario usu1 on usu1.id_usuario = cgc.id_usuario_reg
                        inner join conta.tcuenta cuen on cuen.id_cuenta = cgc.id_cuenta
                        left join segu.tusuario usu2 on usu2.id_usuario = cgc.id_usuario_mod
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
