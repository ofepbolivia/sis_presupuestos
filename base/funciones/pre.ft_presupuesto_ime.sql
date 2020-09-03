CREATE OR REPLACE FUNCTION pre.ft_presupuesto_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de presupuesto
 FUNCION: 		pre.ft_presupuesto_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'pre.tpresupuesto'
 AUTOR: 		Gonzalo Sarmiento Sejas
 FECHA:	        27-02-2013 00:30:39
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
	v_id_presupuesto		integer;
    v_registros_ges			record;
    v_id_gestion_destino	integer;
    v_conta 				integer;
    v_registros 			record;
    v_reg_cc_ori 			record;
    v_reg_pres		    	record;
    v_id_centro_costo 		integer;

    v_num_tramite			varchar;
    v_id_proceso_wf			integer;
    v_id_estado_wf			integer;
    v_codigo_estado 		varchar;
    v_codigo_wf				varchar;
    v_id_depto				integer;
    v_obs					varchar;
    v_id_estado_actual		integer;
    v_id_tipo_estado		integer;
    v_codigo_estado_siguiente			varchar;
    v_registros_proc 					record;
    v_codigo_tipo_pro   				varchar;
    v_operacion 						varchar;
    v_registros_pp 						record;
    v_id_funcionario  					integer;
    v_id_usuario_reg  					integer;
    v_id_estado_wf_ant  				integer;
    v_acceso_directo 					varchar;
    v_clase 							varchar;
    v_parametros_ad 					varchar;
    v_tipo_noti 						varchar;
    v_titulo  							varchar;
    v_id_presupuesto_dos				integer;

    v_fecha_ini							date;
    v_fecha_fin							date;

    v_id_presupuesto_funcionario		integer;
    v_funcionarios						record;
    v_id_funcionario_recu				integer;

    v_presu_partida						record;

    --(may)
    v_id_memoria_calculo				integer;

    v_registros_cig						record;
    v_centro_costo						varchar;
    v_id_partida						integer;
    v_des_partida						varchar;
    v_id_gestion						integer;
    v_importe							numeric;
    v_id_usuario_resp					integer;
    v_id_formulacion_presu				integer;
    v_desc_funcionario1_res				varchar;
    v_desc_persona_reg					varchar;
    v_fecha_reg							varchar;
    v_estado_pre						varchar;
    v_id_memoria_calculo_det_presu		integer;
    v_id_memoria_calculo_presu			integer;
    v_registros_det						record;
    v_importe_presu_par					numeric;


