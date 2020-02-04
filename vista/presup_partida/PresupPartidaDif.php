<?php
/**
 *@package pXP
 *@file    PresupPartidaDif.php
 *@author  BVP
 *@date    
 *@description Archivo con la interfaz para generaci�n de reporte
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.PresupPartidaDif = Ext.extend(Phx.frmInterfaz, {
		
		Atributos : [
		
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'concepto_partida'
			},
			type:'Field',
			form:true 
        },
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
					name: 'categoria'
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
                listWidth:250,
                resizable:true,
                width: 250
                
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
            	sysorigen: 'sis_presupuestos',
                name: 'id_presupuesto',
                fieldLabel: 'Presupuesto',
                allowBlank: false,
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
   			config:{
   				sysorigen:'sis_presupuestos',
       		    name:'id_partida',
   				origen:'PARTIDA',
   				allowBlank:true,
   				fieldLabel:'Partida',
   				gdisplayField:'desc_partida',//mapea al store del grid
   				baseParams: {_adicionar:'si',sw_transaccional: 'movimiento', partida_tipo: 'presupuestaria'},
                width: 250,
   				listWidth: 350
       	     },
   			type:'ComboRec',
   			id_grupo:0,
   			form:true
	   	},        
           {
                config: {
                    name: 'desde',
                    fieldLabel: 'Desde',
                    allowBlank: true,
                    format: 'd/m/Y',
                    width: 150
                },
                type: 'DateField',
                id_grupo: 0,
                form: true
            },
            {
                config: {
                    name: 'hasta',
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
                config: {
                    name: 'comprometido',
                    allowBlank: true,
                    fieldLabel: 'Comprometido',
                    width: 150,                    
                    border: false,
                    style:'text-align:center;font-size:15px;'                    
                },
                type: 'Field',
                id_grupo: 1,
                form: true
            },
            {
                config: {
                    name: 'ejecutado',
                    allowBlank: true,
                    fieldLabel: 'Ejecutado',
                    width: 150,                    
                    border: false,
                    style:'text-align:center;font-size:15px;'                    
                },
                type: 'Field',
                id_grupo: 1,
                form: true
            },
            {
                config: {
                    name: 'pagado',
                    allowBlank: true,
                    fieldLabel: 'Pagado',
                    width: 150,                    
                    border: false,
                    style:'text-align:center;font-size:15px;'
                },
                type: 'Field',
                id_grupo: 1,
                form: true
            },            
        ],
		
		topBar : true,
		botones : false,
		labelSubmit: 'Aplicar Filtro',
		tooltipSubmit : '<b>Reporte Proyecto Presupeustario</b>',
        tabsouth: [        
            {
                url: '../../../sis_presupuestos/vista/presup_partida/DetallePresParDif.php',
                title: 'Detalle Diferencias',
                width: '70%',
                height: '71%',
                cls: 'DetallePresParDif'
            },
        ],		
		constructor : function(config) {
			Phx.vista.PresupPartidaDif.superclass.constructor.call(this, config);
			this.init();            			
			this.iniciarEventos();
		},
		clean: (id, c) => {            
            id.reset();
            id.store.baseParams.id_gestion =c.value;				
            id.modificado=true;            
        },		
		iniciarEventos:function(){        
			
			this.Cmp.id_gestion.on('select',function(c,r,n){
							
                this.clean(this.Cmp.id_presupuesto, c);
                this.clean(this.Cmp.id_partida, c);
					
                this.Cmp.desde.setValue('01/01/'+r.data.gestion);
                this.Cmp.hasta.setValue('31/12/'+r.data.gestion);                
				
			},this);
			
			this.Cmp.id_partida.on('select',function(c,r,n){				 
                 
				 if (r.data.nombre_partida == 'Todos'){
				 	this.Cmp.concepto_partida.setValue('Todas');
				 }
				 else{
				 	this.Cmp.concepto_partida.setValue('('+r.data.codigo +') '+r.data.nombre_partida);
				 }				 
				
			},this);
		},						
		tipo : 'reporte',
		clsSubmit : 'bprint',        
		Grupos : [{
			layout : 'column',
			items : [{
				xtype : 'fieldset',
				layout : 'form',
				border : true,
				title : 'Datos para el Filtro',
				bodyStyle : 'padding:0 10px 0;',
				columnWidth : '500px',
				items : [],
				id_grupo : 0,
				collapsible : true
			},
            {
                xtype : 'fieldset',
                layout : 'form',
                border : true,
                title : 'Datos Partida Ejcucion',
                bodyStyle : 'padding:0 10px 0;',
                columnWidth : '500px',
                items : [],
                id_grupo : 1,
                collapsible : true
            }]
		}],			

        listener: function(data) {                                 
            Ext.Ajax.request({
                        url: '../../sis_presupuestos/control/PartidaEjecucion/totalDetallePresupuesto',
                        params: {
                                id_presupuesto: data.id_presupuesto,
                                id_partida : data.id_partida,
                                id_gestion : data.id_gesion                                
                            },
                        success: function (resp) {                            
                            var reg = Ext.decode(Ext.util.Format.trim(resp.responseText));                                                        
                            this.Cmp.comprometido.setValue( String.format('{0}', Ext.util.Format.number(reg.datos[0].comprometido,'0.000,00/i')));
                            this.Cmp.ejecutado.setValue( String.format('{0}', Ext.util.Format.number(reg.datos[0].ejecutado,'0.000,00/i')));
                            this.Cmp.pagado.setValue( String.format('{0}', Ext.util.Format.number(reg.datos[0].pagado,'0.000,00/i')));                            
                        },
                        failure: this.conexionFailure,
                        timeout: this.timeout,
                        scope: this
                    });            
        },
	onSubmit: function(o, x, force){
        var me = this;
            if (me.form.getForm().isValid()) {

                var parametros = me.getValForm()

                console.log('parametros ....', parametros);

                //this.onEnablePanel(this.idContenedor + '-south', parametros)
                this.onEnablePanel(this.TabPanelSouth.getActiveTab().getId(), parametros);
            }
        this.listener(parametros);                        
	}
})
</script>