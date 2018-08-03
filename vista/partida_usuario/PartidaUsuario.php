<?php
/**
 * @package pXP
 * @file gen-PartidaUsuario.php
 * @author  (admin)
 * @date 24-07-2018 20:34:48
 * @description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.PartidaUsuario = Ext.extend(Phx.gridInterfaz, {

            constructor: function (config) {

                this.initButtons = [this.cmbGestion];

                this.maestro = config.maestro;

                var fecha = new Date();
                Ext.Ajax.request({
                    url: '../../sis_parametros/control/Gestion/obtenerGestionByFecha',
                    params: {fecha: fecha.getDate() + '/' + (fecha.getMonth() + 1) + '/' + fecha.getFullYear()},
                    success: function (resp) {
                        var reg = Ext.decode(Ext.util.Format.trim(resp.responseText));
                        this.cmbGestion.setValue(reg.ROOT.datos.id_gestion);
                        this.cmbGestion.setRawValue(fecha.getFullYear());
                        this.store.baseParams.id_gestion = reg.ROOT.datos.id_gestion;
                        this.load({params: {start: 0, limit: this.tam_pag}});
                    },
                    failure: this.conexionFailure,
                    timeout: this.timeout,
                    scope: this
                });
                //llama al constructor de la clase padre
                Phx.vista.PartidaUsuario.superclass.constructor.call(this, config);

                this.cmbGestion.on('select', function () {
                    if (this.validarFiltros()) {
                        this.capturaFiltros();
                    }
                }, this);

                this.init();
                this.load({params: {start: 0, limit: this.tam_pag}})
            },
            cmbGestion: new Ext.form.ComboBox({
                fieldLabel: 'Gestion',
                grupo: [0, 1, 2],
                allowBlank: false,
                emptyText: 'Gestion...',
                store: new Ext.data.JsonStore(
                    {
                        url: '../../sis_parametros/control/Gestion/listarGestion',
                        id: 'id_gestion',
                        root: 'datos',
                        sortInfo: {
                            field: 'gestion',
                            direction: 'ASC'
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
                pageSize: 50,
                queryDelay: 500,
                listWidth: '280',
                width: 80
            }),
            Atributos: [
                {
                    //configuracion del componente
                    config: {
                        labelSeparator: '',
                        inputType: 'hidden',
                        name: 'id_partida_usuario'
                    },
                    type: 'Field',
                    form: true
                },
                {
                    //configuracion del componente
                    config: {
                        labelSeparator: '',
                        inputType: 'hidden',
                        name: 'id_gestion',
                        gwidth: 50
                    },
                    type: 'Field',
                    grid: false,
                    form: true
                },

                {
                    config: {
                        sysorigen: 'sis_presupuestos',
                        name: 'id_partida',
                        origen: 'PARTIDA',
                        allowBlank: false,
                        fieldLabel: 'Partida',
                        valueField: 'id_partida',
                        gdisplayField: 'nombre_partida',//mapea al store del grid
                        baseParams: {sw_transaccional: 'movimiento', partida_tipo: 'presupuestaria'},
                        renderer: function (value, p, record) {
                            return String.format('({0}) - {1} - {2}', record.data['codigo'], record.data['nombre_partida'], record.data['gestion']);
                        },
                        gwidth: 250,
                        width: 280,
                        anchor: '80%',
                        listWidth: 350
                    },
                    type: 'ComboRec',
                    bottom_filter: true,
                    id_grupo: 0,
                    filters: {
                        pfiltro: 'par.codigo#par.nombre_partida',
                        type: 'string'
                    },
                    grid: true,
                    form: true
                },

                {
                    config: {
                        name: 'id_funcionario_resp',
                        origen: 'FUNCIONARIO',
                        tinit: false,
                        qtip: 'Funcionario responsable',
                        fieldLabel: 'Funcionario Resp.',
                        allowBlank: false,
                        gwidth: 250,
                        width: 280,
                        listWidth: 350,
                        valueField: 'id_funcionario',
                        gdisplayField: 'desc_funcionario',//mapea al store del grid
                        anchor: '100%',
                        baseParams: {estado_func: 'activo'},
                        renderer: function (value, p, record) {
                            return String.format('{0}', record.data['desc_funcionario']);
                        }
                    },
                    type: 'ComboRec',
                    id_grupo: 0,
                    filters: {
                        pfiltro: 'FUN.desc_funcionario1::varchar',
                        type: 'string'
                    },
                    bottom_filter: true,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'fecha_inicio_partida_usuario',
                        fieldLabel: 'Fecha Inicio',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer: function (value, p, record) {
                            return value ? value.dateFormat('d/m/Y') : ''
                        }
                    },
                    type: 'DateField',
                    filters: {pfiltro: 'parusu.fecha_inicio_partida_usuario', type: 'date'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'fecha_fin_partida_usuario',
                        fieldLabel: 'Fecha Fin',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer: function (value, p, record) {
                            return value ? value.dateFormat('d/m/Y') : ''
                        }
                    },
                    type: 'DateField',
                    filters: {pfiltro: 'parusu.fecha_fin_partida_usuario', type: 'date'},
                    id_grupo: 1,
                    //valorInicial:('01/01/' '),
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'estado_partida_usuario',
                        fieldLabel: 'Estado',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 20
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'parusu.estado_partida_usuario', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },
                {
                    config: {
                        name: 'observaciones',
                        fieldLabel: 'Observaciones',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 250,
                        maxLength: 450
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'parusu.observaciones', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },


                {
                    config: {
                        name: 'estado_reg',
                        fieldLabel: 'Estado Reg.',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 10
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'parusu.estado_reg', type: 'string'},
                    id_grupo: 1,
                    grid: false,
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
                    type: 'Field',
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
                    filters: {pfiltro: 'parusu.fecha_reg', type: 'date'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },
                {
                    config: {
                        name: 'id_usuario_ai',
                        fieldLabel: 'Fecha creación',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 4
                    },
                    type: 'Field',
                    filters: {pfiltro: 'parusu.id_usuario_ai', type: 'numeric'},
                    id_grupo: 1,
                    grid: false,
                    form: false
                },
                {
                    config: {
                        name: 'usuario_ai',
                        fieldLabel: 'Funcionaro AI',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 300
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'parusu.usuario_ai', type: 'string'},
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
                    type: 'Field',
                    filters: {pfiltro: 'usu2.cuenta', type: 'string'},
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
                    filters: {pfiltro: 'parusu.fecha_mod', type: 'date'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                }
            ],
            tam_pag: 50,
            title: 'Partida Usuario',
            ActSave: '../../sis_presupuestos/control/PartidaUsuario/insertarPartidaUsuario',
            ActDel: '../../sis_presupuestos/control/PartidaUsuario/eliminarPartidaUsuario',
            ActList: '../../sis_presupuestos/control/PartidaUsuario/listarPartidaUsuario',
            id_store: 'id_partida_usuario',
            fields: [
                {name: 'id_partida_usuario', type: 'numeric'},
                {name: 'estado_reg', type: 'string'},
                {name: 'fecha_inicio_partida_usuario', type: 'date', dateFormat: 'Y-m-d'},
                {name: 'fecha_fin_partida_usuario', type: 'date', dateFormat: 'Y-m-d'},
                {name: 'estado_partida_usuario', type: 'string'},
                {name: 'observaciones', type: 'string'},
                {name: 'id_partida', type: 'numeric'},
                {name: 'id_usuario_reg', type: 'numeric'},
                {name: 'fecha_reg', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
                {name: 'id_usuario_ai', type: 'numeric'},
                {name: 'usuario_ai', type: 'string'},
                {name: 'id_usuario_mod', type: 'numeric'},
                {name: 'fecha_mod', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
                {name: 'usr_reg', type: 'string'},
                {name: 'usr_mod', type: 'string'},

                'codigo',
                'nombre_partida',
                'id_funcionario_resp',
                'desc_funcionario',
                'id_gestion',
                'gestion'

            ],
            sortInfo: {
                field: 'id_partida_usuario',
                direction: 'ASC'
            },
            validarFiltros: function () {
                if (this.cmbGestion.isValid()) {
                    return true;
                }
                else {
                    return false;
                }

            },
            capturaFiltros: function (combo, record, index) {

                //this.desbloquearOrdenamientoGrid();
                this.getParametrosFiltro();
                this.load({params: {start: 0, limit: 50}});
            },
            getParametrosFiltro: function () {
                this.store.baseParams.id_gestion = this.cmbGestion.getValue();

            },

            onButtonNew: function () {
                this.getComponente('fecha_inicio_partida_usuario').enable();
                this.getComponente('id_partida').enable();
                this.getComponente('id_funcionario').enable();

                //console.log('values', new Date('01/01/' + this.cmbGestion.getRawValue()), (Date)('31/12/' + this.cmbGestion.getRawValue()));

                //this.getComponente('fecha_inicio_partida_usuario').setValue('01/01/' + r.data.gestion);
                if (this.validarFiltros()) {
                    Phx.vista.PartidaUsuario.superclass.onButtonNew.call(this);

                    this.Cmp.fecha_inicio_partida_usuario.setValue((new Date('01/01/' + this.cmbGestion.getRawValue())).dateFormat('d/m/Y'));
                    this.Cmp.fecha_fin_partida_usuario.setValue((new Date('12/31/' + this.cmbGestion.getRawValue())).dateFormat('d/m/Y'));

                    this.Cmp.id_gestion.setValue(this.cmbGestion.getValue());
                    this.Cmp.id_partida.reset();
                    this.Cmp.id_partida.store.baseParams.id_gestion = this.cmbGestion.getValue();
                    this.Cmp.id_partida.modificado = true;


                }
                else {
                    alert("Seleccione una gestion primero");
                }
            },
            onButtonAct: function () {
                if (!this.validarFiltros()) {
                    alert('Seleccione una gestion primero')
                }
                else {
                    this.getParametrosFiltro();
                    Phx.vista.PartidaUsuario.superclass.onButtonAct.call(this);
                }
            },
            onButtonEdit: function () {
                Phx.vista.PartidaUsuario.superclass.onButtonEdit.call(this);
                this.getComponente('fecha_inicio_partida_usuario').disable();
                this.getComponente('id_partida').disable();
                this.getComponente('id_funcionario').disable();
                //this.Cmp.fecha_inicio_partida_usuario.disable();

            },
            iniciarEvento: function () {


            },


            bdel: false,
            bsave: false
        }
    )
</script>

