<?php
/**
*@package pXP
*@file gen-PresupPartida.php
*@author  (admin)
*@date 29-02-2016 19:40:34
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
//var estado = 'Nestor';
Phx.vista.PresupPartidaEstado=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
		//this.title = config.nombre_estado;
    	//llama al constructor de la clase padre
		Phx.vista.PresupPartidaEstado.superclass.constructor.call(this,config);
		this.init();
		this.bloquearMenus();
        this.addButton('btnMemoria',{ text :'Reporte PDF', iconCls:'bpdf32', disabled: true, handler : this.reporteEstadoPdf ,tooltip : '<b>Reporte</b><br/><b>Reporte</b>'});
	},
			
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_presup_partida'
			},
			type:'Field',
			form:false 
		},
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_presupuesto'
			},
			type:'Field',
			form:false 
		},
	   	{
   			config:{
   				sysorigen:'sis_presupuestos',
       		    name:'id_partida',
   				origen:'PARTIDA',
   				allowBlank:false,
   				fieldLabel:'Partida',
   				gdisplayField:'desc_partida',//mapea al store del grid
   				baseParams: {sw_transaccional: 'movimiento', partida_tipo: 'presupuestaria'},
   				renderer:function(value, p, record){
   					
   					 if(record.data.tipo_reg != 'summary'){
	            	   return String.format('{0} - ({1})', record.data['desc_partida'],   record.data['desc_gestion']);
	            	 }
	            	 else{
	            	 	''
	            	 }
               	 
                },
   				gwidth:459,
   				width: 280,
   				listWidth: 350
       	     },
   			type:'ComboRec',
   			bottom_filter: true,
   			id_grupo:0,
   			filters:{	
		        pfiltro: 'prpa.desc_partida',
				type: 'string'
			},
   		   
   			grid:true,   			
   			form:false
	   	},
		
		{
			config:{
				name: 'importe',
				fieldLabel: 'Según Memoria',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				galign: 'right ',
				maxLength:1179650,
				renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
							return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
						}
						else{
							return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));
						}
						
					}
			},
				type:'NumberField',
				filters:{pfiltro:'prpa.importe',type:'numeric'},
				id_grupo:1,
				grid: true,
				form: false
		},
		
		{
			config:{
				name: 'importe_aprobado',
				fieldLabel: 'Aprobado',
				gwidth: 100,
				galign: 'right ',
				renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
							return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
						}
						else{
							return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));
						}
						
					}
			},
				type: 'NumberField',
				filters: { pfiltro:'prpa.importe', type: 'numeric' },
				id_grupo: 1,
				grid: true,
				form: false
		},
		{
			config:{
				name: 'ajustado',
				fieldLabel: 'Ajustes',
				gwidth: 100,
				galign: 'right ',
				sortable: false,
				renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
							return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
						}
						else{
							var tmp = record.data.importe_aprobado -  record.data.formulado;
							return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(tmp,'0,000.00'));
						}
						
					}
			},
				type: 'NumberField',
				//filters: { pfiltro:'prpa.formulado', type: 'numeric' },
				id_grupo: 1,
				grid: true,
				form: false
		},
		{
			config:{
				name: 'formulado',
				fieldLabel: 'Vigente',
				gwidth: 100,
				galign: 'right ',
				renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
							return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
						}
						else{
							return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));
						}
						
					}
			},
				type: 'NumberField',
				filters: { pfiltro:'prpa.formulado', type: 'numeric' },
				id_grupo: 1,
				grid: true,
				form: false
		},
		{
			config:{
				name: 'comprometido',
				fieldLabel: 'Comprometido',
				gwidth: 100,
				galign: 'right ',
				renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
							return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
						}
						else{
							return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));
						}
						
					}
			},
				type: 'NumberField',
				filters: { pfiltro:'prpa.comprometido', type: 'numeric' },
				id_grupo: 1,
				grid: true,
				form: false
		},
		{
			config:{
				name: 'ejecutado',
				fieldLabel: 'Ejecutado',
				gwidth: 100,
				galign: 'right ',
				renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
							return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
						}
						else{
							return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));
						}
						
					}
			},
				type: 'NumberField',
				filters: { pfiltro:'prpa.ejecutado', type: 'numeric' },
				id_grupo: 1,
				grid: true,
				form: false
		},
		{
			config:{
				name: 'pagado',
				fieldLabel: 'Pagado',
				gwidth: 100,
				galign: 'right ',
				renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
							return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
						}
						else{
							return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));
						}
						
					}
			},
				type: 'NumberField',
				filters: { pfiltro:'prpa.pagado', type: 'numeric' },
				id_grupo: 1,
				grid: true,
				form: false
		},
		{
			config:{
				name: 'porc_ejecucion',
				fieldLabel: '% Ejecución',
				gwidth: 100,
				galign: 'right ',
				sortable: false,
				renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
							return  String.format('{0} %', Math.round(value*100)/100);
						}
						else{
							
							var tmp = (record.data['ejecutado'] /record.data['formulado'])*100
							
							return  String.format('<b><font size=2 >{0} %</font><b>', Math.round(tmp*100)/100);
						}
						
					}
			},
				type: 'NumberField',
				id_grupo: 1,
				grid: true,
				form: false
		},
		
		
		{
			config:{
				name: 'estado_reg',
				fieldLabel: 'Estado Reg.',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:10
			},
				type:'TextField',
				filters:{pfiltro:'prpa.estado_reg',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		
		{
			config:{
				name: 'fecha_reg',
				fieldLabel: 'Fecha creación',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y', 
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
			},
				type:'DateField',
				filters:{pfiltro:'prpa.fecha_reg',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'usr_reg',
				fieldLabel: 'Creado por',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'usu1.cuenta',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'usr_mod',
				fieldLabel: 'Modificado por',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'usu2.cuenta',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'fecha_mod',
				fieldLabel: 'Fecha Modif.',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y', 
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
			},
				type:'DateField',
				filters:{pfiltro:'prpa.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'id_usuario_ai',
				fieldLabel: '',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'prpa.id_usuario_ai',type:'numeric'},
				id_grupo:1,
				grid:false,
				form:false
		},
		{
			config:{
				name: 'usuario_ai',
				fieldLabel: 'Funcionaro AI',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:300
			},
				type:'TextField',
				filters:{pfiltro:'prpa.usuario_ai',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		}
	],
	tam_pag:50,	
	title:'Presupuesto',
	ActList:'../../sis_presupuestos/control/PresupPartida/listarPresupPartidaEstado',
	id_store:'id_presup_partida',
	fields:[
		{name:'id_presup_partida', type: 'numeric'},
		{name:'tipo', type: 'string'},
		{name:'id_moneda', type: 'numeric'},
		{name:'id_partida', type: 'numeric'},
		{name:'id_centro_costo', type: 'numeric'},
		{name:'fecha_hora', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'estado_reg', type: 'string'},
		{name:'id_presupuesto', type: 'numeric'},
		{name:'importe', type: 'numeric'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'usuario_ai', type: 'string'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},'desc_partida','desc_gestion','importe_aprobado','tipo_reg',
		'formulado','comprometido','ejecutado','pagado','ajustado','porc_ejecucion'
		
	],
	
	onReloadPage:function(m){
		this.maestro=m;
		//console.log('hunesadad',this.maestro);
        this.store.baseParams={
		id_presupuesto:this.maestro.id_presupuesto,
		codigo_categoria: this.maestro.codigo_categoria,
		codigo_cc:this.maestro.codigo_cc,
		codigo_uo: this.maestro.codigo_uo,
		desc_tcc:this.maestro.desc_tcc,
		desc_tipo_presupuesto: this.maestro.desc_tipo_presupuesto,
		descripcion:this.maestro.descripcion,
		estado: this.maestro.estado,
		estado_pres:this.maestro.estado_pres,
		estado_reg: this.maestro.estado_reg,
		estado_reg_uo:this.maestro.estado_reg_uo,
		fecha_fin_pres: this.maestro.fecha_fin_pres,
		fecha_inicio_pres:this.maestro.fecha_inicio_pres,
		fecha_mod: this.maestro.fecha_mod,
		fecha_reg:this.maestro.fecha_reg,
		id_categoria_prog: this.maestro.id_categoria_prog,
		id_centro_costo:this.maestro.id_centro_costo,
		id_estado_wf: this.maestro.id_estado_wf,
		id_gestion: this.maestro.id_gestion,
		id_proceso_wf: this.maestro.id_proceso_wf,
		id_tipo_cc: this.maestro.id_tipo_cc,
		id_uo:this.maestro.id_uo,
		id_usuario_mod: this.maestro.id_usuario_mod,
		id_usuario_reg: this.maestro.id_usuario_reg,
		momento_pres:this.maestro.momento_pres,
		mov_pres: this.maestro.mov_pres,
		movimiento_tipo_pres:this.maestro.movimiento_tipo_pres,
		nombre_uo: this.maestro.nombre_uo,
		nro_tramite: this.maestro.nro_tramite,
		obs_wf:this.maestro.obs_wf,
		sw_consolidado: this.maestro.sw_consolidado,
		tipo_pres: this.maestro.tipo_pres,
		usr_mod:this.maestro.usr_mod,
		usr_reg: this.maestro.usr_reg
		};
        this.load({ params: { start: 0, limit: 50 }});
        
    },

	reporteEstadoPdf: function () {
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url: '../../sis_presupuestos/control/PresupPartida/listarPresupPartidaEstadoPdf',
                params: {
					"start":"0","limit":"50","sort":"desc_partida","dir":"ASC",
					id_presup_partida: this.store.baseParams.id_presup_partida,
                    tipo: this.store.baseParams.tipo,
                    id_moneda: this.store.baseParams.id_moneda,
					id_partida: this.store.baseParams.id_partida,
                    id_centro_costo: this.store.baseParams.id_centro_costo,
                    fecha_hora: this.store.baseParams.fecha_hora,
                    estado_reg: this.store.baseParams.estado_reg,
					id_presupuesto: this.store.baseParams.id_presupuesto,
                    importe: this.store.baseParams.importe,
					usuario_ai: this.store.baseParams.usuario_ai,
					fecha_reg: this.store.baseParams.fecha_reg,
					id_usuario_reg: this.store.baseParams.id_usuario_reg,
					desc_partida: this.store.baseParams.desc_partida,
					desc_gestion: this.store.baseParams.desc_gestion,
                    importe_aprobado: this.store.baseParams.importe_aprobado,
                    formulado: this.store.baseParams.formulado,
                    comprometido: this.store.baseParams.comprometido,
					ejecutado: this.store.baseParams.ejecutado,
					pagado: this.store.baseParams.pagado,
					ajustado: this.store.baseParams.ajustado,
                    porc_ejecucion: this.store.baseParams.porc_ejecucion,
					codigo_categoria: this.store.baseParams.codigo_categoria,
					codigo_cc: this.store.baseParams.codigo_cc,
					codigo_uo: this.store.baseParams.codigo_uo,
					desc_tcc: this.store.baseParams.desc_tcc,
					desc_tipo_presupuesto: this.store.baseParams.desc_tipo_presupuesto,
					descripcion: this.store.baseParams.descripcion,
					estado: this.store.baseParams.estado,
					estado_reg_uo: this.store.baseParams.estado_reg_uo,
					fecha_fin_pres: this.store.baseParams.fecha_fin_pres,
					fecha_inicio_pres:this.store.baseParams.fecha_inicio_pres,
					fecha_mod: this.store.baseParams.fecha_mod,
					id_categoria_prog: this.store.baseParams.id_categoria_prog,
					id_estado_wf: this.store.baseParams.id_estado_wf,
					id_gestion: this.store.baseParams.id_gestion,
					id_proceso_wf: this.store.baseParams.id_proceso_wf,
					id_tipo_cc: this.store.baseParams.id_tipo_cc,
					id_uo:this.store.baseParams.id_uo,
					id_usuario_mod: this.store.baseParams.id_usuario_mod,
					momento_pres:this.store.baseParams.momento_pres,
					mov_pres: this.store.baseParams.mov_pres,
					movimiento_tipo_pres:this.store.baseParams.movimiento_tipo_pres,
					nombre_uo: this.store.baseParams.nombre_uo,
					nro_tramite: this.store.baseParams.nro_tramite,
					obs_wf:this.store.baseParams.obs_wf,
					sw_consolidado: this.store.baseParams.sw_consolidado,
					tipo_pres: this.store.baseParams.tipo_pres,
					usr_mod:this.store.baseParams.usr_mod,
					usr_reg: this.store.baseParams.usr_reg
					
	                },
                success: this.successExport,
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });
        },
    
	sortInfo:{
		field: 'id_presup_partida',
		direction: 'ASC'
	},
	
	

	bdel: false,
	bedit: false,
	bsave: false,
	bnew: false
})
</script>
		
		