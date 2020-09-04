<?php
/**
 *@package pXP
 *@file    FormSolicitudDet.php
 *@author  Maylee Perez Pastor
 *@date    05-08-2020
 *@description permite subir archivo excel para la formulacion presupuestaria
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.FormDetalleSubirFormulacion=Ext.extend(Phx.frmInterfaz,{

            constructor:function(config)
            {
                Ext.apply(this,config);
                Phx.vista.FormDetalleSubirFormulacion.superclass.constructor.call(this,config);
                this.init();
                //this.load({params: {start: 0, limit: this.tam_pag}});

                this.iniciarEventos();
                //this.loadValoresIniciales();

                var fecha = new Date();
                var gestion= fecha.getFullYear() + 1;
                Ext.Ajax.request({
                    url: '../../sis_parametros/control/Gestion/obtenerGestionByFecha',
                    params: {fecha: fecha.getDate() + '/' + (fecha.getMonth() + 1) + '/' + gestion},
                    success: function (resp) {
                        var reg = Ext.decode(Ext.util.Format.trim(resp.responseText));
                        //console.log('llegages', reg)
                        this.Cmp.id_gestion.setValue(reg.ROOT.datos.id_gestion);
                        this.Cmp.id_gestion.setRawValue(gestion);
                    },
                    failure: this.conexionFailure,
                    timeout: this.timeout,
                    scope: this
                });

                this.Cmp.id_gestion.on('select', this.capturarEventos, this);
            },

            loadValoresIniciales:function()
            {
                //Phx.vista.FormDetalleSubirFormulacion.superclass.loadValoresIniciales.call(this);
                //this.getComponente('id_solicitud').setValue(this.id_solicitud);
            },

            successSave:function(resp)
            {
                // Phx.CP.loadingHide();
                Phx.CP.getPagina(this.idContenedorPadre).reload();
                Phx.CP.loadingHide();
                this.panel.close();
            },


            Atributos:[
                /*{
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'id_solicitud'

                    },
                    type:'Field',
                    form:true

                },*/
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
                        anchor:'100%'

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
                        name: 'codigo',
                        fieldLabel: 'Codigo Archivo',
                        allowBlank:false,
                        anchor:'100%',
                        emptyText:'Obtener de...',
                        triggerAction: 'all',
                        lazyRender:true,
                        mode: 'local',
                        store:['FORMUPRESU'],
                        tpl: '<tpl for="."><div class="x-combo-list-item"><p><b>FORMUPRESU</b></p><p>FORMATO PLANILLA PRESUPUESTARIA</p></div></tpl>',
                    },
                    type:'ComboBox',
                    valorInicial: 'FORMUPRESU',
                    id_grupo:0,
                    form:true
                },

                {
                    config: {
                        name: 'id_funcionario',
                        hiddenName: 'id_funcionario',
                        origen: 'FUNCIONARIOCAR',
                        fieldLabel: 'Funcionario',
                        allowBlank: false,
                        anchor:'100%',
                        valueField: 'id_funcionario',
                        gdisplayField: 'desc_funcionario1',
                        baseParams: {es_combo_solicitud: 'si'},
                        renderer: function (value, p, record) {
                            return String.format('{0}', record.data['desc_funcionario1']);
                        }
                    },
                    type: 'ComboRec',//ComboRec
                    id_grupo: 1,
                    filters: {pfiltro: 'fun.desc_funcionario1', type: 'string'},
                    bottom_filter: true,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'observaciones',
                        fieldLabel: 'Descripción',
                        allowBlank: false,
                        anchor:'100%',
                        maxLength: 2000
                    },
                    type: 'TextArea',
                    filters: {pfiltro: 'fp.observaciones', type: 'string'},
                    form: true
                },
                {
                    config:{
                        fieldLabel: "Documento",
                        allowBlank:false,
                        gwidth: 130,
                        inputType:'file',
                        name: 'archivo',
                        buttonText: '',
                        maxLength:150,
                        anchor:'100%'
                    },
                    type:'Field',
                    form:true
                },
            ],
            title:'Subir Detalle Formulación',
            fileUpload:true,
            ActSave:'../../sis_presupuestos/control/Presupuesto/subirDetalleFormulacion',

            capturarEventos: function () {

                this.store.baseParams.id_gestion = this.id_gestion.getValue();

                console.log('gestionidform', this.store.baseParams.id_gestion)
                this.load({params: {start: 0, limit: this.tam_pag}});
            },

            iniciarEventos: function () {
                this.Cmp.codigo.setValue('FORMUPRESU');

                //this.Cmp.id_gestion.setValue(gestion);
            }

    }
    )
</script>
