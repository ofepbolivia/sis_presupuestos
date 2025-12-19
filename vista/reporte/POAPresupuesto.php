<?php
/**
 * @package pXP
 * @file    AccionCortoPlazo.php
 * @author  Nataniel Molina
 * @date    20-11-2025 (Original)
 * @description Archivo con la interfaz para generación de reporte de ACP
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.POAPresupuesto = Ext.extend(Phx.frmInterfaz, {

        constructor: function (config) {
            Phx.vista.POAPresupuesto.superclass.constructor.call(this, config);
            this.init();
            this.iniciarEventos();
        },

        iniciarEventos: function () {
            // Evento 1: Al seleccionar una Gestión
            this.Cmp.id_gestion.on('select', function (cmb, rec) {
                const idGestion = rec.data.id_gestion;
            }, this);
        },

        Atributos: [
            {
                config: {
                    name: 'id_gestion',
                    hiddenName: 'id_gestion',
                    fieldLabel: 'Gestión',
                    allowBlank: false,
                    gwidth: 100,
                    store: new Ext.data.JsonStore({
                        url: '../../sis_parametros/control/Gestion/obtenerGestiones',
                        id: 'id_gestion',
                        root: 'datos',
                        fields: ['id_gestion', 'gestion'],
                        totalProperty: 'total',
                        sortInfo: {field: 'gestion', direction: 'DESC'}
                    }),
                    valueField: 'id_gestion',
                    displayField: 'gestion',
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    queryDelay: 1000,
                    minChars: 2,
                    anchor: '70%',
                },
                type: 'ComboBox',
                id_grupo: 0,
                form: true
            },
            {
                config: {
                    name: 'tipo_reporte',
                    fieldLabel: 'Formato del Reporte',
                    allowBlank: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'local',
                    store: new Ext.data.ArrayStore({
                        fields: ['tipo', 'valor'],
                        data: [['pdf', 'PDF'], ['excel', 'Excel']]
                    }),
                    anchor: '70%',
                    valueField: 'tipo',
                    displayField: 'valor',
                    value: 'pdf'
                },
                type: 'ComboBox',
                id_grupo: 0,
                form: true
            }
        ],
        title: 'Reportes Acción a Corto Plazo (POA)',
        ActSave: '../../sis_presupuestos/control/ProgramaOperacionesReporte/articulacionPoa',
        timeout: 3000000,

        topBar: true,
        botones: true,
        labelSubmit: 'Imprimir',
        tooltipSubmit: '<b>Estimado usuario</b><br>Elija los campos necesarios e imprima su reporte.',

        agregarArgsExtraSubmit: function () {
            this.argumentExtraSubmit.id_gestion = this.Cmp.id_gestion.getValue();
            this.argumentExtraSubmit.gestion = this.Cmp.id_gestion.getRawValue();
        },

        tipo: 'reporte',
        clsSubmit: 'bprint',

        Grupos: [{
            layout: 'column',
            labelAlign: 'top',
            border: false,
            autoScroll: true,
            items: [
                {
                    columnWidth: .5,
                    border: false,
                    layout: 'anchor',
                    autoScroll: true,
                    autoHeight: true,
                    collapseFirst: false,
                    collapsible: false,
                    anchor: '100%',
                    items: [
                        {
                            anchor: '100%',
                            bodyStyle: 'padding-right:5px;',
                            autoHeight: true,
                            border: false,
                            items: [
                                {
                                    xtype: 'fieldset',
                                    layout: 'form',
                                    border: true,
                                    title: 'Datos para el reporte',
                                    items: [],
                                    id_grupo: 0
                                }
                            ]
                        }
                    ]
                }
            ]
        }]
    });
</script>