<?php
/**
*@package pXP
*@file gen-CpProyecto.php
*@author  (admin)
*@date 19-04-2016 14:40:49
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.CpProyecto=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
		var me = this;
		me.maestro=config.maestro;
    	me.initButtons=[me.cmbGestion];
    	//llama al constructor de la clase padre
		Phx.vista.CpProyecto.superclass.constructor.call(this,config);
		this.cmbGestion.on('select', function(combo, record, index){
			this.capturaFiltros();
        },this);
		
		this.bloquearOrdenamientoGrid();
		this.init();
	},
			
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_cp_proyecto'
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
			config:{
				name: 'codigo',
				fieldLabel: 'codigo',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:100
			},
				type:'TextField',
				filters:{pfiltro:'cppr.codigo',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		
		{
			config:{
				name: 'codigo_sisin',
				fieldLabel: 'codigo_sisin',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:100
			},
				type:'TextField',
				filters:{pfiltro:'cppr.codigo_sisin',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'descripcion',
				fieldLabel: 'descripcion',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:500
			},
				type:'TextField',
				filters:{pfiltro:'cppr.descripcion',type:'string'},
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
				filters:{pfiltro:'cppr.estado_reg',type:'string'},
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
				filters:{pfiltro:'cppr.id_usuario_ai',type:'numeric'},
				id_grupo:1,
				grid:false,
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
				name: 'fecha_reg',
				fieldLabel: 'Fecha creación',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y', 
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
			},
				type:'DateField',
				filters:{pfiltro:'cppr.fecha_reg',type:'date'},
				id_grupo:1,
				grid:true,
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
				filters:{pfiltro:'cppr.usuario_ai',type:'string'},
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
				filters:{pfiltro:'cppr.fecha_mod',type:'date'},
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
		}
	],
	tam_pag:50,	
	title:'CP Proyecto', //fRnk: modificado título Poryecto
	ActSave:'../../sis_presupuestos/control/CpProyecto/insertarCpProyecto',
	ActDel:'../../sis_presupuestos/control/CpProyecto/eliminarCpProyecto',
	ActList:'../../sis_presupuestos/control/CpProyecto/listarCpProyecto',
	id_store:'id_cp_proyecto',
	fields: [
		{name:'id_cp_proyecto', type: 'numeric'},
		{name:'codigo', type: 'string'},
		{name:'id_gestion', type: 'numeric'},
		{name:'codigo_sisin', type: 'string'},
		{name:'descripcion', type: 'string'},
		{name:'estado_reg', type: 'string'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usuario_ai', type: 'string'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		
	],
	sortInfo:{
		field: 'id_cp_proyecto',
		direction: 'ASC'
	},
	
	cmbGestion: new Ext.form.ComboBox({
				fieldLabel: 'Gestion',
				allowBlank: false,
				emptyText:'Gestion...',
				blankText: 'Año',
				store:new Ext.data.JsonStore(
				{
					url: '../../sis_parametros/control/Gestion/listarGestion',
					id: 'id_gestion',
					root: 'datos',
					sortInfo:{
						field: 'gestion',
						direction: 'ASC'
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
	
	capturaFiltros:function(combo, record, index){
        this.desbloquearOrdenamientoGrid();
        if(this.validarFiltros()){
        	this.store.baseParams.id_gestion = this.cmbGestion.getValue();
	        this.load(); 
        }
        
    },
    
    validarFiltros:function(){
        if(this.cmbGestion.validate()){
            this.desbloquearOrdenamientoGrid();
            return true;
        }
        else{
            this.bloquearOrdenamientoGrid();
            return false;
        }
    },
    onButtonAct:function(){
    	if(!this.validarFiltros()){
            alert('Especifique los filtros antes')
        }
    },
    
     onButtonAct:function(){
        if(!this.validarFiltros()){
            alert('Especifique el año y el mes antes')
         }
        else{
            this.store.baseParams.id_gestion=this.cmbGestion.getValue();
            Phx.vista.CpProyecto.superclass.onButtonAct.call(this);
        }
    },
    
    onButtonNew:function(){
	    if(!this.validarFiltros()){
	        alert('Especifique el año y el mes antes')
	    }
	    else{
	      Phx.vista.CpProyecto.superclass.onButtonNew.call(this);
	      this.Cmp.id_gestion.setValue(this.cmbGestion.getValue());
	    } 
    },
	bdel: true,
	bsave: false
	}
)
</script>
		
		