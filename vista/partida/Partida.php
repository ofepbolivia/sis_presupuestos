<?php
/**
*@package pXP
*@file gen-Partida.php
*@author  (admin)
*@date 23-11-2012 20:06:53
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.Partida=Ext.extend(Phx.arbGridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
		this.initButtons=[this.cmbGestion,this.cmbTipo];
    	//llama al constructor de la clase padre
		Phx.vista.Partida.superclass.constructor.call(this,config);
		this.init();
		this.loaderTree.baseParams={id_gestion:0,tipo:'gasto'};
		
		this.cmbGestion.on('select',this.capturaFiltros,this);
		
		this.cmbTipo.on('select',this.capturaFiltros,this);
		this.addButton('btnImprimir',
			{
				text: 'Imprimir',
				iconCls: 'bprint',
				disabled: true,
				handler: this.imprimirCbte,
				tooltip: '<b>Imprimir Clasificador</b><br/>Imprime el clasificador en el formato oficial.'
			}
		);
		//Crea el botón para llamar a la replicación
		this.addButton('btnRepRelCon',
			{
				text: 'Duplicar Partidas',
				iconCls: 'bchecklist',
				disabled: false,
				handler: this.duplicarPartidas,
				tooltip: '<b>Clonar  las partidas para las gestión siguiente </b><br/>Clonar las partidas, para la gestión siguiente guardando las relacion entre las mismas'
			}
		);
		
	},
	
	duplicarPartidas: function(){
		if(this.cmbGestion.getValue()){
			Phx.CP.loadingShow(); 
	   		Ext.Ajax.request({
				url: '../../sis_presupuestos/control/Partida/clonarPartidasGestion',
			  	params:{
			  		id_gestion: this.cmbGestion.getValue()
			      },
			      success:this.successRep,
			      failure: this.conexionFailure,
			      timeout:this.timeout,
			      scope:this
			});
		}
		else{
			alert('Primero debe selecionar la gestion origen');
		}
   		
   },
   
   successRep:function(resp){
        Phx.CP.loadingHide();
        var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
        if(!reg.ROOT.error){
            this.reload();
            alert(reg.ROOT.datos.observaciones)
        }else{
            alert('Ocurrió un error durante el proceso')
        }
	},
	
	capturaFiltros:function(combo, record, index){
		
		this.loaderTree.baseParams={id_gestion:this.cmbGestion.getValue(),tipo:this.cmbTipo.getValue()};
		this.root.reload();
	},
    
    loadValoresIniciales:function()
	{
		Phx.vista.Partida.superclass.loadValoresIniciales.call(this);
		this.getComponente('id_gestion').setValue(this.cmbGestion.getValue());	
		this.getComponente('tipo').setValue(this.cmbTipo.getValue());	
	},
	
	imprimirCbte: function(){
		Phx.CP.loadingShow();
		Ext.Ajax.request({
						//url : '../../sis_contabilidad/control/IntComprobante/reporteComprobante',
						url : '../../sis_presupuestos/control/Partida/reporteClasificador', //Correcion de la ruta sis_presupuesto a sis_presupuestos
						params : {
							'id_gestion' : this.cmbGestion.getValue()
						},
						success : this.successExport,
						failure : this.conexionFailure,
						timeout : this.timeout,
						scope : this
					});
	},
	
			
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_partida'
			},
			type:'Field',
			form:true 
		},
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_gestion'
			},
			type:'Field',
			form:true 
		},
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'tipo'
			},
			type:'Field',
			form:true 
		},
		
		{
			config:{
				name: 'id_partida_fk',
				inputType:'hidden'
			},
			type:'Field',
			form:true
		},
		{
			config:{
				name: 'text',
				fieldLabel: 'Partida',
				allowBlank: false,
				anchor: '80%',
				gwidth: 400,
				maxLength:100
			},
			type:'TextField',
			id_grupo:1,
			grid:true,
			form:false
		},
		{
			config:{
				name: 'codigo',
				fieldLabel: 'Codigo',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:20
			},
			type:'TextField',
			filters:{pfiltro:'par.codigo',type:'string'},
			id_grupo:1,
			grid:true,
			form:true
		},{
            config:{
                name: 'nombre_partida',
                fieldLabel: 'Nombre',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:200
            },
            type:'TextField',
            filters:{pfiltro:'par.nombre_partida',type:'string'},
            id_grupo:1,
            grid:true,
            form:true
        },
        {
            config:{
                name: 'descripcion',
                fieldLabel: 'Descripcion',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:900
            },
            type:'TextArea',
            filters:{pfiltro:'par.descripcion',type:'string'},
            id_grupo:1,
            grid:true,
            form:true
        },
	       	{
	       		config:{
	       			name:'sw_movimiento',
	       			fieldLabel:'Movimiento',
	       			allowBlank:false,
	       			emptyText:'Tipo...',
	       			typeAhead: true,
	       		    triggerAction: 'all',
	       		    lazyRender:true,
	       		    mode: 'local',
	       		    gwidth: 100,
	       		    store:['presupuestaria','flujo']
	       		},
	       		type:'ComboBox',
	       		id_grupo:0,
	       		filters:{	
	       		         type: 'list',
	       		         pfiltro:'par.sw_movimiento',
	       				 options: ['fresupuestaria','flujo'],	
	       		 	},
	       		grid:true,
	       		form:true
	       	},
	       	{
	       		config:{
	       			name:'sw_transaccional',
	       			fieldLabel:'Tipo Partida',
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
	       		filters:{	
	       		         type: 'list',
	       		         pfiltro:'par.sw_transaccional',
	       				 options: ['movimiento','titular'],	
	       		 	},
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
			filters:{pfiltro:'par.estado_reg',type:'string'},
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
				anchor: '80%',
				gwidth: 100,
				renderer:function (value,p,record){return value?value.dateFormat('d/m/Y h:i:s'):''}
			},
			type:'DateField',
			filters:{pfiltro:'par.fecha_reg',type:'date'},
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
		},
		{
			config:{
				name: 'fecha_mod',
				fieldLabel: 'Fecha Modif.',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				renderer:function (value,p,record){return value?value.dateFormat('d/m/Y h:i:s'):''}
			},
			type:'DateField',
			filters:{pfiltro:'par.fecha_mod',type:'date'},
			id_grupo:1,
			grid:true,
			form:false
		}
	],
	title:'Partida',
	ActSave:'../../sis_presupuestos/control/Partida/insertarPartida',
	ActDel:'../../sis_presupuestos/control/Partida/eliminarPartida',
	ActList:'../../sis_presupuestos/control/Partida/listarPartidaArb',
	id_store:'id_partida',
	textRoot:'PARTIDAS',
    id_nodo:'id_partida',
    id_nodo_p:'id_partida_fk',
	fields: [
		'id',
        'tipo_meta',
		{name:'id_partida', type: 'numeric'},
		{name:'id_partida_fk', type: 'numeric'},
		{name:'tipo', type: 'string'},
		{name:'descripcion', type: 'string'},
		{name:'codigo', type: 'string'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_reg', type: 'date', dateFormat:'Y-m-d H:i:s'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date', dateFormat:'Y-m-d H:i:s'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},'id_gestion','sw_transaccional','sw_movimiento'
		
	],
	
	cmbGestion:new Ext.form.ComboBox({
				fieldLabel: 'Gestion',
				allowBlank: true,
				emptyText:'Gestion...',
				store:new Ext.data.JsonStore(
				{
					url: '../../sis_parametros/control/Gestion/listarGestion',
					id: 'id_gestion',
					root: 'datos',
					sortInfo:{
						field: 'gestion',
						direction: 'DESC'
					},
					totalProperty: 'total',
					fields: ['id_gestion','gestion'],
					// turn on remote sorting
					remoteSort: true,
					baseParams:{par_filtro:'gestion'}
				}),
				valueField: 'id_gestion',
				triggerAction: 'all',
				displayField: 'gestion',
			    hiddenName: 'id_gestion',
    			mode:'remote',
				pageSize:50,
				queryDelay:500,
				listWidth:'280',
				width:80
			}),
	
	cmbTipo:new Ext.form.ComboBox({
	       			name:'movimiento',
	       			fieldLabel:'Movimiento',
	       			allowBlank:false,
	       			emptyText:'Tipo...',
	       			typeAhead: true,
	       		    triggerAction: 'all',
	       		    lazyRender:true,
	       		    value:'gasto',
	       		    mode: 'local',
	       		    width: 70,
	       		    store:['recurso','gasto']
	       		}),
	sortInfo:{
		field: 'id_partida',
		direction: 'ASC'
	},
	bdel:true,
	bsave:false,
	rootVisible:true,
	expanded:false,
	
	onButtonNew:function(){
		if(this.cmbGestion.getValue()){
	        var nodo = this.sm.getSelectedNode();           
	        Phx.vista.Partida.superclass.onButtonNew.call(this);
	     }
	     else
	     {
	     	alert("Seleccione una gestion primero.");
	     	
	     }   
    },
    
    preparaMenu:function(n){
        if(n.attributes.tipo_nodo == 'hijo' || n.attributes.tipo_nodo == 'raiz' || n.attributes.id == 'id'){
            this.tbar.items.get('b-new-'+this.idContenedor).enable()
        }
        else {
            this.tbar.items.get('b-new-'+this.idContenedor).disable()
        }
        // llamada funcion clase padre
            Phx.vista.Partida.superclass.preparaMenu.call(this,n);
    },
    
    /*EnableSelect:function(n){
    	console.log('pasa...')
        var nivel = n.getDepth();
        var direc = this.getNombrePadre(n)
        
        console.log(direc)
        if(direc){            
            Phx.vista.Partida.superclass.EnableSelect.call(this,n)
        }        
    },
    
    getNombrePadre:function(n){
        var direc
        var padre = n.parentNode;
        if(padre){
            if(padre.attributes.id!='id'){
               direc = n.attributes.nombre +' - '+ this.getNombrePadre(padre)
               return direc;
            }else{
                
                return n.attributes.nombre;
            }
        }
        else{
                return undefined;
        }       
     }*/
}
)
</script>
		
		