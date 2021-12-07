CREATE OR REPLACE FUNCTION pre.ft_partida_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de presupuesto
 FUNCION: 		pre.ft_partida_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'pre.tpartida'
 AUTOR: 		 (admin)
 FECHA:	        23-11-2012 20:06:53
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
    v_where				varchar;
    v_inner				varchar;
    v_id_gestion		integer;
    v_gestion			varchar;
    v_add_filtro		varchar;

BEGIN

	v_nombre_funcion = 'pre.ft_partida_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'PRE_PAR_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		admin
 	#FECHA:		23-11-2012 20:06:53
	***********************************/

	if(p_transaccion='PRE_PAR_SEL')then

    	begin

            v_inner = '';

            IF pxp.f_existe_parametro(p_tabla,'id_cuenta') THEN

             v_inner = 'inner join conta.tcuenta_partida c on  c.id_partida = par.id_partida and c.id_cuenta ='|| v_parametros.id_cuenta::varchar;

            END IF;

            -- si existe el filtro de gestion actual , filtramos en funcion a la fecha actual
            IF  pxp.f_existe_parametro(p_tabla,'filtro_ges')   THEN

               --recuepra gestion actual
                v_gestion =  EXTRACT(YEAR FROM  now())::varchar;

                select
                 ges.id_gestion
                into
                 v_id_gestion
                from param.tgestion ges
              where ges.gestion::varchar  = v_gestion and ges.estado_reg = 'activo';
            END IF;

            v_add_filtro = '0=0 and ';
            IF  v_id_gestion is not null THEN
              v_add_filtro = ' par.id_gestion = '||v_id_gestion::varchar|| '  and  par.estado_reg = ''activo'' and ';
            END IF;

    		--Sentencia de la consulta
			v_consulta:='select
						par.id_partida,
						par.estado_reg,
						par.id_partida_fk,
						par.tipo,
						par.descripcion,
						par.codigo,
						par.id_usuario_reg,
						par.fecha_reg,
						par.id_usuario_mod,
						par.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,

                        par.nombre_partida,
                        par.sw_movimiento,
                        par.sw_transaccional,
                        par.id_gestion,
                        ges.gestion as desc_gestion

                        from pre.tpartida par
						inner join segu.tusuario usu1 on usu1.id_usuario = par.id_usuario_reg
                        inner join param.tgestion ges on ges.id_gestion = par.id_gestion
						left join segu.tusuario usu2 on usu2.id_usuario = par.id_usuario_mod  '||
                        v_inner || '
				        where  '||v_add_filtro;

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;
	/*********************************
     #TRANSACCION:  'ALM_CLA_ARB_SEL'
     #DESCRIPCION:    Consulta de datos
     #AUTOR:            Gonzalo Sarmiento
     #FECHA:            24-09-2012
    ***********************************/

    elseif(p_transaccion='PRE_PAR_ARB_SEL')then

        begin
              if(v_parametros.id_padre = '%') then
                v_where := ' par.id_partida_fk is NULL';

              else
                v_where := ' par.id_partida_fk = '||v_parametros.id_padre;
              end if;




            --Sentencia de la consulta
            v_consulta:='select
                        par.id_partida,
                        par.id_partida_fk,
                        par.codigo,
                        par.tipo,
                        par.descripcion,
                         case
                          when (par.id_partida_fk is null )then
                               ''raiz''::varchar
                          when (par.sw_transaccional = ''titular'' )then
                               ''hijo''::varchar
                         when (par.sw_transaccional = ''movimiento'' )then
                               ''hoja''::varchar
                          END as tipo_nodo,
                        par.nombre_partida,
                        par.sw_movimiento,
                        par.sw_transaccional,
                        par.id_gestion
                        from pre.tpartida par
                        where  '||v_where|| '
                        and id_gestion =  '||COALESCE( v_parametros.id_gestion,0)|| '
                        and tipo =  '''||COALESCE(v_parametros.tipo,'gasto')|| '''
                        ORDER BY par.codigo';
            raise notice '%',v_consulta;

            --Devuelve la respuesta
            return v_consulta;

        end;

	/*********************************
 	#TRANSACCION:  'PRE_PAR_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin
 	#FECHA:		23-11-2012 20:06:53
	***********************************/

	elsif(p_transaccion='PRE_PAR_CONT')then

		begin

        v_inner = '';

            IF pxp.f_existe_parametro(p_tabla,'id_cuenta') THEN
             v_inner = 'inner join conta.tcuenta_partida c on  c.id_partida = par.id_partida and c.id_cuenta ='|| v_parametros.id_cuenta::varchar;
            END IF;

            -- si existe el filtro de gestion actual , filtramos en funcion a la fecha actual
            IF  pxp.f_existe_parametro(p_tabla,'filtro_ges')   THEN

               --recuepra gestion actual
                v_gestion =  EXTRACT(YEAR FROM  now())::varchar;

                select
                 ges.id_gestion
                into
                 v_id_gestion
                from param.tgestion ges
              where ges.gestion::varchar  = v_gestion and ges.estado_reg = 'activo';
            END IF;

            v_add_filtro = '0=0 and ';
            IF  v_id_gestion is not null THEN
              v_add_filtro = ' par.id_gestion = '||v_id_gestion::varchar|| '  and  par.estado_reg = ''activo'' and ';
            END IF;
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(par.id_partida)
					    from pre.tpartida par
						inner join segu.tusuario usu1 on usu1.id_usuario = par.id_usuario_reg
                        inner join param.tgestion ges on ges.id_gestion = par.id_gestion
						left join segu.tusuario usu2 on usu2.id_usuario = par.id_usuario_mod  '||
                        v_inner || '
				       where  '||v_add_filtro;

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
   	#TRANSACCION:  'PRE_PLCUPAR_SEL'
   	#DESCRIPCION:	Consulta de datos plan de cuentas por partida
   	#AUTOR:		breydi vasquez
   	#FECHA:		22-10-2020
  	***********************************/

  	elsif(p_transaccion='PRE_PLCUPAR_SEL')then

      	begin

      		--Sentencia de la consulta
  			v_consulta:='select
                                cta.id_cuenta,
                                cupa.id_partida,
                                cupa.id_cuenta_partida,
                                cta.nombre_cuenta,
                                cta.sw_transaccional as tipo_nodo,
                                cta.nro_cuenta,
                                cta.desc_cuenta,
                                cta.id_moneda,
                                mon.codigo as desc_moneda,
                                cta.tipo_cuenta,
                                cta.sw_auxiliar,
                                cta.tipo_cuenta_pat,
                                cta.sw_transaccional,
                                cta.id_gestion,
                                cta.valor_incremento,
                                array_to_string( cta.eeff, '','',''null'')::varchar as eeff,
                                cta.sw_control_efectivo,
                                csc.id_config_subtipo_cuenta,
                                csc.nombre as desc_csc,
                                cta.tipo_act
                                from conta.tcuenta_partida cupa
                                left join conta.tcuenta cta on cta.id_cuenta = cupa.id_cuenta
                                left join param.tmoneda mon on mon.id_moneda = cta.id_moneda
                                left join conta.tconfig_subtipo_cuenta csc on csc.id_config_subtipo_cuenta = cta.id_config_subtipo_cuenta
                                inner join segu.tusuario usu1 on usu1.id_usuario = cupa.id_usuario_reg
                                left join segu.tusuario usu2 on usu2.id_usuario = cupa.id_usuario_mod
                                where cta.sw_transaccional=''movimiento''
                                and cta.estado_reg = ''activo''
                                and
  							  ';

  			--Definicion de la respuesta
  			v_consulta:=v_consulta||v_parametros.filtro;
  			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
  			raise notice '%',v_consulta;
  			--Devuelve la respuesta
  			return v_consulta;

  		end;


  	/*********************************
   	#TRANSACCION:  'PRE_PLCUPAR_CONT'
   	#DESCRIPCION:	Conteo de registros plan de cuentas por partida
   	#AUTOR:		breydi vasquez
   	#FECHA:		22-10-2020
  	***********************************/

  	elsif(p_transaccion='PRE_PLCUPAR_CONT')then

  		begin

  			--Sentencia de la consulta de conteo de registros
  			v_consulta:='select
                                count(cupa.id_cuenta_partida)
                                from conta.tcuenta_partida cupa
                                left join conta.tcuenta cta on cta.id_cuenta = cupa.id_cuenta
                                left join param.tmoneda mon on mon.id_moneda = cta.id_moneda
                                left join conta.tconfig_subtipo_cuenta csc on csc.id_config_subtipo_cuenta = cta.id_config_subtipo_cuenta
                                inner join segu.tusuario usu1 on usu1.id_usuario = cupa.id_usuario_reg
                                left join segu.tusuario usu2 on usu2.id_usuario = cupa.id_usuario_mod
                                where cta.sw_transaccional=''movimiento''
                                and cta.estado_reg = ''activo''
                                and
                                ';

  			--Definicion de la respuesta
  			v_consulta:=v_consulta||v_parametros.filtro;

  			--Devuelve la respuesta
  			return v_consulta;

  		end;

        /*********************************
        #TRANSACCION:  'PRE_PARAFA_SEL'
        #DESCRIPCION:	Consulta de datos
        #AUTOR:		maylee.perez
        #FECHA:		18-10-2021 20:06:53
        ***********************************/

        elsif(p_transaccion='PRE_PARAFA_SEL')then

            begin

                v_inner = '';

                IF pxp.f_existe_parametro(p_tabla,'id_cuenta') THEN

                 v_inner = 'inner join conta.tcuenta_partida c on  c.id_partida = par.id_partida and c.id_cuenta ='|| v_parametros.id_cuenta::varchar;

                END IF;

                -- si existe el filtro de gestion actual , filtramos en funcion a la fecha actual
                --IF  pxp.f_existe_parametro(p_tabla,'filtro_ges')   THEN

                   --recuepra gestion actual
                    v_gestion =  EXTRACT(YEAR FROM  now())::varchar;

                    select
                     ges.id_gestion
                    into
                     v_id_gestion
                    from param.tgestion ges
                  where ges.gestion::varchar  = v_gestion and ges.estado_reg = 'activo';
                --END IF;

                v_add_filtro = '0=0 and ';
                IF  v_id_gestion is not null THEN
                  v_add_filtro = ' par.id_gestion = '||v_id_gestion::varchar|| '  and  par.estado_reg = ''activo'' and ';
                END IF;

                --Sentencia de la consulta
                v_consulta:='select
                            par.id_partida,
                            par.estado_reg,
                            par.id_partida_fk,
                            par.tipo,
                            par.descripcion,
                            par.codigo,
                            par.id_usuario_reg,
                            par.fecha_reg,
                            par.id_usuario_mod,
                            par.fecha_mod,
                            usu1.cuenta as usr_reg,
                            usu2.cuenta as usr_mod,

                            par.nombre_partida,
                            par.sw_movimiento,
                            par.sw_transaccional,
                            par.id_gestion,
                            ges.gestion as desc_gestion

                            from pre.tpartida par
                            inner join segu.tusuario usu1 on usu1.id_usuario = par.id_usuario_reg
                            inner join param.tgestion ges on ges.id_gestion = par.id_gestion
                            left join segu.tusuario usu2 on usu2.id_usuario = par.id_usuario_mod

                            left join pre.tconcepto_partida conp  on conp.id_partida =par.id_partida
							left join param.tconcepto_ingas cin on cin.id_concepto_ingas= conp.id_concepto_ingas  '||
                            v_inner || '
                            where cin.sw_autorizacion::varchar like ''%fondo_avance%''::varchar and  '||v_add_filtro;

                --Definicion de la respuesta
                v_consulta:=v_consulta||v_parametros.filtro;
                v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
                raise notice 'resp %',v_consulta;
                --Devuelve la respuesta
                return v_consulta;

            end;

          /*********************************
          #TRANSACCION:  'PRE_PARAFA_CONT'
          #DESCRIPCION:	Conteo de registros
          #AUTOR:		maylee.perez
          #FECHA:		18-10-2021 20:06:53
          ***********************************/

          elsif(p_transaccion='PRE_PARAFA_CONT')then

              begin

              v_inner = '';

                  IF pxp.f_existe_parametro(p_tabla,'id_cuenta') THEN
                   v_inner = 'inner join conta.tcuenta_partida c on  c.id_partida = par.id_partida and c.id_cuenta ='|| v_parametros.id_cuenta::varchar;
                  END IF;

                  -- si existe el filtro de gestion actual , filtramos en funcion a la fecha actual
                  IF  pxp.f_existe_parametro(p_tabla,'filtro_ges')   THEN

                     --recuepra gestion actual
                      v_gestion =  EXTRACT(YEAR FROM  now())::varchar;

                      select
                       ges.id_gestion
                      into
                       v_id_gestion
                      from param.tgestion ges
                    where ges.gestion::varchar  = v_gestion and ges.estado_reg = 'activo';
                  END IF;

                  v_add_filtro = '0=0 and ';
                  IF  v_id_gestion is not null THEN
                    v_add_filtro = ' par.id_gestion = '||v_id_gestion::varchar|| '  and  par.estado_reg = ''activo'' and ';
                  END IF;
                  --Sentencia de la consulta de conteo de registros
                  v_consulta:='select count(par.id_partida)
                              from pre.tpartida par
                              inner join segu.tusuario usu1 on usu1.id_usuario = par.id_usuario_reg
                              inner join param.tgestion ges on ges.id_gestion = par.id_gestion
                              left join segu.tusuario usu2 on usu2.id_usuario = par.id_usuario_mod

                              left join pre.tconcepto_partida conp  on conp.id_partida =par.id_partida
                              left join param.tconcepto_ingas cin on cin.id_concepto_ingas= conp.id_concepto_ingas  '||
                              v_inner || '
                              where cin.sw_autorizacion::varchar like ''%fondo_avance%''::varchar and  '||v_add_filtro;

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
