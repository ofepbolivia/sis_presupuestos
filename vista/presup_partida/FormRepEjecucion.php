<?php
/**
 *@package pXP
 *@file    GenerarLibroBancos.php
 *@author  Gonzalo Sarmiento Sejas
 *@date    01-12-2014
 *@description Archivo con la interfaz para generaci�n de reporte
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.FormRepEjecucion = Ext.extend(Phx.frmInterfaz, {
		
		Atributos : [
		
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'concepto'
			},
			type:'Field',
			form:true 
		},
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'subtitulo'
			},
			type:'Field',
			form:true 
		},		
		{
            config:{
                name:'id_gestion',
                fieldLabel:'Gestión',
                allowBlank:true,
                emptyText:'Gestión...',
                store: new Ext.data.JsonStore({
                         url: '../../sis_parametros/control/Gestion/listarGestion',
                         id: 'id_gestion',
                         root: 'datos',
                         sortInfo:{
                            field: 'gestion',
                            direction: 'DESC'
                    },
                    totalProperty: 'total',
                    fields: ['id_gestion','gestion','moneda','codigo_moneda'],
                    // turn on remote sorting
                    remoteSort: true,
                    baseParams:{par_filtro:'gestion'}
                    }),
                valueField: 'id_gestion',
                displayField: 'gestion',
                //tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{nro_cuenta}</b></p><p>{denominacion}</p></div></tpl>',
                hiddenName: 'id_gestion',
                forceSelection:true,
                typeAhead: false,
                triggerAction: 'all',
                lazyRender:true,
                mode:'remote',
                pageSize:10,
                queryDelay:1000,
                listWidth:600,
                resizable:true,
                anchor:'100%'
                
            },
            type:'ComboBox',
            id_grupo:0,
            filters:{   
                        pfiltro:'gestion',
                        type:'string'
                    },
            grid:true,
            form:true
        },
        
        {
            config:{
            	name: 'tipo_pres',
				fieldLabel: 'Tipo',
				grupo: [0, 1, 2],
				allowBlank: false,
				emptyText:'Filtro...',
				store : new Ext.data.JsonStore({
							url:'../../sis_presupuestos/control/TipoPresupuesto/listarTipoPresupuesto',
							id : 'codigo',
							root: 'datos',
							sortInfo:{
									field: 'codigo',
									direction: 'ASC'
							},
							totalProperty: 'total',
							fields: ['codigo', 'nombre', 'movimiento'],
							remoteSort: true,
							baseParams: { par_filtro:'nombre' }
				}),
				valueField : 'codigo',
			    displayField : 'nombre',
			    hiddenName : 'codigo',
				enableMultiSelect : true,
				triggerAction : 'all',
				lazyRender : true,
				mode : 'remote',
				pageSize : 20,
				width : 150,
				anchor : '80%',
				listWidth : '280',
				resizable : true,
				minChars : 2
			},
            type:'AwesomeCombo',
            id_grupo:0,
            filters:{   
                        pfiltro:'gestion',
                        type:'string'
                    },
            form:true
        },
		
		
		{
			config:{
				name:'tipo_reporte',
				fieldLabel:'Filtrar por',
				typeAhead: true,
				allowBlank:false,
	    		triggerAction: 'all',
	    		emptyText:'Tipo...',
	    		selectOnFocus:false,
				mode:'local',
				store:new Ext.data.ArrayStore({
	        	fields: ['ID', 'valor'],
	        	data :	[
		        	        ['programa', 'Programa'],
                            ['proyecto', 'Proyecto'],
                            ['actividad', 'Actividad'],
                            ['orga_financ', 'Organismo Financiador'],
                            ['fuente_financ', 'Fuente Financiamiento'],
                            ['unidad_ejecutora', 'Unidad Ejecutora'],
		        	        ['categoria', 'Categoría Programática'],	
							['presupuesto', 'Presupuesto'],
                            ['centro_costo', 'Centro de Costo'],
						]	        				
	    		}),
				valueField:'ID',
				displayField:'valor',
				width:250,			
				
			},
			type:'ComboBox',
			id_grupo:1,
			form:true
		},
		{
			config: {
				name: 'id_cp_proyecto',
				fieldLabel: 'Proyecto',
				allowBlank: false,
				emptyText: 'Elija una opción...',
				store: new Ext.data.JsonStore({
					url: '../../sis_presupuestos/control/CpProyecto/listarCpProyecto',
					id: 'id_cp_proyecto',
					root: 'datos',
					sortInfo: {
						field: 'codigo',
						direction: 'ASC'
					},
					totalProperty: 'total',
					fields: ['id_cp_proyecto', 'descripcion', 'codigo'],
					remoteSort: true,
					baseParams: {par_filtro: 'codigo#descripcion'}
				}),
				valueField: 'id_cp_proyecto',
				displayField: 'descripcion',
				gdisplayField: 'desc_proyecto',
				hiddenName: 'id_cp_proyecto',
				forceSelection: true,
				typeAhead: false,
				triggerAction: 'all',
				lazyRender: true,
				mode: 'remote',
				pageSize: 15,
				queryDelay: 1000,
				anchor: '100%',
				gwidth: 150,
                minChars: 2,
                resizable:true,
				tpl:'<tpl for="."><div class="x-combo-list-item"><p>{codigo}-{descripcion}</p> </div></tpl>',
				renderer : function(value, p, record) {
					return String.format('({1})  {0}', record.data['desc_proyecto'],record.data['codigo_proyecto']);
				}
			},
			type: 'ComboBox',
			id_grupo: 0,
			filters: {pfiltro: 'desc_proyecto',type: 'string'},
			grid: true,
			form: true
		},
		{
			config: {
				name: 'id_cp_actividad',
				fieldLabel: 'Actividad',
				allowBlank: false,
				emptyText: 'Elija una opción...',
				store: new Ext.data.JsonStore({
					url: '../../sis_presupuestos/control/CpActividad/listarCpActividad',
					id: 'id_cp_actividad',
					root: 'datos',
					sortInfo: {
						field: 'codigo',
						direction: 'ASC'
					},
					totalProperty: 'total',
					fields: ['id_cp_actividad', 'descripcion', 'codigo'],
					remoteSort: true,
					baseParams: {par_filtro: 'codigo#descripcion'}
				}),
				valueField: 'id_cp_actividad',
				displayField: 'descripcion',
				gdisplayField: 'desc_actividad',
				hiddenName: 'id_cp_actividad',
				forceSelection: true,
				typeAhead: false,
				triggerAction: 'all',
				lazyRender: true,
				mode: 'remote',
				pageSize: 15,
				queryDelay: 1000,
				anchor: '100%',
				gwidth: 150,
                minChars: 2,
                resizable:true,
				tpl:'<tpl for="."><div class="x-combo-list-item"><p>{codigo}-{descripcion}</p> </div></tpl>',
				renderer : function(value, p, record) {
					return String.format('({1})  {0}', record.data['desc_actividad'],record.data['codigo_actividad']);
				}
			},
			type: 'ComboBox',
			id_grupo: 0,
			filters: {pfiltro: 'desc_actividad',type: 'string'},
			grid: true,
			form: true
		},
		{
			config: {
				name: 'id_cp_organismo_fin',
				fieldLabel: 'Organismo Financiador',
				allowBlank: false,
				emptyText: 'Elija una opción...',
				store: new Ext.data.JsonStore({
					url: '../../sis_presupuestos/control/CpOrganismoFin/listarCpOrganismoFin',
					id: 'id_cp_organismo_fin',
					root: 'datos',
					sortInfo: {
						field: 'codigo',
						direction: 'ASC'
					},
					totalProperty: 'total',
					fields: ['id_cp_organismo_fin', 'descripcion', 'codigo'],
					remoteSort: true,
					baseParams: {par_filtro: 'codigo#descripcion'}
				}),
				valueField: 'id_cp_organismo_fin',
				displayField: 'descripcion',
				gdisplayField: 'desc_origen_fin',
				hiddenName: 'id_cp_organismo_fin',
				forceSelection: true,
				typeAhead: false,
				triggerAction: 'all',
				lazyRender: true,
				mode: 'remote',
				pageSize: 15,
				queryDelay: 1000,
				anchor: '100%',
				gwidth: 150,
                minChars: 2,
                resizable:true,
				tpl:'<tpl for="."><div class="x-combo-list-item"><p>{codigo}-{descripcion}</p> </div></tpl>',
				renderer : function(value, p, record) {
					return String.format('({1})  {0}', record.data['desc_origen_fin'],record.data['codigo_origen_fin']);
				}
			},
			type: 'ComboBox',
			id_grupo: 0,
			filters: {pfiltro: 'desc_organismo_fin',type: 'string'},
			grid: true,
			form: true
		},
		
		{
			config: {
				name: 'id_cp_fuente_fin',
				fieldLabel: 'Fuente Financiador',
				allowBlank: false,
				emptyText: 'Elija una opción...',
				store: new Ext.data.JsonStore({
					url: '../../sis_presupuestos/control/CpFuenteFin/listarCpFuenteFin',
					id: 'id_cp_fuente_fin',
					root: 'datos',
					sortInfo: {
						field: 'codigo',
						direction: 'ASC'
					},
					totalProperty: 'total',
					fields: ['id_cp_fuente_fin', 'descripcion', 'codigo'],
					remoteSort: true,
					baseParams: {par_filtro: 'codigo#descripcion'}
				}),
				valueField: 'id_cp_fuente_fin',
				displayField: 'descripcion',
				gdisplayField: 'desc_fuente_fin',
				hiddenName: 'id_cp_fuente_fin',
				forceSelection: true,
				typeAhead: false,
				triggerAction: 'all',
				lazyRender: true,
				mode: 'remote',
				pageSize: 15,
				queryDelay: 1000,
				anchor: '100%',
				gwidth: 150,
                minChars: 2,
                resizable:true,
				tpl:'<tpl for="."><div class="x-combo-list-item"><p>{codigo}-{descripcion}</p> </div></tpl>',
				renderer : function(value, p, record) {
					return String.format('({1})  {0}', record.data['desc_fuente_fin'],record.data['codigo_fuente_fin']);
				}
			},
			type: 'ComboBox',
			id_grupo: 0,
			filters: {pfiltro: 'desc_fuente_fin',type: 'string'},
			grid: true,
			form: true
		},

        {
            config: {
                name: 'id_unidad_ejecutora',
                fieldLabel: 'Unidad Ejecutora',
                allowBlank: false,
                emptyText: 'Elija una opción...',
                store: new Ext.data.JsonStore({
                    url: '../../sis_presupuestos/control/UnidadEjecutora/listarUnidadEjecutora',
                    id: 'id_unidad_ejecutora',
                    root: 'datos',
                    sortInfo: {
                        field: 'codigo',
                        direction: 'ASC'
                    },
                    totalProperty: 'total',
                    fields: ['id_unidad_ejecutora', 'nombre', 'codigo'],
                    remoteSort: true,
                    baseParams: {par_filtro: 'codigo#nombre'}
                }),
                valueField: 'id_unidad_ejecutora',
                displayField: 'nombre',
                gdisplayField: 'desc_unidad_ejecutora',
                hiddenName: 'id_unidad_ejecutora',
                forceSelection: true,
                typeAhead: false,
                triggerAction: 'all',
                lazyRender: true,
                mode: 'remote',
                pageSize: 15,
                queryDelay: 1000,
                anchor: '100%',
                gwidth: 150,
                minChars: 2,
                resizable:true,
                tpl:'<tpl for="."><div class="x-combo-list-item"><p>{codigo}-{nombre}</p> </div></tpl>',
                renderer : function(value, p, record) {
                    return String.format('({1})  {0}',record.data['desc_unidad_ejecutora'], record.data['codigo_unidad_ejecutora']);
                }
            },
            type: 'ComboBox',
            id_grupo: 0,
            filters: {pfiltro: 'desc_unidad_ejecutora',type: 'string'},
            grid: true,
            form: true
        },                               
		{
			config:{
				name: 'id_categoria_programatica',
				fieldLabel: 'Categoria Programatica',
				qtip: 'la categoria programatica permite la integración de reportes para sigma',
				allowBlank: false,
				emptyText : '...',
				store : new Ext.data.JsonStore({
							url:'../../sis_presupuestos/control/CategoriaProgramatica/listarCategoriaProgramatica',
							id : 'id_categoria_programatica',
							root: 'datos',
							sortInfo:{field: 'codigo_categoria',direction: 'ASC'},
							totalProperty: 'total',
							fields: ['codigo_categoria','id_categoria_programatica','descripcion'],
							remoteSort: true,
							baseParams:{par_filtro:'descripcion#codigo_categoria',_adicionar:'si'}
				}),
			   valueField: 'id_categoria_programatica',
			   displayField: 'codigo_categoria',
			   gdisplayField: 'codigo_categoria',
			   hiddenName: 'id_categoria_programatica',
			   forceSelection:true,
			   typeAhead: true,
			   triggerAction: 'all',
			   lazyRender:true,
			   mode:'remote',
			   pageSize:10,
			   queryDelay:1000,
			   width: 150,
			   listWidth: 280,
               minChars:2,
               resizable:true,
			   tpl:'<tpl for="."><div class="x-combo-list-item"><p style="color:green"><b>{codigo_categoria}</b></p><p>{descripcion}</p> </div></tpl>'
			},
			type:'ComboBox',
			id_grupo:1,
			form:true
		},
		{
            config:{
            	sysorigen: 'sis_presupuestos',
                name: 'id_presupuesto',
                fieldLabel: 'Presupuesto',
                allowBlank: false,
                tinit: false,
                baseParams: {_adicionar:'si'},
                origen: 'PRESUPUESTO',
                width: 350,
   				listWidth: 350
            },
            type: 'ComboRec',
            id_grupo: 0,
            form: true
        },
		
		{
			
			config: {
				name: 'id_cp_programa',
				fieldLabel: 'Programa',
				allowBlank: false,
				emptyText: 'Elija una opción...',
				store: new Ext.data.JsonStore({
					url: '../../sis_presupuestos/control/CpPrograma/listarCpPrograma',
					id: 'id_cp_programa',
					root: 'datos',
					sortInfo: {field: 'codigo',direction: 'ASC'},
					totalProperty: 'total',
					fields: ['id_cp_programa', 'descripcion', 'codigo'],
					remoteSort: true,
					baseParams: {par_filtro: 'codigo#descripcion',_adicionar:'si'}
				}),
				valueField: 'id_cp_programa',
				displayField: 'descripcion',
				gdisplayField: 'desc_programa',
				hiddenName: 'id_cp_programa',
				forceSelection: true,
				typeAhead: false,
				triggerAction: 'all',
				lazyRender: true,
				mode: 'remote',
				pageSize: 15,
				queryDelay: 1000,
				anchor: '100%',
                minChars: 2,
                resizable:true,
				tpl:'<tpl for="."><div class="x-combo-list-item"><p>{codigo}-{descripcion}</p> </div></tpl>'
			},
			type: 'ComboBox',
			form: true
		},
	   	
		{
			config:{
				name:'formato_reporte',
				fieldLabel:'Formato del Reporte',
				typeAhead: true,
				allowBlank:false,
	    		triggerAction: 'all',
	    		emptyText:'Formato...',
	    		selectOnFocus:true,
				mode:'local',
				store:new Ext.data.ArrayStore({
	        	fields: ['ID', 'valor'],
	        	data :[ ['pdf','PDF'],	
						['csv','CSV']]	        				
	    		}),
				valueField:'ID',
				displayField:'valor',
				width:250,			
				
			},
			type:'ComboBox',
			id_grupo:1,
			form:true
		},
		{
				config:{
					name: 'fecha_ini',
					fieldLabel: 'Desde',
					allowBlank: true,
					format: 'd/m/Y',
					width: 150,
                    //data : ['fecha_ini']
				},
				type: 'DateField',
				id_grupo: 0,
				form: true
		  },
		  {
				config:{
					name: 'fecha_fin',
					fieldLabel: 'Hasta',
					allowBlank: true,
					format: 'd/m/Y',
					width: 150
				},
				type: 'DateField',
				id_grupo: 0,
				form: true
		  },
		
		
		{
			config:{
				name:'nivel',
				fieldLabel:'Nivel',
				typeAhead: true,
				allowBlank:false,
	    		triggerAction: 'all',
	    		emptyText:'Tipo...',
	    		selectOnFocus:true,
				mode:'local',
				store:new Ext.data.ArrayStore({
	        	fields: ['ID', 'valor'],
	        	data :	[
		        	        ['1',' <= 1'],
		        	        ['2',' <= 2'],	
							['3',' <= 3'],
							['4',' Todo'],
							['5','Solo movimiento']
						]	        				
	    		}),
				valueField:'ID',
				displayField:'valor',
				width:250,			
				
			},
			type:'ComboBox',
			id_grupo:1,
			form:true
		}],
		
		
		title : 'Reporte Libro Compras Ventas IVA',		
		ActSave : '../../sis_presupuestos/control/MemoriaCalculo/reporteMemoriaCalculo',
		
		topBar : true,
		botones : false,

		labelSubmit : 'Generar',
		tooltipSubmit : '<b>Reporte Proyecto Presupeustario</b>',
		
		constructor : function(config) {
			Phx.vista.FormRepEjecucion.superclass.constructor.call(this, config);
			this.init();
			
			this.ocultarComponente(this.Cmp.id_categoria_programatica);
            this.ocultarComponente(this.Cmp.id_cp_proyecto);            
			this.ocultarComponente(this.Cmp.id_presupuesto);
			this.ocultarComponente(this.Cmp.id_cp_programa);
            this.ocultarComponente(this.Cmp.id_cp_actividad);
            this.ocultarComponente(this.Cmp.id_cp_organismo_fin);
            this.ocultarComponente(this.Cmp.id_cp_fuente_fin);
            this.ocultarComponente(this.Cmp.id_unidad_ejecutora);
						
			this.iniciarEventos();
		},

		clean: (id, c) => {            
            id.reset();
            id.store.baseParams.id_gestion =c.value;				
            id.modificado=true;            
        },
		iniciarEventos:function(){        
			
			this.Cmp.id_gestion.on('select',function(c,r,n){                
				this.clean(this.Cmp.id_categoria_programatica, c);
                this.clean(this.Cmp.id_presupuesto, c);
                this.clean(this.Cmp.id_cp_programa, c);
                this.clean(this.Cmp.id_cp_proyecto, c);
                this.clean(this.Cmp.id_cp_actividad, c);
                this.clean(this.Cmp.id_cp_organismo_fin, c);
                this.clean(this.Cmp.id_cp_fuente_fin, c);
                this.clean(this.Cmp.id_unidad_ejecutora, c);
                    										
                console.log('record',r)
                
                this.Cmp.fecha_ini.setValue('01/01/'+r.data.gestion);
                this.Cmp.fecha_fin.setValue('31/12/'+r.data.gestion);
													
			},this);
			
			
			this.Cmp.tipo_reporte.on('select',function(combo, record, index){
				console.log(record, index)
				
				this.Cmp.id_categoria_programatica.reset();
				this.Cmp.id_presupuesto.reset();
				this.Cmp.id_cp_programa.reset();
                this.Cmp.id_cp_proyecto.reset();
                this.Cmp.id_cp_actividad.reset();
                this.Cmp.id_cp_organismo_fin.reset();
                this.Cmp.id_cp_fuente_fin.reset();
                this.Cmp.id_unidad_ejecutora.reset();
                this.Cmp.nivel.reset();                
				
				console.log('--->',record.data.ID)
                switch (record.data.ID) {
                    case 'programa':
                        this.ocultarComponente(this.Cmp.id_categoria_programatica);
                        this.ocultarComponente(this.Cmp.id_cp_proyecto);
                        this.ocultarComponente(this.Cmp.id_presupuesto);
                        this.ocultarComponente(this.Cmp.id_cp_actividad);
                        this.ocultarComponente(this.Cmp.id_cp_organismo_fin);
                        this.ocultarComponente(this.Cmp.id_cp_fuente_fin);
                        this.ocultarComponente(this.Cmp.id_unidad_ejecutora);

                        this.mostrarComponente(this.Cmp.id_cp_programa);					
                        this.mostrarComponente(this.Cmp.nivel);                                            
                        break;
                    case 'categoria':
                        this.ocultarComponente(this.Cmp.id_presupuesto);
                        this.ocultarComponente(this.Cmp.id_cp_programa);
                        this.ocultarComponente(this.Cmp.id_cp_proyecto);                    
                        this.ocultarComponente(this.Cmp.id_cp_actividad);
                        this.ocultarComponente(this.Cmp.id_cp_organismo_fin);
                        this.ocultarComponente(this.Cmp.id_cp_fuente_fin);
                        this.ocultarComponente(this.Cmp.id_unidad_ejecutora);

                        this.mostrarComponente(this.Cmp.id_categoria_programatica);
                        this.mostrarComponente(this.Cmp.nivel);
                        break;
                    case 'presupuesto':                    
                        this.ocultarComponente(this.Cmp.id_categoria_programatica);					
                        this.ocultarComponente(this.Cmp.id_cp_programa);
                        this.ocultarComponente(this.Cmp.id_cp_proyecto);                    
                        this.ocultarComponente(this.Cmp.id_cp_actividad);
                        this.ocultarComponente(this.Cmp.id_cp_organismo_fin);
                        this.ocultarComponente(this.Cmp.id_cp_fuente_fin);
                        this.ocultarComponente(this.Cmp.id_unidad_ejecutora);
                                            
                        this.mostrarComponente(this.Cmp.id_presupuesto);
                        this.mostrarComponente(this.Cmp.nivel); 
                        break;               
                    case 'centro_costo':
                        this.ocultarComponente(this.Cmp.id_categoria_programatica);
                        this.ocultarComponente(this.Cmp.id_presupuesto);
                        this.ocultarComponente(this.Cmp.id_cp_programa);
                        this.ocultarComponente(this.Cmp.id_cp_proyecto);                    
                        this.ocultarComponente(this.Cmp.id_cp_actividad);
                        this.ocultarComponente(this.Cmp.id_cp_organismo_fin);
                        this.ocultarComponente(this.Cmp.id_cp_fuente_fin);
                        this.ocultarComponente(this.Cmp.id_unidad_ejecutora);                                                            
                        this.ocultarComponente(this.Cmp.nivel);    
                        break;
                    case 'proyecto':
                        this.ocultarComponente(this.Cmp.id_categoria_programatica);
                        this.ocultarComponente(this.Cmp.id_presupuesto);
                        this.ocultarComponente(this.Cmp.id_cp_programa);                                   
                        this.ocultarComponente(this.Cmp.id_cp_actividad);
                        this.ocultarComponente(this.Cmp.id_cp_organismo_fin);
                        this.ocultarComponente(this.Cmp.id_cp_fuente_fin);
                        this.ocultarComponente(this.Cmp.id_unidad_ejecutora);

                        this.mostrarComponente(this.Cmp.id_cp_proyecto);
                        this.mostrarComponente(this.Cmp.nivel);                    
                        break;
                    case 'actividad':
                        this.ocultarComponente(this.Cmp.id_categoria_programatica);
                        this.ocultarComponente(this.Cmp.id_presupuesto);
                        this.ocultarComponente(this.Cmp.id_cp_programa);
                        this.ocultarComponente(this.Cmp.id_cp_proyecto);                    
                        this.ocultarComponente(this.Cmp.id_cp_organismo_fin);
                        this.ocultarComponente(this.Cmp.id_cp_fuente_fin);
                        this.ocultarComponente(this.Cmp.id_unidad_ejecutora);

                        this.mostrarComponente(this.Cmp.id_cp_actividad);                    
                        this.mostrarComponente(this.Cmp.nivel);                    
                        break;
                    case 'orga_financ':
                        this.ocultarComponente(this.Cmp.id_categoria_programatica);
                        this.ocultarComponente(this.Cmp.id_presupuesto);
                        this.ocultarComponente(this.Cmp.id_cp_programa);
                        this.ocultarComponente(this.Cmp.id_cp_proyecto);                        
                        this.ocultarComponente(this.Cmp.id_cp_fuente_fin);
                        this.ocultarComponente(this.Cmp.id_unidad_ejecutora);
                        this.ocultarComponente(this.Cmp.id_cp_actividad);

                        this.mostrarComponente(this.Cmp.id_cp_organismo_fin);                    
                        this.mostrarComponente(this.Cmp.nivel);
                        break;
                    case 'fuente_financ':
                        this.ocultarComponente(this.Cmp.id_categoria_programatica);
                        this.ocultarComponente(this.Cmp.id_presupuesto);
                        this.ocultarComponente(this.Cmp.id_cp_programa);
                        this.ocultarComponente(this.Cmp.id_cp_proyecto);                        
                        this.ocultarComponente(this.Cmp.id_cp_organismo_fin);
                        this.ocultarComponente(this.Cmp.id_unidad_ejecutora);
                        this.ocultarComponente(this.Cmp.id_cp_actividad);

                        this.mostrarComponente(this.Cmp.id_cp_fuente_fin);                    
                        this.mostrarComponente(this.Cmp.nivel);                    
                        break;
                    case 'unidad_ejecutora':
                        this.ocultarComponente(this.Cmp.id_categoria_programatica);
                        this.ocultarComponente(this.Cmp.id_presupuesto);
                        this.ocultarComponente(this.Cmp.id_cp_programa);
                        this.ocultarComponente(this.Cmp.id_cp_proyecto);                        
                        this.ocultarComponente(this.Cmp.id_cp_fuente_fin);
                        this.ocultarComponente(this.Cmp.id_cp_organismo_fin);
                        this.ocultarComponente(this.Cmp.id_cp_actividad);

                        this.mostrarComponente(this.Cmp.id_unidad_ejecutora);                    
                        this.mostrarComponente(this.Cmp.nivel);                    
                        break;
                }								
			}, this);

			this.Cmp.tipo_pres.on('change',function(){				  
				  this.Cmp.id_presupuesto.reset();
				  this.Cmp.id_presupuesto.store.baseParams.codigos_tipo_pres = this.Cmp.tipo_pres.getValue();				
				  this.Cmp.id_presupuesto.modificado = true; 
				   
			}, this);
			
		
		},
		
		
		
		tipo : 'reporte',
		clsSubmit : 'bprint',
		
		Grupos : [{
			layout : 'column',
			items : [{
				xtype : 'fieldset',
				layout : 'form',
				border : true,
				title : 'Datos para el reporte',
				bodyStyle : 'padding:0 10px 0;',
				columnWidth : '500px',
				items : [],
				id_grupo : 0,
				collapsible : true
			}]
		}],
		
    ActSave:'../../sis_presupuestos/control/PresupPartida/reporteEjecucion',
            
	onSubmit: function(o, x, force){
        var n;
        switch (this.Cmp.tipo_reporte.getValue()) {
            case 'categoria':                
                id = this.Cmp.id_categoria_programatica.getValue();                                
                    this.Cmp.id_categoria_programatica.store.data.items.forEach(e => {                                               
                        e.data.id_categoria_programatica == id && this.Cmp.subtitulo.setValue(`${e.data.codigo_categoria} - ${e.data.descripcion}`);
                    });      
                this.Cmp.concepto.setValue(this.Cmp.id_categoria_programatica.getRawValue());                         
                break;
            case 'programa':
                this.Cmp.concepto.setValue(this.Cmp.id_cp_programa.getRawValue());
                break;
            case 'presupuesto':
                this.Cmp.concepto.setValue(this.Cmp.id_presupuesto.getRawValue());
                break;
            case 'proyecto':                
                id = this.Cmp.id_cp_proyecto.getValue();
                    this.Cmp.id_cp_proyecto.store.data.items.forEach(e => {
                        e.data.id_cp_proyecto == id && this.Cmp.subtitulo.setValue(`${e.data.codigo} - ${e.data.descripcion}`);
                    });                 
                this.Cmp.concepto.setValue(this.Cmp.id_cp_proyecto.getRawValue());
                break;
            case 'actividad':
                id = this.Cmp.id_cp_actividad.getValue();
                    this.Cmp.id_cp_actividad.store.data.items.forEach(e => {
                        e.data.id_cp_actividad == id && this.Cmp.subtitulo.setValue(`${e.data.codigo} - ${e.data.descripcion}`);
                    });                                
                this.Cmp.concepto.setValue(this.Cmp.id_cp_actividad.getRawValue());            
                break;
            case 'orga_financ':
                    id = this.Cmp.id_cp_organismo_fin.getValue();
                    this.Cmp.id_cp_organismo_fin.store.data.items.forEach(e => {
                        e.data.id_cp_organismo_fin == id && this.Cmp.subtitulo.setValue(`${e.data.codigo} - ${e.data.descripcion}`);
                    });                    
                this.Cmp.concepto.setValue(this.Cmp.id_cp_organismo_fin.getRawValue());
                break;
            case 'fuente_financ':
                    id = this.Cmp.id_cp_fuente_fin.getValue();
                    this.Cmp.id_cp_fuente_fin.store.data.items.forEach(e => {
                        e.data.id_cp_fuente_fin == id && this.Cmp.subtitulo.setValue(`${e.data.codigo} - ${e.data.descripcion}`);
                    });                
                this.Cmp.concepto.setValue(this.Cmp.id_cp_fuente_fin.getRawValue());            
                break;
            case 'unidad_ejecutora':
                    id = this.Cmp.id_unidad_ejecutora.getValue();
                    this.Cmp.id_unidad_ejecutora.store.data.items.forEach(e => {
                        e.data.id_unidad_ejecutora == id && this.Cmp.subtitulo.setValue(`${e.data.codigo} - ${e.data.nombre}`);
                    });                
                this.Cmp.concepto.setValue(this.Cmp.id_unidad_ejecutora.getRawValue());
                break;            
        }             

		Phx.vista.FormRepEjecucion.superclass.onSubmit.call(this,o, x, force);
	},
	
	successSave :function(resp){
       Phx.CP.loadingHide();
       var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
        if (reg.ROOT.error) {
            alert('error al procesar');
            return
       } 
       
       var nomRep = reg.ROOT.detalle.archivo_generado;
        if(Phx.CP.config_ini.x==1){  			
        	nomRep = Phx.CP.CRIPT.Encriptar(nomRep);
        }
       
        if(this.Cmp.formato_reporte.getValue()=='pdf'){
        	window.open('../../../lib/lib_control/Intermediario.php?r='+nomRep+'&t='+new Date().toLocaleTimeString())
        }
        else{
        	window.open('../../../reportes_generados/'+nomRep+'?t='+new Date().toLocaleTimeString())
        }
       
	}
})
</script>