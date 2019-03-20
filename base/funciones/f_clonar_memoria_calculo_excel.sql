CREATE OR REPLACE FUNCTION pre.f_clonar_memoria_calculo_excel (
  p_gestion_siguiente integer
)
RETURNS void AS
$body$
DECLARE
    v_nombre_funcion   	text;
    v_resp    			varchar;
    v_mensaje 			varchar;

   
    v_record			record;
    v_registros			record;
    v_id_memoria_calculo		integer;
    v_estado				varchar;
    v_gestion				integer;
    v_id_partida			integer;
    v_id_gestion			integer;

    v_id_partida_nuevo			integer;
    v_id_gestion_actual			integer;
    v_id_gestion_siguiente		integer;
    v_id_presupuesto_nuevo		integer;
   
    v_importe_detalle			numeric;
    v_id_registro					integer;
    va_id_periodo 				integer[];
    va_id_periodo_an 				integer[];
    v_id_usuario			integer;
    v_id_concepto_ingas		integer;
    v_id_periodo			integer;
    

BEGIN
v_nombre_funcion = 'pre.f_clonar_memoria_calculo_excel';


---id_gestion siguiente
select e.id_gestion
into
v_id_gestion_siguiente
from param.tgestion e
where e.gestion = p_gestion_siguiente;

FOR v_record in (select *
                from pre.tmemoria_calculo_excel m
                where m.procesado='no') LOOP



        ---recuperar id presupuesto gestion nueva
    	select p.id_presupuesto
		into
        v_id_presupuesto_nuevo
        from pre.tpresupuesto p
        inner join param.tcentro_costo c on c.id_centro_costo=p.id_centro_costo
        inner join param.ttipo_cc tc on tc.id_tipo_cc=c.id_tipo_cc
        where tc.codigo = v_record.codigo_cc
        and c.id_gestion=v_id_gestion_siguiente;

        ----recuperar la partida actual gestion nueva
        select p.id_partida
        into
        v_id_partida_nuevo
        from pre.tpartida p
        where p.codigo = v_record.codigo_partida
        and p.id_gestion=v_id_gestion_siguiente;
        
        --obtenemos el id_usuario
        select us.id_usuario
        into
        v_id_usuario
        from segu.tusuario us
        where us.cuenta=v_record.usuario;
        
        
       IF NOT EXISTS (	select 1
           					 from pre.tpresup_partida
           					where id_partida = v_id_partida_nuevo and id_presupuesto = v_id_presupuesto_nuevo) THEN
           		
                INSERT INTO pre.tpresup_partida
                (id_presupuesto,
                 id_partida,
                 id_centro_costo,
                 id_usuario_reg
                 )
                 VALUES
                (v_id_presupuesto_nuevo,
                 v_id_partida_nuevo,
                 v_id_presupuesto_nuevo,
                 v_id_usuario);

        END IF;
        
        --obtenemos el id_concepto de gasto
        Select con.id_concepto_ingas
        into
        v_id_concepto_ingas
        from param.tconcepto_ingas con
        where con.desc_ingas=v_record.concepto;
        
        --validamos que el concepto de gasto exista
        if v_id_concepto_ingas is null then
        
        	raise EXCEPTION 'El concepto de gasto %, no existe en la base de datos.',v_record.concepto;
        end if;

		insert into pre.tmemoria_calculo(
              id_concepto_ingas,
              importe_total,
              obs,
              id_presupuesto,
              estado_reg,--
              id_usuario_ai,--
              fecha_reg,
              usuario_ai,
              id_usuario_reg,
              fecha_mod,
              id_usuario_mod,
              id_partida
          	) values(
              v_id_concepto_ingas,
              v_record.importe,
              v_record.justificacion,
              v_id_presupuesto_nuevo,
              'activo',
              null,
              now(),
              null,
              v_id_usuario,
              null,
              null,
              v_id_partida_nuevo)RETURNING id_memoria_calculo into v_id_memoria_calculo;

              if(v_record.numero_periodo is not null)then
              
                      select per.id_periodo
                      into v_id_periodo
                      from param.tperiodo per
                      where per.id_gestion =  v_id_gestion_siguiente
                      and per.periodo=v_record.numero_periodo;
                      
                      --insertamos en memoria detalle
                      insert into pre.tmemoria_det(
                                    importe,
                                    estado_reg,
                                    id_periodo,
                                    id_memoria_calculo,
                                    usuario_ai,
                                    fecha_reg,
                                    id_usuario_reg,
                                    id_usuario_ai,
                                    cantidad_mem,
                                    importe_unitario
                                  )
                                  values
                                  ( v_record.importe,
                                    'activo',
                                    v_id_periodo,
                                    v_id_memoria_calculo,
                                    null,
                                    now(),
                                    v_id_usuario,
                                    null,
                                    1,
                                    v_record.importe);
              
              else

                  FOR v_registros in (  select
                                          per.id_periodo
                                          from param.tperiodo per
                                          where per.id_gestion =  v_id_gestion_siguiente
                                          order by per.periodo asc) LOOP


                                 insert into pre.tmemoria_det(
                                    importe,
                                    estado_reg,
                                    id_periodo,
                                    id_memoria_calculo,
                                    usuario_ai,
                                    fecha_reg,
                                    id_usuario_reg,
                                    id_usuario_ai,
                                    cantidad_mem,
                                    importe_unitario
                                  )
                                  values
                                  ( v_record.importe/12,
                                    'activo',
                                    v_registros.id_periodo,
                                    v_id_memoria_calculo,
                                    null,
                                    now(),
                                    v_id_usuario,
                                    null,
                                    1,
                                    v_record.importe / 12);

                     END LOOP;
                     
                 End if;    

		Update pre.tmemoria_calculo_excel
        set
        procesado='si'
        where pre.tmemoria_calculo_excel.id_memoria_calculo_excel=v_record.id_memoria_calculo_excel;

 END LOOP;

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