<?php
/**
*@package pXP
*@file DetallePresParDif.php
*@author  bvp
*@date 
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.DetallePresParDif=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
        this.initButtons = ['-', '<span style="font-size:14px;color:#2B4364;font-weight:bold;">Tipo Movimiento: </span>',this.cmbTipoMovi,
                            '-', '<span style="font-size:14px;color:#2B4364;font-weight:bold;">Diferencias: </span>',this.cmbDiferencia,
                            '-', '<span style="font-size:14px;color:#2B4364;font-weight:bold;">Diferencias: </span>',this.cmbMoneda,
                            '-'];
		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.DetallePresParDif.superclass.constructor.call(this,config);
        this.cmbTipoMovi.on('select', function () {
                this.validarFiltros('movi') == true && this.capturarMovimiento();                
        }, this);
        this.cmbDiferencia.on('select', function (){
            this.validarFiltros('dif') == true && this.capturarMovimiento();                
        }, this),
        this.cmbMoneda.on('select', function() {
            this.validarFiltros('mone') == true && this.capturarMovimiento();
        }, this);
		this.grid.getTopToolbar().disable();
		this.grid.getBottomToolbar().disable();
		this.init();
		//this.load({params:{start:0, limit:this.tam_pag}})
    },
    // filtro tipo Movimiento
    cmbTipoMovi: new Ext.form.ComboBox({
            fieldLabel: 'movimiento',                
            allowBlank: true,
            emptyText: 'Movimiento...',                
            typeAhead: true,            
            triggerAction: 'all',            
            selectOnFocus: false,
            mode: 'local',
            store: new Ext.data.ArrayStore({
                fields: ['ID', 'valor'],              
                data: [
                    ['todos', 'Todos'],
                    ['comprometido', 'Comprometido'],
                    ['pagado', 'Pagado'],
                    ['ejecutado', 'Ejecutado'],
                    ['formulado', 'Formulado']
                ]
            }),
            valueField: 'ID',
            displayField: 'valor',
            width: 140,
            style:'font-weight:bold',
            tpl: '<tpl for="."><div class="x-combo-list-item"><b>{valor}</b></p></div></tpl>'            
        }),         
    cmbDiferencia: new Ext.form.ComboBox({
            fieldLabel: 'diferencia',                
            allowBlank: true,
            emptyText: 'Diferencias...',                
            typeAhead: true,            
            triggerAction: 'all',            
            selectOnFocus: false,
            mode: 'local',
            store: new Ext.data.ArrayStore({
                fields: ['ID', 'valor'],              
                data: [['si', 'Si'],
                        ['no', 'No']]
            }),
            valueField: 'ID',
            displayField: 'valor',
            width: 60,
            style:'font-weight:bold',            
            tpl: '<tpl for="."><div class="x-combo-list-item"><b>{valor}</b></p></div></tpl>'                      
    }),    
    cmbMoneda: new Ext.form.ComboBox({
            fieldLabel: 'moneda',                
            allowBlank: true,
            emptyText: 'Moneda...',                
            typeAhead: true,            
            triggerAction: 'all',            
            selectOnFocus: false,
            mode: 'local',
            store: new Ext.data.ArrayStore({
                fields: ['ID', 'valor'],              
                data: [['todos', 'Todos'],
                    ['1', 'Bs'],
                    ['2', '$us']]
            }),
            valueField: 'ID',
            displayField: 'valor',
            width: 70,
            style:'font-weight:bold',                               
            tpl: '<tpl for="."><div class="x-combo-list-item"><b>{valor}</b></p></div></tpl>'                   
    }),       
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
			config:{
				name: 'nro_tramite',
				fieldLabel: 'Nro Tramite',
				allowBlank: true,
				anchor: '80%',
				gwidth: 150,
				maxLength:-5
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
				name: 'tipo_movimiento',
				fieldLabel: 'Tipo Movimiento',
				allowBlank: false,

				renderer:function (value, p, record){
					var dato='';
                    if(record.data.tipo_reg != 'summary'){
					dato = (value=='1')?'Comprometido':dato;
					dato = (dato==''&&value=='2')?'Revertido':dato;
					dato = (dato==''&&value=='3')?'Devengado':dato;
					dato = (dato==''&&value=='4')?'Pagado':dato;
					dato = (dato==''&&value=='5')?'Traspaso':dato;
					dato = (dato==''&&value=='6')?'Reformulacion':dato;
					dato = (dato==''&&value=='7')?'Incremento':dato;
					return String.format('{0}', value);
                    }else{
                        return `<hr><center><b><p style=" color:green; font-size:15px;">Total: </p></b></center>`;
                    }
				},
				store:new Ext.data.ArrayStore({
					fields :['variable','valor'],
					data :  []}),

				valueField: 'variable',
				displayField: 'valor',
				forceSelection: true,
				triggerAction: 'all',
				lazyRender: true,
				resizable:true,
				gwidth: 100,
				listWidth:'500',
				mode: 'local',
				wisth: 380
			},
			type:'ComboBox',
			filters:{pfiltro:'pareje.tipo_movimiento',type:'string'},
			id_grupo:0,
			grid:true,
			form:false
		},
		{
			config:{
				name: 'moneda',
				fieldLabel: 'Moneda',
				allowBlank: false,
				allowNegative: false,
				anchor: '80%',
				gwidth: 50,				
                renderer: (value, p, record) => {
                        var color = 'green';
                        (value == '$us')?color='blue':color;
                        return  String.format(`<div style="font-size:12px; color:${color}; float:right"><b><font>{0}</font><b></div>`,value);
                }                               	                
			},
			type:'NumberField',
			filters:{pfiltro:'pareje.monto',type:'numeric'},
			id_grupo:1,
			grid:true,
			form:false
		},        
		{
			config:{
				name: 'monto',
				fieldLabel: 'Monto',
				allowBlank: false,
				allowNegative: false,
				anchor: '80%',
				gwidth: 110,				
                renderer: (value, p, record) => {                                            
                    if(record.data.tipo_reg != 'summary'){
                        return  String.format('<div style="font-size:12px; float:right"><b><font>{0}</font><b></div>', Ext.util.Format.number(value,'0.000,00/i'));
                    }else{
                        return  String.format('<hr><div style="font-size:15px; float:right; color:black;"><b><font>{0}</font><b></div>', Ext.util.Format.number(record.data.total_monto,'0.000,00/i'));
                    }
                }                               	                
			},
			type:'NumberField',
			filters:{pfiltro:'pareje.monto',type:'numeric'},
			id_grupo:1,
			grid:true,
			form:false
		},        
		{
			config:{
				name: 'monto_mb',
				fieldLabel: 'Monto Moneda Base',
				allowBlank: true,
				anchor: '80%',
				gwidth: 140,
                renderer: (value, p, record) => {                                            
                    if(record.data.tipo_reg != 'summary'){
                        return  String.format('<div style="font-size:12px; float:right"><b><font>{0}</font><b></div>', Ext.util.Format.number(value,'0.000,00/i'));
                    }else{
                        return  String.format('<hr><div style="font-size:15px; float:right; color:black;"><b><font>{0}</font><b></div>', Ext.util.Format.number(record.data.total_mb,'0.000,00/i'));
                    }
                }              	                
			},
			type:'NumberField',
			filters:{pfiltro:'pareje.monto_mb',type:'numeric'},
			id_grupo:1,
			grid:true,
			form:true
		},  
		{
			config:{
				name: 'tipo_cambio',
				fieldLabel: 'Tipo de Cambio',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
                renderer: (value, p, record) => {                                            
                    return  String.format('<div style="font-size:12px; float:right"><b><font>{0}</font><b></div>', Ext.util.Format.number(value,'0.000,00/i'));
                }                                 				
			},
			type:'NumberField',
			filters:{pfiltro:'pareje.tipo_cambio',type:'numeric'},
			id_grupo:1,
			grid:true,
			form:true
		},
		{
			config:{
				name: 'tipo_cambio2',
				fieldLabel: 'Tipo de Cambio2',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
                renderer: (value, p, record) => {                                            
                    return  String.format('<div style="font-size:12px; float:right"><b><font>{0}</font><b></div>', Ext.util.Format.number(value,'0.000,00/i'));
                }                 				
			},
			type:'NumberField',
			filters:{pfiltro:'pareje.tipo_cambio',type:'numeric'},
			id_grupo:1,
			grid:true,
			form:true
		},                       
		{
			config:{
				name: 'cambio_moneda',
				fieldLabel: 'Cambio Real',
				allowBlank: false,
				anchor: '40%',
				gwidth: 130,
                renderer: (value, p, record) => {                                            
                    if(record.data.tipo_reg != 'summary'){
                        return  String.format('<div style="font-size:12px; float:right"><b><font>{0}</font><b></div>', Ext.util.Format.number(value,'0.000,00/i'));
                    }else{
                        return  String.format('<hr><div style="font-size:15px; float:right; color:black;"><b><font>{0}</font><b></div>', Ext.util.Format.number(record.data.total_camo,'0.000,00/i'));
                    }                    
                }                	
			},
				type:'NumberField',				
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'diferencia',
				fieldLabel: 'Diferencia',
				allowBlank: false,
				anchor: '40%',
				gwidth: 140,
                renderer: (value, p, record) => {
                    var color = 'black';
                    (value > 0.00 || value < -0.00)?color='red':color;                                            
                    if(record.data.tipo_reg != 'summary'){
                        return  String.format(`<div style="font-size:12px; color:${color}; float:right"><b><font>{0}</font><b></div>`, Ext.util.Format.number(value,'0.000,00/i')); 
                    }else{
                        return  String.format('<hr><div style="font-size:15px; float:right; color:black;"><b><font>{0}</font><b></div>', Ext.util.Format.number(record.data.total_dif,'0.000,00/i'));
                    }                                        
                }               	
			},
				type:'NumberField',				
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'codigo_cc',				
				fieldLabel: 'Desc. Presupuesto',
				allowBlank: true,
				anchor: '80%',
				gwidth: 200
			},
			type:'TextField',
			//filters:{pfiltro:'pre.descripcion',type:'string'},
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
			//filters:{pfiltro:'pareje.nro_tramite',type:'string'},
			filters:{pfiltro:'cat.codigo_categoria',type:'string'},
			bottom_filter: true,
			id_grupo:1,
			grid:true,
			form:true
		},
		{
			config:{
				name: 'codigo',
				fieldLabel: 'Codigo Partida',
				allowBlank: true,
				anchor: '80%',
				gwidth: 60,
				maxLength:-5
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
				gwidth: 200,
				maxLength:-5
			},
			type:'TextField',
			filters:{pfiltro:'par.nombre_partida',type:'string'},
			bottom_filter: true,
			id_grupo:1,
			grid:true,
			form:true
		},                                         
		{
			config:{
				name: 'fecha_reg',
				fieldLabel: 'Fecha Creación',
				allowBlank: true,
				anchor: '80%',
				gwidth: 120,
				format: 'd/m/Y',
				renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
			},
			type:'DateField',
			filters:{pfiltro:'pareje.fecha_reg',type:'date'},
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
			bottom_filter: true,
			id_grupo:1,
			grid:true,
			form:false
		}
	],
	tam_pag:50,	
	title:'Partida Ejecucion',	
	ActList:'../../sis_presupuestos/control/PartidaEjecucion/listarDetallePresParDif',
	id_store:'id_partida_ejecucion',
	fields: [
		{name:'id_partida_ejecucion', type: 'numeric'},
		{name:'id_int_comprobante', type: 'numeric'},				
		{name:'nro_tramite', type: 'string'},
		{name:'tipo_movimiento', type: 'string'},
        {name:'monto', type: 'numeric'},
		{name:'monto_mb', type: 'numeric'},
		{name:'tipo_cambio', type: 'numeric'},	
        {name:'tipo_cambio2', type: 'numeric'},			
        {name:'cambio_moneda', type: 'numeric'},
        {name:'diferencia', type:'numeric'},
        {name:'total_monto', type:'numeric'},
        {name:'total_mb', type:'numeric'},
        {name:'total_camo', type:'numeric'},
        {name:'total_dif', type:'numeric'},
        {name:'tipo_reg', type:'string'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
        {name:'moneda', type: 'string'},
        {name:'desc_pres', type: 'string'},
        {name:'codigo_cc', type: 'string'},
        {name:'codigo_categoria', type: 'string'},
        {name:'codigo', type: 'string'},
        {name:'nombre_partida', type: 'string'}          				
	],
	arrayDefaultColumHidden:['id_partida_ejecucion'],

	sortInfo:{
		field: 'fecha_reg',
		direction: 'DESC'
	},

	loadValoresIniciales:function(){
		Phx.vista.DetallePresParDif.superclass.loadValoresIniciales.call(this);		
	},
	onReloadPage:function(param){
        console.log('param',param);
        console.log('this',this);
        this.cmbTipoMovi.setValue('');
        this.cmbDiferencia.setValue('');
        this.cmbMoneda.setValue('');
		//Se obtiene la gestión en función de la fecha del comprobante para filtrar partidas, cuentas, etc.
		var me = this;
		this.initFiltro(param);
	},

	initFiltro: function(param){
		this.store.baseParams=param;
		this.load( { params: { start:0, limit: this.tam_pag } });
	},
    validarFiltros: function (tipo) {                
        var value = false;
        if (tipo == 'movi'){
            (this.cmbTipoMovi.isValid())?value=true:value;

        }else if(tipo == 'dif'){
            (this.cmbDiferencia.isValid())?value=true:value;
        }else if(tipo == 'mone'){
            (this.cmbMoneda.isValid())?value=true:value;
        }
        return value;
    },

    capturarMovimiento: function (combo, record, index) {
        this.getParametrosFiltro();
        this.load({params: {start: 0, limit:this.tam_pag}});
    },

    getParametrosFiltro: function () {
        this.store.baseParams.tipo_movimiento = this.cmbTipoMovi.getValue();
        this.store.baseParams.diferencia = this.cmbDiferencia.getValue();
        this.store.baseParams.moneda = this.cmbMoneda.getValue();
    },  

	bdel:false,
	bsave:false,
	bedit:false,
	bnew:false,
    btest: false,
	}
)
</script>
		
		