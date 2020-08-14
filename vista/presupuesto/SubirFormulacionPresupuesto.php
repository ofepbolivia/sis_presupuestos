<?php
/**
 * @package pXP
 * @file SubirFormulacionPresupuesto.php
 * @author  Maylee Perez Pastor
 * @date 05-08-2020 00:30:39
 * @description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.SubirFormulacionPresupuesto = Ext.extend(Phx.gridInterfaz, {

        //fheight: '90%',
        //fwidth: '70%',

        nombreVista: 'FormulacionPresupuesto',

        constructor: function (config) {
            //this.maestro = config.maestro;
            this.tbarItems = ['-',
                'Gestión:', this.cmbGestion, '-'
            ];
            var fecha = new Date();
            var gestion= fecha.getFullYear() + 1;
            Ext.Ajax.request({
                url: '../../sis_parametros/control/Gestion/obtenerGestionByFecha',
                params: {fecha: fecha.getDate() + '/' + (fecha.getMonth() + 1) + '/' + gestion},
                success: function (resp) {
                    var reg = Ext.decode(Ext.util.Format.trim(resp.responseText));
                    console.log('llegages', reg)
                    this.cmbGestion.setValue(reg.ROOT.datos.id_gestion);
                    //this.cmbGestion.setRawValue(fecha.getFullYear());
                    this.cmbGestion.setRawValue(gestion);
                    this.store.baseParams.id_gestion = reg.ROOT.datos.id_gestion;
                    this.load({params: {start: 0, limit: this.tam_pag}});
                },
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });

            this.maestro = config;



            //llama al constructor de la clase padre
            Phx.vista.SubirFormulacionPresupuesto.superclass.constructor.call(this, config);
            this.init();

            //carga de grilla
            this.load({params: {start: 0, limit: this.tam_pag}});

            this.iniciarEventos();

            this.addButton('btnDetalleFormulacion', {
                text: 'Subir Formulación',
                iconCls: 'bgear',
                disabled: false,
                handler: this.onDetalleFormulacion,
                tooltip: 'Subir archivo excel con la Formulación Presupuestaria'
            });

            this.store.baseParams = {tipo_interfaz: this.nombreVista};

            this.cmbGestion.on('select', this.capturarEventos, this);

        },
        tam_pag: 50,

        cmbGestion: new Ext.form.ComboBox({
            //name: 'gestion',
            // id: 'gestion_reg',
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
                    // turn on remote sorting
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

            this.store.baseParams.id_gestion = this.cmbGestion.getValue();
            console.log('llegagesgetval2', this.store.baseParams.id_gestion)
            this.load({params: {start: 0, limit: this.tam_pag}});
        },


        Atributos: [
            {
                //configuracion del componente
                config: {
                    labelSeparator: '',
                    inputType: 'hidden',
                    name: 'id_formulacion_presu',
                    fieldLabel: 'ID',
                    gwidth: 50
                },
                type: 'Field',
                grid: false,
                form: false
            },

            {
                config: {
                    name: 'observaciones',
                    fieldLabel: 'Observaciones',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 200,
                    maxLength: 200
                },
                type: 'TextArea',
                filters: {pfiltro: 'fp.observaciones', type: 'string'},
                id_grupo: 1,
                bottom_filter: true,
                grid: true
            },
            {
                config: {
                    name: 'desc_persona',
                    fieldLabel: 'Responsable',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: 100
                },
                type: 'NumberField',
                filters: {pfiltro: 'usures.desc_persona', type: 'string'},
                bottom_filter: true,
                id_grupo: 1,
                grid: true
            },
            {
                config: {
                    name: 'estado_reg',
                    fieldLabel: 'Estado Reg.',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: 10,
                    renderer:function(value, p, record){
                        if(record.data.estado_reg=='inactivo'){
                            return '<tpl style="color:#FA0D05; margin-top:0px; position:absolute; width:100px; float:left;">'+record.data['estado_reg']+'</tpl>';
                        }else{
                            return '<tpl style="color:#02117C; margin-top:0px; position:absolute; width:100px; float:left;">'+record.data['estado_reg']+'</tpl>';
                            //return String.format('{0}', value);
                        }
                    }
                },
                type: 'TextField',
                filters: {pfiltro: 'fp.estado_reg', type: 'string'},
                id_grupo: 1,
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'usr_reg',
                    fieldLabel: 'Creado por',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: 4
                },
                type: 'NumberField',
                filters: {pfiltro: 'usu1.cuenta', type: 'string'},
                id_grupo: 1,
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'fecha_reg',
                    fieldLabel: 'Fecha creación',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer: function (value, p, record) {
                        return value ? value.dateFormat('d/m/Y H:i:s') : ''
                    }
                },
                type: 'DateField',
                filters: {pfiltro: 'fp.fecha_reg', type: 'date'},
                id_grupo: 1,
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'fecha_mod',
                    fieldLabel: 'Fecha Modif.',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer: function (value, p, record) {
                        return value ? value.dateFormat('d/m/Y H:i:s') : ''
                    }
                },
                type: 'DateField',
                filters: {pfiltro: 'fp.fecha_mod', type: 'date'},
                id_grupo: 1,
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'usr_mod',
                    fieldLabel: 'Modificado por',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: 4
                },
                type: 'NumberField',
                filters: {pfiltro: 'usu2.cuenta', type: 'string'},
                id_grupo: 1,
                grid: true,
                form: false
            }
        ],

        title: 'Presupuesto',
        //ActSave: '../../sis_presupuestos/control/Presupuesto/insertarPresupuesto',
        ActDel: '../../sis_presupuestos/control/Presupuesto/eliminarDetalleFormulacion',
        ActList: '../../sis_presupuestos/control/Presupuesto/listarFormulacionPresu',
        id_store: 'id_formulacion_presu',
        fields: [
            {name: 'id_formulacion_presu', type: 'numeric'},
            {name: 'observaciones', type: 'string'},
            {name: 'id_usuario_responsable', type: 'numeric'},
            {name: 'desc_persona', type: 'string'},
            {name: 'id_usuario_reg', type: 'numeric'},
            {name: 'id_usuario_mod', type: 'numeric'},
            {name: 'fecha_reg', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
            {name: 'fecha_mod', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
            {name: 'estado_reg', type: 'string'},
            {name: 'usr_reg', type: 'string'},
            {name: 'usr_mod', type: 'string'},
            {name: 'id_gestion', type: 'numeric'}

        ],


        sortInfo: {
            field: 'fp.id_formulacion_presu',
            direction: 'DESC'
        },

        bnew: false,
        bedit: false,
        bdel: true,
        bsave: false,

        tabsouth: [
            {
                url: '../../../sis_presupuestos/vista/presupuesto/SubirFormulacionPresupuestoDetalle.php',
                title: 'Detalle',
                height: '50%',
                cls: 'SubirFormulacionPresupuestoDetalle'
            }

        ],

        onDetalleFormulacion: function () {
            //var rec = this.sm.getSelected();
            Phx.CP.loadWindows('../../../sis_presupuestos/vista/presupuesto/FormDetalleSubirFormulacion.php',
                'Subir Formulación',
                {
                    modal: true,
                    width: 450,
                    height: 280
                },
                '',
                this.idContenedor,
                'FormDetalleSubirFormulacion')
        },

        iniciarEventos: function () {


        },


        /*successEstadoSinc: function (resp) {
            Phx.CP.loadingHide();
            resp.argument.wizard.panel.destroy()
            this.reload();
        }*/

    })
</script>
