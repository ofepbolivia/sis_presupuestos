CREATE OR REPLACE FUNCTION pre.ft_presup_partida_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Presupuesto
 FUNCION: 		pre.ft_presup_partida_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'pre.tpresup_partida'
 AUTOR: 		 (admin)
 FECHA:	        29-02-2016 19:40:34
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
	v_id_presup_partida		integer;
    v_registros				record;
    v_factor				numeric;
    v_resp_presu		    varchar;
    v_registro_partidas  	record;
    v_id_partida 			integer;

    v_techo_importe			numeric;
    v_total_importe_presu   numeric;
    v_saldo_comprometer		varchar;

    v_tabla					varchar;
    v_id_moneda_base		integer;
    v_monto					numeric;

BEGIN

    v_nombre_funcion = 'pre.ft_presup_partida_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'PRE_PRPA_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		admin
 	#FECHA:		29-02-2016 19:40:34
	***********************************/

	if(p_transaccion='PRE_PRPA_INS')then

        begin

           select
             pre.estado
           into
            v_registros
           from pre.tpresupuesto pre
           where pre.id_presupuesto = v_parametros.id_presupuesto;


           --TODO aumentar una bnadera de correccion al presupuesto para añadir partidas
           IF  v_registros.estado not in  ('borrador','aprobado') THEN
             raise exception 'Solo puede añadir partidas en presupuesto en estado borrador';
           END IF;


           IF exists(select 1
                    from pre.tpresup_partida pp
                    where pp.id_partida = v_parametros.id_partida
                          and pp.id_presupuesto = v_parametros.id_presupuesto
                          and pp.estado_reg = 'activo') THEN
                raise exception 'esta apartida ya esta relacionada con el presupuesto';
           END IF;

           --control de techo presupuestario
           SELECT
             tecpre.importe_techo_presupuesto
           INTO
             v_techo_importe
           FROM pre.ttecho_presupuestos tecpre
           WHERE tecpre.estado_techo_presupuesto = 'Activo'
           and tecpre.id_presupuesto = v_parametros.id_presupuesto;

           SELECT sum(ppar.importe)
           INTO
           	v_total_importe_presu
           FROM pre.tpresup_partida ppar
           WHERE ppar.id_presupuesto = v_parametros.id_presupuesto;


           IF (v_techo_importe < v_total_importe_presu) THEN
            raise exception 'YA ESTA AL TOPE DE SU TECHO PRESUPUESTARIO, TOTAL IMPORTE:% , TECHO PRESUPUESTARIO: %', v_total_importe_presu, v_techo_importe;
          END IF;


        	--Sentencia de la insercion
        	insert into pre.tpresup_partida(
              id_partida,
              id_centro_costo,
              id_presupuesto,
              id_usuario_ai,
              usuario_ai,
              estado_reg,
              fecha_reg,
              id_usuario_reg,
              id_usuario_mod,
              fecha_mod
          	) values(
              v_parametros.id_partida,
              v_parametros.id_presupuesto,
              v_parametros.id_presupuesto,
              v_parametros._id_usuario_ai,
              v_parametros._nombre_usuario_ai,
              'activo',
              now(),
              p_id_usuario,
              null,
              null



			)RETURNING id_presup_partida into v_id_presup_partida;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','PREPAR almacenado(a) con exito (id_presup_partida'||v_id_presup_partida||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_presup_partida',v_id_presup_partida::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'PRE_PRPA_MOD'
 	#DESCRIPCION:	modificacion de presupuestos y partidas
 	#AUTOR:		admin
 	#FECHA:		29-02-2016 19:40:34
	***********************************/

	elsif(p_transaccion='PRE_PRPA_MOD')then

       begin

           update pre.tpresup_partida pp set
             importe_aprobado =  v_parametros.importe_aprobado
           where id_presup_partida  =  v_parametros.id_presup_partida;

           v_resp = pxp.f_agrega_clave(v_resp,'mensaje','importe aprobado modificado '||v_parametros.id_presup_partida);
           v_resp = pxp.f_agrega_clave(v_resp,'id_presup_partida',v_parametros.id_presup_partida::varchar);

           --Devuelve la respuesta
            return v_resp;


	   end;

	/*********************************
 	#TRANSACCION:  'PRE_PRPA_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		admin
 	#FECHA:		29-02-2016 19:40:34
	***********************************/

	elsif(p_transaccion='PRE_PRPA_ELI')then

		begin

        select	pres.id_partida
					into
                    v_id_partida
            		from pre.tpartida_ejecucion prj
            		inner join pre.tpresup_partida pres on pres.id_presupuesto = prj.id_presupuesto
                    where pres.id_presup_partida = v_parametros.id_presup_partida;
        select
              pa.tipo_movimiento,
              pa.monto_mb,
              pa.monto
             	into
                v_registro_partidas
            from pre.tpresupuesto pr
            inner join pre.tpresup_partida pp on pr.id_presupuesto = pp.id_presupuesto
            inner join pre.tpartida_ejecucion pa on pa.id_presupuesto = pr.id_presupuesto
            where pp.id_presup_partida = v_parametros.id_presup_partida and pa.id_partida = v_id_partida;

        select
              pr.estado,
              pp.importe
              into
            v_registros
            from pre.tpresupuesto pr
            inner join pre.tpresup_partida pp on pr.id_presupuesto = pp.id_presupuesto
           	where pp.id_presup_partida = v_parametros.id_presup_partida;


             --TODO aumentar una bnadera de correccion al presupuesto para añadir partidas

            IF  v_registros.estado = 'formulacion' or v_registros.estado = 'aprobado' and v_registros.importe > 0
            	or v_registros.estado = 'vobopre' or v_registros.estado = 'revision' THEN

                raise exception 'Solo puede elimnar partidas en presupuesto en estado borrador';

            ELSIF  v_registros.estado = 'aprobado' THEN
            	IF  v_registro_partidas.tipo_movimiento = 'formulado' and v_registro_partidas.monto <> 0 THEN
            		raise exception 'Tiene un monto de % %',v_registro_partidas.monto ||' tipo Movimiento ',v_registro_partidas.tipo_movimiento;
			ELSE
                IF v_registros.importe = 0 and v_registro_partidas.tipo_movimiento != 'formulado' and v_registro_partidas.monto <> 0 THEN
            		raise exception 'Tiene un monto de % %',v_registro_partidas.monto ||' tipo Movimiento ',v_registro_partidas.tipo_movimiento;
				END IF;
            END IF;
            END IF;

            IF v_registros.importe > 0 THEN
               raise exception 'Tiene que eliminar primero las memorias de calculo asociadas a esta partida';
            END IF;

           --Sentencia de la eliminacion
			delete from pre.tpresup_partida
            where id_presup_partida=v_parametros.id_presup_partida;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','PREPAR eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_presup_partida',v_parametros.id_presup_partida::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'PRE_PREPARVER_IME'
 	#DESCRIPCION:	VErifica seun por centaje el monto a presupuestar
 	#AUTOR:		admin
 	#FECHA:		29-02-2016 19:40:34
	***********************************/

	elsif(p_transaccion='PRE_PREPARVER_IME')then

		begin

               v_factor = v_parametros.porcentaje_aprobado/100.00;

              --lista los presupeustos partidas
              FOR v_registros in   ( select
                                         pp.id_presup_partida,
                                         pp.importe,
                                         pp.importe_aprobado
                                      from pre.tpresup_partida pp
                                      where pp.id_presupuesto = v_parametros.id_presupuesto) LOOP

                     update pre.tpresup_partida pp set
                        importe_aprobado = importe * v_factor
                     where pp.id_presup_partida  = v_registros.id_presup_partida;

              END LOOP;


               -- actuliza el importe vericado


            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','PRESUPUESTO PARTIDA VERIFICADO AL '||v_parametros.porcentaje_aprobado::Varchar||' %');
            v_resp = pxp.f_agrega_clave(v_resp,'id_presupuesto',v_parametros.id_presupuesto::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;
    /*********************************
 	#TRANSACCION:  'PRE_VERPRE_IME'
 	#DESCRIPCION:	Interface para Verificar Presupuesto
 	#AUTOR:	     Rensi ARteaga Copari
 	#FECHA:		15-08-2013 22:02:47
	***********************************/

	elsif(p_transaccion='PRE_VERPRE_IME')then

		begin

        /***
         *** agregado condicion breydi.vaquez (06/01/2020) para reporte solicitud de compra
         *** primera verificacion a nivel centro de costo 
         ***/
         if v_parametros.sis_origen = 'ADQ' then
         	select presupuesto_aprobado into v_tabla from adq.tsolicitud where id_solicitud = v_parametros.id_solicitud;
         elsif v_parametros.sis_origen = 'OBP' then
         	select presupuesto_aprobado into v_tabla from tes.tobligacion_pago where id_obligacion_pago = v_parametros.id_solicitud;
	     elsif v_parametros.sis_origen = 'MAT' then
         	select presupuesto_aprobado into v_tabla from mat.tsolicitud where id_solicitud = v_parametros.id_solicitud;
         end if;

         if  v_tabla in ( 'verificar', 'sin_presupuesto_cc') then
            	v_resp_presu = pre.f_verificar_presupuesto_partida_centro_costo(v_parametros.id_presupuesto,
                                                                        v_parametros.id_partida,
                                                                        v_parametros.id_moneda,
                                                                        v_parametros.monto_total);
           
         else 
           -- fin breydi.vaquez (06/01/2020)
           		v_resp_presu =    pre.f_verificar_presupuesto_partida ( v_parametros.id_presupuesto,
            									v_parametros.id_partida,
                                                v_parametros.id_moneda,
                                                v_parametros.monto_total);

		  end if;
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Presupuesto verificado)');
            v_resp = pxp.f_agrega_clave(v_resp,'presu_verificado',v_resp_presu);

            --Devuelve la respuesta
            return v_resp;

		end;
   /*********************************
   #TRANSACCION:    'PRE_CAPPRES_REP'
   #DESCRIPCION:     captura de presupuesto a nivel centro de costo para reporte de solicitud de compra
   #AUTOR:           breydi vasquez
   #FECHA:          25-02-2020
  ***********************************/

  ELSEIF(p_transaccion='PRE_CAPPRES_REP')then
  	begin

            SELECT
              (sum(pre.f_get_estado_presupuesto_mb_x_fechas(prpa.id_presupuesto, prpa.id_partida,'formulado',
              ('01/01/'||extract(year from now()))::date,  ('31/12/'||extract(year from now()))::date )) -
              sum(pre.f_get_estado_presupuesto_mb_x_fechas(prpa.id_presupuesto, prpa.id_partida,'comprometido',
              ('01/01/'||extract(year from now()))::date,  ('31/12/'||extract(year from now()))::date )) )
            into v_monto

            FROM pre.tpresup_partida prpa
            INNER JOIN pre.vpresupuesto_cc_x_partida p on p.id_presupuesto = prpa.id_presupuesto
            WHERE  p.id_presupuesto = v_parametros.id_presupuesto
            	   and prpa.id_partida = v_parametros.id_partida;

			v_id_moneda_base = param.f_get_moneda_base();

            IF  v_id_moneda_base != v_parametros.id_moneda THEN

              -- si el tipo de cambio es null utilza el cambio oficial para la fecha
              	v_saldo_comprometer = param.f_convertir_moneda (
			             v_id_moneda_base,
                         v_parametros.id_moneda,
                         v_monto,
                         now()::date,
                         'CUS',50,
                         NULL, 'no');

            ELSE
                v_saldo_comprometer = v_monto;
             END IF;

            v_resp = pxp.f_agrega_clave(v_resp, 'mensaje', 'Presupuesto');
            v_resp = pxp.f_agrega_clave(v_resp, 'captura_presupuesto', v_saldo_comprometer);

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