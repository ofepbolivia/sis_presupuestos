<?php
/**
 *@package PXP
 *@file    FormPartidaEjecutado.php
 *@author  Miguel Alejandro Mamani Villegas
 *@date    03-05-2017
 *@description Archivo con la interfaz para generaci�n de reporte
 */
header("content-type: text/javascript; charset=UTF-8");
?>

<script>
Phx.vista.FormPartidaEjecutado = Ext.extend(Phx.frmInterfaz,{
    Atributos : [
        {
                config:{
                    name: 'nombre',
                    fieldLabel: 'Nombre',
                    allowBlank: true,
                    //emptyText:'Nombre...',
                    //msgTarget: 'side',
                    editable: false,
                    store:new Ext.data.JsonStore(
                        {
                            url:'../../sis_parametros/control/Empresa/listarNombreEmpresa',
                            id: 'id_empresa',
                            root: 'datos',
                            sortInfo:{
                                field: 'nombre',
                                direction: 'DESC'
                            },
                            totalProperty: 'total',
                            fields: ['id_empresa','nombre'],
                            //turn on remote sorting
                            remoteSort: true,
                            baseParams: { par_filtro:'id_empresa#nombre' }
                        }),
                    valueField: 'id_empresa',
                    //displayField: 'nombre',
                    //gdisplayField:'nombre',
                    hiddenName: 'id_empresa',
                    triggerAction: 'all',
                    lazyRender:true,
                    mode:'remote',
                    pageSize:50,
                    queryDelay:500,
                    //anchor:"90%",
                    width: 280,
                    listWidth:280,
                    gwidth:250,
                    minChars:0,
                    visibility: false,
                   // renderer:function (value, p, record){return String.format('{0}', record.data['nombre']);}
                },
                type:'TextField',
                //filters:{pfiltro:'EMP.nombre',type:'string'},
                id_grupo:0,
                egrid:true,
                form:true,
                grid:true
            },
        {
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
                allowBlank:false,
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
                width : 250,
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
  
     //   {

     //           config: {
     //               name: 'nombre_empresa',
     //               fieldLabel: 'Empresa',
     //               typeAhead: true,
     //               allowBlank: true,

     //               triggerAction: 'all',
     //               emptyText: 'Tipo...',
     //               selectOnFocus: false,
     //               mode: 'local',
     //               store: new Ext.data.ArrayStore({
     //                   fields: ['ID', 'valor'],              
     //                   data: [['Gestora Pública de la Seguridad Social de Largo Plazo', 'Gestora Pública de la Seguridad Social de Largo Plazo']]
     //               }),
     //               valueField: 'ID',
     //               displayField: 'valor',
     //               width: 250,
     //           },
     //           type: 'ComboBox',
     //           id_grupo: 1,
     //           form: true

     //       },
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
                        ['presupuesto','Presupuesto']
                    ]
                }),
                valueField:'ID',
                displayField:'valor',
                width:250

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
                minChars:2,
                resizable:true,
                tpl:'<tpl for="."><div class="x-combo-list-item"><p style="color:green;"><b>{codigo_categoria}</b></p><p>{descripcion}</p> </div></tpl>'
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
                width: 250,
                minChars: 2,
                tpl:'<tpl for="."><div class="x-combo-list-item"><p>{codigo}-{descripcion}</p> </div></tpl>'
            },
            type: 'ComboBox',
            form: true
        },

       /*{
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
        },*/


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
                        ['0','Por Grupos y Partidas'],
                       // ['5','Solo una Partida'],
                        ['1','Hasta el Nivel 1'],
                        ['2','Hasta el Nivel 2'],
                        ['3','Hasta el Nivel 3']
                    ]
                }),
                valueField:'ID',
                displayField:'valor',
                width:250

            },
            type:'ComboBox',
            id_grupo:1,
            form:true
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
                listWidth: 250
            },
            type:'ComboRec',
            id_grupo:0,
            form:true
        },
        {
            config:{
                name: 'fecha_ini',
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
            config:{
                name: 'fecha_fin',
                fieldLabel: 'Hasta',
                allowBlank: false,
                format: 'd/m/Y',
                width: 150
            },
            type: 'DateField',
            id_grupo: 0,
            form: true
        },
        {
            config:{
                name:'tipo_movimiento',
                fieldLabel:'Tipo Movimiento',
                typeAhead: true,
                allowBlank:true,
                triggerAction: 'all',
                emptyText:'Tipo...',
                selectOnFocus:true,
               // enableMultiSelect : true,

                mode:'local',
                store:new Ext.data.ArrayStore({
                    fields: ['ID', 'valor'],
                    data :	[
                        ['todos','Todos'],
                        ['comprometido','Comprometido'],
                        ['ejecutado','Ejecutado']
                        ]
                }),
                valueField:'ID',
                displayField:'valor',
                width:250

            },
            type:'ComboBox',
            id_grupo:1,
            form:true
        }
    ],
    title : 'Reporte Partidas',
    ActSave : '../../sis_presupuestos/control/Partida/listarPartidaEjecutado',

    topBar : true,
    botones : false,
    labelSubmit : 'Generar',
    tooltipSubmit : '<b>Reporte Proyecto Presupeustario</b>',

    constructor:function (config) {
        Phx.vista.FormPartidaEjecutado.superclass.constructor.call(this,config);
        this.ocultarComponente(this.Cmp.id_categoria_programatica);
        this.ocultarComponente(this.Cmp.id_presupuesto);
        this.ocultarComponente(this.Cmp.id_cp_programa);
        this.ocultarComponente(this.Cmp.nombre);
        //this.ocultarComponente(this.Cmp.fecha_ini);
        //this.ocultarComponente(this.Cmp.id_partida);
        this.iniciarEventos();
        this.init();
    },

    iniciarEventos:function(){

        this.Cmp.nombre.store.load({params:{start:0, limit:1}, scope:this, callback: function (param,op,suc) {
        this.Cmp.nombre.setValue(param[0].data.nombre);
        this.Cmp.nombre.collapse();
         }});

        this.Cmp.nombre.on('select', function(combo,record,index){
        this.Cmp.nombre.setValue(record.data.nombre);
        },this)

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

            this.Cmp.id_partida.reset();
            this.Cmp.id_partida.store.baseParams.id_gestion = c.value;
            this.Cmp.id_partida.modificado=true;

            this.Cmp.fecha_ini.setValue('01/01/'+r.data.gestion);
            this.Cmp.fecha_fin.setValue('31/12/'+r.data.gestion);


        },this);


        this.Cmp.tipo_reporte.on('select',function(combo, record, index){
            console.log(record, index);

            this.Cmp.id_categoria_programatica.reset();
            this.Cmp.id_presupuesto.reset();
            this.Cmp.id_cp_programa.reset();

            console.log('--->',record.data.ID);
            if(record.data.ID == 'programa'){
                this.ocultarComponente(this.Cmp.id_categoria_programatica);
                this.ocultarComponente(this.Cmp.id_presupuesto);
                this.mostrarComponente(this.Cmp.id_cp_programa);

            }

            if(record.data.ID == 'categoria'){
                this.mostrarComponente(this.Cmp.id_categoria_programatica);
                this.ocultarComponente(this.Cmp.id_presupuesto);
                this.ocultarComponente(this.Cmp.id_cp_programa);

            }

            if(record.data.ID == 'presupuesto'){
                this.ocultarComponente(this.Cmp.id_categoria_programatica);
                this.mostrarComponente(this.Cmp.id_presupuesto);
                this.ocultarComponente(this.Cmp.id_cp_programa);

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

        Phx.vista.FormPartidaEjecutado.superclass.onSubmit.call(this,o, x, force);
    }

   
})
</script>