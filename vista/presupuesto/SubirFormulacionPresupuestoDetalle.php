<?php
/**
 * @package pXP
 * @file SubirFormulacionPresupuestoDetalle.php
 * @author  Maylee Perez Pastor
 * @date 05-08-2020 00:30:39
 * @description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.SubirFormulacionPresupuestoDetalle=Ext.extend(Phx.gridInterfaz,{


            constructor:function(config){
                this.maestro=config.maestro;
                //llama al constructor de la clase padre
                Phx.vista.SubirFormulacionPresupuestoDetalle.superclass.constructor.call(this,config);
                this.grid.getTopToolbar().disable();
                this.grid.getBottomToolbar().disable();
                this.init();
                this.iniciarEventos();


            },



            tam_pag:50,

            Atributos:[
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'id_formulacion_presu_detalle'
                    },
                    type:'Field',
                    form:true
                },
                {
                    config:{
                        labelSeparator:'',
                        name: 'id_formulacion_presu',
                        fieldLabel: 'id_formulacion_presu',
                        inputType:'hidden'
                    },
                    type:'Field',
                    form:true
                },
                {
                    config:{
                        name:'id_centro_costo',
                        origen:'CENTROCOSTO',
                        // baseParams:{filtrar:'grupo_ep'},
                        fieldLabel: 'Centro de Costos',
                        url: '../../sis_parametros/control/CentroCosto/listarCentroCostoFiltradoXDepto',
                        emptyText : 'Centro Costo...',
                        allowBlank:false,
                        gdisplayField:'codigo_cc',//mapea al store del grid
                        gwidth:200,
                    },
                    type:'ComboRec',
                    id_grupo:0,
                    filters:{pfiltro:'cen.codigo_cc',type:'string'},
                    bottom_filter: true,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name:'id_concepto_ingas',
                        fieldLabel:'Concepto Ingreso Gasto',
                        allowBlank:false,
                        emptyText:'Concepto Ingreso Gasto...',
                        store: new Ext.data.JsonStore({
                            url: '../../sis_parametros/control/ConceptoIngas/listarConceptoIngasMasPartida',
                            id: 'id_concepto_ingas',
                            root: 'datos',
                            sortInfo:{
                                field: 'desc_ingas',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_concepto_ingas','tipo','desc_ingas','movimiento','desc_partida','id_grupo_ots','filtro_ot','requiere_ot'],
                            // turn on remote sorting
                            remoteSort: true,
                            baseParams:{par_filtro:'desc_ingas#par.codigo#par.nombre_partida',movimiento:'gasto' ,autorizacion_nulos: 'no'}
                        }),
                        valueField: 'id_concepto_ingas',
                        displayField: 'desc_ingas',
                        gdisplayField:'nombre_ingas',
                        tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{desc_ingas}</b></p><p>TIPO:{tipo}</p><p>MOVIMIENTO:{movimiento}</p> <p>PARTIDA:{desc_partida}</p></div></tpl>',
                        hiddenName: 'id_concepto_ingas',
                        forceSelection:true,
                        typeAhead: false,
                        triggerAction: 'all',
                        lazyRender:true,
                        mode:'remote',
                        pageSize:10,
                        queryDelay:1000,
                        listWidth:600,
                        resizable:true,
                        anchor:'80%',
                        gwidth: 200,
                        renderer:function(value, p, record){return String.format('{0}', record.data['nombre_ingas']);}
                    },
                    type:'ComboBox',
                    id_grupo:0,
                    filters:{
                        pfiltro:'cig.movimiento#cig.desc_ingas',
                        type:'string'
                    },
                    bottom_filter: true,
                    grid:true,
                    form:true
                },

                {
                    config:{
                        name:'id_partida',
                        fieldLabel:'Partida',
                        allowBlank:true,
                        emptyText:'Partida...',
                        store: new Ext.data.JsonStore({
                            url: '../../sis_presupuestos/control/Partida/listarPartida',
                            id: 'id_partida',
                            root: 'datos',
                            sortInfo:{
                                field: 'codigo',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_partida','codigo','nombre_partida'],
                            // turn on remote sorting
                            remoteSort: true,
                            baseParams:{par_filtro:'codigo#nombre_partida',sw_transaccional:'movimiento'}
                        }),
                        valueField: 'id_partida',
                        displayField: 'nombre_partida',
                        gdisplayField:'nombre_partida',
                        tpl:'<tpl for="."><div class="x-combo-list-item"><p>CODIGO:{codigo}</p><p>{nombre_partida}</p></div></tpl>',
                        hiddenName: 'id_partida',
                        forceSelection:true,
                        typeAhead: true,
                        triggerAction: 'all',
                        lazyRender:true,
                        mode:'remote',
                        pageSize:10,
                        listWidth:350,
                        resizable:true,
                        queryDelay:1000,
                        anchor:'80%',
                        renderer:function(value, p, record){return String.format('{0}', record.data['nombre_partida']);}
                    },
                    type:'ComboBox',
                    id_grupo:0,
                    filters:{
                        pfiltro:'nombre_partida',
                        type:'string'
                    },
                    bottom_filter: true,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'justificacion',
                        fieldLabel: 'Justificación',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 200,
                        maxLength:1245184
                    },
                    type:'TextArea',
                    filters:{pfiltro:'fpd.justificacion',type:'numeric'},
                    id_grupo:1,
                    bottom_filter: true,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'nro_contrato',
                        fieldLabel: 'Nro Contrato',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:10
                    },
                    type:'TextField',
                    filters:{pfiltro:'fpd.nro_contrato',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'proveedor',
                        fieldLabel: 'Proveedor',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:10
                    },
                    type:'TextField',
                    filters:{pfiltro:'fpd.proveedor',type:'string'},
                    id_grupo:1,
                    bottom_filter: true,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'hoja_respaldo',
                        fieldLabel: 'Hoja de Respaldo',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:10,
                        renderer: function (value, p, record) {
                            if (record.data.tipo_reg != 'summary') {
                                return String.format('{0}', value);
                            }
                            else {
                                return String.format('<b><font size=3 color="navy">{0}</font><b>', value);
                            }
                        }
                    },
                    type:'TextField',
                    filters:{pfiltro:'fpd.hoja_respaldo',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },

                {
                    config:{
                        name: 'periodo_enero',
                        fieldLabel: 'Enero',
                        currencyChar:' ',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:123456778,
                        galign: 'right ',
                        renderer: function (value, p, record) {
                            return String.format('{0}', Ext.util.Format.number(value, '0,000.00'));
                        }
                    },
                    type:'MoneyField',
                    filters:{pfiltro:'fpd.periodo_enero',type:'numeric'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'periodo_febrero',
                        fieldLabel: 'Febrero',
                        currencyChar:' ',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:123456778,
                        galign: 'right ',
                        renderer: function (value, p, record) {
                            return String.format('{0}', Ext.util.Format.number(value, '0,000.00'));
                        }
                    },
                    type:'MoneyField',
                    filters:{pfiltro:'fpd.periodo_febrero',type:'numeric'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'periodo_marzo',
                        fieldLabel: 'Marzo',
                        currencyChar:' ',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:123456778,
                        galign: 'right ',
                        renderer: function (value, p, record) {
                            return String.format('{0}', Ext.util.Format.number(value, '0,000.00'));
                        }
                    },
                    type:'MoneyField',
                    filters:{pfiltro:'fpd.periodo_marzo',type:'numeric'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'periodo_abril',
                        fieldLabel: 'Abril',
                        currencyChar:' ',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:123456778,
                        galign: 'right ',
                        renderer: function (value, p, record) {
                            return String.format('{0}', Ext.util.Format.number(value, '0,000.00'));
                        }
                    },
                    type:'MoneyField',
                    filters:{pfiltro:'fpd.periodo_abril',type:'numeric'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'periodo_mayo',
                        fieldLabel: 'Mayo',
                        currencyChar:' ',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:123456778,
                        galign: 'right ',
                        renderer: function (value, p, record) {
                            return String.format('{0}', Ext.util.Format.number(value, '0,000.00'));
                        }
                    },
                    type:'MoneyField',
                    filters:{pfiltro:'fpd.periodo_mayo',type:'numeric'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'periodo_junio',
                        fieldLabel: 'Junio',
                        currencyChar:' ',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:123456778,
                        galign: 'right ',
                        renderer: function (value, p, record) {
                            return String.format('{0}', Ext.util.Format.number(value, '0,000.00'));
                        }
                    },
                    type:'MoneyField',
                    filters:{pfiltro:'fpd.periodo_junio',type:'numeric'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'periodo_julio',
                        fieldLabel: 'Julio',
                        currencyChar:' ',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:123456778,
                        galign: 'right ',
                        renderer: function (value, p, record) {
                            return String.format('{0}', Ext.util.Format.number(value, '0,000.00'));
                        }
                    },
                    type:'MoneyField',
                    filters:{pfiltro:'fpd.periodo_julio',type:'numeric'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'periodo_agosto',
                        fieldLabel: 'Agosto',
                        currencyChar:' ',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:123456778,
                        galign: 'right ',
                        renderer: function (value, p, record) {
                            return String.format('{0}', Ext.util.Format.number(value, '0,000.00'));
                        }
                    },
                    type:'MoneyField',
                    filters:{pfiltro:'fpd.periodo_agosto',type:'numeric'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'periodo_septiembre',
                        fieldLabel: 'Septiembre',
                        currencyChar:' ',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:123456778,
                        galign: 'right ',
                        renderer: function (value, p, record) {
                            return String.format('{0}', Ext.util.Format.number(value, '0,000.00'));
                        }
                    },
                    type:'MoneyField',
                    filters:{pfiltro:'fpd.periodo_septiembre',type:'numeric'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'periodo_octubre',
                        fieldLabel: 'Octubre',
                        currencyChar:' ',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:123456778,
                        galign: 'right ',
                        renderer: function (value, p, record) {
                            return String.format('{0}', Ext.util.Format.number(value, '0,000.00'));
                        }
                    },
                    type:'MoneyField',
                    filters:{pfiltro:'fpd.periodo_octubre',type:'numeric'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'periodo_noviembre',
                        fieldLabel: 'Noviembre',
                        currencyChar:' ',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:123456778,
                        galign: 'right ',
                        renderer: function (value, p, record) {
                            return String.format('{0}', Ext.util.Format.number(value, '0,000.00'));
                        }
                    },
                    type:'MoneyField',
                    filters:{pfiltro:'fpd.periodo_noviembre',type:'numeric'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'periodo_diciembre',
                        fieldLabel: 'Diciembre',
                        currencyChar:' ',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:123456778,
                        galign: 'right ',
                        renderer: function (value, p, record) {
                            return String.format('{0}', Ext.util.Format.number(value, '0,000.00'));
                        }
                    },
                    type:'MoneyField',
                    filters:{pfiltro:'fpd.periodo_diciembre',type:'numeric'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'importe_total',
                        fieldLabel: 'Importe Total',
                        currencyChar:' ',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:123456778,
                        galign: 'right ',
                        renderer: function (value, p, record) {
                            if (record.data.tipo_reg != 'summary') {
                                return String.format('{0}', Ext.util.Format.number(value, '0,000.00'));
                            }
                            else {
                                return String.format('<b><font size=3 color="navy">{0}</font><b>', Ext.util.Format.number(value, '0,000.00'));
                            }
                        }
                    },
                    type:'NumberField',
                    filters:{pfiltro:'fpd.importe_total',type:'numeric'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },

                {
                    config:{
                        name: 'estado_reg',
                        fieldLabel: 'Estado Reg.',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:10,
                        renderer:function(value, p, record){
                            if(record.data.estado_reg=='inactivo'){
                                return '<tpl style="color:#FA0D05; margin-top:0px; position:absolute; width:100px; float:left;">'+record.data['estado_reg']+'</tpl>';
                            }else{
                                return '<tpl style="color:#02117C; margin-top:0px; position:absolute; width:100px; float:left;">'+record.data['estado_reg']+'</tpl>';
                                //return String.format('{0}', value);
                            }
                        }
                    },
                    type:'TextField',
                    filters:{pfiltro:'fpd.estado_reg',type:'string'},
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
                    filters:{pfiltro:'fpd.fecha_reg',type:'date'},
                    id_grupo:1,
                    grid:true,
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
                    type:'NumberField',
                    filters:{pfiltro:'usu1.cuenta',type:'string'},
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
                    filters:{pfiltro:'fpd.fecha_mod',type:'date'},
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
                    type:'NumberField',
                    filters:{pfiltro:'usu2.cuenta',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:false
                }
            ],

            title:'Detalle Formulación',
            //ActSave:'../../sis_tesoreria/control/ObligacionDet/insertarObligacionDet',
            //ActDel:'../../sis_presupuestos/control/ObligacionDet/eliminarObligacionDet',
            ActList:'../../sis_presupuestos/control/Presupuesto/listarFormulacionPresuDet',
            id_store:'id_formulacion_presu_detalle',
            fields: [
                {name:'id_formulacion_presu_detalle', type: 'numeric'},
                {name:'id_centro_costo', type: 'numeric'},
                {name:'codigo_cc', type: 'string'},
                {name:'id_concepto_gasto', type: 'numeric'},
                {name:'nombre_ingas', type: 'string'},
                {name:'justificacion', type: 'string'},
                {name:'nro_contrato', type: 'string'},
                {name:'proveedor', type: 'string'},
                {name:'hoja_respaldo', type: 'string'},
                {name:'periodo_enero', type: 'numeric'},
                {name:'periodo_febrero', type: 'numeric'},
                {name:'periodo_marzo', type: 'numeric'},
                {name:'periodo_abril', type: 'numeric'},
                {name:'periodo_mayo', type: 'numeric'},
                {name:'periodo_junio', type: 'numeric'},
                {name:'periodo_julio', type: 'numeric'},
                {name:'periodo_agosto', type: 'numeric'},
                {name:'periodo_septiembre', type: 'numeric'},
                {name:'periodo_octubre', type: 'numeric'},
                {name:'periodo_noviembre', type: 'numeric'},
                {name:'periodo_diciembre', type: 'numeric'},
                {name:'importe_total', type: 'numeric'},
                {name:'id_partida', type: 'numeric'},
                {name:'nombre_partida', type: 'string'},
                {name:'id_formulacion_presu', type: 'numeric'},
                {name:'id_memoria_calculo', type: 'numeric'},

                {name:'id_usuario_reg', type: 'numeric'},
                {name:'id_usuario_mod', type: 'numeric'},
                {name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                {name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                {name:'estado_reg', type: 'string'},
                {name:'usr_reg', type: 'string'},
                {name:'usr_mod', type: 'string'},
                'tipo_reg'
            ],

            onReloadPage:function(m){

                this.maestro=m;

                this.store.baseParams={id_formulacion_presu:this.maestro.id_formulacion_presu};
                this.load({params:{start:0, limit:50}})

            },


            sortInfo:{
                field: 'id_formulacion_presu_detalle',
                direction: 'ASC'
            },
            bdel:false,
            bsave:false,
            bnew:false,
            bedit:false
        }
    )
</script>