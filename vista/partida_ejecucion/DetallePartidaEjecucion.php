<?php
/**
*@package pXP
*@file DetallePartidaEjecucion.php
*@author  (BVP)
*@date 
*@description Archivo
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.DetallePartidaEjecucion=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.DetallePartidaEjecucion.superclass.constructor.call(this,config);
		this.grid.getTopToolbar().disable();
        this.grid.getBottomToolbar().disable();        
        this.init();        
		this.addButton('btnChequeoDocumentosWf',{
				text: 'Documentos',
				iconCls: 'bchecklist',
				disabled: true,
				handler: this.loadCheckDocumentosSolWf,
				tooltip: '<b>Documentos de la Solicitud</b><br/>Subir los documetos requeridos en la solicitud seleccionada.'
		});
        this.addButton('detalleTramite', {
                text: 'Det. N° Tramite',
                iconCls: 'blist',
                disabled: true,
                handler: this.detalleTramite,
                tooltip: '<b>Detalle de N° Tramite.</b>'
        });                        
		//this.load({params:{start:0, limit:this.tam_pag}})
	},
			
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					fieldLabel: 'ID Partida Ejecucion',
					name: 'id_partida_ejecucion'
			},
			type:'Field',
			grid: true,
			form:true 
		},        
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					fieldLabel: 'desde',
					name: 'desde'
			},
			type:'DateField',
			grid: false,
            form: true			
		},
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					fieldLabel: 'hasta',
					name: 'hasta',                    
			},
			type:'DateField',
			grid:false,
			form:true 
		},                
		{
			config:{
				name: 'nro_tramite',
				fieldLabel: 'Nro Tramite',
				allowBlank: true,
				anchor: '80%',
				gwidth: 150				
			},
				type:'TextField',
				filters:{pfiltro:'pareje.nro_tramite',type:'string'},
				bottom_filter: true,
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'comprometido',
				fieldLabel: 'Comprometido',
				allowBlank: false,
				allowNegative: false,
				anchor: '80%',
				gwidth: 100,                
				renderer:function (value,p,record){
                Number.prototype.formatDinero = function(c, d, t){
                    var n = this,
                        c = isNaN(c = Math.abs(c)) ? 2 : c,
                        d = d == undefined ? "." : d,
                        t = t == undefined ? "," : t,
                        s = n < 0 ? "-" : "",
                        i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
                        j = (j = i.length) > 3 ? j % 3 : 0;
                    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
                };
                    return  String.format('<div style="vertical-align:middle;text-align:right;"><span >{0}</span></div>',(parseFloat(value)).formatDinero(2, ',', '.'));
                }
			},
			type:'NumberField',
			filters:{pfiltro:'comprometido',type:'numeric'},
			id_grupo:1,
			grid:true,
			form:false
        },
		{
			config:{
				name: 'ejecutado',
				fieldLabel: 'Ejecutado',
				allowBlank: false,
				allowNegative: false,
				anchor: '80%',
				gwidth: 100,
				renderer:function (value,p,record){
                Number.prototype.formatDinero = function(c, d, t){
                    var n = this,
                        c = isNaN(c = Math.abs(c)) ? 2 : c,
                        d = d == undefined ? "." : d,
                        t = t == undefined ? "," : t,
                        s = n < 0 ? "-" : "",
                        i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
                        j = (j = i.length) > 3 ? j % 3 : 0;
                    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
                };
                    return  String.format('<div style="vertical-align:middle;text-align:right;"><span >{0}</span></div>',(parseFloat(value)).formatDinero(2, ',', '.'));
                }
			},
			type:'NumberField',
			filters:{pfiltro:'ejecutado',type:'numeric'},
			id_grupo:1,
			grid:true,
			form:false
        },
		{
			config:{
				name: 'pagado',
				fieldLabel: 'Pagado',
				allowBlank: false,
				allowNegative: false,
				anchor: '80%',
				gwidth: 100,
				renderer:function (value,p,record){
                    Number.prototype.formatDinero = function(c, d, t){
                        var n = this,
                            c = isNaN(c = Math.abs(c)) ? 2 : c,
                            d = d == undefined ? "." : d,
                            t = t == undefined ? "," : t,
                            s = n < 0 ? "-" : "",
                            i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
                            j = (j = i.length) > 3 ? j % 3 : 0;
                    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
                };
                    return  String.format('<div style="vertical-align:middle;text-align:right;"><span >{0}</span></div>',(parseFloat(value)).formatDinero(2, ',', '.'));
                }                
			},
			type:'NumberField',
			filters:{pfiltro:'pagado',type:'numeric'},
			id_grupo:1,
			grid:true,
			form:false
        },                
		{
			config:{
				name: 'saldo',
				fieldLabel: 'Saldo Devengar',
				allowBlank: false,
				allowNegative: false,
				anchor: '80%',
				gwidth: 100,
				renderer:function (value,p,record){
                    Number.prototype.formatDinero = function(c, d, t){
                        var n = this,
                            c = isNaN(c = Math.abs(c)) ? 2 : c,
                            d = d == undefined ? "." : d,
                            t = t == undefined ? "," : t,
                            s = n < 0 ? "-" : "",
                            i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
                            j = (j = i.length) > 3 ? j % 3 : 0;
                    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
                };
                    return  String.format('<div style="vertical-align:middle;text-align:right;"><span >{0}</span></div>',(parseFloat(value)).formatDinero(2, ',', '.'));
                } 				
			},
			type:'NumberField',
			filters:{pfiltro:'monto',type:'numeric'},
			id_grupo:1,
			grid:true,
			form:false
        },                        
		{
			config:{
				name: 'moneda',
				fieldLabel: 'Moneda',
				allowBlank: true,
				anchor: '80%',
				gwidth: 80
			},
			type:'TextField',
			filters:{pfiltro:'mon.moneda',type:'string'},
			id_grupo:1,
			grid:true,
			form:true
		},
		{
			//configuracion del componente
			config:{
				labelSeparator:'',
				inputType:'hidden',
				fieldLabel: 'ID Presupuesto',
				name: 'id_presupuesto',
				gwidth: 50
			},
			type:'Field',
			grid: false,
			filters:{pfiltro:'pareje.id_presupuesto',type:'string'},
			bottom_filter: true,
			form:true
		},
		{
			config:{
				name: 'codigo_cc',
				//name: 'desc_pres',
				fieldLabel: 'Desc. Presupuesto',
				allowBlank: true,
				anchor: '80%',
				gwidth: 200
			},
			type:'TextField',			
			filters:{pfiltro:'vpre.codigo_cc',type:'string'},
			id_grupo:1,
			bottom_filter: true,
			grid:true,
			form:true
		},
		{
			config:{
				name: 'codigo_categoria',
				fieldLabel: 'Código Categoría Programatica',
				allowBlank: true,
				anchor: '80%',
				gwidth: 150,
				maxLength:1000
			},
			type:'TextField',			
			filters:{pfiltro:'cat.codigo_categoria',type:'string'},
			bottom_filter: true,
			id_grupo:1,
			grid:true,
			form:true
		},

		{
			config: {
				name: 'id_partida',
				fieldLabel: 'ID Partida'

			},
			type: 'TextField',
			id_grupo: 0,			
			grid: false,
			gwidth: 50,
			form: true
		},
		{
			config:{
				name: 'codigo',
				fieldLabel: 'Codigo Partida',
				allowBlank: true,
				anchor: '80%',
				gwidth: 90				
			},
			type:'TextField',
			filters:{pfiltro:'par.codigo',type:'string'},
			bottom_filter: true,
			id_grupo:1,
			grid:true,
			form:true
		},
		{
			config:{
				name: 'nombre_partida',
				fieldLabel: 'Nombre Partida',
				allowBlank: true,
				anchor: '80%',
				gwidth: 200				
			},
			type:'TextField',
			filters:{pfiltro:'par.nombre_partida',type:'string'},
			bottom_filter: true,
			id_grupo:1,
			grid:true,
			form:true
		}
	],
	tam_pag: 50,	
	title:'Detalle Partida Ejecucion',		
	ActList:'../../sis_presupuestos/control/PartidaEjecucion/listarDetallePartidaEjecucion',
	id_store:'id_partida_ejecucion',
	fields: [
		{name:'id_partida_ejecucion', type: 'numeric'},
		{name:'id_int_comprobante', type: 'numeric'},
		{name:'id_moneda', type: 'numeric'},
		{name:'id_presupuesto', type: 'numeric'},
		{name:'id_partida', type: 'numeric'},
		{name:'nro_tramite', type: 'string'},				
        {name:'comprometido', type: 'numeric'},
        {name:'ejecutado', type: 'numeric'},
        {name:'pagado', type: 'numeric'},
        {name:'saldo', type: 'numeric'},				
		{name:'monto_mb', type: 'numeric'},
		{name:'monto', type: 'numeric'},		
		{name:'id_usuario_reg', type: 'numeric'},		
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},        
        {name:'desde', type: 'date', dateFormat: 'Y-m-d'},
        {name:'hasta', type: 'date', dateFormat: 'Y-m-d'},
        //{name:'id_proceso_wf', type:'numeric'},
        'moneda','desc_pres','codigo_cc','codigo_categoria','codigo','nombre_partida',        
		
	],
	bdel:  false,
	bsave: false,
	bedit: false,
	bnew:  false,
    btest: false,

	sortInfo:{
		field: 'id_partida'		
	},    
	arrayDefaultColumHidden:['id_partida_ejecucion','columna_origen','valor_id_origen',
		,'monto_mb','fecha_mod', 'id_usuario_ai','usr_mod','estado_reg'],

	loadValoresIniciales:function(){                
		Phx.vista.DetallePartidaEjecucion.superclass.loadValoresIniciales.call(this);		
    },    
    onReloadPage:function(param){            	
            var me = this;
            this.initFiltro(param);
    },

	initFiltro: function(param){
		this.store.baseParams=param;
		this.load( { params: { start:0, limit: this.tam_pag } });
    },
    loadCheckDocumentosSolWf: function() {
			var rec=this.sm.getSelected();                        
            Ext.Ajax.request({
                        url: '../../sis_presupuestos/control/PartidaEjecucion/getProcesoWf',
                        params: { nro_tramite: rec.data.nro_tramite },
                        success: function (resp) {                                          
                            var reg = Ext.decode(Ext.util.Format.trim(resp.responseText));
                            console.log('reg=> ',reg);
                            
                            rec.data.id_proceso_wf = reg.datos[0].id_proceso_wf;
                            rec.data.nombreVista = this.nombreVista;            
                                Phx.CP.loadWindows('../../../sis_workflow/vista/documento_wf/DocumentoWf.php',
                                        'Chequear documento del WF',
                                        {
                                            width:'90%',
                                            height:500
                                        },
                                        rec.data,
                                        this.idContenedor,
                                        'DocumentoWf'
                            )                            
                        },
                        failure: this.conexionFailure,
                        timeout: this.timeout,
                        scope: this
                    });
	},
    detalleTramite: function(){        
            var rec = this.getSelectedData();
            var NumSelect=this.sm.getCount();            
            if (NumSelect != 0 ){
                Phx.CP.loadWindows('../../../sis_presupuestos/vista/partida_ejecucion/DetalleTramite.php',
                    `<h4 style="font-weight:bold;font-size:15;color:#15428b;">Detalle N° Tramite: ${rec.nro_tramite}</h4>`,
                    {
                        width: '70%',
                        height: '70%'
                    }, rec, this.idContenedor, 'DetalleTramite');
            }else{
                Ext.MessageBox.alert('Alerta', 'Antes debe seleccionar un item.');
            }
    }  
})
</script>
		
		