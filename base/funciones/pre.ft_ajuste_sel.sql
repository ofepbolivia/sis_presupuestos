--------------- SQL ---------------

CREATE OR REPLACE FUNCTION pre.ft_ajuste_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Presupuesto
 FUNCION: 		pre.ft_ajuste_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'pre.tajuste'
 AUTOR: 		 (admin)
 FECHA:	        13-04-2016 13:21:12
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:


 ISSUE            FECHA:		      AUTOR       DESCRIPCION
 0                13/10/2017        RAC       Se aumenta el tipo de interface AjusteConsulta donde se mostraran todo los datos sin fltros
***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;
  v_filtro			varchar;
  --(franklin.espinoza)modificacion presupuestaria
  v_nombre_entidad				varchar;
	v_direccion_admin				varchar;
  v_record						record;
  v_index							integer = 0;
  v_record_funcionario			record;
  v_firmas						VARCHAR[];
  v_firma_fun						varchar;
  v_unidad_ejecutora				varchar;
  v_record_sol					record;
BEGIN

	v_nombre_funcion = 'pre.ft_ajuste_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'PRE_AJU_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		admin
 	#FECHA:		13-04-2016 13:21:12
	***********************************/

	if(p_transaccion='PRE_AJU_SEL')then

    	begin

            v_filtro = ' 0=0  and  ';

             IF  v_parametros.tipo_interfaz = 'AjusteInicio' THEN
                IF p_administrador !=1   THEN
                   v_filtro = 'aju.id_usuario_reg='||p_id_usuario||'   and ';
                END IF;

             ELSEIF  v_parametros.tipo_interfaz = 'AjusteVb' THEN
                 IF p_administrador !=1 THEN
                   v_filtro = '(ew.id_funcionario = '||v_parametros.id_funcionario_usu::varchar||')  and  aju.estado not in (''borrador'', ''aprobado'') and ';
                ELSE
                    v_filtro = 'aju.estado not in (''borrador'', ''aprobado'') and ';
                END IF;

             ELSEIF  v_parametros.tipo_interfaz = 'AjusteConsulta' THEN
                --RAC 13/10/2017,  esta interface no tendra filtros
                v_filtro = ' 0 = 0  and ';
             ELSE
               raise exception 'Tipo de interface no reconocida %', v_parametros.tipo_interfaz;
             END IF;



            --  Sentencia de la consulta
			v_consulta:='select
                            aju.id_ajuste,
                            aju.id_estado_wf,
                            aju.estado_reg,
                            aju.estado,
                            aju.justificacion,
                            aju.id_proceso_wf,
                            --aju.tipo_ajuste,
                            (case when substring(tpw.descripcion,12) = ''inc_comprometido'' then ''inc_comprometido'' when substring(tpw.descripcion,12) = ''rev_comprometido'' then ''rev_comprometido'' when aju.tipo_ajuste not in (''rev_comprometido'',''rev_comprometido'') then aju.tipo_ajuste else ''rev_total_comprometido'' end)::varchar as tipo_ajuste,
                            aju.nro_tramite,
                            aju.id_usuario_reg,
                            aju.fecha_reg,
                            aju.usuario_ai,
                            aju.id_usuario_ai,
                            aju.id_usuario_mod,
                            aju.fecha_mod,
                            usu1.cuenta as usr_reg,
                            usu2.cuenta as usr_mod,
                            aju.fecha,
                            aju.id_gestion	,
                            aju.importe_ajuste,
                            aju.movimiento,
                            aju.nro_tramite as nro_tramite_aux,
                            mon.codigo as desc_moneda,
                            mon.id_moneda
						from pre.tajuste aju
						inner join segu.tusuario usu1 on usu1.id_usuario = aju.id_usuario_reg
                        inner join wf.testado_wf ew on ew.id_estado_wf = aju.id_estado_wf
                        inner join wf.tproceso_wf tpw on tpw.id_proceso_wf = aju.id_proceso_wf
                        inner join param.tmoneda mon on mon.id_moneda = aju.id_moneda
						left join segu.tusuario usu2 on usu2.id_usuario = aju.id_usuario_mod
				        where  '||v_filtro;



			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

            raise notice 'consulta: %  ...... %',v_consulta,v_parametros.filtro ;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'PRE_AJU_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin
 	#FECHA:		13-04-2016 13:21:12
	***********************************/

	elsif(p_transaccion='PRE_AJU_CONT')then

		begin

            v_filtro = ' 0=0  and  ';

             IF  v_parametros.tipo_interfaz = 'AjusteInicio'  THEN
                IF p_administrador !=1   THEN
                   v_filtro = 'aju.id_usuario_reg='||p_id_usuario||'  and  aju.estado = ''borrador'' and ';
                ELSE
                   v_filtro = 'aju.estado = ''borrador'' and ';
                END IF;

             ELSEIF  v_parametros.tipo_interfaz = 'AjusteVb' THEN
                IF p_administrador !=1 THEN
                   v_filtro = '(ew.id_funcionario = '||v_parametros.id_funcionario_usu::varchar||')  and  aju.estado not in (''borrador'', ''aprobado'') and ';
                ELSE
                    v_filtro = 'aju.estado not in (''borrador'', ''aprobado'') and ';
                END IF;
             ELSEIF  v_parametros.tipo_interfaz = 'AjusteConsulta' THEN
                --RAC 13/10/2017,  esta interface no tendra filtros
                v_filtro = ' 0 = 0  and ';
             ELSE
               raise exception 'Tipo de interface no reconocida %', v_parametros.tipo_interfaz;
             END IF;

            --Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_ajuste)
					    from pre.tajuste aju
                          inner join segu.tusuario usu1 on usu1.id_usuario = aju.id_usuario_reg
                          inner join wf.testado_wf ew on ew.id_estado_wf = aju.id_estado_wf
                          inner join param.tmoneda mon on mon.id_moneda = aju.id_moneda
                          left join segu.tusuario usu2 on usu2.id_usuario = aju.id_usuario_mod
				        where  '||v_filtro;

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;
	/*********************************
 	#TRANSACCION:  'PRE_MOD_PRES_REP'
 	#DESCRIPCION:	Modificacion Presupuestaria
 	#AUTOR:		(franklin.espinoza)
 	#FECHA:		13-08-2019 17:21:12
	***********************************/

	elsif(p_transaccion='PRE_MOD_PRES_REP')then

		begin

           SELECT taj.estado, taj.id_estado_wf, taj.justificacion, taj.id_gestion
            INTO v_record_sol
            FROM pre.tajuste taj
            WHERE taj.id_proceso_wf = v_parametros.id_proceso_wf;

            IF(v_record_sol.estado='borrador' OR v_record_sol.estado='revision' or v_record_sol.estado='aprobado')THEN
              v_index = 1;
              FOR v_record IN (WITH RECURSIVE firmas(id_estado_fw, id_estado_anterior,fecha_reg, codigo, id_funcionario) AS (
                                SELECT tew.id_estado_wf, tew.id_estado_anterior , tew.fecha_reg, te.codigo, tew.id_funcionario
                                FROM wf.testado_wf tew
                                INNER JOIN wf.ttipo_estado te ON te.id_tipo_estado = tew.id_tipo_estado
                                WHERE tew.id_estado_wf = v_record_sol.id_estado_wf

                                UNION ALL

                                SELECT ter.id_estado_wf, ter.id_estado_anterior, ter.fecha_reg, te.codigo, ter.id_funcionario
                                FROM wf.testado_wf ter
                                INNER JOIN firmas f ON f.id_estado_anterior = ter.id_estado_wf
                                INNER JOIN wf.ttipo_estado te ON te.id_tipo_estado = ter.id_tipo_estado
                                WHERE f.id_estado_anterior IS NOT NULL
                            )SELECT distinct on (codigo) codigo, fecha_reg , id_estado_fw, id_estado_anterior, id_funcionario FROM firmas ORDER BY codigo, fecha_reg DESC) LOOP
                  IF(v_record.codigo = 'borrador' OR v_record.codigo = 'revision' /*OR v_record.codigo = 'aprobado'*/)THEN
                    SELECT vf.desc_funcionario1, vf.nombre_cargo, vf.oficina_nombre
                    INTO v_record_funcionario
                    FROM orga.vfuncionario_cargo_lugar vf
                    WHERE vf.id_funcionario = v_record.id_funcionario;
                    v_firmas[v_index] = v_record.codigo::VARCHAR||','||v_record.fecha_reg::VARCHAR||','||v_record_funcionario.desc_funcionario1::VARCHAR||','||v_record_funcionario.nombre_cargo::VARCHAR||','||v_record_funcionario.oficina_nombre;
                    v_index = v_index + 1;
                  END IF;
              END LOOP;

              if v_record_sol.estado = 'aprobado' then
              	v_firma_fun = array_to_string(v_firmas,';');
              else
	            v_firma_fun = '';
              end if;
            ELSE
            	v_firma_fun = '';
        	END IF;

        		------
            SELECT (''||te.codigo||' '||te.nombre)::varchar
            INTO v_nombre_entidad
            FROM param.tempresa te;
            ------
            SELECT (''||tda.codigo||' '||tda.nombre)::varchar
            INTO v_direccion_admin
            FROM pre.tdireccion_administrativa tda;
			------
            SELECT (''||tue.codigo||' '||tue.nombre)::varchar
            INTO v_unidad_ejecutora
            FROM pre.tunidad_ejecutora tue;


			--Sentencia de la consulta de conteo de registros
			v_consulta:='
            SELECT vcp.id_categoria_programatica AS id_cp,  ttc.codigo AS centro_costo,
            vcp.codigo_programa , vcp.codigo_proyecto, vcp.codigo_actividad, vcp.codigo_fuente_fin, vcp.codigo_origen_fin, vcp.codigo_unidad_ejecutora,
            tpar.codigo AS codigo_partida, tpar.nombre_partida ,
            sum(tsd.importe) AS precio_total,tmo.codigo AS codigo_moneda, ts.nro_tramite,
            '''||v_nombre_entidad||'''::varchar AS nombre_entidad,
            COALESCE('''||v_direccion_admin||'''::varchar, '''') AS direccion_admin,
            '''||v_unidad_ejecutora||'''::varchar AS unidad_ejecutora,
            COALESCE('''||v_firma_fun||'''::varchar, '''') AS firmas,
            COALESCE('''||v_record_sol.justificacion||'''::varchar,'''') AS justificacion,
            COALESCE(tet.codigo::varchar,''00''::varchar) AS codigo_transf,


            tew.fecha_reg::date AS fecha_soli,
            COALESCE(tg.gestion, (extract(year from now()::date))::integer) AS gestion,
            ts.estado,
            ts.tipo_ajuste,
            tsd.tipo_ajuste as tipo_ajuste_det,
            ts.fecha_reg::date as fecha_solicitud

            FROM pre.tajuste ts
            INNER JOIN pre.tajuste_det tsd ON tsd.id_ajuste = ts.id_ajuste
            INNER JOIN pre.tpartida tpar ON tpar.id_partida = tsd.id_partida

			      inner join wf.testado_wf tew on tew.id_estado_wf = ts.id_estado_wf

            inner join param.tgestion tg on tg.id_gestion = ts.id_gestion

            INNER JOIN param.tcentro_costo tcc ON tcc.id_centro_costo = tsd.id_presupuesto
            INNER JOIN param.ttipo_cc ttc ON ttc.id_tipo_cc = tcc.id_tipo_cc

            INNER JOIN pre.tpresupuesto	tp ON tp.id_presupuesto = tsd.id_presupuesto --tpp.id_presupuesto
            INNER JOIN pre.vcategoria_programatica vcp ON vcp.id_categoria_programatica = tp.id_categoria_prog

            INNER JOIN param.tmoneda tmo ON tmo.id_moneda = ts.id_moneda

            left JOIN pre.tpresupuesto_partida_entidad tppe ON tppe.id_partida = tpar.id_partida AND tppe.id_presupuesto = tp.id_presupuesto
            left JOIN pre.tentidad_transferencia tet ON tet.id_entidad_transferencia = tppe.id_entidad_transferencia

            WHERE tsd.estado_reg = ''activo'' AND ts.id_proceso_wf = '||v_parametros.id_proceso_wf;

			v_consulta =  v_consulta || ' GROUP BY vcp.id_categoria_programatica, tpar.codigo, ttc.codigo,vcp.codigo_programa,vcp.codigo_proyecto, vcp.codigo_actividad,
            vcp.codigo_fuente_fin, vcp.codigo_origen_fin, vcp.codigo_unidad_ejecutora, tpar.nombre_partida, tmo.codigo, ts.nro_tramite, tet.codigo,
            tew.fecha_reg, tg.gestion, ts.estado, ts.tipo_ajuste, tsd.tipo_ajuste, ts.fecha_reg::date';
			v_consulta =  v_consulta || ' ORDER BY tsd.tipo_ajuste asc, tpar.codigo, vcp.id_categoria_programatica, ttc.codigo asc ';

            --Devuelve la respuesta
            RAISE NOTICE 'v_consulta %',v_consulta;
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