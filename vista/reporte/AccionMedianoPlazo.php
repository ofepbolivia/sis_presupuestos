<?php
/**
 * @package pXP
 * @file    AccionMedianoPlazo.php
 * @author  Franklin Espinoza Alvarez / Mejorado por IA
 * @date    12-11-2025 (Original) / 18-11-2025 (Mejora)
 * @description Archivo con la interfaz para generación de reporte de POA
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.AccionMedianoPlazo = Ext.extend(Phx.frmInterfaz, {

        controlarSeleccionExclusiva: function (combo, valueField) {
            combo.on('select', function (cmb, rec) {
                const idTodos = '0';
                // Obtener SIEMPRE la selección REAL desde el combo.
                let valores = cmb.getValue().toString().split(',');
                const valorSeleccionado = rec.data[valueField].toString();

                // Caso 1: Si el usuario seleccionó "Todos los ítems"
                if (valorSeleccionado === idTodos) {
                    // Dejar solo "0"
                    cmb.setValue(idTodos);
                    // Forzar visualización correcta
                    cmb.setRawValue('Todos los ítems');
                    return;
                }

                // Caso 2: Si selecciona cualquier otro ítem y estaba "0"
                if (valores.includes(idTodos)) {
                    // Eliminar el "Todos los ítems"
                    valores = valores.filter(v => v !== idTodos);
                    // Establecer nuevamente la selección limpia
                    cmb.setValue(valores.join(','));
                }
            }, this);
        },

        constructor: function (config) {
            Phx.vista.AccionMedianoPlazo.superclass.constructor.call(this, config);
            this.init();
            this.iniciarEventos();
            this.configurarEstadoInicial();
        },

        // Define el estado inicial de los campos al cargar la interfaz
        configurarEstadoInicial: function () {
            // Inicialmente, se asume que el filtro por defecto es 'presupuesto' (Centro de Costo)
            // y que no se ha seleccionado nada en 'Gestión' ni 'Tipo de AMP'.

            // Ocultar Partida
            this.Cmp.id_partida.setVisible(false);
            this.Cmp.id_partida.allowBlank = true;

            // Ocultar Presupuesto (Se mostrará por defecto, pero se oculta si no hay 'Gestión')
            this.Cmp.id_presupuesto.setVisible(false);
            this.Cmp.id_presupuesto.allowBlank = true;

            // Si el valor por defecto de filtrar_por es 'presupuesto', lo mostramos al inicio
            if (this.Cmp.filtrar_por.getValue() === 'presupuesto') {
                this.Cmp.id_presupuesto.setVisible(true);
                this.Cmp.id_presupuesto.allowBlank = false;

                this.Cmp.id_presupuesto.setValue('0');
                this.Cmp.id_presupuesto.setRawValue('Todos los ítems');
            }

            this.Cmp.id_partida.setValue('0');
            this.Cmp.id_partida.setRawValue('Todos los ítems');
        },

        iniciarEventos: function () {
            // ---- SELECCIÓN EXCLUSIVA ----
            this.controlarSeleccionExclusiva(this.Cmp.tipo_amp, 'id_objetivo');
            this.controlarSeleccionExclusiva(this.Cmp.id_presupuesto, 'id_presupuesto');
            this.controlarSeleccionExclusiva(this.Cmp.id_partida, 'id_partida');

            // Evento 1: Al seleccionar una Gestión
            this.Cmp.id_gestion.on('select', function (cmb, rec) {
                const idGestion = rec.data.id_gestion;

                // 1. Resetear y configurar Partida
                this.Cmp.id_partida.store.baseParams.id_gestion = idGestion;
                this.Cmp.id_partida.reset();
                this.Cmp.id_partida.modificado = true;

                // 2. Resetear y configurar Presupuesto
                this.Cmp.id_presupuesto.store.baseParams.id_gestion = idGestion;
                this.Cmp.id_presupuesto.reset();
                this.Cmp.id_presupuesto.modificado = true;

                // 3. Configurar y Cargar Tipo de AMP
                this.Cmp.tipo_amp.store.baseParams.id_gestion = idGestion;
                this.Cmp.tipo_amp.reset();
                this.Cmp.tipo_amp.setVisible(true);
                this.Cmp.tipo_amp.allowBlank = false;
                this.Cmp.tipo_amp.store.load({params: {start: 0, limit: this.Cmp.tipo_amp.pageSize}});
            }, this);

            // Evento 2: Al seleccionar un Tipo de AMP (¡NUEVA LÓGICA CLAVE!)
            this.Cmp.tipo_amp.on('select', function (cmb, rec) {
                // El AwesomeCombo devuelve los IDs separados por coma (cadena)
                const idObjetivoString = this.Cmp.tipo_amp.getValue();
                const idGestion = this.Cmp.id_gestion.getValue();
                const tipoFiltro = this.Cmp.filtrar_por.getValue();

                // Si no hay gestión o objetivo, no hacemos nada
                if (!idGestion || !idObjetivoString) {
                    return;
                }

                if (tipoFiltro === 'presupuesto') {
                    // Cargar Presupuestos (Centro de Costo)
                    this.Cmp.id_presupuesto.store.baseParams.id_objetivo = idObjetivoString;
                    this.Cmp.id_presupuesto.reset();
                    this.Cmp.id_presupuesto.modificado = true;
                    this.Cmp.id_presupuesto.store.load({params: {start: 0, limit: this.Cmp.id_presupuesto.pageSize}});
                } else if (tipoFiltro === 'partida') {
                    // Cargar Partida
                    this.Cmp.id_partida.store.baseParams.id_objetivo = idObjetivoString;
                    this.Cmp.id_partida.reset();
                    this.Cmp.id_partida.modificado = true;
                    this.Cmp.id_partida.store.load({params: {start: 0, limit: this.Cmp.id_partida.pageSize}});
                }
            }, this);

            // Evento 3: Al seleccionar el tipo de filtro (Presupuesto o Partida)
            this.Cmp.filtrar_por.on('select', function (cmb, rec) {
                const tipoFiltro = rec.data.tipo_filtro;
                const idObjetivoString = this.Cmp.tipo_amp.getValue();

                if (tipoFiltro === 'presupuesto') {
                    // Mostrar Presupuestos (Centro de Costo)
                    this.Cmp.id_partida.setVisible(false);
                    this.Cmp.id_partida.allowBlank = true;
                    this.Cmp.id_partida.reset();

                    this.Cmp.id_presupuesto.setVisible(true);
                    this.Cmp.id_presupuesto.allowBlank = false;

                    this.Cmp.id_presupuesto.setValue('0');
                    this.Cmp.id_presupuesto.setRawValue('Todos los ítems');

                    // Cargar el store si ya hay un Tipo de AMP seleccionado
                    if (idObjetivoString) {
                        this.Cmp.id_presupuesto.store.baseParams.id_objetivo = idObjetivoString;
                        this.Cmp.id_presupuesto.modificado = true;
                        this.Cmp.id_presupuesto.store.load({
                            params: {
                                start: 0,
                                limit: this.Cmp.id_presupuesto.pageSize
                            }
                        });
                    }
                } else { // 'partida'
                    // Mostrar Partida
                    this.Cmp.id_partida.setVisible(true);
                    this.Cmp.id_partida.allowBlank = false;

                    this.Cmp.id_partida.setValue('0');
                    this.Cmp.id_partida.setRawValue('Todos los ítems');

                    this.Cmp.id_presupuesto.setVisible(false);
                    this.Cmp.id_presupuesto.allowBlank = true;
                    this.Cmp.id_presupuesto.reset();

                    // Cargar el store si ya hay un Tipo de AMP seleccionado
                    if (idObjetivoString) {
                        this.Cmp.id_partida.store.baseParams.id_objetivo = idObjetivoString;
                        this.Cmp.id_partida.modificado = true;
                        this.Cmp.id_partida.store.load({params: {start: 0, limit: this.Cmp.id_partida.pageSize}});
                    }
                }
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
                    name: 'tipo_amp',
                    hiddenName: 'id_amp',
                    fieldLabel: 'Tipo de AMP',
                    allowBlank: true,
                    store: new Ext.data.JsonStore({
                        url: '../../sis_presupuestos/control/Objetivo/obtenerAMedianoPlazo',
                        id: 'id_objetivo',
                        root: 'datos',
                        fields: ['id_objetivo', 'descripcion'],
                        totalProperty: 'total',
                        sortInfo: {field: 'id_objetivo', direction: 'ASC'},
                        baseParams: {},
                        autoLoad: false
                    }),
                    valueField: 'id_objetivo',
                    displayField: 'descripcion',
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    queryDelay: 1000,
                    minChars: 2,
                    anchor: '70%',
                    enableMultiSelect: true,
                    separador: ','
                },
                type: 'AwesomeCombo',
                id_grupo: 0,
                form: true
            },
            {
                config: {
                    name: 'filtrar_por',
                    fieldLabel: 'Filtrar por',
                    allowBlank: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'local',
                    store: new Ext.data.ArrayStore({
                        fields: ['tipo_filtro', 'valor'],
                        data: [['presupuesto', 'Centro de Costo'], ['partida', 'Partida']]
                    }),
                    anchor: '70%',
                    valueField: 'tipo_filtro',
                    displayField: 'valor',
                    value: 'presupuesto' // Establecer un valor por defecto
                },
                type: 'ComboBox',
                id_grupo: 0,
                form: true
            },
            {
                config: {
                    name: 'id_partida',
                    fieldLabel: 'Partida',
                    allowBlank: true,
                    emptyText: 'Seleccione...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_presupuestos/control/ObjetivoPartida/obtenerPartidasPorObjetivos',
                        id: 'id_partida',
                        root: 'datos',
                        fields: ['id_partida', 'codigo', 'desc_partida'],
                        totalProperty: 'total',
                        sortInfo: {field: 'codigo', direction: 'ASC'},
                        baseParams: {},
                        autoLoad: false
                    }),
                    valueField: 'id_partida',
                    displayField: 'desc_partida',
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    queryDelay: 1000,
                    minChars: 2,
                    anchor: '70%',
                    enableMultiSelect: true,
                    separador: ','
                },
                type: 'AwesomeCombo',
                id_grupo: 0,
                form: true
            },
            {
                config: {
                    name: 'id_presupuesto',
                    fieldLabel: 'Centro de Costo', // Etiqueta actualizada
                    allowBlank: true,
                    emptyText: 'Seleccione...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_presupuestos/control/ObjetivoPresupuesto/obtenerCentroCostosPorObjetivos',
                        id: 'id_presupuesto',
                        root: 'datos',
                        fields: ['id_presupuesto', 'desc_presupuesto'],
                        totalProperty: 'total',
                        baseParams: {},
                        autoLoad: false
                    }),
                    valueField: 'id_presupuesto',
                    displayField: 'desc_presupuesto',
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    queryDelay: 1000,
                    minChars: 2,
                    anchor: '70%',
                    enableMultiSelect: true,
                    separador: ','
                },
                type: 'AwesomeCombo',
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
        title: 'Reportes Acción a Mediano Plazo (POA)',
        ActSave: '../../sis_presupuestos/control/ProgramaOperacionesReporte/accionMedianoPlazo',
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