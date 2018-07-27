<?php
/**
 * @package pXP
 * @file gen-TechoPresupuestos.php
 * @author  (admin)
 * @date 09-07-2018 18:45:47
 * @description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.TechoPresupuestos = Ext.extend(Phx.gridInterfaz, {

            constructor: function (config) {
                this.maestro = config.maestro;
                //llama al constructor de la clase padre
                Phx.vista.TechoPresupuestos.superclass.constructor.call(this, config);
                this.init();
                this.bloquearMenus();

                //this.load({params: {start: 0, limit: this.tam_pag}})
            },

            Atributos: [
                {
                    //configuracion del componente
                    config: {
                        labelSeparator: '',
                        inputType: 'hidden',
                        name: 'id_techo_presupuesto'
                    },
                    type: 'Field',
                    form: true
                },
                {
                    //configuracion del componente
                    config: {
                        labelSeparator: '',
                        inputType: 'hidden',
                        name: 'id_presupuesto'
                    },
                    type: 'Field',
                    form: true
                },

                {
                    config: {
                        name: 'importe_techo_presupuesto',
                        fieldLabel: 'Importe',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 1000000,
                        minValue: 0,
                        renderer: function (value, p, record) {
                            Number.prototype.formatDinero = function (c, d, t) {
                                var n = this,
                                    c = isNaN(c = Math.abs(c)) ? 2 : c,
                                    d = d == undefined ? "." : d,
                                    t = t == undefined ? "," : t,
                                    s = n < 0 ? "-" : "",
                                    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
                                    j = (j = i.length) > 3 ? j % 3 : 0;
                                return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
                            };
                            if (record.data.tipo_reg != 'summary') {
                                return String.format('{0}', (parseFloat(value)).formatDinero(2, ',', '.'));
                            }
                            else {
                                return String.format('<b><font size=2 >{0}</font><b>', (parseFloat(value)).formatDinero(2, ',', '.'));
                            }

                        }
                    },
                    type: 'NumberField',
                    filters: {pfiltro: 'tecpre.importe_techo_presupuesto', type: 'numeric'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'observaciones',
                        fieldLabel: 'Observaciones',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 350
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'tecpre.observaciones', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'estado_techo_presupuesto',
                        fieldLabel: 'Estado',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 20
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'tecpre.estado_techo_presupuesto', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: false
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
                    filters: {pfiltro: 'tecpre.estado_reg', type: 'string'},
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
                    filters: {pfiltro: 'tecpre.fecha_reg', type: 'date'},
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
                    filters: {pfiltro: 'tecpre.id_usuario_ai', type: 'numeric'},
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
                    filters: {pfiltro: 'tecpre.usuario_ai', type: 'string'},
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
                    filters: {pfiltro: 'tecpre.fecha_mod', type: 'date'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                }
            ],
            tam_pag: 50,
            title: 'Techo Presupuestario',
            ActSave: '../../sis_presupuestos/control/TechoPresupuestos/insertarTechoPresupuestos',
            ActDel: '../../sis_presupuestos/control/TechoPresupuestos/eliminarTechoPresupuestos',
            ActList: '../../sis_presupuestos/control/TechoPresupuestos/listarTechoPresupuestos',
            id_store: 'id_techo_presupuesto',
            fields: [
                {name: 'id_techo_presupuesto', type: 'numeric'},
                {name: 'estado_reg', type: 'string'},
                {name: 'importe_techo_presupuesto', type: 'numeric'},
                {name: 'observaciones', type: 'string'},
                {name: 'id_presupuesto', type: 'numeric'},
                {name: 'estado_techo_presupuesto', type: 'string'},
                {name: 'id_usuario_reg', type: 'numeric'},
                {name: 'fecha_reg', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
                {name: 'id_usuario_ai', type: 'numeric'},
                {name: 'usuario_ai', type: 'string'},
                {name: 'id_usuario_mod', type: 'numeric'},
                {name: 'fecha_mod', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
                {name: 'usr_reg', type: 'string'},
                {name: 'usr_mod', type: 'string'},

            ],
            sortInfo: {
                field: 'id_techo_presupuesto',
                direction: 'DESC'
            },
            onReloadPage: function (m) {
                this.maestro = m;

                this.store.baseParams = {id_presupuesto: this.maestro.id_presupuesto};

                this.load({params: {start: 0, limit: 50}});

                console.log('errrrorr maestro', this.maestro)
                //console.log('errrrorr maestro id', this.maestro.id_presupuesto)
            },

            loadValoresIniciales: function () {
                Phx.vista.TechoPresupuestos.superclass.loadValoresIniciales.call(this);
                this.Cmp.id_presupuesto.setValue(this.maestro.id_presupuesto);

            },

            bdel: false,
            bsave: false,
            btest: false,
            bedit: false
        }
    )
</script>
		
		