CREATE OR REPLACE FUNCTION pre.f_rep_ejecucion (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS SETOF record AS
$body$
DECLARE

v_parametros  		record;
v_nombre_funcion   	text;
v_resp				varchar;
v_sw 				integer;
v_sw2 				integer;
v_count 			integer;
v_consulta 			varchar;
v_registros  		record;  -- PARA ALMACENAR EL CONJUNTO DE DATOS RESULTADO DEL SELECT
v_reg_resp  		record;


v_i 				integer;
v_nivel_inicial		integer;
v_total 			numeric;
v_tipo_cuenta		varchar;
v_incluir_cierre	varchar;
va_id_presupuesto	INTEGER[];
v_desc_categ		text;
v_cod_cate			varchar;

/*dev: breydi.vasquez
date: 09/09/2020*/
v_id_cat_prog		record;
va_id_presupuesto_cat  integer[];

BEGIN

     v_nombre_funcion = 'pre.f_rep_ejecucion';
     v_parametros = pxp.f_get_record(p_tabla);


    /*********************************
     #TRANSACCION:    'PRE_EJECUCION_REP'
     #DESCRIPCION:     reporte de ejecucion presupuestaria
     #AUTOR:           rensi arteaga copari  kplian
     #FECHA:           26-04-2016
    ***********************************/

	IF(p_transaccion='PRE_EJECUCION_REP')then

        --raise exception 'error';

        -- 1) Crea una tabla temporal con los datos que se utilizaran

        CREATE TEMPORARY TABLE temp_prog (
                                id_temp 	integer,
                                id_partida integer,
                                codigo_partida varchar,
                                nombre_partida varchar,
                                id_partida_fk integer,
                                nivel_partida integer,
                                sw_transaccional varchar,
                                importe	numeric,
                                importe_aprobado NUMERIC,
                                formulado NUMERIC,
                                comprometido NUMERIC,
                                ejecutado NUMERIC,
                                pagado NUMERIC,
                                ajustado NUMERIC,
                                porc_ejecucion NUMERIC) ON COMMIT DROP;


         --determinar array de presupuestos
         --raise exception '%',v_parametros.id_cp_organismo_fin;

             IF v_parametros.tipo_reporte = 'programa' and v_parametros.id_cp_programa is not null and v_parametros.id_cp_programa != 0 THEN

                     SELECT
                         pxp.aggarray(p.id_presupuesto)
                     into
                        va_id_presupuesto
                     FROM pre.tpresupuesto p
                     inner join pre.tcategoria_programatica cp on cp.id_categoria_programatica = p.id_categoria_prog
                     where cp.id_cp_programa = v_parametros.id_cp_programa
                            and p.tipo_pres::text = ANY (string_to_array(v_parametros.tipo_pres::text,','));


            ELSEIF v_parametros.tipo_reporte = 'categoria' and v_parametros.id_categoria_programatica is not null and v_parametros.id_categoria_programatica != 0 THEN

                     SELECT
                         pxp.aggarray(p.id_presupuesto)
                     into
                        va_id_presupuesto
                     FROM pre.tpresupuesto p
                     where p.id_categoria_prog = v_parametros.id_categoria_programatica
                     and p.tipo_pres  = ANY (string_to_array(v_parametros.tipo_pres::text,','));


            ELSEIF v_parametros.tipo_reporte = 'presupuesto' and v_parametros.id_presupuesto is not null and v_parametros.id_presupuesto != 0 THEN

                 va_id_presupuesto[1] = v_parametros.id_presupuesto;

                 select
                 	 (cat.codigo_categoria||' '||cat.descripcion)::text
         		 into
	                 v_desc_categ
                 from pre.tpresupuesto pr
                 inner join pre.vcategoria_programatica cat on cat.id_categoria_programatica = pr.id_categoria_prog
                 where pr.id_presupuesto = v_parametros.id_presupuesto;

            ELSEIF v_parametros.tipo_reporte = 'proyecto' and v_parametros.id_cp_proyecto is not null and v_parametros.id_cp_proyecto != 0 THEN

                   SELECT
                       pxp.aggarray(p.id_presupuesto)
                   into
                       va_id_presupuesto
                   FROM pre.tpresupuesto p
                   inner join pre.tcategoria_programatica cp on cp.id_categoria_programatica = p.id_categoria_prog
                   where cp.id_cp_proyecto = v_parametros.id_cp_proyecto
                   and p.tipo_pres::text  = ANY (string_to_array(v_parametros.tipo_pres::text,','));

            ELSEIF v_parametros.tipo_reporte = 'actividad' and v_parametros.id_cp_actividad is not null and v_parametros.id_cp_actividad != 0 THEN

                   SELECT
                       pxp.aggarray(p.id_presupuesto)
                   into
                       va_id_presupuesto
                   FROM pre.tpresupuesto p
                   inner join pre.tcategoria_programatica cp on cp.id_categoria_programatica = p.id_categoria_prog
                   where cp.id_cp_actividad = v_parametros.id_cp_actividad
                   and p.tipo_pres::text  = ANY (string_to_array(v_parametros.tipo_pres::text,','));

            ELSEIF v_parametros.tipo_reporte = 'orga_financ' and v_parametros.id_cp_organismo_fin is not null and v_parametros.id_cp_organismo_fin != 0 THEN

                   SELECT
                       pxp.aggarray(p.id_presupuesto)
                   into
                       va_id_presupuesto
                   FROM pre.tpresupuesto p
                   inner join pre.tcategoria_programatica cp on cp.id_categoria_programatica = p.id_categoria_prog
                   where cp.id_cp_organismo_fin = v_parametros.id_cp_organismo_fin
                   and p.tipo_pres::text  = ANY (string_to_array(v_parametros.tipo_pres::text,','));

            ELSEIF v_parametros.tipo_reporte = 'fuente_financ' and v_parametros.id_cp_fuente_fin is not null and v_parametros.id_cp_fuente_fin != 0 THEN

                   SELECT
                       pxp.aggarray(p.id_presupuesto)
                   into
                       va_id_presupuesto
                   FROM pre.tpresupuesto p
                   inner join pre.tcategoria_programatica cp on cp.id_categoria_programatica = p.id_categoria_prog
                   where cp.id_cp_fuente_fin = v_parametros.id_cp_fuente_fin
                   and p.tipo_pres::text  = ANY (string_to_array(v_parametros.tipo_pres::text,','));

            ELSEIF v_parametros.tipo_reporte = 'unidad_ejecutora' and v_parametros.id_unidad_ejecutora is not null and v_parametros.id_unidad_ejecutora != 0 THEN

                   SELECT
                       pxp.aggarray(p.id_presupuesto)
                   into
                       va_id_presupuesto
                   FROM pre.tpresupuesto p
                   inner join pre.tcategoria_programatica cp on cp.id_categoria_programatica = p.id_categoria_prog
                   where cp.id_unidad_ejecutora = v_parametros.id_unidad_ejecutora
                   and cp.id_gestion = v_parametros.id_gestion
                 and p.tipo_pres::text  = ANY (string_to_array(v_parametros.tipo_pres::text,','));

             ELSE


                   SELECT
                       pxp.aggarray(p.id_presupuesto)
                   into
                      va_id_presupuesto
                   FROM pre.vpresupuesto p
                   where p.id_gestion = v_parametros.id_gestion
                   and p.tipo_pres  = ANY (string_to_array(v_parametros.tipo_pres::text,','));

           END IF;


         -- listado consolidado segun parametros
       if (v_parametros.tipo_reporte = 'centro_costo') then

    		        CREATE TEMPORARY TABLE temp_prog_costo (
                    			cod_pro VARCHAR,
                                codigo VARCHAR,
                                descripcion VARCHAR,
                                id_categoria INTEGER,
                                importe	NUMERIC,
                                importe_aprobado NUMERIC,
                                modificado NUMERIC,
                                vigente NUMERIC,
                                comprometido NUMERIC,
                                ejecutado NUMERIC,
                                ajustado NUMERIC,
                                pagado NUMERIC,
                                porc_ejecucion NUMERIC) ON COMMIT DROP;

            PERFORM pre.f_ins_centro_costo(v_parametros.id_gestion,
            									 v_parametros.tipo_pres,
                                                 v_parametros.fecha_ini,
                                                 v_parametros.fecha_fin);

    		insert into temp_prog_costo(cod_pro, descripcion,id_categoria
                ,importe,importe_aprobado,modificado,
                vigente,comprometido,ejecutado,pagado,porc_ejecucion)

            SELECT
			  vca.codigo_categoria,
              'TOTAL' as descripcion,
              pro.id_categoria,
              sum(pro.importe),
              sum(pro.importe_aprobado),
              sum(pro.modificado),
              sum(pro.vigente),
              sum(pro.comprometido),
              sum(pro.ejecutado),
              sum(pro.pagado)
              ,case when sum(pro.importe_aprobado) =0 then
                0
              else
                round(((sum(pro.ejecutado)/sum(pro.importe_aprobado))*100),2)
              end
            FROM temp_prog_costo pro
            inner join pre.tcategoria_programatica catp on catp.id_categoria_programatica = pro.id_categoria
            inner join pre.vcategoria_programatica vca on vca.id_categoria_programatica = catp.id_categoria_programatica
            group by pro.id_categoria,
		             vca.codigo_categoria;

			FOR v_registros in ( SELECT
                                COALESCE(v_desc_categ,'')::text as desc_cat,
            					cod_pro as categoria,
                                0::integer as id_partida,
                                codigo as codigo_partida,
                                descripcion::varchar as nombre_partida,
                                0::integer as nivel_partida,
                                (importe) as importe,
                                (importe_aprobado) as importe_aprobado,
                                (modificado) as formulado,
                                (comprometido) as comprometido,
                                (ejecutado) as ejecutado,
                                (pagado) as pagado,
            				            (vigente) as ajustado,
                                (porc_ejecucion) as porc_ejecucion,
                                ''::varchar as codigo_categoria,
                                ''::varchar as descripcion_cate
                                from temp_prog_costo
                                order by id_categoria,
                                codigo)LOOP

                             RETURN NEXT v_registros;
			end loop;

      /*
      dev: breydi.vasquez
      descripcion: data para reporte Resumen unidad_ejecutora y CategoriaProgramatica
      date: 09/09/2020
      */
      elsif (v_parametros.tipo_reporte = 'resumen_unidad_ejecutora' or v_parametros.tipo_reporte = 'resumen_categoria') then

         for v_id_cat_prog in (SELECT
                                       id_categoria_programatica
                                       FROM
                                       pre.vcategoria_programatica cpr
                                       WHERE  id_gestion = v_parametros.id_gestion
                                       order by id_categoria_programatica ASC)

           loop

                  SELECT
                      pxp.aggarray(p.id_presupuesto)
                  into
                     va_id_presupuesto_cat
                  FROM pre.tpresupuesto p
                  where p.id_categoria_prog = v_id_cat_prog.id_categoria_programatica
                  and p.tipo_pres  = ANY (string_to_array(v_parametros.tipo_pres::text,','));

                 FOR v_registros in (
                 select
                    par.id_partida,
                    par.codigo as codigo_partida,
                    par.nombre_partida,
                    par.sw_transaccional,
                    par.nivel_partida
                 from pre.tpartida par
                 where       par.id_gestion = v_parametros.id_gestion
                        and  par.id_partida_fk is null
                        and  par.tipo in (select
                                         tipr.movimiento
                                         from pre.ttipo_presupuesto tipr
                                         where tipr.codigo = ANY(string_to_array(v_parametros.tipo_pres::text,','))
                                         group by
                                         tipr.movimiento)
                        ) LOOP


                PERFORM pre.f_rep_ejecucion_recursivo(
                                                v_id_cat_prog.id_categoria_programatica,
                                                v_registros.id_partida,
                                                v_registros.codigo_partida,
                                                v_registros.nombre_partida,
                                                va_id_presupuesto_cat,
                                                v_parametros.fecha_ini,
                                                v_parametros.fecha_fin,
                                                v_registros.sw_transaccional,
                                                v_registros.nivel_partida);


            END LOOP;

           end loop;

           if v_parametros.tipo_reporte = 'resumen_unidad_ejecutora' then
              FOR v_registros in (
                                   SELECT
                                     ''::text as desc_cat,
                                     ''::varchar as categoria,
                                     t.id_partida,
                                     t.codigo_partida,
                                     t.nombre_partida,
                                     t.nivel_partida,
                                     (t.importe) as importe,
                                     (t.importe_aprobado) as importe_aprobado,
                                     (t.formulado) as formulado,
                                     (t.comprometido) as comprometido,
                                     (t.ejecutado) as ejecutado,
                                     (t.pagado) as pagado,
                                     (t.ajustado) as ajustado,
                                     (t.porc_ejecucion) as porc_ejecucion,
                                     cat.codigo_categoria::varchar,
                                     cat.descripcion::varchar as descripcion_cate
                                   FROM temp_prog t
                                   inner join pre.vcategoria_programatica cat on cat.id_categoria_programatica = t.id_temp
                                   WHERE   t.nivel_partida  = 0

                                  order by cat.codigo_categoria asc) LOOP

                    RETURN NEXT v_registros;

            END LOOP;
            elsif v_parametros.tipo_reporte = 'resumen_categoria' then

               FOR v_registros in (
                                   SELECT
                                     ''::text as desc_cat,
                                     ''::varchar as categoria,
                                     t.id_partida,
                                     t.codigo_partida,
                                     t.nombre_partida,
                                     t.nivel_partida,
                                     sum((t.importe)) as importe,
                                     sum((t.importe_aprobado)) as importe_aprobado,
                                     sum((t.formulado)) as formulado,
                                     sum((t.comprometido)) as comprometido,
                                     sum((t.ejecutado)) as ejecutado,
                                     sum((t.pagado)) as pagado,
                                     sum((t.ajustado)) as ajustado,
                                     sum((t.porc_ejecucion)) as porc_ejecucion,
                                     (cat.codigo_programa||' '||cat.codigo_proyecto||' '||cat.codigo_actividad)::varchar as codigo_categoria,
                                     (cat.desc_actividad)::varchar as descripcion_cate
                                   FROM temp_prog t
                                   inner join pre.vcategoria_programatica cat on cat.id_categoria_programatica = t.id_temp
                                   WHERE   t.nivel_partida  = 0
                                   group by
                                   t.id_partida,
                                   t.codigo_partida,
                                   t.nombre_partida,
                                   t.nivel_partida,
                                   cat.codigo_programa,
                                   cat.codigo_proyecto,
                                   cat.codigo_actividad,
                                   cat.desc_actividad
                                   order by cat.codigo_actividad asc) LOOP

                    RETURN NEXT v_registros;

            END LOOP;
            end if;

       else
         -- lista las partida basicas de cada presupuesto
         FOR v_registros in (
                  select
                     par.id_partida,
                     par.codigo as codigo_partida,
                     par.nombre_partida,
                     par.sw_transaccional,
                     par.nivel_partida
                  from pre.tpartida par
                  where       par.id_gestion = v_parametros.id_gestion
                         and  par.id_partida_fk is null
                         and  par.tipo in (select
                                          tipr.movimiento
                                          from pre.ttipo_presupuesto tipr
                                          where tipr.codigo = ANY(string_to_array(v_parametros.tipo_pres::text,','))
                                          group by
                                          tipr.movimiento)
                         ) LOOP


                 PERFORM pre.f_rep_ejecucion_recursivo(
                                                 null,
                                                 v_registros.id_partida,
                                                 v_registros.codigo_partida,
                                                 v_registros.nombre_partida,
                                                 va_id_presupuesto,
                                                 v_parametros.fecha_ini,
                                                 v_parametros.fecha_fin,
                                                 v_registros.sw_transaccional,
                                                 v_registros.nivel_partida);


         END LOOP;

         FOR v_registros in (
                              SELECT
                                COALESCE(v_desc_categ,'')::text as desc_cat,
                                ''::varchar as categoria,
                                id_partida,
                                codigo_partida,
                                nombre_partida,
                                nivel_partida,
                                (importe) as importe,
                                (importe_aprobado) as importe_aprobado,
                                (formulado) as formulado,
                                (comprometido) as comprometido,
                                (ejecutado) as ejecutado,
                                (pagado) as pagado,
                                (ajustado) as ajustado,
                                (porc_ejecucion) as porc_ejecucion,
                                ''::varchar as codigo_categoria,
                                ''::varchar as descripcion_cate
                              FROM temp_prog
                              WHERE

                                  CASE WHEN v_parametros.nivel = 4  THEN   -- todos
                                         0 = 0
                                      WHEN v_parametros.nivel = 5  THEN     --solo movimiento
                                        sw_transaccional = 'movimiento' or nivel_partida = 0
                                      ELSE
                                        nivel_partida <= v_parametros.nivel
                                      END

                             order by codigo_partida) LOOP

               RETURN NEXT v_registros;

       END LOOP;
    END IF;
 /*********************************
 #TRANSACCION:    'PRE_EJEXPAR_REP'
 #DESCRIPCION:     reporte de ejecucion por partida
 #AUTOR:           rensi arteaga copari  kplian
 #FECHA:           26-04-2016
***********************************/

