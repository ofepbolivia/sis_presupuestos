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
            this.Grupos = [{

                xtype: 'fieldset',
                border: false,
                autoScroll: true,
                layout: 'form',
                items: [],
                id_grupo: 0

            },
                this.panelResumen
            ];

            Phx.vista.FormFiltro.superclass.constructor.call(this, config);
            this.init();

            this.ocultarComponente(this.Cmp.id_categoria_programatica);
            this.ocultarComponente(this.Cmp.id_presupuesto);

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
                            ['categoria', 'Categoría Programática'],
                            ['presupuesto', 'Presupuesto']
                        ]
                    }),
                    valueField: 'ID',
                    displayField: 'valor',
                    width: 150,

                },
                type: 'ComboBox',
                id_grupo: 1,
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
                    minChars: 2,
                    tpl: '<tpl for="."><div class="x-combo-list-item"><p>{codigo_categoria}</p><p>{descripcion}</p> </div></tpl>'
                },
                type: 'ComboBox',
                id_grupo: 1,
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
            }

        ],
        labelSubmit: '<i class="fa fa-check"></i> Aplicar Filtro',
        east: {
            url: '../../../sis_presupuestos/vista/partida_ejecucion/PartidaEjecucion.php',
            title: 'Detalle Ejecucion',
            width: '70%',
            cls: 'PartidaEjecucion'
        },
        /*south: {
        url: '../../../sis_presupuestos/vista/partida_ejecucion/PartidaEjecucion.php',
        title: 'Detalle Ejecucion',
        height: '70%',
        cls: 'PartidaEjecucion'
        },*/

        title: 'Filtros Para el Reporte de Ejecución',
        // Funcion guardar del formulario
        onSubmit: function () {
            var me = this;
            if (me.form.getForm().isValid()) {

                var parametros = me.getValForm()

                console.log('parametros ....', parametros);

                this.onEnablePanel(this.idContenedor + '-east', parametros)
            }
            //
            if (this.Cmp.tipo_reporte.getValue() == 'categoria') {
                this.Cmp.concepto.setValue(this.Cmp.id_categoria_programatica.getRawValue());
            }

            if (this.Cmp.tipo_reporte.getValue() == 'presupuesto') {
                this.Cmp.concepto.setValue(this.Cmp.id_presupuesto.getRawValue());
            }

            //console.log('datos de lo paramtros',o,x,y)
            Phx.vista.FormFiltro.superclass.onSubmit.call(this, o);
            //Phx.vista.FormFiltro.superclass.onSubmit.call(this, o, x, force);

        },
        iniciarEventos: function () {
            this.Cmp.id_gestion.on('select', function (cmb, rec, ind) {

                //Ext.apply(this.Cmp.id_cuenta.store.baseParams,{id_gestion: rec.data.id_gestion})
                Ext.apply(this.Cmp.id_partida.store.baseParams, {id_gestion: rec.data.id_gestion})
                //Ext.apply(this.Cmp.id_centro_costo.store.baseParams,{id_gestion: rec.data.id_gestion})
                Ext.apply(this.Cmp.id_presupuesto.store.baseParams, {id_gestion: rec.data.id_gestion})
                Ext.apply(this.Cmp.id_categoria_programatica.store.baseParams, {id_gestion: rec.data.id_gestion})

                this.Cmp.id_categoria_programatica.reset();
                this.Cmp.id_categoria_programatica.store.baseParams.id_gestion = cmb.value;
                this.Cmp.id_categoria_programatica.modificado = true;

                this.Cmp.id_presupuesto.reset();
                this.Cmp.id_presupuesto.store.baseParams.id_gestion = cmb.value;
                this.Cmp.id_presupuesto.modificado = true;

            }, this);


            this.Cmp.tipo_reporte.on('select', function (combo, record, index) {
                console.log(record, index)
                console.log('si', index)

                this.Cmp.id_categoria_programatica.reset();
                this.Cmp.id_presupuesto.reset();


                console.log('acaaaa', record.data.ID)


                if (record.data.ID == 'categoria') {
                    this.mostrarComponente(this.Cmp.id_categoria_programatica);
                    this.ocultarComponente(this.Cmp.id_presupuesto);
                }

                if (record.data.ID == 'presupuesto') {
                    this.ocultarComponente(this.Cmp.id_categoria_programatica);
                    this.mostrarComponente(this.Cmp.id_presupuesto);
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