BEGIN

    v_nombre_funcion = 'pre.ft_presupuesto_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'PRE_PRE_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		RAC
 	#FECHA:		08-06-2017 00:30:39
	***********************************/

	if(p_transaccion='PRE_PRE_INS')then

        begin
        	-------------------------------------------------------------------------
            --  EL ID de centro de costo siempre sera igual al id de presupeusto
            -------------------------------------------------------------------------

             --  validar que el el id_tipo_cc sea un nodo hoja y tenga un techo presupeustario definidio

             select
               tcc.movimiento,
               tcc.id_ep,
               tcc.codigo,
               tcc.descripcion
              into
               v_registros
             from param.ttipo_cc tcc
             where tcc.id_tipo_cc = v_parametros.id_tipo_cc;

            IF  v_registros.movimiento != 'si' THEN
               raise exception 'solo puede crear centro de costos para tipos que sean de  movimiento';
            END IF;

            --  validar que cada id_tipo_cc solo se use para una vez en cada gestion
            IF  EXISTS (select 1
                        from param.tcentro_costo cc
                        where    cc.id_tipo_cc = v_parametros.id_tipo_cc
                             and cc.id_gestion = v_parametros.id_gestion
                             and cc.estado_reg = 'activo') THEN
                 raise exception 'este tipo ya tiene un centro de costo registrado para esta gestión';
            END IF;


        	--Sentencia de la insercion
        	insert into param.tcentro_costo(
              estado_reg,
              id_ep,
              id_gestion,
              id_uo,
              id_usuario_reg,
              fecha_reg,
              id_usuario_mod,
              fecha_mod,
              id_tipo_cc
          	) values(
              'activo',
              v_registros.id_ep,  --RAC 05/06/2017 la ep se origina en el tipo de centro
              v_parametros.id_gestion,
              v_parametros.id_uo,
              p_id_usuario,
              now(),
              null,
              null,
              v_parametros.id_tipo_cc
			)RETURNING id_centro_costo into v_id_centro_costo;




            --Sentencia de la insercion
           insert into pre.tpresupuesto(
                    id_presupuesto,
                    id_centro_costo,
                    estado,
                    estado_reg,
                    id_usuario_reg,
                    fecha_reg,
                    descripcion,
                    tipo_pres,
                    sw_consolidado,
                    id_categoria_prog,
                    fecha_inicio_pres,
                    fecha_fin_pres
            ) values(
                    v_id_centro_costo,
                    v_id_centro_costo,
                    'borrador', --crea el presupeusto en estado borrador
                    'activo',
                    p_id_usuario,
                    now(),
                    v_parametros.descripcion,
                    v_parametros.tipo_pres,
                    v_parametros.sw_consolidado,
                    v_parametros.id_categoria_prog,
                    v_parametros.fecha_inicio_pres,
                    v_parametros.fecha_fin_pres

             );




			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Presupuestos almacenado(a) con exito (id_presupuesto'||v_id_presupuesto||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_presupuesto',v_id_centro_costo::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'PRE_PRE_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		Gonzalo Sarmiento Sejas
 	#FECHA:		27-02-2013 00:30:39
	***********************************/

	elsif(p_transaccion='PRE_PRE_MOD')then

		begin



            select
              p.estado,
              p.tipo_pres
            into
             v_reg_pres
            from pre.tpresupuesto p
            where  p.id_presupuesto = v_parametros.id_presupuesto;

            IF v_reg_pres.estado != 'borrador' and v_reg_pres.tipo_pres != v_parametros.tipo_pres THEN
              raise exception 'Solo puede editar el tipo de presupuesto  en estado borrador';
            END IF;

            select
               tcc.movimiento,
               tcc.id_ep
              into
               v_registros
             from param.ttipo_cc tcc
             where tcc.id_tipo_cc = v_parametros.id_tipo_cc;

            IF  v_registros.movimiento != 'si' THEN
               raise exception 'solo puede crear centro de costos para tipos que sean de  movimiento';
            END IF;


             --  validar que cada id_tipo_cc solo se use para una vez en cada gestion
            IF  EXISTS (select 1
                        from param.tcentro_costo cc
                        where    cc.id_tipo_cc = v_parametros.id_tipo_cc
                             and cc.id_gestion = v_parametros.id_gestion
                             and cc.estado_reg = 'activo'
                             and cc.id_centro_costo !=  v_parametros.id_presupuesto) THEN

                 raise exception 'este tipo ya tiene un centro de costo registrado para esta gestión';
            END IF;



            --Sentencia de la modificacion
			update param.tcentro_costo set
                id_ep =   v_registros.id_ep,  --RAC 05/06/"017 la ep se origina en el tipo de centro
                id_gestion = v_parametros.id_gestion,
                id_uo = v_parametros.id_uo,
                id_usuario_mod = p_id_usuario,
                fecha_mod = now(),
                id_tipo_cc = v_parametros.id_tipo_cc
			where id_centro_costo=v_parametros.id_presupuesto;


            --Sentencia de la modificacion
			update pre.tpresupuesto set
              tipo_pres = v_parametros.tipo_pres,
              descripcion = v_parametros.descripcion,
              sw_consolidado = v_parametros.sw_consolidado,
              id_categoria_prog = v_parametros.id_categoria_prog,
              fecha_mod = now(),
              id_usuario_mod = p_id_usuario,
              fecha_inicio_pres = v_parametros.fecha_inicio_pres,
              fecha_fin_pres = v_parametros.fecha_fin_pres
			where id_presupuesto=v_parametros.id_presupuesto;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Presupuestos modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_presupuesto',v_parametros.id_presupuesto::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'PRE_PRE_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		Gonzalo Sarmiento Sejas
 	#FECHA:		27-02-2013 00:30:39
	***********************************/

	elsif(p_transaccion='PRE_PRE_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from pre.tpresupuesto
            where id_presupuesto=v_parametros.id_presupuesto;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Presupuestos eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_presupuesto',v_parametros.id_presupuesto::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'PRE_INITRA_IME'
 	#DESCRIPCION:	Iniciar tramite de presupuesto
 	#AUTOR:		Rensi Artega Copari (KPLIAN)
 	#FECHA:		02/03/2016 00:30:39
	***********************************/

	elsif(p_transaccion='PRE_INITRA_IME')then

		begin



                 select
                   cc.id_gestion,
                   cc.codigo_cc,
                   pre.nro_tramite
                 into
                   v_reg_pres
                 from pre.tpresupuesto pre
                 inner join param.vcentro_costo cc on cc.id_centro_costo = pre.id_presupuesto
                 where pre.id_presupuesto = v_parametros.id_presupuesto;


                  v_codigo_wf = pxp.f_get_variable_global('pre_wf_codigo');

              IF  v_reg_pres.nro_tramite is not NULL or  v_reg_pres.nro_tramite !='' THEN
                 raise exception 'El trámite ya fue iniciado % ', v_reg_pres.nro_tramite;
              END IF;

              -- obtiene numero de tramite
                 SELECT
                       ps_num_tramite ,
                       ps_id_proceso_wf ,
                       ps_id_estado_wf ,
                       ps_codigo_estado
                    into
                       v_num_tramite,
                       v_id_proceso_wf,
                       v_id_estado_wf,
                       v_codigo_estado

                  FROM wf.f_inicia_tramite(
                       p_id_usuario,
                       v_parametros._id_usuario_ai,
                       v_parametros._nombre_usuario_ai,
                       v_reg_pres.id_gestion,
                       v_codigo_wf,
                       v_parametros.id_funcionario_usu,
                       NULL,
                       'Inicio de tramite.... ',
                       v_reg_pres.codigo_cc);



            update pre.tpresupuesto  p  set
               nro_tramite = v_num_tramite,
               id_estado_wf = v_id_estado_wf,
               id_proceso_wf = v_id_proceso_wf,
               estado = v_codigo_estado
            where p.id_presupuesto = v_parametros.id_presupuesto;



            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','se inicio el tramite del presupuesto' );
            v_resp = pxp.f_agrega_clave(v_resp,'id_presupuesto',v_parametros.id_presupuesto::varchar);


            --Devuelve la respuesta
            return v_resp;

		end;


    /*********************************
 	#TRANSACCION:  'PRE_CLONARPRE_IME'
 	#DESCRIPCION:	Clona los presupuestos y centros de costos para la siguiente gestion
 	#AUTOR:	    Rensi Arteaga Copari
 	#FECHA:		04-08-2015 00:30:39
	***********************************/

	elsif(p_transaccion='PRE_CLONARPRE_IME')then

		begin

            -------------------------------------------------------------------
            --  REGLA, el id_centro_costo tiene que ser igual al id_presupuesto
            --------------------------------------------------------------------
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
          v_conta = 0;
            --clonamos presupuestos y centros de costos
            FOR v_registros in (
                                  select
                                     p.* ,
                                     cc.id_tipo_cc,
                                     ci.id_categoria_programatica_dos
                                  from pre.tpresupuesto p
                                  inner join param.tcentro_costo cc on cc.id_centro_costo = p.id_centro_costo
                                  left join pre.tcategoria_programatica_ids ci on ci.id_categoria_programatica_uno = p.id_categoria_prog
                                  where cc.id_gestion = v_parametros.id_gestion
                                  and p.estado_reg = 'activo'
                                  and p.estado = 'aprobado') LOOP



                    -- preguntamos si ya existe en la tabla de ids
                    v_id_presupuesto_dos = NULL;
                    select
                       i.id_presupuesto_dos
                     into
                       v_id_presupuesto_dos
                    from pre.tpresupuesto_ids i
                    where i.id_presupuesto_uno = v_registros.id_presupuesto ;

                     IF v_id_presupuesto_dos is null  THEN

                         -- clonamos el centro de costos
                           select
                               *
                           into
                              v_reg_cc_ori
                           from param.tcentro_costo cc
                           where cc.id_centro_costo = v_registros.id_centro_costo;



                         --insertamos nuevo centro de costo
                           INSERT INTO  param.tcentro_costo
                                      (
                                        id_usuario_reg,
                                        fecha_reg,
                                        estado_reg,
                                        id_ep,
                                        id_uo,
                                        id_gestion,
                                        id_tipo_cc
                                      )
                                      VALUES (
                                        p_id_usuario,
                                        now(),
                                        'activo',
                                        v_reg_cc_ori.id_ep,
                                        v_reg_cc_ori.id_uo,
                                        v_id_gestion_destino,
                                        v_reg_cc_ori.id_tipo_cc
                                      ) RETURNING id_centro_costo into v_id_centro_costo;


                     --TODO revisar el estado de formualcion del presupeusto ......        OJO
                     --  insertamos nuevo presupuesto
                     INSERT INTO  pre.tpresupuesto
                                  (
                                    id_usuario_reg,
                                    fecha_reg,
                                    estado_reg,
                                    id_presupuesto,
                                    id_centro_costo,
                                    tipo_pres,
                                    estado_pres,
                                    id_categoria_prog,
                                    id_parametro,
                                    id_fuente_financiamiento,
                                    id_concepto_colectivo,
                                    cod_fin,
                                    cod_prg,
                                    cod_pry,
                                    cod_act,
                                    descripcion,
                                    sw_consolidado,
                                    fecha_inicio_pres,
                                    fecha_fin_pres
                                  )
                                  VALUES (
                                    p_id_usuario,
                                    now(),
                                    'activo',
                                    v_id_centro_costo, --id_presupeusto tiene que ser igual al id centro de costo
                                    v_id_centro_costo,
                                    v_registros.tipo_pres,
                                    v_registros.estado_pres,
                                    v_registros.id_categoria_programatica_dos,
                                    v_registros.id_parametro,
                                    v_registros.id_fuente_financiamiento,
                                    v_registros.id_concepto_colectivo,
                                    v_registros.cod_fin,
                                    v_registros.cod_prg,
                                    v_registros.cod_pry,
                                    v_registros.cod_act,
                                    v_registros.descripcion,
                                    v_registros.sw_consolidado,
                                    ('01-01' ||'-'||v_registros_ges.gestion + 1)::date,
                                    ('31-12' ||'-'||v_registros_ges.gestion + 1)::date

                                  )RETURNING id_presupuesto into v_id_presupuesto;


                        /*****************************Iniciamos Tramite en la inserccion******************************************/

                        			/*Recuperamos el id Funcionario*/
                                    	select fun.id_funcionario into v_id_funcionario_recu
                                        from segu.tusuario usu
                                        inner join orga.tfuncionario fun on fun.id_persona = usu.id_persona
                                        where usu.id_usuario = p_id_usuario;
                                    /********************************/


                                  select
                                       cc.id_gestion,
                                       cc.codigo_cc,
                                       pre.nro_tramite
                                     into
                                       v_reg_pres
                                     from pre.tpresupuesto pre
                                     inner join param.vcentro_costo cc on cc.id_centro_costo = pre.id_presupuesto
                                     where pre.id_presupuesto = v_id_presupuesto;


                                      v_codigo_wf = pxp.f_get_variable_global('pre_wf_codigo');

                                  IF  v_reg_pres.nro_tramite is not NULL or  v_reg_pres.nro_tramite !='' THEN
                                     raise exception 'El trámite ya fue iniciado % ', v_reg_pres.nro_tramite;
                                  END IF;

                                  -- obtiene numero de tramite
                                     SELECT
                                           ps_num_tramite ,
                                           ps_id_proceso_wf ,
                                           ps_id_estado_wf ,
                                           ps_codigo_estado
                                        into
                                           v_num_tramite,
                                           v_id_proceso_wf,
                                           v_id_estado_wf,
                                           v_codigo_estado

                                      FROM wf.f_inicia_tramite(
                                           p_id_usuario,
                                           NULL,
                                           NULL,
                                           v_reg_pres.id_gestion,
                                           v_codigo_wf,
                                           v_id_funcionario_recu,
                                           NULL,
                                           'Inicio de tramite.... ',
                                           v_reg_pres.codigo_cc);



                                update pre.tpresupuesto  p  set
                                   nro_tramite = v_num_tramite,
                                   id_estado_wf = v_id_estado_wf,
                                   id_proceso_wf = v_id_proceso_wf,
                                   estado = v_codigo_estado
                                where p.id_presupuesto = v_id_presupuesto;
                        /**********************************************************************************************************/

                                  INSERT INTO pre.tpresupuesto_ids (id_presupuesto_uno, id_presupuesto_dos, sw_cambio_gestion )
                                  VALUES ( v_registros.id_presupuesto, v_id_presupuesto, 'gestion');
                                   v_conta = v_conta + 1;

                     /*****************************************Aumentando para que inserte los funcionarios************************************************************/

                              for v_funcionarios in (select
                                                      fun.*
                                                      from pre.tpresupuesto p
                                                      inner join param.tcentro_costo cc on cc.id_centro_costo = p.id_centro_costo
                                                      inner join pre.tpresupuesto_funcionario fun on fun.id_presupuesto = p.id_presupuesto
                                                      where cc.id_gestion = v_parametros.id_gestion and  fun.id_presupuesto = v_registros.id_presupuesto
                                                      and p.estado_reg = 'activo'
                                                      and p.estado = 'aprobado' ) loop

                                          INSERT INTO  pre.tpresupuesto_funcionario
                                          (
                                            id_usuario_reg,
                                            fecha_reg,
                                            estado_reg,
                                            id_presupuesto,
                                            id_funcionario,
                                            accion
                                          )
                                          VALUES (
                                            p_id_usuario,
                                            now(),
                                            'activo',
                                            v_id_centro_costo, --id_presupeusto tiene que ser igual al id centro de costo
                                            v_funcionarios.id_funcionario,
                                            v_funcionarios.accion

                                          )RETURNING id_presupuesto_funcionario into v_id_presupuesto_funcionario;

                              end loop;
                  	/*******************************************************************************************************************************************************/


                 -- raise exception '%, %',('01-01' ||'-'||v_registros_ges.gestion + 1)::date , ('31-12' ||'-'||v_registros_ges.gestion + 1)::date ;
                     ELSE
                        --si el presupeusto ya existe modificarlo

                          update param.tcentro_costo set
                             id_tipo_cc = v_registros.id_tipo_cc
                          where id_centro_costo = v_id_presupuesto_dos;

                          update pre.tpresupuesto  c set
                             descripcion = v_registros.descripcion,
                             id_categoria_prog = v_registros.id_categoria_programatica_dos,
                             sw_consolidado = v_registros.sw_consolidado
                          where id_centro_costo = v_id_presupuesto_dos;



                     END IF;

            END LOOP;



            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Presupuestos clonados para la gestion: '||v_registros_ges.gestion::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'observaciones','Se insertaron presupuestos: '|| v_conta::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;
    /*********************************
 	#TRANSACCION:  'PRE_SIGESTP_IME'
 	#DESCRIPCION:  cambia al siguiente estado
 	#AUTOR:		RAC
 	#FECHA:		04-03-2016 12:12:51
	***********************************/

	elseif(p_transaccion='PRE_SIGESTP_IME')then
        begin

         /*   PARAMETROS

        $this->setParametro('id_proceso_wf_act','id_proceso_wf_act','int4');
        $this->setParametro('id_tipo_estado','id_tipo_estado','int4');
        $this->setParametro('id_funcionario_wf','id_funcionario_wf','int4');
        $this->setParametro('id_depto_wf','id_depto_wf','int4');
        $this->setParametro('obs','obs','text');
        $this->setParametro('json_procesos','json_procesos','text');
        */

        --obtenermos datos basicos
          select
              p.id_proceso_wf,
              p.id_estado_wf,
              p.estado

             into
              v_id_proceso_wf,
              v_id_estado_wf,
              v_codigo_estado

          from pre.tpresupuesto p
          where p.id_presupuesto = v_parametros.id_presupuesto;

          --------------------------------------------
          --  validamos que el presupeusto tenga por
          --  lo menos una partida asignada
          --------------------------------------------
          IF not EXISTS(
                        select 1
                        from pre.tpresup_partida pp
                        where     pp.estado_reg = 'activo'
                              and pp.id_presupuesto = v_parametros.id_presupuesto) THEN

              --raise exception 'Por lo menos necesita asignar una partida para formulación';
          END IF;


          /*--------------------------------------------------------------
          (may) 07-01-2020
          update para el importe verificado aprobado y al validarlo sea
          todos un 100% su importe registrado
          ----------------------------------------------------------------*/

          IF (v_codigo_estado = 'formulacion') THEN

          			  for v_presu_partida in (  select *
                                                from pre.tpresup_partida pp
                                                where  pp.estado_reg = 'activo'
                                                and pp.id_presupuesto = v_parametros.id_presupuesto
                                                ) loop
                     --raise exception 'llegaa % , %', v_presu_partida.importe ,v_presu_partida.id_presup_partida  ;
                                    UPDATE pre.tpresup_partida SET
                                    importe_aprobado = v_presu_partida.importe
                                    WHERE id_presupuesto = v_parametros.id_presupuesto
                                    and id_presup_partida = v_presu_partida.id_presup_partida;

          			end loop;


          END IF;

          ---


          -- recupera datos del estado

           select
            ew.id_tipo_estado ,
            te.codigo
           into
            v_id_tipo_estado,
            v_codigo_estado
          from wf.testado_wf ew
          inner join wf.ttipo_estado te on te.id_tipo_estado = ew.id_tipo_estado
          where ew.id_estado_wf = v_parametros.id_estado_wf_act;



           -- obtener datos tipo estado
           select
                 te.codigo
            into
                 v_codigo_estado_siguiente
           from wf.ttipo_estado te
           where te.id_tipo_estado = v_parametros.id_tipo_estado;

           IF  pxp.f_existe_parametro(p_tabla,'id_depto_wf') THEN
              v_id_depto = v_parametros.id_depto_wf;
           END IF;

           IF  pxp.f_existe_parametro(p_tabla,'obs') THEN
                  v_obs=v_parametros.obs;
           ELSE
                  v_obs='---';
           END IF;

           ---------------------------------------
           -- REGISTA EL SIGUIENTE ESTADO DEL WF.
           ---------------------------------------

           v_id_estado_actual =  wf.f_registra_estado_wf(  v_parametros.id_tipo_estado,
                                                           v_parametros.id_funcionario_wf,
                                                           v_parametros.id_estado_wf_act,
                                                           v_id_proceso_wf,
                                                           p_id_usuario,
                                                           v_parametros._id_usuario_ai,
                                                           v_parametros._nombre_usuario_ai,
                                                           v_id_depto,
                                                           v_obs);

          --------------------------------------
          -- registra los procesos disparados
          --------------------------------------

          FOR v_registros_proc in ( select * from json_populate_recordset(null::wf.proceso_disparado_wf, v_parametros.json_procesos::json)) LOOP

               -- get cdigo tipo proceso
               select
                  tp.codigo
               into
                  v_codigo_tipo_pro
               from wf.ttipo_proceso tp
               where  tp.id_tipo_proceso =  v_registros_proc.id_tipo_proceso_pro;


              -- disparar creacion de procesos seleccionados
              SELECT
                       ps_id_proceso_wf,
                       ps_id_estado_wf,
                       ps_codigo_estado
                 into
                       v_id_proceso_wf,
                       v_id_estado_wf,
                       v_codigo_estado
              FROM wf.f_registra_proceso_disparado_wf(
                       p_id_usuario,
                       v_parametros._id_usuario_ai,
                       v_parametros._nombre_usuario_ai,
                       v_id_estado_actual,
                       v_registros_proc.id_funcionario_wf_pro,
                       v_registros_proc.id_depto_wf_pro,
                       v_registros_proc.obs_pro,
                       v_codigo_tipo_pro,
                       v_codigo_tipo_pro);


           END LOOP;



          --------------------------------------------------
          --  ACTUALIZA EL NUEVO ESTADO DEL PRESUPUESTO
          ----------------------------------------------------


          IF  pre.f_fun_inicio_presupuesto_wf(p_id_usuario,
           									v_parametros._id_usuario_ai,
                                            v_parametros._nombre_usuario_ai,
                                            v_id_estado_actual,
                                            v_id_proceso_wf,
                                            v_codigo_estado_siguiente) THEN


          END IF;



          -- si hay mas de un estado disponible  preguntamos al usuario
          v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se realizo el cambio de estado del presupuesto id='||v_parametros.id_presupuesto);
          v_resp = pxp.f_agrega_clave(v_resp,'operacion','cambio_exitoso');


          -- Devuelve la respuesta
          return v_resp;

     end;


	/*********************************
 	#TRANSACCION:  'PR_ANTEPR_IME'
 	#DESCRIPCION: retrocede el estado de presupuestos
 	#AUTOR:		RAC
 	#FECHA:		19-02-2016 12:12:51
	***********************************/

	elseif(p_transaccion='PR_ANTEPR_IME')then
        begin

        v_operacion = 'anterior';

        IF  pxp.f_existe_parametro(p_tabla , 'estado_destino')  THEN
           v_operacion = v_parametros.estado_destino;
        END IF;



        --obtenermos datos basicos
        select
            pp.id_presupuesto,
            pp.id_proceso_wf,
            pp.estado,
            pwf.id_tipo_proceso
        into
            v_registros_pp

        from pre.tpresupuesto  pp
        inner  join wf.tproceso_wf pwf  on  pwf.id_proceso_wf = pp.id_proceso_wf
        where pp.id_proceso_wf  = v_parametros.id_proceso_wf;


        IF v_registros_pp.estado = 'aprobado' THEN
            raise exception 'El presupuesto ya se encuentra aprobado, solo puede modificar a traves de la interface de ajustes presupuestarios';
        END IF;


        v_id_proceso_wf = v_registros_pp.id_proceso_wf;

        IF  v_operacion = 'anterior' THEN
            --------------------------------------------------
            --Retrocede al estado inmediatamente anterior
            -------------------------------------------------
           --recuperaq estado anterior segun Log del WF
              SELECT

                 ps_id_tipo_estado,
                 ps_id_funcionario,
                 ps_id_usuario_reg,
                 ps_id_depto,
                 ps_codigo_estado,
                 ps_id_estado_wf_ant
              into
                 v_id_tipo_estado,
                 v_id_funcionario,
                 v_id_usuario_reg,
                 v_id_depto,
                 v_codigo_estado,
                 v_id_estado_wf_ant
              FROM wf.f_obtener_estado_ant_log_wf(v_parametros.id_estado_wf);





        ELSE
           --recupera el estado inicial
           -- recuperamos el estado inicial segun tipo_proceso

             SELECT
               ps_id_tipo_estado,
               ps_codigo_estado
             into
               v_id_tipo_estado,
               v_codigo_estado
             FROM wf.f_obtener_tipo_estado_inicial_del_tipo_proceso(v_registros_pp.id_tipo_proceso);



             --busca en log e estado de wf que identificamos como el inicial
             SELECT
               ps_id_funcionario,
              ps_id_depto
             into
              v_id_funcionario,
             v_id_depto


             FROM wf.f_obtener_estado_segun_log_wf(v_id_estado_wf, v_id_tipo_estado);




        END IF;



         --configurar acceso directo para la alarma
             v_acceso_directo = '';
             v_clase = '';
             v_parametros_ad = '';
             v_tipo_noti = 'notificacion';
             v_titulo  = 'Visto Bueno';


           IF   v_codigo_estado_siguiente not in('borrador','formulacion','vobopre','aprobado','anulado')   THEN
                  v_acceso_directo = '../../../sis_presupuestos/vista/presupuesto/PresupuestoVb.php';
                 v_clase = 'PresupuestoVb';
                 v_parametros_ad = '{filtro_directo:{campo:"pre.id_proceso_wf",valor:"'||v_id_proceso_wf::varchar||'"}}';
                 v_tipo_noti = 'notificacion';
                 v_titulo  = 'Visto Bueno';

           END IF;


          -- registra nuevo estado

          v_id_estado_actual = wf.f_registra_estado_wf(
              v_id_tipo_estado,                --  id_tipo_estado al que retrocede
              v_id_funcionario,                --  funcionario del estado anterior
              v_parametros.id_estado_wf,       --  estado actual ...
              v_id_proceso_wf,                 --  id del proceso actual
              p_id_usuario,                    -- usuario que registra
              v_parametros._id_usuario_ai,
              v_parametros._nombre_usuario_ai,
              v_id_depto,                       --depto del estado anterior
              '[RETROCESO] '|| v_parametros.obs,
              v_acceso_directo,
              v_clase,
              v_parametros_ad,
              v_tipo_noti,
              v_titulo);

              IF  not pre.f_fun_regreso_presupuesto_wf(p_id_usuario,
                                                   v_parametros._id_usuario_ai,
                                                   v_parametros._nombre_usuario_ai,
                                                   v_id_estado_actual,
                                                   v_parametros.id_proceso_wf,
                                                   v_codigo_estado) THEN

               raise exception 'Error al retroceder estado';

            END IF;


         -- si hay mas de un estado disponible  preguntamos al usuario
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se realizo el cambio de estado)');
            v_resp = pxp.f_agrega_clave(v_resp,'operacion','cambio_exitoso');


          --Devuelve la respuesta
            return v_resp;





        end;

      /*********************************
      #TRANSACCION:  'PRE_FORMUPRE_INS'
      #DESCRIPCION:	Insercion detalle Formulacion Presupuestaria
      #AUTOR:		Maylee Perez Pastor
      #FECHA:		05-08-2020
      ***********************************/

      elsif(p_transaccion='PRE_FORMUPRE_INS')then

           begin

          --raise EXCEPTION 'llegabd % ',v_parametros.id_gestion;

          	 v_centro_costo = (select substring(v_parametros.centro_costo from 1 for 3));
             v_id_gestion = v_parametros.id_gestion::integer;


             --CENTRO DE COSTO
            /* SELECT cc.id_centro_costo
             into v_id_centro_costo
             from param.vcentro_costo cc
             where (trim(cc.codigo_cc))::varchar = (trim(v_parametros.centro_costo))::varchar;   */

             SELECT cc.id_centro_costo
             into v_id_centro_costo
             from param.tcentro_costo cc
             join param.ttipo_cc tcc on tcc.id_tipo_cc = cc.id_tipo_cc
             --where (trim(tcc.codigo))::varchar = (trim(v_centro_costo))::varchar
             where (tcc.codigo)::varchar = (v_centro_costo)::varchar
             and cc.id_gestion =  v_id_gestion;

             IF (v_id_centro_costo is null)THEN
             	RAISE EXCEPTION 'No se encuentra parametrizado el Centro de Costo %',v_parametros.centro_costo;
             END IF;


             --CONCEPTO DE GASTO
              select cig.id_concepto_ingas,cig.desc_ingas
               into v_registros_cig
              from param.tconcepto_ingas cig
              where cig.estado_reg = 'activo'
              and orga.final_palabra(regexp_replace(trim(regexp_replace(upper(cig.desc_ingas)::text, '\s+',
                ' ', 'g')), '\r|\n', '', 'g')) = orga.final_palabra(regexp_replace(trim(regexp_replace(upper(v_parametros.concepto_gasto)::text, '\s+', ' ', 'g')), '\r|\n', '', 'g'));
     -- regexp_replace(field, E'(^[\\n\\r]+)|([\\n\\r]+$)', '', 'g' )
     
             IF (v_registros_cig.id_concepto_ingas is null)THEN
             	RAISE EXCEPTION 'No se encuentra parametrizado el Concepto de Gasto %',v_parametros.concepto_gasto;
             END IF;



             --FUNCIONARIO RESPONSABLE
             SELECT usu.id_usuario, fun.desc_funcionario1
             INTO v_id_usuario_resp, v_desc_funcionario1_res
             FROM segu.vusuario usu
             join orga.vfuncionario_persona fun on fun.id_persona= usu.id_persona
             WHERE fun.id_funcionario= v_parametros.id_responsable;

             --
             SELECT usu.desc_persona, (p.fecha_reg::date||' '|| to_char(p.fecha_reg, 'HH12:MI:SS'))::varchar as fecha
             INTO v_desc_persona_reg, v_fecha_reg
             FROM pre.tformulacion_presu p
             left join pre.tformulacion_presu_detalle pd on pd.id_formulacion_presu = p.id_formulacion_presu
             left join segu.vusuario usu on usu.id_usuario = p.id_usuario_reg
             WHERE  pd.id_concepto_gasto = v_registros_cig.id_concepto_ingas
             and pd.id_centro_costo = v_id_centro_costo
             and p.estado_reg = 'activo' and pd.estado_reg= 'activo';


             select pres.id_presupuesto, pres.estado
             into v_id_presupuesto, v_estado_pre
             from pre.tpresupuesto pres
             where pres.id_centro_costo = v_id_centro_costo
             and pres.estado_reg = 'activo';


              --CONTROL NO REGISTRE DE UN PRESUPUESTO APROBADO
              IF v_estado_pre = 'aprobado' THEN
               raise exception 'No puede agregar Conceptos de Gasto a la Memoria de Cálculo de un presupuesto aprobado';
           	  END IF;

               --recupera partida a partir del presupuesto y concepto de gasto
               SELECT par.id_partida, par.codigo||' - '|| par.nombre_partida
               into v_id_partida, v_des_partida
               FROM pre.tpresupuesto pre
               JOIN param.tcentro_costo cc ON cc.id_centro_costo = pre.id_centro_costo
               JOIN pre.tpartida par ON par.id_gestion = cc.id_gestion
               JOIN pre.tconcepto_partida cp ON cp.id_partida = par.id_partida
               JOIN param.tconcepto_ingas cig ON cig.id_concepto_ingas = cp.id_concepto_ingas
               where pre.id_presupuesto = v_id_presupuesto
               and cig.id_concepto_ingas = v_registros_cig.id_concepto_ingas;

               IF (v_id_partida is null)THEN
               		RAISE EXCEPTION 'No se encuentra parametrizado la Partida para el Concepto de Gasto %',v_registros_cig.desc_ingas;
               END IF;



               --CONTROL NO REPITA EN EL MISMO PRESUPUESTO UN CONCEPTO DE GASTO
               IF EXISTS (SELECT 1
                          FROM pre.tformulacion_presu_detalle pd
                          join pre.tformulacion_presu p on p.id_formulacion_presu = pd.id_formulacion_presu
                          WHERE pd.id_concepto_gasto = v_registros_cig.id_concepto_ingas
                          and pd.id_centro_costo = v_id_centro_costo
                          and p.id_gestion = v_id_gestion
                          and pd.estado_reg= 'activo' and p.estado_reg = 'activo'
                          and (trim(pd.justificacion))::varchar = (trim(v_parametros.justificacion))::varchar) THEN

                          SELECT p.id_formulacion_presu
                          into v_id_memoria_calculo_presu
                          FROM pre.tformulacion_presu p
                          WHERE upper(p.observaciones) = upper(v_parametros.observaciones)
                          and p.id_usuario_responsable=v_id_usuario_resp
                          and p.estado_reg != 'inactivo';

                          delete from pre.tformulacion_presu
                          where id_formulacion_presu = v_id_memoria_calculo_presu;

                          FOR v_id_memoria_calculo_det_presu in (SELECT id_memoria_det
                                                                  FROM pre.tmemoria_det
                                                                  WHERE id_memoria_calculo = v_id_memoria_calculo_presu) LOOP

                           			 --Sentencia de la eliminacion
                                    delete from pre.tmemoria_det
                                    where id_memoria_det = v_id_memoria_calculo_det_presu;

                           END LOOP;

                          RAISE EXCEPTION 'El documento ya fue registrado para el Centro de Costo % con el Concepto de Gasto %, por el usuario % el día %.',v_parametros.centro_costo,v_registros_cig.desc_ingas ,v_desc_persona_reg,v_fecha_reg ;


                END IF;


                --INSERTAR TABLA FORMULACION
                IF NOT EXISTS (SELECT 1
                              FROM pre.tformulacion_presu p
                              WHERE upper(p.observaciones) = upper(v_parametros.observaciones)
                              and p.id_usuario_responsable=v_id_usuario_resp
                              and p.estado_reg != 'inactivo'  ) THEN

                               --CONTROL NO REPITA EN EL MISMO PRESUPUESTO UN CONCEPTO DE GASTO
                               IF EXISTS (SELECT 1
                                          FROM pre.tformulacion_presu p
                                          join pre.tformulacion_presu_detalle pd on pd.id_formulacion_presu = p.id_formulacion_presu
                                          WHERE upper(p.observaciones) = upper(v_parametros.observaciones)
                                          and p.id_usuario_responsable= v_id_usuario_resp
                                          and p.id_gestion = v_id_gestion
                                          and pd.id_concepto_gasto = v_registros_cig.id_concepto_ingas
                                          and pd.id_centro_costo = v_id_centro_costo
                                          and p.estado_reg = 'activo' and pd.estado_reg= 'activo'
                                          and (trim(pd.justificacion))::varchar = (trim(v_parametros.justificacion))::varchar) THEN

                                          RAISE EXCEPTION 'El documento ya fue registrado por el usuario % el dia %. ',v_desc_persona_reg,v_fecha_reg ;
                                END IF;


                         insert into pre.tformulacion_presu(
                                          id_usuario_responsable,
                                          observaciones,
                                          id_gestion,

                                          fecha_reg,
                                          fecha_mod,
                                          estado_reg,
                                          id_usuario_reg,
                                          id_usuario_mod

                                   ) values(

                                          v_id_usuario_resp,
                                          v_parametros.observaciones,
                                          v_id_gestion,

                                          now(),
                                          null,
                                          'activo',
                                          p_id_usuario,
                                          null



                                   )RETURNING id_formulacion_presu into v_id_formulacion_presu;
                 END IF;
                 ----


          -- raise exception 'llegabd1 % - %',v_id_presupuesto, v_registros_cig.id_concepto_ingas;



                 --INSERTAR TABLA PRESUP-PARTIDA
                 IF NOT EXISTS (select 1
                 				from pre.tpresup_partida
           						where id_partida = v_id_partida
                                and id_presupuesto = v_id_presupuesto) THEN


                      INSERT INTO pre.tpresup_partida(id_presupuesto,
                      								  id_partida,
                                                      id_centro_costo,
                                                      id_usuario_reg
                                                      )VALUES(
                                                      v_id_presupuesto,
                                                      v_id_partida,
                                                      v_id_centro_costo,
                                                      v_id_usuario_resp);

          		 END IF;

                 ----



                   --  validar que exista el presupuesto
                  IF  EXISTS (select 1
                              from pre.tpresupuesto pres
                              where pres.id_centro_costo = v_id_centro_costo
                              and pres.estado_reg = 'activo') THEN

                  			  --insercion MEMORIA DE CALCULO
                              insert into pre.tmemoria_calculo(
                                id_concepto_ingas,
                                importe_total,
                                obs,
                                id_presupuesto,
                                estado_reg,
                                id_usuario_ai,
                                fecha_reg,
                                usuario_ai,
                                id_usuario_reg,
                                fecha_mod,
                                id_usuario_mod,
                                id_partida
                              ) values(
                                v_registros_cig.id_concepto_ingas,
                                v_parametros.importe_total,
                                v_parametros.justificacion, --replace(v_parametros.obs, '\n', ' '),
                                v_id_presupuesto,
                                'activo',
                                v_parametros._id_usuario_ai,
                                now(),
                                v_parametros._nombre_usuario_ai,
                                v_id_usuario_resp, --p_id_usuario,
                                null,
                                null,
                                v_id_partida

                              )RETURNING id_memoria_calculo into v_id_memoria_calculo;


                             --inserta MEMORIA DET
                             -- inserta valores para todos los periodos de la gestion con valor 0

                             FOR v_registros in (select per.id_periodo, per.periodo
                                                  from param.tperiodo per
                                                  where per.id_gestion = v_id_gestion
                                                  and per.estado_reg = 'activo'
                                                  order by per.fecha_ini) LOOP

                                               IF (v_registros.periodo = 1) THEN
                                                  v_importe = v_parametros.periodo_enero;
                                               ELSIF (v_registros.periodo = 2) THEN
                                                  v_importe = v_parametros.periodo_febrero;
                                               ELSIF (v_registros.periodo = 3) THEN
                                                  v_importe = v_parametros.periodo_marzo;
                                               ELSIF (v_registros.periodo = 4) THEN
                                                  v_importe = v_parametros.periodo_abril;
                                               ELSIF (v_registros.periodo = 5) THEN
                                                  v_importe = v_parametros.periodo_mayo;
                                               ELSIF (v_registros.periodo = 6) THEN
                                                  v_importe = v_parametros.periodo_junio;
                                               ELSIF (v_registros.periodo = 7) THEN
                                                  v_importe = v_parametros.periodo_julio;
                                               ELSIF (v_registros.periodo = 8) THEN
                                                  v_importe = v_parametros.periodo_agosto;
                                               ELSIF (v_registros.periodo = 9) THEN
                                                  v_importe = v_parametros.periodo_septiembre;
                                               ELSIF (v_registros.periodo = 10) THEN
                                                  v_importe = v_parametros.periodo_octubre;
                                               ELSIF (v_registros.periodo = 11) THEN
                                                  v_importe = v_parametros.periodo_noviembre;
                                               ELSIF (v_registros.periodo = 12) THEN
                                                  v_importe = v_parametros.periodo_diciembre;
                                               END IF;

                                              insert into pre.tmemoria_det(
                                                  importe,
                                                  importe_unitario,
                                                  estado_reg,
                                                  id_periodo,
                                                  id_memoria_calculo,
                                                  usuario_ai,
                                                  fecha_reg,
                                                  id_usuario_reg,
                                                  id_usuario_ai
                                                )
                                                values
                                                (
                                                  v_importe,
                                                  v_importe,
                                                  'activo',
                                                  v_registros.id_periodo,
                                                  v_id_memoria_calculo,
                                                  v_parametros._nombre_usuario_ai,
                                                  now(),
                                                  v_id_usuario_resp, --p_id_usuario,
                                                  v_parametros._id_usuario_ai);

                              END LOOP;

                              --INSERTAR TABLA FORMULACION DETALLE
                  			  --v_id_formulacion_presu = v_id_formulacion_presu ;
                  			 FOR v_id_formulacion_presu in (SELECT p.id_formulacion_presu
                                      FROM pre.tformulacion_presu p
                                      WHERE upper(p.observaciones) = upper(v_parametros.observaciones)
                                      and p.id_usuario_responsable=v_id_usuario_resp) LOOP

                                      insert into pre.tformulacion_presu_detalle(
                                                    id_centro_costo,
                                                    id_concepto_gasto,
                                                    justificacion,
                                                    nro_contrato,
                                                    proveedor,
                                                    hoja_respaldo,
                                                    periodo_enero,
                                                    periodo_febrero,
                                                    periodo_marzo,
                                                    periodo_abril,
                                                    periodo_mayo,
                                                    periodo_junio,
                                                    periodo_julio,
                                                    periodo_agosto,
                                                    periodo_septiembre,
                                                    periodo_octubre,
                                                    periodo_noviembre,
                                                    periodo_diciembre,
                                                    importe_total,
                                                    id_partida,
                                                    id_formulacion_presu,
                                                    id_memoria_calculo,

                                                    id_usuario_reg,
                                                    id_usuario_mod,
                                                    fecha_reg,
                                                    fecha_mod,
                                                    estado_reg
                                                    )
                                                    values
                                                    (
                                                    v_id_centro_costo,
                                                    v_registros_cig.id_concepto_ingas,
                                                    v_parametros.justificacion,
                                                    COALESCE(v_parametros.nro_contrato, ''),
                                                    COALESCE(v_parametros.proveedor, ''),
                                                    COALESCE(v_parametros.hoja_respaldo, ''),
                                                    v_parametros.periodo_enero,
                                                    v_parametros.periodo_febrero,
                                                    v_parametros.periodo_marzo,
                                                    v_parametros. periodo_abril,
                                                    v_parametros.periodo_mayo,
                                                    v_parametros.periodo_junio,
                                                    v_parametros.periodo_julio,
                                                    v_parametros.periodo_agosto,
                                                    v_parametros.periodo_septiembre,
                                                    v_parametros.periodo_octubre,
                                                    v_parametros.periodo_noviembre,
                                                    v_parametros.periodo_diciembre,
                                                    v_parametros.importe_total,
                                                    v_id_partida,
                                                    v_id_formulacion_presu,
                                                    v_id_memoria_calculo,

                                                    p_id_usuario,
                                                    null,
                                                    now(),
                                                    null,
                                                    'activo'
                                                      );

                              			END LOOP;

                 ELSE
                  		raise exception 'No existe el registro para el Centro de Costo %', v_parametros.centro_costo;
                  END IF;

              --Definicion de la respuesta
              v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Detalle almacenado(a) con exito (id_formulacion_presu'||v_id_formulacion_presu||')');
              v_resp = pxp.f_agrega_clave(v_resp,'id_formulacion_presu',v_id_formulacion_presu::varchar);

              --Devuelve la respuesta
              return v_resp;

          end;
      /*********************************
      #TRANSACCION:  'PRE_FORMUPRE_ELI'
      #DESCRIPCION:	Eliminar detalle Formulacion Presupuestaria
      #AUTOR:		Maylee Perez Pastor
      #FECHA:		05-08-2020
      ***********************************/

      elsif(p_transaccion='PRE_FORMUPRE_ELI')then

          begin

              /*IF v_registros.estado = 'aprobado' THEN
              	raise exception 'no puede eliminar Conceptos de Gasto de un Presupuesto aprobado';
              END IF;*/

              update pre.tformulacion_presu  set
              estado_reg = 'inactivo'
              where id_formulacion_presu = v_parametros.id_formulacion_presu;

              update pre.tformulacion_presu_detalle set
              estado_reg = 'inactivo'
              where id_formulacion_presu = v_parametros.id_formulacion_presu;


              FOR v_registros_det in (  SELECT fp.id_centro_costo, fp.id_partida,  fp.importe_total
                                                     FROM pre.tformulacion_presu_detalle fp
                                                    WHERE fp.id_formulacion_presu = v_parametros.id_formulacion_presu) LOOP

                 		/*delete from  pre.tpresup_partida
                              where id_centro_costo = v_registros_det.id_centro_costo
                              and id_partida = v_registros_det.id_partida;*/
                              SELECT prp.importe
                              INTO v_importe_presu_par
                              FROM pre.tpresup_partida prp
                              where prp.id_centro_costo = v_registros_det.id_centro_costo
                              and prp.id_partida = v_registros_det.id_partida;

                              update pre.tpresup_partida set
              					importe = COALESCE(COALESCE(v_importe_presu_par,0)::numeric - COALESCE(v_registros_det.importe_total,0)::numeric, 0)
                              where id_centro_costo = v_registros_det.id_centro_costo
                              and id_partida = v_registros_det.id_partida;

               END LOOP;


              FOR v_id_memoria_calculo_presu in (SELECT fp.id_memoria_calculo
                                                FROM pre.tformulacion_presu_detalle fp
                                                WHERE fp.id_formulacion_presu = v_parametros.id_formulacion_presu) LOOP



                           FOR v_id_memoria_calculo_det_presu in (SELECT id_memoria_det
                                                                  FROM pre.tmemoria_det
                                                                  WHERE id_memoria_calculo = v_id_memoria_calculo_presu) LOOP

                           			 --Sentencia de la eliminacion
                                    delete from pre.tmemoria_det
                                    where id_memoria_det = v_id_memoria_calculo_det_presu;

                           END LOOP;

                           --Sentencia de la eliminacion
                          delete from pre.tmemoria_calculo
                          where id_memoria_calculo = v_id_memoria_calculo_presu;


              END LOOP;




              --Definicion de la respuesta
              v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Detalle de gastos de formulacion eliminados');
              v_resp = pxp.f_agrega_clave(v_resp,'id_formulacion_presu',v_parametros.id_formulacion_presu::varchar);

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
