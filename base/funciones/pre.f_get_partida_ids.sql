CREATE OR REPLACE FUNCTION pre.f_get_partida_ids (
  p_id_partida integer,
  p_tipo varchar = 'siguiente'::character varying
)
RETURNS integer AS
$body$
/*
Autor: RCM
Fecha: 09/12/2013
Descripción: Función que devuelve el id_partida equivalente anterior o siguiente de la tabla pre.tpartida_ids
*/

DECLARE

	v_resp varchar;
	v_id_partida integer;
    v_nombre_funcion varchar;

BEGIN

 	v_nombre_funcion = 'pre.f_get_presupuesto_ids';

	if p_id_partida is null then
    	return null;
    end if;
	--1.Verificación de existencia de la partida
    if not exists(select 1 from pre.tpartida
    			where id_partida = p_id_partida) then
    	raise exception 'Partida con id: %, inexistente.',p_id_partida;
    end if;

    --Se verifica si se busca la cuenta anterior o la siguiente
    if p_tipo = 'siguiente' then
    	--Obtiene la cuenta de la siguiente gestión
        select p.id_partida_dos
        into v_id_partida
        from pre.tpartida_ids p
        where p.id_partida_uno = p_id_partida;
    else
    	--Obtiene la cuenta anterior
        select p.id_partida_uno
        into v_id_partida
        from pre.tpartida_ids p
        where p.id_partida_dos = p_id_partida;
    end if;

    return v_id_partida;

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

ALTER FUNCTION pre.f_get_partida_ids (p_id_partida integer, p_tipo varchar)
  OWNER TO postgres;
