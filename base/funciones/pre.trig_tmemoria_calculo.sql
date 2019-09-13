CREATE OR REPLACE FUNCTION pre.trig_tmemoria_calculo (
)
RETURNS trigger AS
$body$
DECLARE
     v_reg_pres_par 			record;
     v_reg_pres_par_new			record;
     v_reg						record;
     v_id_partida_new			integer;
     v_id_partida_old			integer;
     v_id_presupuesto			integer;
     v_id_presupuesto_new		integer;
     v_importe_total_segun_memoria_old	numeric;
     v_importe_total_segun_memoria_new	numeric;
BEGIN
   --     select (current_database()::text)||'_'||NEW.cuenta into g_new_login;
   --   select (current_database()::text)||'_'||OLD.cuenta into g_old_login;
   
   
  
   
   
    IF TG_OP = 'INSERT' THEN
    
           /*select 
                mc.id_partida,
                mc.id_presupuesto
           into
               v_id_partida_new,
               v_id_presupuesto
           FROM pre.vmemoria_calculo mc where mc.id_memoria_calculo = NEW.id_memoria_calculo;*/
           
           --RAC 22/06/2017 
           --cadaa vez que insertemos uan memoria de calculo vamos a recalcular el monto para la partida
            select 
                 sum(mc.importe_total)
              into
                 v_importe_total_segun_memoria_new
             from pre.vmemoria_calculo mc
             where mc.id_partida = NEW.id_partida and mc.id_presupuesto = NEW.id_presupuesto;
            --fin 
   
      
       
           update pre.tpresup_partida pp set      
              importe = COALESCE(v_importe_total_segun_memoria_new ,0)       
           where id_presupuesto = NEW.id_presupuesto and id_partida = NEW.id_partida;
   
   
   ELSIF TG_OP = 'UPDATE' THEN
   
             
   			
           
           ---validacion de presu_partida nuevo
           select 
             pp.importe,
             pp.id_presup_partida
           into
             v_reg_pres_par_new
           from pre.tpresup_partida pp
           where pp.id_presupuesto = NEW.id_presupuesto and pp.id_partida = NEW.id_partida;
           
            IF v_reg_pres_par_new is null THEN
            
	            raise exception 'No se encontro  la relacion presupuesto_partida para el id_presupuesto_new: % , id_partida_new: %',NEW.id_presupuesto ,NEW.id_partida ;
           		insert into pre.tpresup_partida
                (id_partida,
                id_presupuesto)
                values(
                NEW.id_partida,
                NEW.id_presupuesto);            
           
           END IF;
           
           
           
           
           
           
           --RAC 22/06/2017 OLD partida
           --cadaa vez que insertemos una  memoria de calculo vamos a recalcular el monto para la partida
            select 
                 sum(mc.importe_total)
              into
                 v_importe_total_segun_memoria_old
             from pre.vmemoria_calculo mc
             where mc.id_partida = OLD.id_partida and mc.id_presupuesto = OLD.id_presupuesto;
            --fin 
           
           --RAC 22/06/2017 NEW PARTIDA
           --cadaa vez que insertemos uan memoria de calculo vamos a recalcular el monto para la partida
            select 
                 sum(mc.importe_total)
              into
                 v_importe_total_segun_memoria_new
             from pre.vmemoria_calculo mc
             where mc.id_partida = NEW.id_partida and mc.id_presupuesto = NEW.id_presupuesto;
            --fin 
       
   		
          --vieja partida
          update pre.tpresup_partida pp set      
            importe = COALESCE(v_importe_total_segun_memoria_old,0)       
          where id_partida=OLD.id_partida and id_presupuesto=OLD.id_presupuesto;
          
          
          --nueva partida
          update pre.tpresup_partida pp set      
            importe = COALESCE(v_importe_total_segun_memoria_new,0)       
          where id_partida=NEW.id_partida and id_presupuesto=NEW.id_presupuesto;
      
        
   
   
   ELSEIF TG_OP = 'DELETE' THEN
    
          --solo con presupuesto y el concepto de gasto recuperamos la partida
            
                  
           
           --RAC 22/06/2017 
           --cadaa vez que insertemos uan memoria de calculo vamos a recalcular el monto para la partida
          select 
                 sum(mc.importe_total)
              into
                 v_importe_total_segun_memoria_new
          from pre.vmemoria_calculo mc
          where mc.id_partida = OLD.id_partida and mc.id_presupuesto = OLD.id_presupuesto;
          --fin 
           
          
           
           
          update pre.tpresup_partida pp set      
            importe = COALESCE(v_importe_total_segun_memoria_new,0)      
          where pp.id_partida = OLD.id_partida and pp.id_presupuesto = OLD.id_presupuesto;
         
   END IF;   
 
   
   RETURN NULL;
   
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;