<?php
/**
*@package pXP
*@file PlanCuentaPartida.php
*@author  breydi vasquez
*@date 22-10-2020
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

  header("content-type: text/javascript; charset=UTF-8");
  ?>
  <script>
  Phx.vista.PlanCuentaPartida=Ext.extend(Phx.gridInterfaz,{

  	constructor:function(config){
  		this.maestro=config.maestro;
      	//llama al constructor de la clase padre
  		Phx.vista.PlanCuentaPartida.superclass.constructor.call(this,config);
  		this.init();
      this.bloquearMenus();
  	},

    Atributos:[
  		{
  			//configuracion del componente
  			config:{
  					labelSeparator:'',
  					inputType:'hidden',
  					name: 'id_cuenta_partida'
  			},
  			type:'Field',
  			form:false
  		},
  		{
  			config:{
  				name: 'id_partida',
  				inputType:'hidden'
  			},
  			type:'Field',
  			form: true
  		},
  		{
  			config:{
  				name: 'id_gestion',
  				inputType:'hidden'
  			},
  			type:'Field',
  			grid:false,
  			form:true
  		},
  		{
  			config: {
  				name: 'tipo_cuenta',
  				fieldLabel: 'Tipo Cuenta',
  				typeAhead: false,
  				forceSelection: false,
  				allowBlank: false,
  				emptyText: 'Tipos...',
  				store: new Ext.data.JsonStore({
  					url: '../../sis_contabilidad/control/ConfigTipoCuenta/listarConfigTipoCuenta',
  					id: 'tipo_cuenta',
  					root: 'datos',
  					sortInfo: {
  						field: 'nro_base',
  						direction: 'ASC'
  					},
  					totalProperty: 'total',
  					fields: ['tipo_cuenta', 'nro_base'],
  					// turn on remote sorting
  					remoteSort: true,
  					baseParams: {par_filtro: 'tipo_cuenta'}
  				}),
  				valueField: 'tipo_cuenta',
  				displayField: 'tipo_cuenta',
  				gdisplayField: 'tipo_cuenta',
  				triggerAction: 'all',
  				lazyRender: true,
  				mode: 'remote',
  				pageSize: 20,
  				queryDelay: 200,
  				listWidth:280,
  				minChars: 2,
  				gwidth: 300
  				},
  			type: 'ComboBox',
  			id_grupo: 0,
  			form: false,
  			grid:false
  		},
  		{
  	       		config:{
  	       			name:'tipo_cuenta_pat',
  	       			fieldLabel:'Cap./Res.',
  	       			allowBlank:false,
  	       			emptyText:'Tipo...',
  	       			typeAhead: true,
  	       		    triggerAction: 'all',
  	       		    lazyRender:true,
  	       		    mode: 'local',
  	       		    gwidth: 100,
  	       		    store:['capital','reserva','resultado']
  	       		},
  	       		type:'ComboBox',
  	       		id_grupo:0,
  	       		grid:false,
  	       		form:false
  	       },
  	       {
  			config:{
  				name: 'digito',
  				fieldLabel: 'Digito',
  				allowBlank: false,
  				allowNegative: false,
  				vtype: 'alpha',
  				//regex: new RegExp('/^[0-9]$/'),
  				//regexText: 'Solo números',
  				anchor: '80%',
  				gwidth: 100,
  				maxLength:5
  			},
  			type:'Field',
  			id_grupo:1,
  			grid:false,
  			form:false
  		},
  		// {
  		// 	config:{
  		// 		name: 'text',
  		// 		fieldLabel: 'Cuenta',
  		// 		allowBlank: false,
  		// 		gwidth: 400,
  		// 		width: 400,
  		// 		maxLength:100
  		// 	},
  		// 	type:'TextField',
  		// 	id_grupo:1,
  		// 	grid:true,
  		// 	form:false
  		// },
      {
  			config:{
  				name: 'nro_cuenta',
  				fieldLabel: 'Nro Cuenta',
  				allowBlank: false,
  				anchor: '80%',
  				gwidth: 100,
  				maxLength:30
  			},
  			type:'TextField',
        bottom_filtro: true,
  			id_grupo:1,
  			grid:true,
  			form:false
  		},
  		{
  			config:{
  				name: 'nombre_cuenta',
  				fieldLabel: 'Nombre Cuenta',
  				allowBlank: false,
  				anchor: '80%',
  				gwidth: 100,
  				maxLength:100
  			},
  			type:'TextField',
  			id_grupo:1,
  			grid:false,
  			form:false
  		},
  		{
  			config:{
  				name: 'desc_cuenta',
  				fieldLabel: 'Desc Cuenta',
  				allowBlank: false,
  				anchor: '80%',
  				gwidth: 200,
  				maxLength:500
  			},
  			type:'TextArea',
        bottom_filtro: true,
  			id_grupo:1,
  			grid:true,
  			form:false
  		},
  		{
  	       		config:{
  	       			name:'sw_transaccional',
  	       			fieldLabel:'Operación',
  	       			allowBlank:false,
  	       			emptyText:'Tipo...',
  	       			typeAhead: true,
  	       		    triggerAction: 'all',
  	       		    lazyRender:true,
  	       		    mode: 'local',
  	       		    gwidth: 100,
  	       		    store:['movimiento','titular']
  	       		},
  	       		type:'ComboBox',
  	       		id_grupo:0,
  	       		grid:true,
  	       		form:false
  	     },
  		 {
  			config: {
  				name: 'id_config_subtipo_cuenta',
  				fieldLabel: 'Subtipo',
  				typeAhead: false,
  				forceSelection: false,
  				allowBlank: false,
  				emptyText: 'Tipos...',
  				store: new Ext.data.JsonStore({
  					url: '../../sis_contabilidad/control/ConfigSubtipoCuenta/listarConfigSubtipoCuenta',
  					id: 'id_config_subtipo_cuenta',
  					root: 'datos',
  					sortInfo: {
  						field: 'codigo',
  						direction: 'ASC'
  					},
  					totalProperty: 'total',
  					fields: ['tipo_cuenta', 'id_config_subtipo_cuenta','nombre','codigo'],
  					// turn on remote sorting
  					remoteSort: true,
  					baseParams: {par_filtro: 'cst.nombre#cst.codigo'}
  				}),
  				valueField: 'id_config_subtipo_cuenta',
  				displayField: 'nombre',
  				gdisplayField: 'desc_csc',
  				triggerAction: 'all',
  				lazyRender: true,
  				mode: 'remote',
  				pageSize: 20,
  				queryDelay: 200,
  				listWidth:280,
  				minChars: 2,
  				gwidth: 90
  				},
  			type: 'ComboBox',
  			id_grupo: 0,
  			form: false,
  			grid:true
  		},

  		 {
     			config:{
         		    name:'id_moneda',
            		origen:'MONEDA',
     				fieldLabel:'Moneda',
     				allowBlank:false,
     				gdisplayField:'desc_moneda',//mapea al store del grid
     			    gwidth:50,
     			     renderer:function (value, p, record){return String.format('{0}', record.data['desc_moneda']);}
         	     },
     			type:'ComboRec',
     			id_grupo:1,
     			grid:true,
     			form:false
     	      },
     	      {
  	       		config:{
  	       			name:'sw_auxiliar',
  	       			fieldLabel:'Permite Auxiliares',
  	       			allowBlank:false,
  	       			emptyText:'Tipo...',
  	       			typeAhead: true,
  	       		    triggerAction: 'all',
  	       		    lazyRender:true,
  	       		    mode: 'local',
  	       		    gwidth: 100,
  	       		    store:['si','no']
  	       		},
  	       		type:'ComboBox',
  	       		id_grupo:0,
  	       		grid:true,
  	       		form:false
  	       },
  	       {
  	       		config:{
  	       			name:'sw_control_efectivo',
  	       			fieldLabel:'Control Efectivo',
  	       			qtip: 'Para identificar la cuentas contables que manejas efectivo, como bancos y cajas',
  	       			allowBlank:false,
  	       			emptyText:'Tipo...',
  	       			typeAhead: true,
  	       		    triggerAction: 'all',
  	       		    lazyRender:true,
  	       		    mode: 'local',
  	       		    gwidth: 100,
  	       		    store:['si','no']
  	       		},
  	       		type:'ComboBox',
  	       		id_grupo:0,
  	       		grid:true,
  	       		form:false
  	       	},

     	      {
  	       		config:{
  	       			name:'valor_incremento',
  	       			fieldLabel:'Incremento',
  	       			qtip: 'si la cuenta es negativa resta en el mayor',
  	       			allowBlank:false,
  	       			emptyText:'Tipo...',
  	       			typeAhead: true,
  	       		    triggerAction: 'all',
  	       		    lazyRender:true,
  	       		    mode: 'local',
  	       		    gwidth: 100,
  	       		    store:['positivo','negativo']
  	       		},
  	       		type:'ComboBox',
  	       		valorInicial: 'positivo',
  	       		id_grupo:0,
  	       		grid:true,
  	       		form:false
  	       	},
     	      {
         			config:{
         				name:'eeff',
         				fieldLabel:'EEFF',
         				allowBlank:true,
         				emptyText:'Roles...',
         				store: new Ext.data.ArrayStore({
                          fields: ['variable', 'valor'],
                          data : [ ['balance', 'Balance'],
                                   ['resultado', 'Resultado'],
                                 ]
                          }),
         				valueField: 'variable',
  				    displayField: 'valor',
         				forceSelection:true,
         				typeAhead: true,
             			triggerAction: 'all',
             			lazyRender:true,
         				mode:'local',
         				pageSize:10,
         				queryDelay:1000,
         				width:250,
         				minChars:2,
  	       			enableMultiSelect:true
         			},
         			type:'AwesomeCombo',
         			id_grupo:0,
         			grid:true,
         			form:false
         	},
     	    {
         			config:{
         				name:'tipo_act',
         				fieldLabel:'Tipo Actualización',
         				qtip:'define si la cuenta realiza actualización AITB, y el origen, sistema de contabilidad o activos fijos',
         				allowBlank:true,
         				emptyText:'Roles...',
         				store: new Ext.data.ArrayStore({
                          fields: ['variable', 'valor'],
                          data : [ ['no', 'No actualiza'],
                                   ['conta', 'Sistema de Contabilidad'],
                                   ['activos', 'Sistema de Activos FIjos'],
                                 ]
                          }),
         				valueField: 'variable',
  				    displayField: 'valor',
         				forceSelection:true,
         				typeAhead: true,
             			triggerAction: 'all',
             			lazyRender:true,
         				mode:'local',
         				pageSize:10,
         				queryDelay:1000,
         				width:250,
         				minChars:2,
  	       			enableMultiSelect:true
         			},
         			type:'ComboBox',
         			id_grupo:0,
         			grid:true,
         			form:false
         	},
          {
     			config: {
     				name: 'id_cuenta',
     				fieldLabel: 'Cuenta',
     				typeAhead: false,
     				forceSelection: false,
     				allowBlank: false,
     				emptyText: 'Tipos...',
     				store: new Ext.data.JsonStore({
     					url: '../../sis_presupuestos/control/Partida/listPlanCuentaPartida',
     					id: 'id_cuenta',
     					root: 'datos',
     					sortInfo: {
     						field: 'nro_cuenta',
     						direction: 'ASC'
     					},
     					totalProperty: 'total',
     					fields: ['id_cuenta','nombre_cuenta', 'nro_cuenta'],
     					// turn on remote sorting
     					remoteSort: true,
     					baseParams: {par_filtro: 'cta.nombre_cuenta#cta.nro_cuenta'}
     				}),
     				valueField: 'id_cuenta',
     				displayField: 'nro_cuenta',
     				gdisplayField: 'nro_cuenta',
            tpl:'<tpl for="."><div class="x-combo-list-item" style="color: black"><p><b>N° cuenta: {nro_cuenta}</b></p><p style="color: #80251e"><b>Nombre: {nombre_cuenta}</b></p></div></tpl>',
            hiddenName: 'id_cuenta',
     				triggerAction: 'all',
     				lazyRender: true,
     				mode: 'remote',
     				pageSize: 20,
     				queryDelay: 200,
     				listWidth:380,
     				minChars: 2,
            resizable:true,
            gwidth:200,
            width: 380,
     				},
     			type: 'ComboBox',
     			id_grupo: 0,
     			form: true,
     			grid:false
     		},
        {
  			config:{
  				name: 'sw_deha',
  				qtip:'segun el movimeinto de la cuenta se peude filtar que partida se pueden usar',
  				fieldLabel: 'Debe / Haber',
  				allowBlank: false,
  				anchor: '40%',
  				gwidth: 50,
  				emptyText:'si/no...',
         			typeAhead: true,
         		    triggerAction: 'all',
         		    lazyRender:true,
         		    mode: 'local',
         		    valueField: 'inicio',
         		    store:['debe','haber']
  			},
  			type:'ComboBox',
  			id_grupo:1,
  			filters:{
  	       		         type: 'list',
  	       				 pfiltro:'cupa.sw_deha',
  	       				 options: ['debe','haber'],
  	       		 	},
  			grid:false,
  			form:true
  		},

  		{
  			config:{
  				name: 'se_rega',
  				qtip:'segun el movimeinto de la partida se peude filtar que partida se pueden usar',
  				fieldLabel: 'Recurso / Gasto',
  				allowBlank: false,
  				anchor: '40%',
  				gwidth: 50,
  				emptyText:'si/no...',
         			typeAhead: false,
         		    triggerAction: 'all',
         		    lazyRender:true,
         		    mode: 'local',
         		    valueField: 'inicio',

         		    store:['recurso','gasto']
  			},
  			type:'ComboBox',
  			id_grupo:1,
  			filters:{
  	       		         type: 'list',
  	       				 pfiltro:'cupa.se_rega',
  	       				 options: ['recurso','gasto'],
  	       		 	},
  			grid:false,
  			form:true
  		},
  	],

  	onReloadPage:function(m,a,b){
  		this.maestro=m;
  	},
  	postReloadPage:function(m,a,b){

        if(this.maestro.tipo_nodo == 'hoja'){
    			this.store.baseParams={id_partida:this.maestro.id_partida, id_gestion: this.maestro.id_gestion};
    			this.load({params:{start:0, limit:50}})
    		}
    		else{
    			this.bloquearMenus();
    			this.store.removeAll();
    		}
  	},


  	loadValoresIniciales:function() {
      // this.Cmp.id_cuenta.store.baseParams = Ext.apply(this.Cmp.id_cuenta.store.baseParams,   {id_gestion: this.maestro.id_gestion,id_partida:this.maestro.id_partida});
      Phx.vista.PlanCuentaPartida.superclass.loadValoresIniciales.call(this);
      this.Cmp.id_cuenta.store.baseParams = Ext.apply(this.Cmp.id_cuenta.store.baseParams,   {id_gestion: this.maestro.id_gestion});
      this.Cmp.id_cuenta.modificado = true;
      this.Cmp.id_gestion.setValue(this.maestro.id_gestion);
      this.Cmp.id_partida.setValue(this.maestro.id_partida);
  	},

    onButtonNew:function(n){
      this.window.setSize(550, 230);
      Phx.vista.PlanCuentaPartida.superclass.onButtonNew.call(this);
    },

  	tam_pag:50,
  	title:'Plan Cuenta Partida',
  	ActList:'../../sis_presupuestos/control/Partida/listPlanCuentaPartida',
    ActSave:'../../sis_contabilidad/control/CuentaPartida/insertarCuentaPartida',
    ActDel:'../../sis_contabilidad/control/CuentaPartida/eliminarCuentaPartida',
  	id_store:'id_cuenta_partida',
    fields: [
  		{name:'id_cuenta', type: 'numeric'},
      {name:'id_partida', type: 'numeric'},
  		{name:'estado_reg', type: 'string'},
  		{name:'nombre_cuenta', type: 'string'},
  		{name:'sw_auxiliar', type: 'numeric'},
  		{name:'tipo_cuenta', type: 'string'},
  		{name:'desc_cuenta', type: 'string'},
  		{name:'nro_cuenta', type: 'string'},
  		{name:'id_moneda', type: 'numeric'},
      {name:'id_cuenta_partida', type: 'numeric'},
  		{name:'sw_transaccional', type: 'string'},
  		{name:'id_gestion', type: 'numeric'},'desc_moneda',
  		'valor_incremento','eeff','sw_control_efectivo',
  		'id_config_subtipo_cuenta','desc_csc','tipo_act'
  	],
  	sortInfo:{
  		field: 'nro_cuenta',
  		direction: 'ASC'
  	},
  	bdel: true,
    bedit: false,
  	bsave: false,
    bnew: true
  	}
  )
  </script>
