<?php
/**
*@package pXP
*@file gen-MatrizConversionDeuda.php
*@author  (ismael.valdivia)
*@date 30-11-2021 18:07:32
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.MatrizConversionDeuda=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.MatrizConversionDeuda.superclass.constructor.call(this,config);
		this.init();
		this.load({params:{start:0, limit:this.tam_pag}})
	},



	onButtonNew: function() {
			Phx.vista.MatrizConversionDeuda.superclass.onButtonNew.call(this);

			this.ocultarComponente(this.Cmp.id_partida_origen);
			this.ocultarComponente(this.Cmp.id_partida_destino);

			this.Cmp.id_gestion_origen.on('select',function(c,r,n){
				this.Cmp.id_partida_origen.store.baseParams.id_gestion = this.Cmp.id_gestion_origen.getValue();

				this.Cmp.id_partida_origen.reset();
				this.Cmp.id_partida_destino.reset();
				this.Cmp.id_gestion_destino.reset();
				/*Aqui para listar las partidas de la gestion seleccionada*/

				var añoSiguiente = (parseInt(r.data.gestion)+1);
				/*Aqui para selecccionar la gestion destino en base a la gestion origen*/
				this.Cmp.id_gestion_destino.store.load({params:{start:0,limit:50},
							 callback : function (r) {
								 for (var i = 0; i < r.length; i++) {
									 if (r[i].data.gestion == añoSiguiente) {
										 this.Cmp.id_gestion_destino.setValue(r[i].data.id_gestion);
										 this.Cmp.id_gestion_destino.fireEvent('select', this.Cmp.id_gestion_destino,this.Cmp.id_gestion_destino.store.getById(r[i].data.id_gestion));
									 }
								 }
								}, scope : this
						});
				/***********************************************************************/

				/*Aqui para mostrar los campos*/
				this.mostrarComponente(this.Cmp.id_partida_origen);
				/******************************/
			},this);


			this.Cmp.id_partida_origen.on('select',function(c,r,n){
				this.mostrarComponente(this.Cmp.id_partida_destino);
			},this);


			this.Cmp.id_gestion_destino.on('select',function(c,r,n){
				this.Cmp.id_partida_destino.store.baseParams.id_gestion_destino = this.Cmp.id_gestion_destino.getValue();
			},this);
	},


	onButtonEdit: function() {
			Phx.vista.MatrizConversionDeuda.superclass.onButtonEdit.call(this);
			var rec = this.sm.getSelected();

			this.ocultarComponente(this.Cmp.id_partida_origen);
			this.ocultarComponente(this.Cmp.id_partida_destino);


			this.Cmp.id_gestion_origen.store.load({params:{start:0,limit:50},
						 callback : function (r) {
							 for (var i = 0; i < r.length; i++) {
								 if (r[i].data.id_gestion == rec.data.id_gestion_origen) {
									 this.Cmp.id_gestion_origen.setValue(r[i].data.id_gestion);
									 this.Cmp.id_gestion_origen.fireEvent('select', this.Cmp.id_gestion_origen,this.Cmp.id_gestion_origen.store.getById(r[i].data.id_gestion));
								 }
							 }
							}, scope : this
					});

			this.Cmp.id_gestion_origen.on('select',function(c,r,n){
				this.Cmp.id_partida_origen.store.baseParams.id_gestion = this.Cmp.id_gestion_origen.getValue();
				/*Aqui para listar las partidas de la gestion seleccionada*/


				this.Cmp.id_partida_origen.reset();



				this.Cmp.id_partida_origen.store.baseParams.id_partida = rec.data.id_partida_origen;

				this.Cmp.id_partida_origen.store.load({params:{start:0,limit:50},
							 callback : function (r) {
								 if (r.length == 1) {
										 this.Cmp.id_partida_origen.setValue(r[0].data.id_partida);
										 this.Cmp.id_partida_origen.fireEvent('select', this.Cmp.id_partida_origen,this.Cmp.id_partida_origen.store.getById(r[0].data.id_partida));
								 }
								}, scope : this
						});
				this.Cmp.id_partida_origen.store.baseParams.id_partida = '';


				var añoSiguiente = (parseInt(r.data.gestion)+1);
				/*Aqui para selecccionar la gestion destino en base a la gestion origen*/
				this.Cmp.id_gestion_destino.store.load({params:{start:0,limit:50},
							 callback : function (r) {
								 for (var i = 0; i < r.length; i++) {
									 if (r[i].data.gestion == añoSiguiente) {
										 this.Cmp.id_gestion_destino.setValue(r[i].data.id_gestion);
										 this.Cmp.id_gestion_destino.fireEvent('select', this.Cmp.id_gestion_destino,this.Cmp.id_gestion_destino.store.getById(r[i].data.id_gestion));
									 }
								 }
								}, scope : this
						});
				/***********************************************************************/

				/*Aqui para mostrar los campos*/
				this.mostrarComponente(this.Cmp.id_partida_origen);
				/******************************/
			},this);





			//
			//
			this.Cmp.id_partida_origen.on('select',function(c,r,n){
				this.mostrarComponente(this.Cmp.id_partida_destino);
			},this);
			//
			//
			this.Cmp.id_gestion_destino.on('select',function(c,r,n){

			
				this.Cmp.id_partida_destino.store.baseParams.id_gestion_destino = this.Cmp.id_gestion_destino.getValue();

				this.Cmp.id_partida_destino.store.baseParams.id_partida = rec.data.id_partida_destino;

				this.Cmp.id_partida_destino.store.load({params:{start:0,limit:50},
							 callback : function (r) {
								 if (r.length == 1) {
										 this.Cmp.id_partida_destino.setValue(r[0].data.id_partida);
										 this.Cmp.id_partida_destino.fireEvent('select', this.Cmp.id_partida_destino,this.Cmp.id_partida_destino.store.getById(r[0].data.id_partida));
								 }
								}, scope : this
						});

						this.Cmp.id_partida_destino.store.baseParams.id_partida = '';

			},this);




	},

	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_matriz_conversion'
			},
			type:'Field',
			form:true
		},
		{
			config: {
				name: 'id_gestion_origen',
				fieldLabel: 'Gestion Origen',
				allowBlank: true,
				emptyText: 'Elija una opción...',
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
				gdisplayField: 'gestion',
				hiddenName: 'id_gestion_origen',
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
				renderer : function(value, p, record) {
					return String.format('{0}', record.data['gestion_origen']);
				}
			},
			type: 'ComboBox',
			id_grupo: 0,
			filters: {pfiltro: 'gestion',type: 'string'},
			grid: true,
			form: true
		},
		{
			config: {
				name: 'id_partida_origen',
				fieldLabel: 'Partida Origen',
				allowBlank: true,
				hidden:false,
				emptyText: 'Elija una opción...',
				store: new Ext.data.JsonStore({
					url: '../../sis_presupuestos/control/MatrizConversionDeuda/listarPartidaOrigen',
					id: 'id_partida',
					root: 'datos',
					sortInfo: {
						field: 'codigo',
						direction: 'ASC'
					},
					totalProperty: 'total',
					fields: ['id_partida', 'desc_partida', 'nombre_partida', 'codigo'],
					remoteSort: true,
					baseParams: {par_filtro: 'nombre_partida#codigo'}
				}),
				valueField: 'id_partida',
				displayField: 'desc_partida',
				gdisplayField: 'desc_partida',
				hiddenName: 'id_partida_origen',
				forceSelection: true,
				typeAhead: false,
				triggerAction: 'all',
				lazyRender: true,
				mode: 'remote',
				pageSize: 15,
				queryDelay: 1000,
				anchor: '100%',
				gwidth: 300,
				minChars: 2,
				renderer : function(value, p, record) {
					return String.format('{0}', record.data['desc_partida_origen']);
				},
				listeners: {
					beforequery: function(qe){
						delete qe.combo.lastQuery;
					}
				}
			},
			type: 'ComboBox',
			id_grupo: 0,
			filters: {pfiltro: 'nombre_partida#codigo',type: 'string'},
			grid: true,
			form: true
		},
		{
			config: {
				name: 'id_gestion_destino',
				fieldLabel: 'Gestion Destino',
				allowBlank: true,
				emptyText: 'Elija una opción...',
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
				gdisplayField: 'gestion',
				hiddenName: 'id_gestion_destino',
				forceSelection: true,
				typeAhead: false,
				triggerAction: 'all',
				lazyRender: true,
				mode: 'remote',
				pageSize: 15,
				queryDelay: 1000,
				anchor: '100%',
				disabled:true,
				gwidth: 150,
				minChars: 2,
				renderer : function(value, p, record) {
					return String.format('{0}', record.data['gestion_destino']);
				}
			},
			type: 'ComboBox',
			id_grupo: 0,
			filters: {pfiltro: 'gestion',type: 'string'},
			grid: true,
			form: true
		},
		{
			config: {
				name: 'id_partida_destino',
				fieldLabel: 'Partida Destino',
				allowBlank: true,
				hidden:false,
				emptyText: 'Elija una opción...',
				store: new Ext.data.JsonStore({
					url: '../../sis_presupuestos/control/MatrizConversionDeuda/listarPartidaDestino',
					id: 'id_partida',
					root: 'datos',
					sortInfo: {
						field: 'codigo',
						direction: 'ASC'
					},
					totalProperty: 'total',
					fields: ['id_partida', 'desc_partida_destino', 'nombre_partida', 'codigo'],
					remoteSort: true,
					baseParams: {par_filtro: 'nombre_partida#codigo'}
				}),
				valueField: 'id_partida',
				displayField: 'desc_partida_destino',
				gdisplayField: 'desc_partida_destino',
				hiddenName: 'id_partida_destino',
				forceSelection: true,
				typeAhead: false,
				triggerAction: 'all',
				lazyRender: true,
				mode: 'remote',
				pageSize: 15,
				queryDelay: 1000,
				anchor: '100%',
				gwidth: 300,
				minChars: 2,
				renderer : function(value, p, record) {
					return String.format('{0}', record.data['desc_partida_destino']);
				},
				listeners: {
					beforequery: function(qe){
						delete qe.combo.lastQuery;
					}
				}
			},
			type: 'ComboBox',
			id_grupo: 0,
			filters: {pfiltro: 'movtip.nombre',type: 'string'},
			grid: true,
			form: true
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
				name: 'fecha_reg',
				fieldLabel: 'Fecha creación',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y',
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
			},
				type:'DateField',
				filters:{pfiltro:'macon.fecha_reg',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'id_usuario_ai',
				fieldLabel: 'Fecha creación',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'macon.id_usuario_ai',type:'numeric'},
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
				filters:{pfiltro:'macon.usuario_ai',type:'string'},
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
				filters:{pfiltro:'macon.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
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
				filters:{pfiltro:'macon.estado_reg',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		}
	],
	tam_pag:50,
	title:'Matriz de conversion deuda',
	ActSave:'../../sis_presupuestos/control/MatrizConversionDeuda/insertarMatrizConversionDeuda',
	ActDel:'../../sis_presupuestos/control/MatrizConversionDeuda/eliminarMatrizConversionDeuda',
	ActList:'../../sis_presupuestos/control/MatrizConversionDeuda/listarMatrizConversionDeuda',
	id_store:'id_matriz_conversion',
	fields: [
		{name:'id_matriz_conversion', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
		{name:'id_gestion_origen', type: 'numeric'},
		{name:'id_partida_origen', type: 'numeric'},
		{name:'id_partida_destino', type: 'numeric'},
		{name:'id_gestion_destino', type: 'numeric'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},

		{name:'gestion_destino', type: 'string'},
		{name:'gestion_origen', type: 'string'},
		{name:'desc_partida_origen', type: 'string'},
		{name:'desc_partida_destino', type: 'string'},

	],
	sortInfo:{
		field: 'id_matriz_conversion',
		direction: 'ASC'
	},
	bdel:true,
	bsave:true
	}
)
</script>
