CREATE OR REPLACE FUNCTION pre.presu_partida_memeoria_md (
)
RETURNS void AS
$body$
DECLARE

	v_id_funcionario	integer;
    v_record			record;
    v_md				numeric ;
    v_importe			numeric;
    v_id_presup_partida integer;


BEGIN

FOR v_record in (select me.id_partida,
						p.codigo,
						me.id_presupuesto,
						sum(me.importe_total) as importe
						from pre.tmemoria_calculo me
                        inner join pre.tpartida p on p.id_partida = me.id_partida
                        where p.id_gestion = 17 and me.estado_reg='activo'
                        group by me.id_partida, me.id_presupuesto, p.codigo 
                        order by me.id_presupuesto ASC, me.id_partida asc) loop
                        
              
                        
			Select pp.importe, pp.id_presup_partida
            into v_importe,v_id_presup_partida
            from pre.tpresup_partida pp
            where pp.id_partida = v_record.id_partida
            and pp.id_presupuesto = v_record.id_presupuesto;
            
            if v_id_presup_partida is null then
                raise exception 'No existe la relacion presupuesto partida, id_presupuesto: %, id_partida: %.',v_record.id_presupuesto, v_record.id_partida;
            end if; 
            
            
            if (v_importe<>v_record.importe)then 
            	raise notice 'Partida: %, presupuesto: %, importe presupartida: %, importe memoria:%',v_record.codigo,v_record.id_presupuesto,v_importe,v_record.importe;
            
            
            	
            
            	/*UPDATE pre.tpresup_partida  set
                    importe = v_record.importe
                where id_partida = v_record.id_partida
                and id_presupuesto = v_record.id_presupuesto;*/

            
            end if;
                        
                        
            /*
			UPDATE pre.tpresup_partida  set
                    importe = 0,
                    importe_aprobado = 0
            where id_partida = v_record.id_partida
            and id_presupuesto = v_record.id_presupuesto;*/

            /*UPDATE pre.tpresup_partida  set
                    importe = v_record.importe,
                    importe_aprobado = v_record.importe
            where id_partida = v_record.id_partida
            and id_presupuesto = v_record.id_presupuesto;*/

            /*UPDATE pre.tpartida_ejecucion  set
                    monto = v_record.importe,
                    monto_mb = v_record.importe
            where id_partida = v_record.id_partida
            and id_presupuesto = v_record.id_presupuesto
            and fecha = '01/01/2017'
            and tipo_movimiento =  'formulado';*/



end loop;


END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;