ELSEIF(p_transaccion='PRE_EJEXPAR_REP')then


      if v_parametros.tipo_reporte = 'presupuesto' then
                  select
                  (cat.codigo_categoria ||' - '|| cat.descripcion)::varchar into v_cod_cate
                  from pre.tpresupuesto pr
                  inner join pre.vcategoria_programatica cat on cat.id_categoria_programatica = pr.id_categoria_prog
                  where pr.id_presupuesto = v_parametros.id_presupuesto;
      else v_cod_cate = '';
      end if;

      if ( v_parametros.tipo_reporte = 'centro_costo') then
            FOR v_registros in (SELECT * FROM(SELECT 	v_cod_cate::varchar as cod_cat,
                                                        p.id_presupuesto,
                                                        p.codigo_cc,
                                                        p.id_categoria_prog,
                                                        sum(COALESCE(prpa.importe, 0::numeric)) AS importe,
                                                        sum(prpa.importe_aprobado) as importe_aprobado,
                                                        sum(pre.f_get_estado_presupuesto_mb_x_fechas(prpa.id_presupuesto, prpa.id_partida,'formulado',v_parametros.fecha_ini,v_parametros.fecha_fin)) AS formulado,
                                                        sum(pre.f_get_estado_presupuesto_mb_x_fechas(prpa.id_presupuesto, prpa.id_partida,'comprometido',v_parametros.fecha_ini,v_parametros.fecha_fin)) AS comprometido,
                                                        sum(pre.f_get_estado_presupuesto_mb_x_fechas(prpa.id_presupuesto, prpa.id_partida,'ejecutado',v_parametros.fecha_ini,v_parametros.fecha_fin)) AS ejecutado,
                                                        sum(pre.f_get_estado_presupuesto_mb_x_fechas(prpa.id_presupuesto, prpa.id_partida, 'pagado',v_parametros.fecha_ini,v_parametros.fecha_fin)) AS pagado


                                              FROM pre.tpresup_partida prpa
                                              INNER JOIN pre.vpresupuesto_cc_x_partida p on p.id_presupuesto = prpa.id_presupuesto
                                              WHERE
                                                 p.tipo_pres::text = ANY (string_to_array(v_parametros.tipo_pres::text,',')) and
                                              CASE
                                                      WHEN v_parametros.id_partida = 0  THEN   -- todos
                                                            0 = 0 and p.id_gestion = v_parametros.id_gestion
                                                      WHEN v_parametros.id_partida is null then
                                                          p.id_gestion = v_parametros.id_gestion
                                                      ELSE
                                                            prpa.id_partida = v_parametros.id_partida
                                                      END
                                              GROUP BY
                                              p.id_presupuesto,
                                              p.codigo_cc,
                                              p.id_categoria_prog

											union

                                            SELECT
                                            v_cod_cate::varchar as cod_cat,
                                            0::integer,
                                            'TOTAL CATEGORIA: '|| cat.codigo_categoria::varchar,
                                            p.id_categoria_prog,
                                            sum(COALESCE(prpa.importe, 0::numeric)) AS importe,
                                            sum(prpa.importe_aprobado) as importe_aprobado,
                                            sum(pre.f_get_estado_presupuesto_mb_x_fechas(prpa.id_presupuesto, prpa.id_partida,'formulado',v_parametros.fecha_ini,v_parametros.fecha_fin)) AS formulado,
                                            sum(pre.f_get_estado_presupuesto_mb_x_fechas(prpa.id_presupuesto, prpa.id_partida,'comprometido',v_parametros.fecha_ini,v_parametros.fecha_fin)) AS comprometido,
                                            sum(pre.f_get_estado_presupuesto_mb_x_fechas(prpa.id_presupuesto, prpa.id_partida,'ejecutado',v_parametros.fecha_ini,v_parametros.fecha_fin)) AS ejecutado,
                                            sum(pre.f_get_estado_presupuesto_mb_x_fechas(prpa.id_presupuesto, prpa.id_partida, 'pagado',v_parametros.fecha_ini,v_parametros.fecha_fin)) AS pagado
                                            FROM pre.tpresup_partida prpa
                                            INNER JOIN pre.vpresupuesto_cc_x_partida p on p.id_presupuesto = prpa.id_presupuesto
                                            inner join pre.vcategoria_programatica cat on cat.id_categoria_programatica = p.id_categoria_prog
                                            WHERE
                                            p.tipo_pres::text = ANY (string_to_array(v_parametros.tipo_pres::text,',')) and
                                            CASE
                                                    WHEN v_parametros.id_partida = 0  THEN   -- todos
                                                          0 = 0 and p.id_gestion = v_parametros.id_gestion
                                                    WHEN v_parametros.id_partida is null then
	                                                    p.id_gestion = v_parametros.id_gestion
                                                    ELSE
                                                          prpa.id_partida = v_parametros.id_partida
                                                    END
                                            GROUP BY
                                            p.id_categoria_prog,
                                            cat.codigo_categoria

                                    ) tmp
                                              WHERE
                                                  tmp.importe > 0 or
                                                  tmp.importe_aprobado > 0 or
                                                  tmp.formulado > 0 or
                                                  tmp.comprometido > 0 or
                                                  tmp.ejecutado > 0 or
                                                  tmp.pagado > 0
                                               order by
                                                  id_categoria_prog,
                                                  tmp.codigo_cc
                                             ) LOOP

               RETURN NEXT v_registros;


      END LOOP;
    else

      FOR v_registros in (SELECT * FROM(SELECT 			v_cod_cate::varchar as cod_cat,
                                                        p.id_presupuesto,
                                                        p.codigo_cc,
                                                        p.id_categoria_prog,
                                                        sum(COALESCE(prpa.importe, 0::numeric)) AS importe,
                                                        sum(prpa.importe_aprobado) as importe_aprobado,
                                                        sum(pre.f_get_estado_presupuesto_mb_x_fechas(prpa.id_presupuesto, prpa.id_partida,'formulado',v_parametros.fecha_ini,v_parametros.fecha_fin)) AS formulado,
                                                        sum(pre.f_get_estado_presupuesto_mb_x_fechas(prpa.id_presupuesto, prpa.id_partida,'comprometido',v_parametros.fecha_ini,v_parametros.fecha_fin)) AS comprometido,
                                                        sum(pre.f_get_estado_presupuesto_mb_x_fechas(prpa.id_presupuesto, prpa.id_partida,'ejecutado',v_parametros.fecha_ini,v_parametros.fecha_fin)) AS ejecutado,
                                                        sum(pre.f_get_estado_presupuesto_mb_x_fechas(prpa.id_presupuesto, prpa.id_partida, 'pagado',v_parametros.fecha_ini,v_parametros.fecha_fin)) AS pagado


                                              FROM pre.tpresup_partida prpa
                                              INNER JOIN pre.vpresupuesto_cc_x_partida p on p.id_presupuesto = prpa.id_presupuesto
                                              WHERE
                                                 p.tipo_pres::text = ANY (string_to_array(v_parametros.tipo_pres::text,',')) and

											  CASE
                                               WHEN v_parametros.tipo_reporte = 'categoria' and v_parametros.id_categoria_programatica is not null THEN
                                                   CASE
                                                    WHEN v_parametros.id_categoria_programatica = 0   THEN   -- todos
                                                           0=0 and p.id_gestion = v_parametros.id_gestion
                                                        ELSE
                                                            p.id_categoria_prog = v_parametros.id_categoria_programatica
                                                        END

                                               WHEN v_parametros.tipo_reporte = 'presupuesto' and v_parametros.id_presupuesto is not null THEN
                                                   CASE
                                                    WHEN v_parametros.id_presupuesto = 0   THEN   -- todos
                                                           0=0 and p.id_gestion = v_parametros.id_gestion
                                                        ELSE
                                                            p.id_presupuesto = v_parametros.id_presupuesto
                                                        END
                                               WHEN v_parametros.tipo_reporte = 'programa' and v_parametros.id_cp_programa is not null THEN
                                                    CASE
                                                      WHEN v_parametros.id_cp_programa = 0   THEN   -- todos
                                                             0=0 and p.id_gestion = v_parametros.id_gestion
                                                          ELSE
                                                              p.id_cp_programa = v_parametros.id_cp_programa
                                                          END
                                               WHEN v_parametros.tipo_reporte = 'proyecto' and v_parametros.id_cp_proyecto is not null THEN
                                                    CASE
                                                      WHEN v_parametros.id_cp_proyecto = 0   THEN   -- todos
                                                             0=0 and p.id_gestion = v_parametros.id_gestion
                                                          ELSE
                                                              p.id_cp_proyecto = v_parametros.id_cp_proyecto
                                                          END
                                               WHEN v_parametros.tipo_reporte = 'actividad' and v_parametros.id_cp_actividad is not null THEN
                                                    CASE
                                                      WHEN v_parametros.id_cp_actividad = 0   THEN   -- todos
                                                             0=0 and p.id_gestion = v_parametros.id_gestion
                                                          ELSE
                                                              p.id_cp_actividad = v_parametros.id_cp_actividad
                                                          END
                                               WHEN v_parametros.tipo_reporte = 'orga_financ' and v_parametros.id_cp_organismo_fin is not null THEN
                                                    CASE
                                                      WHEN v_parametros.id_cp_organismo_fin = 0   THEN   -- todos
                                                             0=0 and p.id_gestion = v_parametros.id_gestion
                                                          ELSE
                                                              p.id_cp_organismo_fin = v_parametros.id_cp_organismo_fin
                                                          END
                                               WHEN v_parametros.tipo_reporte = 'fuente_financ' and v_parametros.id_cp_fuente_fin is not null THEN
                                                    CASE
                                                      WHEN v_parametros.id_cp_fuente_fin = 0   THEN   -- todos
                                                             0=0 and p.id_gestion = v_parametros.id_gestion
                                                          ELSE
                                                              p.id_cp_fuente_fin = v_parametros.id_cp_fuente_fin
                                                    	  END
                                               WHEN v_parametros.tipo_reporte = 'unidad_ejecutora' and v_parametros.id_unidad_ejecutora is not null THEN
                                                    CASE
                                                      WHEN v_parametros.id_unidad_ejecutora = 0   THEN   -- todos
                                                             0=0 and p.id_gestion = v_parametros.id_gestion
                                                          ELSE
                                                              p.id_unidad_ejecutora = v_parametros.id_unidad_ejecutora
                                                          END
                                               	END
                                               AND
                                               CASE
                                                    WHEN v_parametros.id_partida = 0  THEN   -- todos
                                                          0 = 0
                                                    WHEN v_parametros.id_partida is null then
	                                                    p.id_gestion = v_parametros.id_gestion
                                                    ELSE
                                                          prpa.id_partida = v_parametros.id_partida
                                                    END

                                              GROUP BY
                                              p.id_presupuesto,
                                              p.codigo_cc,
                                              p.id_categoria_prog

                                    ) tmp
                                              WHERE
                                                  tmp.importe > 0 or
                                                  tmp.importe_aprobado > 0 or
                                                  tmp.formulado > 0 or
                                                  tmp.comprometido > 0 or
                                                  tmp.ejecutado > 0 or
                                                  tmp.pagado > 0
                                               order by
                                                  tmp.codigo_cc
                                             ) LOOP

                          -- raise exception '... %',v_registros;
               RETURN NEXT v_registros;

      END LOOP;
	END IF;

END IF;

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
COST 100 ROWS 1000;
