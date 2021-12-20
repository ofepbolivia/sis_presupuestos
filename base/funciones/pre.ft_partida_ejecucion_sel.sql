CREATE OR REPLACE FUNCTION pre.ft_partida_ejecucion_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Presupuesto
 FUNCION: 		pre.ft_partida_ejecucion_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'pre.tpartida_ejecucion'
 AUTOR: 		 (gvelasquez)
 FECHA:	        03-10-2016 15:47:23
 COMENTARIOS:
***************************************************************************
  HISTORIAL DE MODIFICACIONES:


 ISSUE            FECHA:		      AUTOR       DESCRIPCION
 0                10/10/2017           RAC         Agrgar trasaccion para listado de nro de tramite
***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;
    v_pre_codigo_proc_macajsutable   varchar;
    v_id_gestion					 integer;

    --breydi.vasquez 10-11-2019
    v_consul						 varchar;
    v_desde							 varchar;
    v_hasta							 varchar;

    --17-12-2021(may)
    v_sql_tabla_todo		varchar;
    v_reg_op				record;
    v_reg_cd				record;

BEGIN

	v_nombre_funcion = 'pre.ft_partida_ejecucion_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'PRE_PAREJE_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		gvelasquez
 	#FECHA:		03-10-2016 15:47:23
	***********************************/

	if(p_transaccion='PRE_PAREJE_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						pareje.id_partida_ejecucion,
						pareje.id_int_comprobante,
						pareje.id_moneda,
                        mon.moneda,
						pareje.id_presupuesto,
                        pre.descripcion as desc_pres,
                        vpre.codigo_cc,
                        cat.codigo_categoria,
						pareje.id_partida,
                        par.codigo,
                        par.nombre_partida,
						pareje.nro_tramite,
						pareje.tipo_cambio,
						pareje.columna_origen,
						pareje.tipo_movimiento,
						pareje.id_partida_ejecucion_fk,
						pareje.estado_reg,
						pareje.fecha,
						pareje.monto_mb,
						pareje.monto,
						pareje.valor_id_origen,
						pareje.id_usuario_reg,
						pareje.fecha_reg,
						pareje.usuario_ai,
						pareje.id_usuario_ai,
						pareje.fecha_mod,
						pareje.id_usuario_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod

						from pre.tpartida_ejecucion pareje
                        inner join pre.tpresupuesto pre on pre.id_presupuesto = pareje.id_presupuesto
                        INNER JOIN pre.vpresupuesto vpre ON vpre.id_presupuesto = pre.id_presupuesto
                        inner join pre.vcategoria_programatica cat on cat.id_categoria_programatica = pre.id_categoria_prog
                        inner join pre.tpartida par on par.id_partida = pareje.id_partida
                        inner join param.tmoneda mon on mon.id_moneda = pareje.id_moneda
						inner join segu.tusuario usu1 on usu1.id_usuario = pareje.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = pareje.id_usuario_mod
				        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			raise notice 'La consulta es:  %', v_consulta;
            --raise EXCEPTION 'Provocando el error';

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'PRE_PAREJE_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		gvelasquez
 	#FECHA:		03-10-2016 15:47:23
	***********************************/

	elsif(p_transaccion='PRE_PAREJE_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_partida_ejecucion)
					    from pre.tpartida_ejecucion pareje
              inner join pre.tpresupuesto pre on pre.id_presupuesto = pareje.id_presupuesto
              INNER JOIN pre.vpresupuesto vpre ON vpre.id_presupuesto = pre.id_presupuesto

              inner join pre.vcategoria_programatica cat on cat.id_categoria_programatica = pre.id_categoria_prog
              inner join pre.tpartida par on par.id_partida = pareje.id_partida
              inner join param.tmoneda mon on mon.id_moneda = pareje.id_moneda
					    inner join segu.tusuario usu1 on usu1.id_usuario = pareje.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = pareje.id_usuario_mod
					    where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
 	#TRANSACCION:  'PRE_LISTRAPE_SEL'
 	#DESCRIPCION:	Lista nro de tramite para interface de ajustes, icnremetosy compromisos presupesutario
 	#AUTOR:		rac
 	#FECHA:		11/10/2017
	***********************************/

	ELSEIF(p_transaccion='PRE_LISTRAPE_SEL')then

    	begin

            v_pre_codigo_proc_macajsutable =  pxp.f_get_variable_global('pre_codigo_proc_macajsutable');
            raise notice '-> fil :%',COALESCE(v_pre_codigo_proc_macajsutable,'''TEST''');

            --recueerar la gestion de la fecha

            select
               p.id_gestion
            into
              v_id_gestion
            from param.tperiodo p
            where  v_parametros.fecha_ajuste::Date BETWEEN p.fecha_ini::Date and p.fecha_fin::date;

            IF v_id_gestion is null  THEN
               raise exception 'no se encontro gestion para la fecha: %',v_parametros.fecha_ajuste;
            END IF;

            raise notice '-> ges :%',v_id_gestion;

			--15-06-2021 (may) funcion se pone muy lenta
            /*v_consulta:='select
                             DISTINCT ON (pe.nro_tramite)
                             pr.id_gestion,
                             pe.nro_tramite,
                             pm.codigo,
                             pe.id_moneda,
                             mon.codigo as desc_moneda
                          from pre.tpartida_ejecucion pe
                          inner join wf.tproceso_wf pwf on pwf.nro_tramite = pe.nro_tramite
                          inner join wf.ttipo_proceso tp on tp.id_tipo_proceso = pwf.id_tipo_proceso
                          inner join wf.tproceso_macro pm on pm.id_proceso_macro = tp.id_proceso_macro
                          inner join param.tperiodo pr on pe.fecha BETWEEN pr.fecha_ini and pr.fecha_fin
                          inner join param.tmoneda mon on mon.id_moneda = pe.id_moneda
                          where pm.codigo in ('||COALESCE(v_pre_codigo_proc_macajsutable,'''TEST''') ||')
                          and  pr.id_gestion = '||v_id_gestion::Varchar|| ' and ';*/


    		--Sentencia de la consulta
			/*v_consulta:='select
                             pr.id_gestion,
                             pe.nro_tramite,
                             pm.codigo,
                             pe.id_moneda,
                             mon.codigo as desc_moneda
                          from pre.tpartida_ejecucion pe
                          inner join tes.tobligacion_pago pag on pag.num_tramite = pe.nro_tramite
                          inner join wf.tproceso_wf pwf on pwf.id_proceso_wf = pag.id_proceso_wf
                          inner join wf.ttipo_proceso tp on tp.id_tipo_proceso = pwf.id_tipo_proceso
                          inner join wf.tproceso_macro pm on pm.id_proceso_macro = tp.id_proceso_macro
                          inner join param.tperiodo pr on pe.fecha BETWEEN pr.fecha_ini and pr.fecha_fin
                          inner join param.tmoneda mon on mon.id_moneda = pe.id_moneda
                          where pm.codigo in ('||COALESCE(v_pre_codigo_proc_macajsutable,'''TEST''') ||')
                          and pr.id_gestion = '||v_id_gestion::Varchar|| ' and ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
            v_consulta:=v_consulta||' group by pr.id_gestion,
                                              pe.nro_tramite,
                                              pm.codigo,
                                              pe.id_moneda,
                                              mon.codigo ';*/

            /*v_consulta:='select
                              pr.id_gestion,
                              pag.num_tramite,
                              pm.codigo,
                              pag.id_moneda,
                              mon.codigo as desc_moneda
                          from  tes.tobligacion_pago pag
                          inner join wf.tproceso_wf pwf on pwf.id_proceso_wf = pag.id_proceso_wf
                          inner join wf.ttipo_proceso tp on tp.id_tipo_proceso = pwf.id_tipo_proceso
                          inner join wf.tproceso_macro pm on pm.id_proceso_macro = tp.id_proceso_macro
                          inner join param.tperiodo pr on pag.fecha BETWEEN pr.fecha_ini and pr.fecha_fin
                          inner join param.tmoneda mon on mon.id_moneda = pag.id_moneda
                          where pm.codigo in ('||COALESCE(v_pre_codigo_proc_macajsutable,'''TEST''') ||')
                          and pr.id_gestion = '||v_id_gestion::Varchar|| ' and ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

            v_consulta:=v_consulta||' group by pr.id_gestion,
                                              pag.num_tramite,
                                              pm.codigo,
                                              pag.id_moneda,
                                              mon.codigo';
             */

            --(may)modificacion listado se aumenta para FA
            v_sql_tabla_todo = 'CREATE TEMPORARY TABLE temp_list
                                            (	id_gestion INTEGER,
                                                nro_tramite VARCHAR,
                                                codigo VARCHAR,
                                                id_moneda INTEGER,
                                                desc_moneda VARCHAR
                                            ) ON COMMIT DROP';
            EXECUTE(v_sql_tabla_todo);



            FOR v_reg_op in ( (select pr.id_gestion,
                                    pag.num_tramite,
                                    pm.codigo,
                                    pag.id_moneda,
                                    mon.codigo as desc_moneda
                          from  tes.tobligacion_pago pag
                          inner join wf.tproceso_wf pwf on pwf.id_proceso_wf = pag.id_proceso_wf
                          inner join wf.ttipo_proceso tp on tp.id_tipo_proceso = pwf.id_tipo_proceso
                          inner join wf.tproceso_macro pm on pm.id_proceso_macro = tp.id_proceso_macro
                          inner join param.tperiodo pr on pag.fecha BETWEEN pr.fecha_ini and pr.fecha_fin
                          inner join param.tmoneda mon on mon.id_moneda = pag.id_moneda
                          where pm.codigo in ('CINTPD','CNAPD','CINTBR','PCE','PCP','PD','PGA','PPM','TES-PD','PU')
                          and pr.id_gestion = v_id_gestion
                          group by pr.id_gestion,
                                    pag.num_tramite,
                                    pm.codigo,
                                    pag.id_moneda,
                                    mon.codigo)

                        union

                          (select
                              pr.id_gestion,
                              cdoc.nro_tramite as num_tramite,
                              pm.codigo,
                              cdoc.id_moneda,
                              mon.codigo as desc_moneda
                          from  cd.tcuenta_doc cdoc
                          inner join wf.tproceso_wf pwf on pwf.id_proceso_wf = cdoc.id_proceso_wf
                          inner join wf.ttipo_proceso tp on tp.id_tipo_proceso = pwf.id_tipo_proceso
                          inner join wf.tproceso_macro pm on pm.id_proceso_macro = tp.id_proceso_macro
                          inner join param.tperiodo pr on cdoc.fecha BETWEEN pr.fecha_ini and pr.fecha_fin
                          inner join param.tmoneda mon on mon.id_moneda = cdoc.id_moneda
                          where pm.codigo in ('FA')
                          and pr.id_gestion = v_id_gestion
                          group by pr.id_gestion,
                                    cdoc.nro_tramite,
                                    pm.codigo,
                                    cdoc.id_moneda,
                                    mon.codigo)
            			)LOOP



                        insert into temp_list (id_gestion,
                                               nro_tramite,
                                              codigo,
                                              id_moneda,
                                              desc_moneda

                                              ) values (

                                              v_reg_op.id_gestion,
                                              v_reg_op.num_tramite,
                                              v_reg_op.codigo,
                                              v_reg_op.id_moneda,
                                              v_reg_op.desc_moneda
                                              );

            END LOOP;

            v_consulta:='select
                              tlist.id_gestion,
                              tlist.nro_tramite,
                              tlist.codigo,
                              tlist.id_moneda,
                              tlist.desc_moneda
                          from  temp_list tlist
                          where ';

            v_consulta:=v_consulta||v_parametros.filtro;

			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			raise notice 'La consulta es:  %', v_consulta;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'PRE_LISTRAPE_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		rac
 	#FECHA:		11/10/2017
	***********************************/

	elsif(p_transaccion='PRE_LISTRAPE_CONT')then

		begin

            v_pre_codigo_proc_macajsutable =  pxp.f_get_variable_global('pre_codigo_proc_macajsutable');


            --recuperar  la gestion de la fecha
            select
               p.id_gestion
            into
              v_id_gestion
            from param.tperiodo p
            where  v_parametros.fecha_ajuste BETWEEN p.fecha_ini and p.fecha_fin;

            IF v_id_gestion is null  THEN
               raise exception 'no se encontro gestion para la fecha: %',v_parametros.fecha_ajuste;
            END IF;



			--Sentencia de la consulta de conteo de registros
			/*v_consulta:='select
                             count( DISTINCT pe.nro_tramite)
					     from pre.tpartida_ejecucion pe
                          inner join tes.tobligacion_pago pag on pag.num_tramite = pe.nro_tramite
                          inner join wf.tproceso_wf pwf on pwf.id_proceso_wf = pag.id_proceso_wf
                          inner join wf.ttipo_proceso tp on tp.id_tipo_proceso = pwf.id_tipo_proceso
                          inner join wf.tproceso_macro pm on pm.id_proceso_macro = tp.id_proceso_macro
                          inner join param.tperiodo pr on pe.fecha BETWEEN pr.fecha_ini and pr.fecha_fin
                          inner join param.tmoneda mon on mon.id_moneda = pe.id_moneda
                          where pm.codigo in ('||COALESCE(v_pre_codigo_proc_macajsutable,'''TEST''') ||')
                          and pr.id_gestion = '||v_id_gestion::Varchar|| ' and ';*/

            /*v_consulta:='select
                             count( DISTINCT pag.num_tramite)
                          from  tes.tobligacion_pago pag
                          inner join wf.tproceso_wf pwf on pwf.id_proceso_wf = pag.id_proceso_wf
                          inner join wf.ttipo_proceso tp on tp.id_tipo_proceso = pwf.id_tipo_proceso
                          inner join wf.tproceso_macro pm on pm.id_proceso_macro = tp.id_proceso_macro
                          inner join param.tperiodo pr on pag.fecha BETWEEN pr.fecha_ini and pr.fecha_fin
                          inner join param.tmoneda mon on mon.id_moneda = pag.id_moneda
                          where pm.codigo in ('||COALESCE(v_pre_codigo_proc_macajsutable,'''TEST''') ||')
                          and pr.id_gestion = '||v_id_gestion::Varchar|| ' and ';*/

                      v_sql_tabla_todo = 'CREATE TEMPORARY TABLE temp_list
                                            (	id_gestion INTEGER,
                                                nro_tramite VARCHAR,
                                                codigo VARCHAR,
                                                id_moneda INTEGER,
                                                desc_moneda VARCHAR
                                            ) ON COMMIT DROP';
                      EXECUTE(v_sql_tabla_todo);



                      FOR v_reg_op in ( (select pr.id_gestion,
                                              pag.num_tramite,
                                              pm.codigo,
                                              pag.id_moneda,
                                              mon.codigo as desc_moneda
                                    from  tes.tobligacion_pago pag
                                    inner join wf.tproceso_wf pwf on pwf.id_proceso_wf = pag.id_proceso_wf
                                    inner join wf.ttipo_proceso tp on tp.id_tipo_proceso = pwf.id_tipo_proceso
                                    inner join wf.tproceso_macro pm on pm.id_proceso_macro = tp.id_proceso_macro
                                    inner join param.tperiodo pr on pag.fecha BETWEEN pr.fecha_ini and pr.fecha_fin
                                    inner join param.tmoneda mon on mon.id_moneda = pag.id_moneda
                                    where pm.codigo in ('CINTPD','CNAPD','CINTBR','PCE','PCP','PD','PGA','PPM','TES-PD','PU')
                                    and pr.id_gestion = v_id_gestion
                                    group by pr.id_gestion,
                                              pag.num_tramite,
                                              pm.codigo,
                                              pag.id_moneda,
                                              mon.codigo)

                                  union

                                    (select
                                        pr.id_gestion,
                                        cdoc.nro_tramite as num_tramite,
                                        pm.codigo,
                                        cdoc.id_moneda,
                                        mon.codigo as desc_moneda
                                    from  cd.tcuenta_doc cdoc
                                    inner join wf.tproceso_wf pwf on pwf.id_proceso_wf = cdoc.id_proceso_wf
                                    inner join wf.ttipo_proceso tp on tp.id_tipo_proceso = pwf.id_tipo_proceso
                                    inner join wf.tproceso_macro pm on pm.id_proceso_macro = tp.id_proceso_macro
                                    inner join param.tperiodo pr on cdoc.fecha BETWEEN pr.fecha_ini and pr.fecha_fin
                                    inner join param.tmoneda mon on mon.id_moneda = cdoc.id_moneda
                                    where pm.codigo in ('FA')
                                    and pr.id_gestion = v_id_gestion
                                    group by pr.id_gestion,
                                              cdoc.nro_tramite,
                                              pm.codigo,
                                              cdoc.id_moneda,
                                              mon.codigo)
                                  )LOOP



                                  insert into temp_list (id_gestion,
                                                         nro_tramite,
                                                        codigo,
                                                        id_moneda,
                                                        desc_moneda

                                                        ) values (

                                                        v_reg_op.id_gestion,
                                                        v_reg_op.num_tramite,
                                                        v_reg_op.codigo,
                                                        v_reg_op.id_moneda,
                                                        v_reg_op.desc_moneda
                                                        );

                      END LOOP;

                         v_consulta:='select count( tlist.nro_tramite)
                          from  temp_list tlist
                          where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;

 /*********************************
        #TRANSACCION:  'PRE_DETPAREJE_SEL'
        #DESCRIPCION:	Consulta de datos consolidado partida ejecucion
        #AUTOR:			breydi.vasquez
        #FECHA:		10-11-2019
        ***********************************/

        elsif(p_transaccion='PRE_DETPAREJE_SEL')then

            begin
            if v_parametros.desde is null then
            	v_desde = 'null::date';
            else
            	v_desde = ''''||v_parametros.desde||'''::date';
            end if;

            if v_parametros.hasta is null then
	            v_hasta = 'null::date';
            else
                v_hasta = ''''||v_parametros.hasta||'''::date';
            end if;

            	create temp table cosolidado_partida_ejecucion(
                  moneda 			 varchar,
                  descripcion		 varchar,
                  codigo_cc			 text,
                  codigo_categoria	 varchar,
                  nro_tramite		 varchar,
                  nombre_partida 	 varchar,
                  codigo 			 varchar,
                  id_presupuesto 	 integer,
                  id_partida		 integer,
                  id_moneda			 integer,
                  comprometido		 numeric,
                  ejecutado			 numeric,
                  pagado			 numeric,
                  saldo              numeric,
                  desde				 date,
                  hasta				 date
                )on commit drop;

                v_consul:= 'with recursive detalle (id_int_comprobante, moneda, id_moneda, id_presupuesto, descripcion, codigo_cc, codigo_categoria,
										              id_partida, codigo, nombre_partida, nro_tramite, comprometido, ejecutado, pagado)
                                                      as
                (select
                  pareje.id_int_comprobante,
                  mon.moneda,
                  pareje.id_moneda,
                  pareje.id_presupuesto,
                  pre.descripcion,
                  vpre.codigo_cc,
                  cat.codigo_categoria,
                  pareje.id_partida,
                  par.codigo,
                  par.nombre_partida,
                  pareje.nro_tramite,
                  case when pareje.tipo_movimiento = ''comprometido'' then
                          pareje.monto
                       else
                           0.00
                       end,
                  case when pareje.tipo_movimiento = ''ejecutado'' then
                          pareje.monto
                      else
                          0.00
                      end,
                  case when pareje.tipo_movimiento = ''pagado'' then
                          pareje.monto
                      else
                          0.00
                      end
                  from pre.tpartida_ejecucion pareje
                  inner join pre.tpresupuesto pre on pre.id_presupuesto = pareje.id_presupuesto
                  INNER JOIN pre.vpresupuesto vpre ON vpre.id_presupuesto = pre.id_presupuesto
                  inner join pre.vcategoria_programatica cat on cat.id_categoria_programatica = pre.id_categoria_prog
                  inner join pre.tpartida par on par.id_partida = pareje.id_partida
                  inner join param.tmoneda mon on mon.id_moneda = pareje.id_moneda
                  inner join segu.tusuario usu1 on usu1.id_usuario = pareje.id_usuario_reg
                  left join segu.tusuario usu2 on usu2.id_usuario = pareje.id_usuario_mod
                  where ' || v_parametros.filtro || '
                  )
	            insert into cosolidado_partida_ejecucion
                select
                  de.moneda,
                  de.descripcion,
                  de.codigo_cc,
                  de.codigo_categoria,
                  de.nro_tramite,
                  de.nombre_partida,
                  de.codigo,
                  de.id_presupuesto,
                  de.id_partida,
                  de.id_moneda,
                  sum(de.comprometido) as comprometido,
                  sum(de.ejecutado) as ejecutado,
                  sum(de.pagado)	as pagado,
                  (sum(de.comprometido) - sum(de.ejecutado)) as saldo,
                  '||v_desde||',
                  '||v_hasta||'
                from detalle de
                group by
                    de.nro_tramite,
                    de.moneda,
                    de.descripcion,
                    de.codigo_cc,
                    de.codigo_categoria,
                    de.nro_tramite,
                    de.nombre_partida,
                    de.codigo,
                    de.id_presupuesto,
                    de.id_partida,
                    de.id_moneda';

                execute (v_consul);

                v_consulta:= ' select * from cosolidado_partida_ejecucion ';
				v_consulta:= v_consulta ||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion;
                v_consulta:= v_consulta || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

                --Devuelve la respuesta
                return v_consulta;

        end;

	/*********************************
 	#TRANSACCION:  'PRE_DETPAREJE_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		breydi.vasquez
 	#FECHA:		10-11-2019
	***********************************/

	elsif(p_transaccion='PRE_DETPAREJE_CONT')then
    	begin

          	create temp table cosolidado_partida_ejecucion(
                  moneda 			 varchar,
                  descripcion		 varchar,
                  codigo_cc			 text,
                  codigo_categoria	 varchar,
                  nro_tramite		 varchar,
                  nombre_partida 	 varchar,
                  codigo 			 varchar,
                  id_presupuesto 	 integer,
                  id_partida		 integer,
                  id_moneda			 integer,
                  comprometido		 numeric,
                  ejecutado			 numeric,
                  pagado			 numeric,
                  saldo              numeric
                )on commit drop;
                v_consul:= 'with recursive detalle (id_int_comprobante, moneda, id_moneda, id_presupuesto, descripcion, codigo_cc, codigo_categoria,
										              id_partida, codigo, nombre_partida, nro_tramite, comprometido, ejecutado, pagado)
                                                      as
                (select
                  pareje.id_int_comprobante,
                  mon.moneda,
                  pareje.id_moneda,
                  pareje.id_presupuesto,
                  pre.descripcion,
                  vpre.codigo_cc,
                  cat.codigo_categoria,
                  pareje.id_partida,
                  par.codigo,
                  par.nombre_partida,
                  pareje.nro_tramite,
                  case when pareje.tipo_movimiento = ''comprometido'' then
                          pareje.monto
                       else
                           0.00
                       end,
                  case when pareje.tipo_movimiento = ''ejecutado'' then
                          pareje.monto
                      else
                          0.00
                      end,
                  case when pareje.tipo_movimiento = ''pagado'' then
                          pareje.monto
                      else
                          0.00
                      end
                  from pre.tpartida_ejecucion pareje
                  inner join pre.tpresupuesto pre on pre.id_presupuesto = pareje.id_presupuesto
                  INNER JOIN pre.vpresupuesto vpre ON vpre.id_presupuesto = pre.id_presupuesto
                  inner join pre.vcategoria_programatica cat on cat.id_categoria_programatica = pre.id_categoria_prog
                  inner join pre.tpartida par on par.id_partida = pareje.id_partida
                  inner join param.tmoneda mon on mon.id_moneda = pareje.id_moneda
                  inner join segu.tusuario usu1 on usu1.id_usuario = pareje.id_usuario_reg
                  left join segu.tusuario usu2 on usu2.id_usuario = pareje.id_usuario_mod
                  where ' || v_parametros.filtro|| '
                  )
	            insert into cosolidado_partida_ejecucion
                select
                  moneda,
                  descripcion,
                  codigo_cc,
                  codigo_categoria,
                  nro_tramite,
                  nombre_partida,
                  codigo,
                  id_presupuesto,
                  id_partida,
                  id_moneda,
                  sum(comprometido) as comprometido,
                  sum(ejecutado) as ejecutado,
                  sum(pagado)	as pagado,
                  (sum(comprometido) - sum(ejecutado)) as saldo
                from detalle
                group by
                    nro_tramite,
                    moneda,
                    descripcion,
                    codigo_cc,
                    codigo_categoria,
                    nro_tramite,
                    nombre_partida,
                    codigo,
                    id_presupuesto,
                    id_partida,
                    id_moneda';
                execute (v_consul);
                v_consulta:= ' select count(nro_tramite) from cosolidado_partida_ejecucion ';
                return v_consulta;
        end;

	/*********************************
 	#TRANSACCION:  'PRE_TOPAREJE_SEL'
 	#DESCRIPCION:	captura de totales comprometido-ejecutado-pagado-devengado de filtro partida ejecucion
 	#AUTOR:		breydi.vasquez
 	#FECHA:		10-11-2019
	***********************************/

	elsif(p_transaccion='PRE_TOPAREJE_SEL')then

		begin

                v_consulta:= 'with recursive totales (com, eje, pag)as
                           (select
                                case when  pareje.tipo_movimiento = ''comprometido'' then
                                      pareje.monto_mb
                                else 0.00 end,
                                case when pareje.tipo_movimiento = ''ejecutado'' then
                                      pareje.monto_mb
                                else 0.00 end,
                                case when pareje.tipo_movimiento = ''pagado'' then
                                      pareje.monto_mb
                                else 0.00 end
                                from pre.tpartida_ejecucion pareje
                                inner join pre.tpresupuesto pre on pre.id_presupuesto = pareje.id_presupuesto
                                INNER JOIN pre.vpresupuesto vpre ON vpre.id_presupuesto = pre.id_presupuesto
                                inner join pre.vcategoria_programatica cat on cat.id_categoria_programatica = pre.id_categoria_prog
                                inner join pre.tpartida par on par.id_partida = pareje.id_partida
                                inner join param.tmoneda mon on mon.id_moneda = pareje.id_moneda
                                inner join segu.tusuario usu1 on usu1.id_usuario = pareje.id_usuario_reg
                                left join segu.tusuario usu2 on usu2.id_usuario = pareje.id_usuario_mod
				                where ' || v_parametros.filtro|| '
                                )
                                select sum(com),
                                       sum(eje),
                                       sum(pag),
                                       (sum(com) - sum(eje))
                                from totales ';

			--Devuelve la respuesta
            raise notice '%',v_consulta;
			return v_consulta;

	  end;

  /*********************************
  #TRANSACCION:  'TES_DENTRAM_SEL'
  #DESCRIPCION:	Listado detalle N° tramite
  #AUTOR:	breydi.vasquez
  #FECHA: 10-11-2019
  ***********************************/

	elsif(p_transaccion='TES_DENTRAM_SEL')then

      begin
          --Sentencia pagos con libro de bancos exterior y observacion

            CREATE TEMPORARY TABLE ttemp_eval_det (
              id_partida_ejecucion	integer,
              id_partida_ejecucion_fk   integer,
              moneda					varchar,
              comprometido				numeric,
              ejecutado					numeric,
              pagado					numeric,
              nro_tramite				varchar,
              tipo_movimiento			varchar,
              nombre_partida			varchar,
              codigo					varchar,
              codigo_categoria			varchar,
              fecha						date,
              codigo_cc              	varchar,
              usr_reg					varchar,
              usr_mod 					varchar,
              fecha_reg					timestamp,
              fecha_mod                 timestamp,
              estado_reg				varchar
            )ON COMMIT DROP;

		 v_consul:= 'with recursive detalle (id_partida_ejecucion, id_partida_ejecucion_fk, moneda, id_moneda, id_presupuesto, descripcion, codigo_cc, codigo_categoria,
										              id_partida, codigo, nombre_partida, nro_tramite, tipo_movimiento, fecha,
                                                      usr_reg, usr_mod, fecha_reg, fecha_mod, estado_reg, comprometido, ejecutado, pagado)
                                                      as
                (select
				  pareje.id_partida_ejecucion,
                  pareje.id_partida_ejecucion_fk,
                  mon.moneda,
                  pareje.id_moneda,
                  pareje.id_presupuesto,
                  pre.descripcion,
                  vpre.codigo_cc,
                  cat.codigo_categoria,
                  pareje.id_partida,
                  par.codigo,
                  par.nombre_partida,
                  pareje.nro_tramite,
                  pareje.tipo_movimiento,
                  pareje.fecha,
                  usu1.cuenta,
                  usu2.cuenta,
                  pareje.fecha_reg,
                  pareje.fecha_mod,
                  pareje.estado_reg,
                  case when pareje.tipo_movimiento = ''comprometido'' then
                          pareje.monto
                       else
                           0.00
                       end,
                  case when pareje.tipo_movimiento = ''ejecutado'' then
                          pareje.monto
                      else
                          0.00
                      end,
                  case when pareje.tipo_movimiento = ''pagado'' then
                          pareje.monto
                      else
                          0.00
                      end
                  from pre.tpartida_ejecucion pareje
                  inner join pre.tpresupuesto pre on pre.id_presupuesto = pareje.id_presupuesto
                  INNER JOIN pre.vpresupuesto vpre ON vpre.id_presupuesto = pre.id_presupuesto
                  inner join pre.vcategoria_programatica cat on cat.id_categoria_programatica = pre.id_categoria_prog
                  inner join pre.tpartida par on par.id_partida = pareje.id_partida
                  inner join param.tmoneda mon on mon.id_moneda = pareje.id_moneda
                  inner join segu.tusuario usu1 on usu1.id_usuario = pareje.id_usuario_reg
                  left join segu.tusuario usu2 on usu2.id_usuario = pareje.id_usuario_mod
                  where ' || v_parametros.filtro|| '
                  )

                insert into ttemp_eval_det

                select
	              id_partida_ejecucion,
                  id_partida_ejecucion_fk,
                  moneda,
                  comprometido,
                  ejecutado,
                  pagado,
                  nro_tramite,
                  tipo_movimiento,
                  nombre_partida,
                  codigo,
                  codigo_categoria,
                  fecha,
                  codigo_cc,
                  usr_reg,
                  usr_mod,
                  fecha_reg,
                  fecha_mod,
                  estado_reg
                from detalle ';

            execute(v_consul);

          v_consulta:='select * from ttemp_eval_det
          		where tipo_movimiento = '''||v_parametros.estado_func||''' ';

		  v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
          --Devuelve la respuesta
          return v_consulta;
      end;

	/*********************************
 	#TRANSACCION:  'TES_DENTRAM_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:	 breydi.vasquez
 	#FECHA:		10-11-2019
	***********************************/

	elsif(p_transaccion='TES_DENTRAM_CONT')then

		begin
  --Sentencia pagos con libro de bancos exterior y observacion

            CREATE TEMPORARY TABLE ttemp_eval_det (
              id_partida_ejecucion		integer,
              id_partida_ejecucion_fk   integer,
              moneda					varchar,
              comprometido				numeric,
              ejecutado					numeric,
              pagado					numeric,
              nro_tramite				varchar,
              tipo_movimiento			varchar,
              nombre_partida			varchar,
              codigo					varchar,
              codigo_categoria			varchar,
              fecha						date,
              codigo_cc              	varchar,
              usr_reg					varchar,
              usr_mod 					varchar,
              fecha_reg					timestamp,
              fecha_mod                 timestamp,
              estado_reg				varchar
            )ON COMMIT DROP;

		 v_consul:= 'with recursive detalle (id_partida_ejecucion, id_partida_ejecucion_fk, moneda, id_moneda, id_presupuesto, descripcion, codigo_cc, codigo_categoria,
										              id_partida, codigo, nombre_partida, nro_tramite, tipo_movimiento, fecha,
                                                      usr_reg, usr_mod, fecha_reg, fecha_mod, estado_reg, comprometido, ejecutado, pagado)
                                                      as
                (select
				  pareje.id_partida_ejecucion,
                  pareje.id_partida_ejecucion_fk,
                  mon.moneda,
                  pareje.id_moneda,
                  pareje.id_presupuesto,
                  pre.descripcion,
                  vpre.codigo_cc,
                  cat.codigo_categoria,
                  pareje.id_partida,
                  par.codigo,
                  par.nombre_partida,
                  pareje.nro_tramite,
                  pareje.tipo_movimiento,
                  pareje.fecha,
                  usu1.cuenta,
                  usu2.cuenta,
                  pareje.fecha_reg,
                  pareje.fecha_mod,
                  pareje.estado_reg,
                  case when pareje.tipo_movimiento = ''comprometido'' then
                          pareje.monto
                       else
                           0.00
                       end,
                  case when pareje.tipo_movimiento = ''ejecutado'' then
                          pareje.monto
                      else
                          0.00
                      end,
                  case when pareje.tipo_movimiento = ''pagado'' then
                          pareje.monto
                      else
                          0.00
                      end
                  from pre.tpartida_ejecucion pareje
                  inner join pre.tpresupuesto pre on pre.id_presupuesto = pareje.id_presupuesto
                  INNER JOIN pre.vpresupuesto vpre ON vpre.id_presupuesto = pre.id_presupuesto
                  inner join pre.vcategoria_programatica cat on cat.id_categoria_programatica = pre.id_categoria_prog
                  inner join pre.tpartida par on par.id_partida = pareje.id_partida
                  inner join param.tmoneda mon on mon.id_moneda = pareje.id_moneda
                  inner join segu.tusuario usu1 on usu1.id_usuario = pareje.id_usuario_reg
                  left join segu.tusuario usu2 on usu2.id_usuario = pareje.id_usuario_mod
                  where ' || v_parametros.filtro|| '
                  )

                insert into ttemp_eval_det

                select
	              id_partida_ejecucion,
                  id_partida_ejecucion_fk,
                  moneda,
                  comprometido,
                  ejecutado,
                  pagado,
                  nro_tramite,
                  tipo_movimiento,
                  nombre_partida,
                  codigo,
                  codigo_categoria,
                  fecha,
                  codigo_cc,
                  usr_reg,
                  usr_mod,
                  fecha_reg,
                  fecha_mod,
                  estado_reg
                from detalle ';

            execute(v_consul);

          v_consulta:='select count(id_partida_ejecucion),
          					  sum(comprometido) as total_comprometido,
                              sum(ejecutado) as total_ejecutado,
                              sum(pagado) as total_pagado
          			 from ttemp_eval_det
	           		 where tipo_movimiento = '''||v_parametros.estado_func||''' ';

			--Devuelve la respuesta
			return v_consulta;

		end;


	/*********************************
 	#TRANSACCION:  'TES_GETPRWF_SEL'
 	#DESCRIPCION:  obtener id_proceso_wf para ver documentacion de tramite
 	#AUTOR:		breydi.vasquez
 	#FECHA:		10-11-2019
	***********************************/

	elsif(p_transaccion='TES_GETPRWF_SEL')then

		begin

			--Sentencia de la consulta de conteo de registros
			v_consulta:='select max(id_proceso_wf)
					    from wf.tproceso_wf
					    where nro_tramite = '''||v_parametros.nro_tramite||'''
                         and ';

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

ALTER FUNCTION pre.ft_partida_ejecucion_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
