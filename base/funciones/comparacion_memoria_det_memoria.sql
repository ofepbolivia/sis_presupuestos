CREATE OR REPLACE FUNCTION pre.comparacion_memoria_det_memoria (
)
RETURNS void AS
$body$
DECLARE

	v_id_funcionario	integer;
    v_record			record;
    v_md				numeric ;
    v_importe			numeric;


BEGIN

FOR v_record in (select me.id_memoria_calculo,
						p.codigo,
						me.id_presupuesto,
                        sum(md.importe) as importe_det,
						me.importe_total as importe
						from pre.tmemoria_det md 
                        inner join pre.tmemoria_calculo me on me.id_memoria_calculo=md.id_memoria_calculo
                        inner join pre.tpartida p on p.id_partida = me.id_partida
                        where p.id_gestion = 17
                        group by me.id_memoria_calculo,p.codigo,me.id_presupuesto
                        order by me.id_partida ASC) loop
                        
                        
                        
			/*Select importe
            into v_importe
            from pre.tpresup_partida pp
            where pp.id_partida = v_record.id_partida
            and pp.id_presupuesto = v_record.id_presupuesto;*/
            
            
            if (v_record.importe_det<>v_record.importe)then 
            	raise notice 'id_memoria_calculo:%, partida: %, id_presupuesto: %, importe detalle: %, importe memoria:%',v_record.id_memoria_calculo ,v_record.codigo,v_record.id_presupuesto,v_record.importe_det,v_record.importe;
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