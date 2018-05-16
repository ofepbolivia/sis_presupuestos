CREATE OR REPLACE FUNCTION pre.f_get_entidad_transferencia_ids (
)
RETURNS integer AS
$body$
/*
Autor: admin.admin
Fecha: 2018
Descripción: Función que devuelve el v_id_entidad_tranferencia equivalente anterior o siguiente de la tabla pre.tentidad_tranferencia_ids
*/

DECLARE
  v_id_entidad_transferencia 	integer;
  p_id_entidad_transferencia	record;
  v_id_entidad_tranferencia		record;
BEGIN
  if p_id_entidad_transferencia is null then
  		return null;
  end if;

  --verificacion de existencia de la partida

    if not exists(select 1 from pre.tentidad_transferencia
    			where id_entidad_transferencia = p_id_entidad_transferencia) then
    	raise exception 'Partida inexistente';
    end if;

 --Se verifica si se busca la cuenta anterior o la siguiente
    if p_tipo = 'siguiente' then
    	--Obtiene la cuenta de la siguiente gestión
        select e.id_entidad_dos
        into v_id_entidad_tranferencia
        from pre.tentidad_transferencia_ids e
        where e.id_entidad_uno = p_id_entidad_transferencia;
    else
    	--Obtiene la cuenta anterior
        select e.id_entidad_uno
        into v_id_entidad_tranferencia
        from pre.tentidad_transferencia_ids e
        where e.id_entidad_dos = p_id_entidad_transferencia;
    end if;

    return v_id_entidad_tranferencia;

END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;