CREATE OR REPLACE FUNCTION pre.comparacion_presu_partida_memoria_calc (
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

FOR v_record in (select pp.id_partida,
						p.codigo,
						pp.id_presupuesto,
                        pp.id_presup_partida,
						pp.importe as importe,
                        pres.descripcion,
                        cat.codigo_categoria
						from pre.tpresup_partida pp
                        inner join pre.tpartida p on p.id_partida = pp.id_partida
                        inner join pre.tpresupuesto pres on pres.id_presupuesto=pp.id_presupuesto
                        inner join pre.vcategoria_programatica cat on cat.id_categoria_programatica=pres.id_categoria_prog
                        where p.id_gestion = 20 and pp.estado_reg='activo' and pp.importe<>0
                        --group by pp.id_partida, pp.id_presupuesto, p.codigo
                        order by cat.codigo_categoria ASC, p.codigo asc) loop

			Select sum(mc.importe_total)
            into v_importe
            from pre.tmemoria_calculo mc
            where mc.id_partida = v_record.id_partida
            and mc.id_presupuesto = v_record.id_presupuesto;

            if v_record.id_presup_partida is null then
                raise exception 'No existe la relacion presupuesto partida, id_presupuesto: %, id_partida: %.',v_record.id_presupuesto, v_record.id_partida;
            end if;

            if v_record.importe  is null then
                raise exception 'El importe en la tabla tpresu_partida es null, id_presupuesto: %, id_partida: %.',v_record.id_presupuesto, v_record.id_partida;
            end if;

             if v_importe is null then
                raise notice 'El importe en la tabla tmemoria_calculo es null, no existe memoria de calculo, id_presupuesto: %, id_partida: %.',v_record.id_presupuesto, v_record.id_partida;

            	/*UPDATE pre.tpresup_partida  set
                    importe = 0
                where id_partida = v_record.id_partida
                and id_presupuesto = v_record.id_presupuesto;*/

            end if;

            if (v_importe<>v_record.importe)then
            	raise notice 'Categoria:%, Presupuesto: %, Partida: %, Importe Presupartida: %, Importe Memoria:%, Diferencia:%', v_record.codigo_categoria, v_record.descripcion, v_record.codigo, v_importe, v_record.importe, v_importe - v_record.importe;


               /* UPDATE pre.tpresup_partida  set
                    importe = v_record.importe
                where id_partida = v_record.id_partida
                and id_presupuesto = v_record.id_presupuesto;
				*/

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

ALTER FUNCTION pre.comparacion_presu_partida_memoria_calc ()
  OWNER TO postgres;