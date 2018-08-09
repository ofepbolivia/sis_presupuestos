<?php
/**
 * @package pXP
 * @file presupuesto_multi_fun.php
 * @author  (BVP)
 * @date 03-08-2018 
 * @description Archivo con la interfaz de funcionario presupuesto
 *
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.presupuesto_multi_fun=Ext.extend(Phx.gridInterfaz,{
	 nombreVista: 'presupuesto_multi_fun',
	 register:'',	 
       constructor:function(config){
		this.tbarItems = ['-',
            'Gestión:',this.cmbGestion,'-'
        ];
     	
      
		this.maestro=config;				
    	//llama al constructor de la clase padre
		Phx.vista.presupuesto_multi_fun.superclass.constructor.call(this,config);
		this.init();
		this.cmbGestion.on('select', this.capturarEventos, this);
		this.iniciarEventos();		
       }, 
            
    cmbGestion: new Ext.form.ComboBox({
        fieldLabel: 'Gestion',
        allowBlank: true,
        emptyText: 'Gestion...',
        blankText: 'Año',
        editable: false,
        store: new Ext.data.JsonStore(
            {
                url: '../../sis_parametros/control/Gestion/listarGestion',
                id: 'id_gestion',
                root: 'datos',
                sortInfo: {
                    field: 'gestion',
                    direction: 'DESC'
                },
                totalProperty: 'total',
                fields: ['id_gestion', 'gestion'],                
                remoteSort: true,
                baseParams: {par_filtro: 'gestion'}
            }),
        valueField: 'id_gestion',
        triggerAction: 'all',
        displayField: 'gestion',
        hiddenName: 'id_gestion',
        mode: 'remote',
        pageSize: 5,
        queryDelay: 500,
        listWidth: '280',
        hidden: false,
        width: 80
    }),
    capturarEventos: function () {
        this.store.baseParams.id_gestion=this.cmbGestion.getValue();
        this.load({params:{start:0, limit:this.tam_pag}});
    },
    
    iniciarEventos : function () {    	    	
	Phx.vista.presupuesto_multi_fun.superclass.iniciarEventos.call();
		
	this.getComponente('id_gestion').on('select',function(c,r,n){																 		     	
    this.Cmp.id_presupuesto.store.baseParams.id_gestion=r.data.id_gestion;    	        
    this.Cmp.id_presupuesto.store.reload();     
	this.Cmp.id_presupuesto.store.baseParams.id_gestion=r.data.id_gestion;			    	          		    		    		    		    					    		    		 																												
	 },this);	
		 		             					
	},       
       Atributos:[
       		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_funcionario'
			},
			type:'Field',
			form:true 
			},
       		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_presupuesto_funcionario'
			},
			type:'Field',
			form:true 
			},			
/*
           {
                //configuracion del componente
                config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'id_gestion',
                    gwidth: 50
                },
                type:'Field',
                grid: false,
                form:true
            },
            */
            {
                config:{
                    name: 'id_gestion',
                    fieldLabel: 'Gestion',
                    allowBlank: false,
                    emptyText : '...',
			        store: new Ext.data.JsonStore(
			            {
			                url: '../../sis_parametros/control/Gestion/listarGestion',
			                id: 'id_gestion',
			                root: 'datos',
			                sortInfo: {
			                    field: 'gestion',
			                    direction: 'DESC'
			                },
			                totalProperty: 'total',
			                fields: ['id_gestion', 'gestion'],			                
			                remoteSort: true,
			                baseParams: {par_filtro: 'gestion'}
			            }),
                    valueField: 'id_gestion',
                    displayField: 'gestion',
                    gdisplayField: 'gestion',
                    hiddenName: 'id_gestion',
                    forceSelection:true,
                    typeAhead: true,
                    triggerAction: 'all',
                    lazyRender:true,
                    mode:'remote',
                    pageSize:10,
                    queryDelay:1000,
                    width: 150,
                    listWidth: 280,
                    gwidth: 50,
                    minChars:2,                    
                },
                type:'ComboBox',
                bottom_filter: true,
                filters:{pfiltro:'vcc.id_gestion#vcc.gestion',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
            },           
            {
                config:{
                    name: 'id_presupuesto',
                    fieldLabel: 'Presupuesto',
                    allowBlank: false,
                    emptyText : '...',
                    store : new Ext.data.JsonStore({
                        url:'../../sis_presupuestos/control/PresupuestoFuncionario/listarPresupuestoFun',
                        id : 'id_presupuesto',
                        root: 'datos',
                        sortInfo:{
                            field: 'id_presupuesto',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_presupuesto','descripcion','codigo_cc'],
                        remoteSort: true,
                        baseParams:{par_filtro:'codigo_cc'}
                    }),					                    
                    valueField: 'id_presupuesto',
                    displayField: 'codigo_cc',
                    gdisplayField: 'codigo_cc',
                    hiddenName: 'id_presupuesto',
                    forceSelection:true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender:true,
                    mode:'remote',
                    pageSize:10,
                    queryDelay:1000,
                    width: 300,                    
                    listWidth: 350,
                    gwidth: 300,
                    minChars:2,
                    enableMultiSelect:true,
                    tpl: new Ext.XTemplate([
                        '<tpl for=".">',
                        '<div class="x-combo-list-item">',
                        '<div class="awesomecombo-item {checked}">',                        
                        '</div><p><b>Presupuesto:</b></p><p><span style="color: black;">{codigo_cc}</span></p>',
                        '</div></tpl>'
                    ]),                    
                    renderer:function(value, p, record){
                    	return String.format('{0}', record.data['codigo_cc']);
                    },                    
                },
                type:'AwesomeCombo',
                bottom_filter: true,
                filters:{pfiltro:'vp.codigo_cc',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
            },            
			/*{
				config:{
					sysorigen: 'sis_presupuestos',
					name: 'id_presupuesto',
					fieldLabel: 'Presupuesto ID',
					allowBlank: true,
					tinit: false,
					baseParams: {_adicionar:'si'},
					origen: 'PRESUPUESTO',
					width: 350,
					listWidth: 350,
					gwidth: 120
				},
				type: 'ComboRec',
				id_grupo: 0,
				grid: true,
				form: true
			},*/
			/*{
				config:{
					name: 'descripcion',
					fieldLabel: 'Presupuesto',
					allowBlank: true,
					anchor: '80%',
					gwidth: 200,
					maxLength:4
				},
				type:'TextField',
				filters:{pfiltro:'p.descripcion',type:'string'},
				id_grupo:1,
				bottom_filter: true,
				grid:true,
				form:false
			},*/			
           /* {
                config:{
                    name:'id_tipo_cc',
                    qtip: 'Tipo de centro de costos, cada tipo solo puede tener un centro por gestión',
                    origen:'TIPOCC',
                    fieldLabel:'Tipo Centro',
                    gdisplayField: 'desc_tcc',
                    allowBlank:false,
                    width:350,
                    gwidth:200

                },
                type:'ComboRec',
                id_grupo:0,
                filters:{pfiltro:'vcc.codigo_tcc#vcc.descripcion_tcc',type:'string'},
                grid:true,
                form:true
            },*/
            {
                config:{
                    name: 'nro_tramite',
                    fieldLabel: 'Nro Tramite',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 150,
                    maxLength:30
                },
                type:'TextField',
                filters:{pfiltro:'p.nro_tramite',type:'string'},
                id_grupo:1,
                bottom_filter: true,
                grid: true,
                form: false
            },              
			{
				config:{
					name: 'accion',
					fieldLabel: 'Acciones',
					allowBlank: false,
					triggerAction:"all",
					forceSelection:true,
	                typeAhead: false,
					store: ['formulacion', 'aprobacion', 'responsable'],
					anchor: '80%',
					mode:'local',
					gwidth: 100
				},
					type:'ComboBox',
					filters:{pfiltro:'p.estado_reg',type:'string'},
					id_grupo:1,
					grid:true,
					form:true					
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
                filters:{pfiltro:'p.estado_reg',type:'string'},
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
                type:'NumberField',
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
                    anchor: '85%',
                    gwidth: 110,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
                },
                type:'DateField',
                filters:{pfiltro:'p.fecha_reg',type:'date'},
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
                filters:{pfiltro:'p.fecha_mod',type:'date'},
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
                type:'NumberField',
                filters:{pfiltro:'usu2.cuenta',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            }
           
        ],

        title:'Presupuesto',
		ActSave:'../../sis_presupuestos/control/PresupuestoFuncionario/insertarPresupuestoFun',
		ActDel:'../../sis_presupuestos/control/PresupuestoFuncionario/eliminarPresupuestoFuncionario',
        ActList:'../../sis_presupuestos/control/PresupuestoFuncionario/listarFuncionarioPresupuesto',
        id_store:'id_presupuesto_funcionario',
        fields: [
            { name:'id_presupuesto', type: 'numeric'},            
            { name:'estado_reg', type: 'string'},
            { name:'accion', type: 'string'},                                               
            { name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
            { name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},            
            { name:'usr_reg', type: 'string'},
            { name:'usr_mod', type: 'string'},                                                  
            { name:'id_funcionario',type:'numeric'},
            { name:'id_tipo_cc', type:'numeric'},
            { name:'desc_tcc', type:'string'},
            { name:'descripcion',type:'string'},
            { name:'id_gestion',type:'numeric'},
            { name:'nro_tramite',type:'string'},
            { name:'gestion',type:'numeric'},
            { name:'codigo_cc',type:'string'},
            { name:'id_presupuesto_funcionario',type:'numeric'}
                                   

        ],
        //bnew: true,
        bedit: true,
        bdel: true,
        bsave: false,

        sortInfo:{
            field: 'id_presupuesto',
            direction: 'ASC'
        },

        fheight: '68%',
        fwidth: '55%',
   
	onReloadPage:function(m){				
		this.maestro=m;									
		this.Cmp.id_presupuesto.store.baseParams.id_funcionario=this.maestro.id_funcionario;				
		this.store.baseParams = {id_funcionario: this.maestro.id_funcionario};
		this.load({ params: {start:0, limit:50 } });
		
	},
	loadValoresIniciales:function(){		
		Phx.vista.presupuesto_multi_fun.superclass.loadValoresIniciales.call(this);
		this.getComponente('id_funcionario').setValue(this.maestro.id_funcionario);				
	},
	onButtonNew:function(){	    
	  Phx.vista.presupuesto_multi_fun.superclass.onButtonNew.call(this);	  	 
	},		
	
	    	
   });
</script>        