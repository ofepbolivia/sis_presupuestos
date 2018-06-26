CREATE OR REPLACE FUNCTION pre.sp_procesos_ejecutados_no_concluidos (
  "r_EP_AC" varchar,
  "r_EP_PG" varchar,
  "r_EP_PR" varchar,
  r_partida varchar,
  "r_CeCo" varchar,
  periodo varchar
)
RETURNS TABLE (
  codigo_categoria varchar,
  id_centro_costo integer,
  partida varchar,
  solicitante varchar,
  proveedor varchar,
  "Descripcion" varchar,
  comprometido numeric,
  devengado numeric,
  pagado numeric,
  fecha_tentativa date,
  dif_com_dev numeric,
  num_tramite varchar
) AS
$body$
DECLARE
  v_resp INT;
BEGIN

END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
RETURNS NULL ON NULL INPUT
SECURITY INVOKER
COST 100 ROWS 1000;