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
Phx.vista.FormRepProgramacion = Ext.extend(Phx.frmInterfaz, {

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
                pageSize:5,
                queryDelay:1000,
                listWidth:250,
                resizable:true,
                width:250

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
				fieldLabel: 'Tipo de Presupuesto',
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
                width:250,
				listWidth : '250',
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
	    		selectOnFocus:true,
				mode:'local',
				store:new Ext.data.ArrayStore({
	        	fields: ['ID', 'valor'],
	        	data :	[
		        	        ['programa','Programa'],
		        	        ['categoria','Categoría Programática'],
							['presupuesto','Presupuesto'],
                            ['unidad_ejecutora', 'Unidad Ejecutora'],
														['formulacion_presu_txt', 'Formulación Presupuestaria por archivo plano']
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
			   width: 250,
			   listWidth: 250,
               resizable:true,
			   minChars:2,
			   tpl:'<tpl for="."><div class="x-combo-list-item"><p style="font-weight:bold; color:green;">{codigo_categoria}</p><p>{descripcion}</p> </div></tpl>'
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
                allowBlank: true,
                tinit: false,
                baseParams: {_adicionar:'si'},
                origen: 'PRESUPUESTO',
                width: 250,
   				listWidth: 250
            },
            type: 'ComboRec',
            id_grupo: 0,
            form: true
        },

		{

			config: {
				name: 'id_cp_programa',
				fieldLabel: 'Programa',
				allowBlank: true,
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
                width:250,
                resizable:true,
                minChars: 2,
				tpl:'<tpl for="."><div class="x-combo-list-item"><p>{codigo}-{descripcion}</p> </div></tpl>'
			},
			type: 'ComboBox',
			form: true
		},

        {
            config: {
                name: 'id_unidad_ejecutora',
                fieldLabel: 'Unidad Ejecutora',
                allowBlank: false,
                emptyText: 'Elija una opción...',
                store: new Ext.data.JsonStore({
                    url: '../../sis_presupuestos/control/UnidadEjecutora/listarUnidadEjecutoraMensual',
                    id: 'id_unidad_ejecutora',
                    root: 'datos',
                    sortInfo: {
                        field: 'codigo',
                        direction: 'ASC'
                    },
                    totalProperty: 'total',
                    fields: ['id_unidad_ejecutora', 'nombre', 'codigo'],
                    remoteSort: true,
                    baseParams: {par_filtro: 'codigo#nombre', _adicionar:'si'}
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
            },
            type: 'ComboBox',
            id_grupo: 0,
            //filters: {pfiltro: 'desc_unidad_ejecutora',type: 'string'},
            grid: true,
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
				name:'nivel',
				fieldLabel:'Nivel Partida',
				typeAhead: true,
				allowBlank:false,
	    		triggerAction: 'all',
	    		emptyText:'Nivel...',
	    		selectOnFocus:true,
				mode:'local',
				store:new Ext.data.ArrayStore({
	        	fields: ['ID', 'valor'],
	        	data :	[
	        				['4','Todos los Niveles'],
							['5','Solo Partidas de Movimiento'],
		        			['1','Hasta el Nivel 1'],
		        			['2','Hasta el Nivel 2'],
							['3','Hasta el Nivel 3']
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
			Phx.vista.FormRepProgramacion.superclass.constructor.call(this, config);
			this.init();

			this.ocultarComponente(this.Cmp.id_categoria_programatica);
			this.ocultarComponente(this.Cmp.id_presupuesto);
			this.ocultarComponente(this.Cmp.id_cp_programa);
            this.ocultarComponente(this.Cmp.id_unidad_ejecutora);

			this.iniciarEventos();
		},

		iniciarEventos:function(){

			this.Cmp.id_gestion.on('select',function(c,r,n){

					this.Cmp.id_categoria_programatica.reset();
					this.Cmp.id_categoria_programatica.store.baseParams.id_gestion =c.value;
					this.Cmp.id_categoria_programatica.modificado=true;

					this.Cmp.id_presupuesto.reset();
					this.Cmp.id_presupuesto.store.baseParams.id_gestion = c.value;
					this.Cmp.id_presupuesto.modificado=true;

					this.Cmp.id_cp_programa.reset();
					this.Cmp.id_cp_programa.store.baseParams.id_gestion = c.value;
					this.Cmp.id_cp_programa.modificado=true;

                    this.Cmp.id_unidad_ejecutora.reset();
					this.Cmp.id_unidad_ejecutora.store.baseParams.id_gestion = c.value;
					this.Cmp.id_unidad_ejecutora.modificado=true;


			},this);


			this.Cmp.tipo_reporte.on('select',function(combo, record, index){
				console.log(record, index)

				this.Cmp.id_categoria_programatica.reset();
				this.Cmp.id_presupuesto.reset();
				this.Cmp.id_cp_programa.reset();
                this.Cmp.id_unidad_ejecutora.reset();
								this.Cmp.formato_reporte.reset();
								this.Cmp.nivel.reset();

				console.log('--->',record.data.ID)
				if(record.data.ID == 'programa'){
					this.ocultarComponente(this.Cmp.id_categoria_programatica);
					this.ocultarComponente(this.Cmp.id_presupuesto);
					this.mostrarComponente(this.Cmp.id_cp_programa);
                    this.ocultarComponente(this.Cmp.id_unidad_ejecutora);
										this.mostrarComponente(this.Cmp.nivel);
										this.mostrarComponente(this.Cmp.formato_reporte);

				}

				else if(record.data.ID == 'categoria'){
					this.mostrarComponente(this.Cmp.id_categoria_programatica);
					this.ocultarComponente(this.Cmp.id_presupuesto);
					this.ocultarComponente(this.Cmp.id_cp_programa);
                    this.ocultarComponente(this.Cmp.id_unidad_ejecutora);
										this.mostrarComponente(this.Cmp.nivel);
										this.mostrarComponente(this.Cmp.formato_reporte);

				}

				else if(record.data.ID == 'presupuesto'){
					this.ocultarComponente(this.Cmp.id_categoria_programatica);
					this.mostrarComponente(this.Cmp.id_presupuesto);
					this.ocultarComponente(this.Cmp.id_cp_programa);
                    this.ocultarComponente(this.Cmp.id_unidad_ejecutora);
										this.mostrarComponente(this.Cmp.nivel);
										this.mostrarComponente(this.Cmp.formato_reporte);

				}
				else if(record.data.ID == 'unidad_ejecutora'){
                    this.ocultarComponente(this.Cmp.id_categoria_programatica);
					this.ocultarComponente(this.Cmp.id_presupuesto);
					this.ocultarComponente(this.Cmp.id_cp_programa);
                    this.mostrarComponente(this.Cmp.id_unidad_ejecutora);
										this.mostrarComponente(this.Cmp.nivel);
										this.mostrarComponente(this.Cmp.formato_reporte);
                }
				else if (record.data.ID == 'formulacion_presu_txt'){
					this.ocultarComponente(this.Cmp.id_categoria_programatica);
					this.ocultarComponente(this.Cmp.id_presupuesto);
					this.ocultarComponente(this.Cmp.id_cp_programa);
					this.ocultarComponente(this.Cmp.id_unidad_ejecutora);
					this.ocultarComponente(this.Cmp.nivel);
					this.ocultarComponente(this.Cmp.formato_reporte);
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

	ActSave:'../../sis_presupuestos/control/MemoriaCalculo/reporteProgramacion',

	onSubmit: function(o, x, force){

		if(this.Cmp.tipo_reporte.getValue()=='categoria'){
			this.Cmp.concepto.setValue(this.Cmp.id_categoria_programatica.getRawValue());
		}
		if(this.Cmp.tipo_reporte.getValue()=='programa'){
			this.Cmp.concepto.setValue(this.Cmp.id_cp_programa.getRawValue());
		}
		if(this.Cmp.tipo_reporte.getValue()=='presupuesto'){
			this.Cmp.concepto.setValue(this.Cmp.id_presupuesto.getRawValue());
		}
        if(this.Cmp.tipo_reporte.getValue()=='unidad_ejecutora'){
			this.Cmp.concepto.setValue(this.Cmp.id_unidad_ejecutora.getRawValue());
		}

		Phx.vista.FormRepProgramacion.superclass.onSubmit.call(this,o, x, force);
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
        }else if(this.Cmp.formato_reporte.getValue()=='csv'){
					window.open('../../../reportes_generados/'+nomRep+'?t='+new Date().toLocaleTimeString())
				}
        else{
					var id_g = this.Cmp.id_gestion.getValue()
					var type = this.Cmp.tipo_pres.getValue()
					var gestion = ''
					this.Cmp.id_gestion.store.data.items.forEach(function(e) {
							if(e.data.id_gestion == id_g) {
									gestion = e.data.gestion
							}
					})
					var tip_p = ''
					if(type == '1'){ tip_p = 'Recursos' }else{tip_p = 'Gasto'};


					nomRep.forEach((item, i) => {
						var data = "&extension=txt";
							  data += "&name_file=FormulacionPresupuestaria"+tip_p+gestion;
								data += "&url=../../../reportes_generados/"+item;
								window.open('../../../lib/lib_control/CTOpenFile.php?' + data);
					});
        }

	}
})
</script>
