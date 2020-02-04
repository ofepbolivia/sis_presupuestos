<?php
/**
 * @package pXP
 * @file    FormFiltro.php
 * @author  Grover Velasquez Colque
 * @date    30-10-2016
 * @description permite filtrar varios campos antes de mostrar el contenido de una grilla
 */
header("content-type: text/javascript; charset=UTF-8");
?>

<script>
    Phx.vista.FormFiltro = Ext.extend(Phx.frmInterfaz, {        
        constructor: function (config) {            
            this.panelResumen = new Ext.Panel({html: ''});  
            this.Grupos = [
                {
                    xtype: 'fieldset',
                    border: false,
                    autoScroll: true,
                    layout: 'form',
                    items: [],
                    id_grupo: 0
                },
                {
                    html:'<div style="font-style:bold;color:blue;text-align:center;height:2px;"></div>'
                },
                {
                    xtype: 'fieldset',
                    border: false,
                    autoScroll: true,
                    layout: 'form',
                    items: [],
                    id_grupo: 1                   
                },                
                this.panelResumen
            ];

            Phx.vista.FormFiltro.superclass.constructor.call(this, config);
            this.init();

            this.ocultarComponente(this.Cmp.id_categoria_programatica);
            this.ocultarComponente(this.Cmp.id_presupuesto);
            this.ocultarComponente(this.Cmp.id_cp_actividad);
            this.ocultarComponente(this.Cmp.id_cp_fuente_fin);
            this.ocultarComponente(this.Cmp.id_cp_organismo_fin);
            this.ocultarComponente(this.Cmp.id_unidad_ejecutora);    
            this.iniciarEventos();


        },

        Atributos: [

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
                config: {
                    name: 'id_gestion',
                    origen: 'GESTION',
                    fieldLabel: 'Gestion',
                    allowBlank: false,
                    width: 150
                },
                type: 'ComboRec',
                id_grupo: 0,
                form: true
            },
            {
                config: {
                    name: 'tipo_reporte',
                    fieldLabel: 'Filtrar por',
                    typeAhead: true,
                    allowBlank: false,

                    triggerAction: 'all',
                    emptyText: 'Tipo...',
                    selectOnFocus: false,
                    mode: 'local',
                    store: new Ext.data.ArrayStore({
                        fields: ['ID', 'valor'],              
                        data: [
                            ['actividad', 'Actividad'],
                            ['categoria', 'Categoría Programática'],
                            ['fuente_financ', 'Fuente Financiamiento'],
                            ['orga_financ', 'Organismo Financiador'],
                            ['presupuesto', 'Presupuesto'],
                            ['unidad_ejecutora', 'Unidad Ejecutora']
                        ]
                    }),
                    valueField: 'ID',
                    displayField: 'valor',
                    width: 150,

                },
                type: 'ComboBox',
                id_grupo: 0,
                form: true
            },

            {
                config: {
                    name: 'id_categoria_programatica',
                    fieldLabel: 'Categoría Programática',
                    //qtip: 'la categoria programatica permite la integración de reportes para sigma',
                    allowBlank: false,
                    emptyText: '...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_presupuestos/control/CategoriaProgramatica/listarCategoriaProgramatica',
                        id: 'id_categoria_programatica',
                        root: 'datos',
                        sortInfo: {field: 'codigo_categoria', direction: 'ASC'},
                        totalProperty: 'total',
                        fields: ['codigo_categoria', 'id_categoria_programatica', 'descripcion'],
                        remoteSort: true,
                        baseParams: {par_filtro: 'descripcion#codigo_categoria', _adicionar: 'si'}
                    }),
                    valueField: 'id_categoria_programatica',
                    displayField: 'codigo_categoria',
                    gdisplayField: 'codigo_categoria',
                    hiddenName: 'id_categoria_programatica',
                    forceSelection: true,
                    typeAhead: true,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    pageSize: 10,
                    queryDelay: 1000,
                    width: 150,
                    listWidth: 280,
                    resizable:true,
                    minChars: 2,                    
                    tpl: '<tpl for="."><div class="x-combo-list-item"><p style="color:green;"><b>{codigo_categoria}</b></p><p>{descripcion}</p> </div></tpl>'
                },
                type: 'ComboBox',
                id_grupo: 0,
                form: true
            },
            {
                config: {
                    sysorigen: 'sis_presupuestos',
                    name: 'id_presupuesto',
                    fieldLabel: 'Presupuesto',
                    allowBlank: true,
                    emptyText: '...',
                    tinit: false,
                    baseParams: {_adicionar: 'si'},
                    origen: 'PRESUPUESTO',
                    //origen: 'CENTROCOSTO',
                    width: 150,
                    listWidth: 350
                },
                type: 'ComboRec',
                id_grupo: 0,
                form: true
            },

            // {
            //     config:{
            //         name: 'id_centro_costo',
            //         fieldLabel: 'Presupuesto',
            //         allowBlank: true,
            //         tinit: false,
            //         origen: 'CENTROCOSTO',
            //         gdisplayField: 'desc_centro_costo',
            //         width: 150
            //     },
            //     type: 'ComboRec',
            //     id_grupo: 0,
            //     form: true
            // },
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
                    pageSize: 10,
                    queryDelay: 1000,                    
                    width: 150,
                    listWidth: 280,
                    resizable:true,
                    minChars: 2,                    
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
                    pageSize: 10,
                    queryDelay: 1000,
                    width: 150,
                    listWidth: 280,
                    resizable:true,
                    minChars: 2,                    
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
                    pageSize: 10,
                    queryDelay: 1000,
                    width: 150,
                    listWidth: 280,
                    resizable:true,
                    minChars: 2,                    
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
                    pageSize: 10,
                    queryDelay: 1000,
                    width: 150,
                    listWidth: 280,
                    resizable:true,
                    minChars: 2,                    
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
                config: {
                    sysorigen: 'sis_presupuestos',
                    name: 'id_partida',
                    origen: 'PARTIDA',
                    allowBlank: true,
                    fieldLabel: 'Partida',
                    width: 150
                },
                type: 'ComboRec',
                id_grupo: 0,
                form: true
            },
            {
                config: {
                    name: 'nro_tramite',
                    allowBlank: true,
                    fieldLabel: 'Nro. de Trámite',
                    width: 150
                },
                type: 'Field',
                id_grupo: 0,
                form: true
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
                    name: 'total_comprometido',
                    allowBlank: true,
                    fieldLabel: 'TOTAL COMPROMETIDO',
                    width: 180,
                    height: 50,
                    border: false,
                    disabled:true,
                    style:'text-align:center;font-size:15px;font-weight:bold;color:blue;'                    
                },
                type: 'Field',
                id_grupo: 1,
                form: true
            },
            {
                config: {
                    name: 'total_ejecutado',
                    allowBlank: true,
                    fieldLabel: 'TOTAL DEVENGADO',
                    width: 180,
                    height: 50,
                    border: false,
                    disabled:true,
                    style:'text-align:center;font-size:15px;font-weight:bold;color:blue;'                    
                },
                type: 'Field',
                id_grupo: 1,
                form: true
            },
            {
                config: {
                    name: 'total_pagado',
                    allowBlank: true,
                    fieldLabel: 'TOTAL PAGADO',
                    width: 180,
                    height: 50,
                    border: false,
                    disabled:true,
                    style:'text-align:center;font-size:15px;font-weight:bold;color:blue;'
                },
                type: 'Field',
                id_grupo: 1,
                form: true
            },
            {
                config: {
                    name: 'total_devengar',
                    allowBlank: true,
                    fieldLabel: 'SALDO POR DEVENGAR',
                    width: 180,
                    height: 50,
                    border: false,
                    disabled:true,
                    style:'text-align:center;font-size:15px;font-weight:bold;color:blue;'                    
                },
                type: 'Field',
                id_grupo: 1,
                form: true
            }                                                
        ],
        labelSubmit: '<i class="fa fa-check"></i> Aplicar Filtro',
        tabeast: [
        //east:
            {
                url: '../../../sis_presupuestos/vista/partida_ejecucion/DetallePartidaEjecucion.php',
                title: 'Consolidado Ejecucion',
                width: '70%',
                cls: 'DetallePartidaEjecucion'
            },         
        //east:    
            {            
                url: '../../../sis_presupuestos/vista/partida_ejecucion/PartidaEjecucion.php',
                title: 'Detalle Ejecucion',
                width: '70%',
                cls: 'PartidaEjecucion'
            },
        ],

        title: 'Filtros Para el Reporte de Ejecución',
        // Funcion guardar del formulario
        listener: function(data) {                        
            Ext.Ajax.request({
                        url: '../../sis_presupuestos/control/PartidaEjecucion/totalPartidaEjecucion',
                        params: {
                                desde: data.desde,
                                hasta: data.hasta,
                                nro_tramite: data.nro_tramite,
                                id_categoria_programatica: data.id_categoria_programatica,
                                id_cp_actividad: data.id_cp_actividad,
                                id_cp_fuente_fin: data.id_cp_fuente_fin,
                                id_cp_organismo_fin: data.id_cp_organismo_fin,                               
                                id_partida: data.id_partida,
                                id_presupuesto: data.id_presupuesto,
                                id_unidad_ejecutora: data.id_unidad_ejecutora                                
                            },
                        success: function (resp) {                            
                            var reg = Ext.decode(Ext.util.Format.trim(resp.responseText));
                            this.Cmp.total_comprometido.setValue( String.format('{0}', Ext.util.Format.number(reg.datos[0].comprometido,'0.000,00/i')));
                            this.Cmp.total_ejecutado.setValue( String.format('{0}', Ext.util.Format.number(reg.datos[0].ejecutado,'0.000,00/i')));
                            this.Cmp.total_pagado.setValue( String.format('{0}', Ext.util.Format.number(reg.datos[0].pagado,'0.000,00/i')));                            
                            this.Cmp.total_devengar.setValue( String.format('{0}', Ext.util.Format.number(reg.datos[0].devengar,'0.000,00/i')));
                        },
                        failure: this.conexionFailure,
                        timeout: this.timeout,
                        scope: this
                    });            
        },
        onSubmit: function () {
            var me = this;
            if (me.form.getForm().isValid()) {

                var parametros = me.getValForm()

                console.log('parametros ....', parametros);

                //this.onEnablePanel(this.idContenedor + '-east', parametros)
                this.onEnablePanel(this.TabPanelEast.getActiveTab().getId(), parametros);
            }
            //
            switch (this.Cmp.tipo_reporte.getValue()) {                
                case 'categoria'  : this.Cmp.concepto.setValue(this.Cmp.id_categoria_programatica.getRawValue()); break;
                case 'presupuesto': this.Cmp.concepto.setValue(this.Cmp.id_presupuesto.getRawValue()); break;
                case 'actividad'  : this.Cmp.concepto.setValue(this.Cmp.id_cp_actividad.getRawValue()); break;
                case 'actividad'  : this.Cmp.concepto.setValue(this.Cmp.id_cp_fuente_fin.getRawValue()); break;
                case 'orga_financ': this.Cmp.concepto.setValue(this.Cmp.id_cp_organismo_fin.getRawValue());break;
                case 'unidad_ejecutora': this.Cmp.concepto.setValue(this.Cmp.id_unidad_ejecutora.getRawValue());break;
            }
            this.listener(parametros);
            //console.log('datos de lo paramtros',o,x,y)
            //Phx.vista.FormFiltro.superclass.onSubmit.call(this, o);
            //Phx.vista.FormFiltro.superclass.onSubmit.call(this, o, x, force);

        },
        appFil : (id, c, r) => {
            if(c != null ){
                id.reset();
                id.store.baseParams.id_gestion = c.value;
                id.modificado = true;
            }else{
                Ext.apply(id.store.baseParams, {id_gestion: r.data.id_gestion});                
            }
        },
        iniciarEventos: function () {            
            this.Cmp.id_gestion.on('select', function (cmb, rec, ind) {                
                
                this.appFil(this.Cmp.id_presupuesto, null ,rec);
                this.appFil(this.Cmp.id_partida, null ,rec);
                this.appFil(this.Cmp.id_categoria_programatica, null ,rec);
                this.appFil(this.Cmp.id_cp_actividad, null ,rec);
                this.appFil(this.Cmp.id_cp_fuente_fin, null ,rec);
                this.appFil(this.Cmp.id_cp_organismo_fin, null ,rec);
                this.appFil(this.Cmp.id_unidad_ejecutora, null ,rec);

                this.appFil(this.Cmp.id_categoria_programatica, cmb, rec);
                this.appFil(this.Cmp.id_presupuesto, cmb, rec);
                this.appFil(this.Cmp.id_cp_actividad, cmb, rec);
                this.appFil(this.Cmp.id_cp_fuente_fin, cmb, rec);
                this.appFil(this.Cmp.id_cp_organismo_fin, cmb, rec);
                this.appFil(this.Cmp.id_unidad_ejecutora, cmb, rec);                

            }, this);


            this.Cmp.tipo_reporte.on('select', function (combo, record, index) {
                this.Cmp.id_categoria_programatica.reset();
                this.Cmp.id_presupuesto.reset();
                this.Cmp.id_cp_actividad.reset();
                this.Cmp.id_cp_fu
                

                switch (record.data.ID) {
                    case 'categoria':                        
                        this.ocultarComponente(this.Cmp.id_cp_actividad);
                        this.ocultarComponente(this.Cmp.id_presupuesto);
                        this.ocultarComponente(this.Cmp.id_cp_fuente_fin); 
                        this.ocultarComponente(this.Cmp.id_cp_organismo_fin);
                        this.ocultarComponente(this.Cmp.id_unidad_ejecutora);
                        this.mostrarComponente(this.Cmp.id_categoria_programatica);
                        break;
                    case 'presupuesto':
                        this.ocultarComponente(this.Cmp.id_cp_actividad);                    
                        this.ocultarComponente(this.Cmp.id_categoria_programatica);
                        this.ocultarComponente(this.Cmp.id_cp_fuente_fin);
                        this.ocultarComponente(this.Cmp.id_cp_organismo_fin);
                        this.ocultarComponente(this.Cmp.id_unidad_ejecutora);
                        this.mostrarComponente(this.Cmp.id_presupuesto);
                        break;
                    case 'actividad':                    
                        this.ocultarComponente(this.Cmp.id_categoria_programatica);
                        this.ocultarComponente(this.Cmp.id_presupuesto);
                        this.ocultarComponente(this.Cmp.id_cp_fuente_fin);
                        this.ocultarComponente(this.Cmp.id_cp_organismo_fin);
                        this.ocultarComponente(this.Cmp.id_unidad_ejecutora);
                        this.mostrarComponente(this.Cmp.id_cp_actividad);
                        break;
                    case 'fuente_financ':     
                        this.ocultarComponente(this.Cmp.id_categoria_programatica);
                        this.ocultarComponente(this.Cmp.id_presupuesto);
                        this.ocultarComponente(this.Cmp.id_cp_actividad);
                        this.ocultarComponente(this.Cmp.id_cp_organismo_fin);
                        this.ocultarComponente(this.Cmp.id_unidad_ejecutora);
                        this.mostrarComponente(this.Cmp.id_cp_fuente_fin);                    
                        break;
                    case 'orga_financ': 
                        this.ocultarComponente(this.Cmp.id_categoria_programatica);
                        this.ocultarComponente(this.Cmp.id_presupuesto);
                        this.ocultarComponente(this.Cmp.id_cp_actividad);
                        this.ocultarComponente(this.Cmp.id_cp_fuente_fin);
                        this.ocultarComponente(this.Cmp.id_unidad_ejecutora);
                        this.mostrarComponente(this.Cmp.id_cp_organismo_fin);
                        break;
                    case 'unidad_ejecutora':
                        this.ocultarComponente(this.Cmp.id_categoria_programatica);
                        this.ocultarComponente(this.Cmp.id_presupuesto);
                        this.ocultarComponente(this.Cmp.id_cp_actividad);
                        this.ocultarComponente(this.Cmp.id_cp_fuente_fin);
                        this.ocultarComponente(this.Cmp.id_cp_organismo_fin);
                        this.mostrarComponente(this.Cmp.id_unidad_ejecutora);
                        break;
                }
            }, this);

            // this.Cmp.id_partida.on('change', function () {
            //
            //     this.Cmp.id_presupuesto.reset();
            //     this.Cmp.id_presupuesto.store.baseParams.codigos_id_partida = this.Cmp.id_partida.getValue();
            //     this.Cmp.id_presupuesto.modificado = true;
            //
            // }, this);
        },


    })
</script>