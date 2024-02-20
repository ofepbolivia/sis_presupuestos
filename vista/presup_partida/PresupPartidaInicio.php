<?php
/**
*@package pXP
*@file gen-SistemaDist.php
*@author  (fprudencio)
*@date 20-09-2011 10:22:05
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
	Phx.vista.PresupPartidaInicio = {
    bedit: false,
    bnew: true,
    bsave: false,
    bdel: true,
	require:'../../../sis_presupuestos/vista/presup_partida/PresupPartida.php',
	requireclase:'Phx.vista.PresupPartida',
	title:'Partidas',
	nombreVista: 'PresupPartidaInicio',
	
	constructor: function(config){
	     this.maestro=config.maestro;
	     //recibe el parametro
		Phx.vista.PresupPartidaInicio.superclass.constructor.call(this,config);
		this.addButton('btnMemoria',{ text :'Reporte PDF', iconCls:'bpdf32', disabled: true, handler : this.reportePdf,tooltip : '<b>Reporte</b><br/><b>Reporte</b>'});
   },
   preparaMenu:function(){
		var rec = this.sm.getSelected();
		var tb = this.tbar;
		Phx.vista.PresupPartidaInicio.superclass.preparaMenu.call(this);
	},
	
	liberaMenu: function() {
		var tb = Phx.vista.PresupPartidaInicio.superclass.liberaMenu.call(this);	
	},

	onReloadPage:function(m){
		this.maestro=m;
        this.store.baseParams={
		id_presupuesto:this.maestro.id_presupuesto,
		id_centro_costo: this.maestro.id_centro_costo,
		codigo_cc:this.maestro.codigo_cc,
		tipo_pres: this.maestro.tipo_pres,
		estado_pres:this.maestro.estado_pres,
		estado_reg: this.maestro.estado_reg,
		estado: this.maestro.estado,
		id_usuario_reg:this.maestro.id_usuario_reg,
		fecha_reg: this.maestro.fecha_reg,
		fecha_mod:this.maestro.fecha_mod,
		nro_tramite:this.maestro.nro_tramite,
		id_usuario_mod: this.maestro.id_usuario_mod
		};
        this.load({ params: { start: 0, limit: 50 }});
        
    },
	reportePdf: function () {
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url: '../../sis_presupuestos/control/PresupPartida/listarPresupPartidaPdf',
                params: {
					"start":"0","limit":"50","sort":"desc_partida","dir":"ASC",
		            id_presup_partida: this.store.baseParams.id_presup_partida,
					id_moneda: this.store.baseParams.id_moneda,
					id_partida: this.store.baseParams.id_partida,
					id_centro_costo: this.store.baseParams.id_centro_costo,
					fecha_hora: this.store.baseParams.fecha_hora,
					id_presupuesto: this.store.baseParams.id_presupuesto,
					id_presup_partida: this.store.baseParams.id_presup_partida,
					importe: this.store.baseParams.importe,
					importe_aprobado: this.store.baseParams.importe_aprobado,
					estado_reg: this.store.baseParams.estado_reg,
					estado: this.store.baseParams.estado,
					nro_tramite: this.store.baseParams.nro_tramite,
					fecha_reg: this.store.baseParams.fecha_reg,
					usr_reg: this.store.baseParams.usr_reg,
					id_usuario_ai: this.store.baseParams.id_usuario_ai,
					usuario_ai: this.store.baseParams.usuario_ai,
					id_usuario_reg: this.store.baseParams.id_usuario_reg,
					id_usuario_mod: this.store.baseParams.id_usuario_mod,
					fecha_mod: this.store.baseParams.fecha_mod,
					usr_mod: this.store.baseParams.usr_mod,
					desc_partida: this.store.baseParams.desc_partida,
					codigo_cc: this.store.baseParams.codigo_cc,
					tipo_pres: this.store.baseParams.tipo_pres,
					desc_gestion: this.store.baseParams.desc_gestion
                },
                success: this.successExport,
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });
        },
};
</script>