CREATE OR REPLACE FUNCTION pre.sp_detalle_presupuesto_partida (
  presupuesto_id integer,
  partida_id integer,
  moneda_id integer,
  usuario_id integer
)
RETURNS TABLE (
  comprometido numeric,
  ejecutado numeric,
  pagado numeric,
  moneda varchar,
  presupuesto integer,
  num_tramite varchar,
  descripcion text,
  gestion integer,
  partida varchar,
  codigo_partida varchar,
  detalle_presupuesto text,
  solicitante text,
  fecha_string text,
  fecha date,
  user_reporte text,
  partida_ejecucion integer,
  proveedor varchar,
  id_moneda integer
) AS
$body$
DECLARE
  v_resp INT;

BEGIN
    RETURN QUERY
    select DISTINCT ON (num_tramite,codigo_cc, id_partida_ejecucion)
    registros.comprometido,registros.ejecutado, registros.pagado,registros.codigo,registros.id_centro_costo,
    registros.num_tramite,registros.descripcion, registros.gestion,
    registros.nombre_partida,registros.partida_cod, registros.codigo_cc, registros.desc_funcionario1,registros.fechaAux,
    registros.fecha_soli, registros.desc_persona, registros.id_partida_ejecucion, registros.desc_proveedor,registros.id_moneda
	from (select
    (select COALESCE(ps_comprometido,0) from pre.f_verificar_com_eje_pag(sd.id_partida_ejecucion, moneda_id)) as comprometido,
	(select COALESCE(ps_ejecutado,0) from pre.f_verificar_com_eje_pag(sd.id_partida_ejecucion, moneda_id)) as ejecutado,
	(select COALESCE(ps_pagado,0) from pre.f_verificar_com_eje_pag(sd.id_partida_ejecucion, moneda_id)) as pagado,
    m.codigo,
    sd.id_centro_costo,
    s.num_tramite,
    coalesce(s.justificacion, '')  ||' - Desc: '|| coalesce(sd.descripcion, '') as descripcion,
    ges.gestion,
    par.nombre_partida,
    par.codigo as partida_cod,
    vpres.codigo_cc,
    soli.desc_funcionario1,
    to_char(s.fecha_soli ,'DD/MM/YYYY') as fechaAux,
    s.fecha_soli,
    usu.desc_persona ,
    sd.id_partida_ejecucion,
    pro.desc_proveedor,
    s.id_moneda
    from
    adq.tsolicitud s
    inner join adq.tsolicitud_det sd on sd.id_solicitud = s.id_solicitud and s.estado_reg = 'activo' and  sd.estado_reg = 'activo'
    inner join param.tmoneda m on m.id_moneda = moneda_id
    inner join param.tcentro_costo tcco on tcco.id_centro_costo = sd.id_centro_costo
    inner join param.tgestion ges on ges.id_gestion = tcco.id_gestion
    inner join pre.tpartida par on par.id_partida = sd.id_partida
    inner join pre.vpresupuesto_cc vpres on vpres.id_centro_costo = sd.id_centro_costo
    inner join orga.vfuncionario soli on soli.id_funcionario = s.id_funcionario
    inner join segu.vusuario  usu on usu.id_usuario = usuario_id
    inner join param.vproveedor pro on pro.id_proveedor = s.id_proveedor
    where --s.id_gestion=17 and s.fecha_soli >'31/10/2019' and

    s.estado not in ('borrador','vbgerencia','vbpresupuestos','anulado')
          AND ((presupuesto_id > 0 AND sd.id_centro_costo in (presupuesto_id))OR(presupuesto_id = 0))
          AND((partida_id > 0 AND sd.id_partida in (partida_id))OR(partida_id = 0))

     union

    select
    (select COALESCE(ps_comprometido,0) from pre.f_verificar_com_eje_pag(odi.id_partida_ejecucion_com, moneda_id)) as comprometido,
	(select COALESCE(ps_ejecutado,0) from pre.f_verificar_com_eje_pag(odi.id_partida_ejecucion_com, moneda_id)) as ejecutado,
	(select COALESCE(ps_pagado,0) from pre.f_verificar_com_eje_pag(odi.id_partida_ejecucion_com, moneda_id)) as pagado,
    m.codigo,
    odi.id_centro_costo,
    op.num_tramite,
    coalesce(op.obs, '') ||' - Desc: '||coalesce(odi.descripcion, ''),
    ges.gestion,
    part.nombre_partida,
    part.codigo,
    vpresu.codigo_cc,
    solici.desc_funcionario1,
    to_char(op.fecha,'DD/MM/YYYY'),
    op.fecha,
    usua.desc_persona,
    odi.id_partida_ejecucion_com,
    pro.desc_proveedor,
    op.id_moneda
    from
    tes.tobligacion_pago op
    inner join tes.tobligacion_det odi on odi.id_obligacion_pago = op.id_obligacion_pago and odi.estado_reg = 'activo' and  op.estado_reg = 'activo'
    inner join param.tmoneda m on m.id_moneda = moneda_id
    inner join param.tcentro_costo tco on tco.id_centro_costo = odi.id_centro_costo
    inner join param.tgestion ges on ges.id_gestion = tco.id_gestion
    inner join pre.tpartida part on part.id_partida = odi.id_partida
    inner join pre.vpresupuesto_cc vpresu on vpresu.id_centro_costo = odi.id_centro_costo
    inner join orga.vfuncionario solici on solici.id_funcionario = op.id_funcionario
    inner join segu.vusuario  usua on usua.id_usuario = usuario_id
    inner join param.vproveedor pro on pro.id_proveedor = op.id_proveedor
    where  op.estado not in ('borrador','vbgerencia','vbpresupuestos','anulado')
    AND ((presupuesto_id > 0 AND odi.id_centro_costo in (presupuesto_id))OR(presupuesto_id = 0))
    AND((partida_id > 0 AND odi.id_partida in (partida_id))OR(partida_id = 0))


union

    select
    (select COALESCE(ps_comprometido,0) from pre.f_verificar_com_eje_pag(tra.id_partida_ejecucion, moneda_id)) as comprometido,
    (select COALESCE(ps_ejecutado,0) from pre.f_verificar_com_eje_pag(tra.id_partida_ejecucion, moneda_id)) as ejecutado,
    (select COALESCE(ps_pagado,0) from pre.f_verificar_com_eje_pag(tra.id_partida_ejecucion, moneda_id)) as pagado,
    m.codigo,
    tra.id_centro_costo,
    ic.nro_tramite,
    ic.glosa1,
    ges.gestion,
    part.nombre_partida,
    part.codigo,
    vpresu.codigo_cc,
    fp.desc_funcionario1,
    to_char(ic.fecha,'DD/MM/YYYY'),
    ic.fecha,
    usua.desc_persona,
    tra.id_partida_ejecucion,
    ic.beneficiario,
    ic.id_moneda
    from
    conta.tint_comprobante ic
    inner join conta.tint_transaccion tra on tra.id_int_comprobante = ic.id_int_comprobante
    and ic.estado_reg = 'validado' and tra.estado_reg = 'activo'
    inner join param.tmoneda m on m.id_moneda = moneda_id
    inner join param.tcentro_costo tco on tco.id_centro_costo = tra.id_centro_costo
    inner join param.tgestion ges on ges.id_gestion = tco.id_gestion
    inner join pre.tpartida part on part.id_partida = tra.id_partida
    inner join pre.vpresupuesto_cc vpresu on vpresu.id_centro_costo = tra.id_centro_costo
    inner join segu.vusuario usu on usu.id_usuario = ic.id_usuario_reg
    inner join orga.vfuncionario_persona fp on fp.id_persona = usu.id_persona
    inner join segu.vusuario usua on usua.id_usuario = usuario_id
    where ((presupuesto_id > 0 AND tra.id_centro_costo in (presupuesto_id))OR(presupuesto_id = 0))
    AND((partida_id > 0 AND tra.id_partida in (partida_id))OR(partida_id = 0))
    and tra.id_partida_ejecucion is not null

    union

    select
    (select COALESCE(ps_comprometido,0) from pre.f_verificar_com_eje_pag(doc.id_partida_ejecucion, moneda_id)) as comprometido,
    (select COALESCE(ps_ejecutado,0) from pre.f_verificar_com_eje_pag(doc.id_partida_ejecucion, moneda_id)) as ejecutado,
    (select COALESCE(ps_pagado,0) from pre.f_verificar_com_eje_pag(doc.id_partida_ejecucion, moneda_id)) as pagado,
    m.codigo,
    doc.id_centro_costo,
    docv.nro_tramite,
    coalesce(doc.descripcion, '') ||' - Desc: '|| coalesce(docv.razon_social, '') as descripcion,
    ges.gestion,
    par.nombre_partida,
    par.codigo as partida_cod,
    vpres.codigo_cc,
    soli.desc_funcionario1,
    to_char(docv.fecha ,'DD/MM/YYYY') as fechaAux,
    docv.fecha,
    usu.desc_persona ,
    doc.id_partida_ejecucion,
    pro.desc_proveedor,
    docv.id_moneda
    from
    conta.tdoc_concepto doc
    inner join conta.tdoc_compra_venta docv on docv.id_doc_compra_venta = doc.id_doc_compra_venta
    and doc.estado_reg = 'activo' and docv.estado_reg = 'activo'
    inner join param.tmoneda m on m.id_moneda = moneda_id
    inner join param.tcentro_costo tcco on tcco.id_centro_costo = doc.id_centro_costo
    inner join param.tgestion ges on ges.id_gestion = tcco.id_gestion
    inner join pre.tpartida par on par.id_partida = doc.id_partida
    inner join pre.vpresupuesto_cc vpres on vpres.id_centro_costo = doc.id_centro_costo
    inner join segu.vusuario usua on usua.id_usuario = doc.id_usuario_reg
    inner join orga.vfuncionario_persona soli on soli.id_persona = usua.id_persona
    inner join segu.vusuario usu on usu.id_usuario = usuario_id
    left join param.vproveedor pro on pro.id_proveedor = docv.id_proveedor
    where ((presupuesto_id > 0 AND doc.id_centro_costo in (presupuesto_id))OR(presupuesto_id = 0))
    AND((partida_id > 0 AND doc.id_partida in (partida_id))OR(partida_id = 0))
    and doc.id_partida_ejecucion is not null

    ) as registros
    where registros.id_moneda = moneda_id
   order by num_tramite,codigo_cc,id_partida_ejecucion,fecha_soli;
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
RETURNS NULL ON NULL INPUT
SECURITY INVOKER
COST 100 ROWS 1000;
