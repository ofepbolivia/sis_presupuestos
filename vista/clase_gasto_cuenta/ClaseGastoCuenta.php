<?php
/**
 *@package pXP
 *@file ClaseGastoCuenta.php
 *@author  Maylee Perez Pastor
 *@date 22-08-2019 02:33:23
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.ClaseGastoCuenta=Ext.extend(Phx.gridInterfaz,{

            constructor:function(config){
                this.maestro=config.maestro;
                this.initButtons=[this.cmbGestion];
                //llama al constructor de la clase padre
                Phx.vista.ClaseGastoCuenta.superclass.constructor.call(this,config);

                this.addButton('clonarCuentas',
                    {
                        //grupo: [0],
                        text: 'Clonar Cuentas',
                        iconCls: 'blist',
                        disabled: false,
                        handler: this.clonarCuenta,
                        tooltip: '<b>Replicación de Datos</b><br/>Clonar Cuentas.'
                    }
                );

                this.init();
                this.bloquearMenus();

                this.cmbGestion.on('select', function(){
                    if(this.validarFiltros()){
                        this.capturaFiltros();
                    }
                },this);

            },

            clonarCuenta: function () {
                //var rec=this.sm.getSelected();
                var rec = this.cmbGestion.getValue();
                var gasto = this.maestro.id_clase_gasto;

                if(this.cmbGestion.getValue()){
                    Ext.Ajax.request({
                        url: '../../sis_presupuestos/control/ClaseGastoCuenta/clonarCuenta',
                        params: {
                            id_gestion: rec,
                            id_clase_gasto:gasto
                        },
                        success: this.successAnular,
                        failure: this.conexionFailure,
                        timeout: this.timeout,
                        scope: this
                    });
                }
                else{
                    alert('primero debe selecionar la gestion origen');
                }

            },

            cmbGestion: new Ext.form.ComboBox({
                fieldLabel: 'Gestion',
                allowBlank: false,
                emptyText:'Gestion...',
                store:new Ext.data.JsonStore(
                    {
                        url: '../../sis_parametros/control/Gestion/listarGestion',
                        id: 'id_gestion',
                        root: 'datos',
                        sortInfo:{
                            field: 'gestion',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_gestion','gestion'],
                        // turn on remote sorting
                        remoteSort: true,
                        baseParams:{par_filtro:'gestion'}
                    }),
                valueField: 'id_gestion',
                triggerAction: 'all',
                displayField: 'gestion',
                hiddenName: 'id_gestion',
                mode:'remote',
                pageSize:50,
                queryDelay:500,
                listWidth:'280',
                width:80
            }),




            Atributos:[
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'id_clase_gasto_cuenta'
                    },
                    type:'Field',
                    form:true
                },
                {
                    config: {
                        name: 'id_clase_gasto',
                        inputType:'hidden',
                    },
                    type: 'Field',
                    form: true
                },
                {
                    config:{
                        sysorigen:'sis_contabilidad',
                        name:'id_cuenta',
                        origen:'CUENTA',
                        allowBlank:false,
                        fieldLabel:'Cuenta',
                        gdisplayField:'desc_cuenta',//mapea al store del grid
                        gwidth:200,
                        width: 350,
                        listWidth: 350
                    },
                    type:'ComboRec',
                    id_grupo:0,
                    filters:{
                        pfiltro:'cgc.desc_cuenta',
                        type:'string'
                    },
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'estado_reg',
                        fieldLabel: 'Estado Reg.',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:10
                    },
                    type:'TextField',
                    filters:{pfiltro:'cgc.estado_reg',type:'string'},
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
                    filters:{pfiltro:'cgc.fecha_reg',type:'date'},
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
                    type:'Field',
                    filters:{pfiltro:'usu1.cuenta',type:'string'},
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
                    type:'Field',
                    filters:{pfiltro:'usu2.cuenta',type:'string'},
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
                    filters:{pfiltro:'cgc.fecha_mod',type:'date'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'id_usuario_ai',
                        fieldLabel: '',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:4
                    },
                    type:'Field',
                    filters:{pfiltro:'cgc.id_usuario_ai',type:'numeric'},
                    id_grupo:1,
                    grid:false,
                    form:false
                },
                {
                    config:{
                        name: 'usuario_ai',
                        fieldLabel: 'Funcionaro AI',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:300
                    },
                    type:'TextField',
                    filters:{pfiltro:'cgc.usuario_ai',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:false
                }
            ],
            tam_pag:50,
            title:'Clase de Gato - Cuenta',
            ActSave:'../../sis_presupuestos/control/ClaseGastoCuenta/insertarClaseGastoCuenta',
            ActDel:'../../sis_presupuestos/control/ClaseGastoCuenta/eliminarClaseGastoCuenta',
            ActList:'../../sis_presupuestos/control/ClaseGastoCuenta/listarClaseGastoCuenta',
            id_store:'id_clase_gasto_cuenta',
            fields: [
                {name:'id_clase_gasto_cuenta', type: 'numeric'},
                {name:'id_cuenta', type: 'numeric'},
                {name:'estado_reg', type: 'string'},
                {name:'id_clase_gasto', type: 'numeric'},
                {name:'id_usuario_ai', type: 'numeric'},
                {name:'usuario_ai', type: 'string'},
                {name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                {name:'id_usuario_reg', type: 'numeric'},
                {name:'id_usuario_mod', type: 'numeric'},
                {name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                {name:'usr_reg', type: 'string'},
                {name:'usr_mod', type: 'string'},'desc_cuenta','id_gestion'

            ],
            onReloadPage:function(m){
                this.maestro=m;
                this.store.baseParams = { id_clase_gasto: this.maestro.id_clase_gasto };
                this.load({ params: {start:0, limit:50 } });

            },
            loadValoresIniciales:function(){
                Phx.vista.ClaseGastoCuenta.superclass.loadValoresIniciales.call(this);
                this.getComponente('id_clase_gasto').setValue(this.maestro.id_clase_gasto);

            },
            sortInfo:{
                field: 'id_clase_gasto_cuenta',
                direction: 'ASC'
            },
            onButtonNew:function(){
                if(this.validarFiltros()){
                    Phx.vista.ClaseGastoCuenta.superclass.onButtonNew.call(this);
                    this.Cmp.id_cuenta.store.baseParams.id_gestion = this.cmbGestion.getValue();
                }
                else{
                    alert('Seleccione una Gestión primero')
                }
            },
            onButtonEdit:function(){
                if(this.validarFiltros()){
                    Phx.vista.ClaseGastoCuenta.superclass.onButtonEdit.call(this);
                    this.Cmp.id_cuenta.store.baseParams.id_gestion = this.cmbGestion.getValue();
                }
                else{
                    alert('Seleccione una Gestión primero')
                }
            },
            onButtonDelete:function(){
                if(this.validarFiltros()){
                    Phx.vista.ClaseGastoCuenta.superclass.onButtonDelete.call(this);
                }
                else{
                    alert('seleccione una gestion primero')
                }
            },
            capturaFiltros:function(combo, record, index){
                // this.desbloquearOrdenamientoGrid();
                this.store.baseParams.id_gestion=this.cmbGestion.getValue();
                this.load();
            },
            validarFiltros:function(){
                if(this.cmbGestion.isValid()){
                    return true;
                }
                else{
                    return false;
                }
            },
            bdel:true,
            bsave:true
        }
    )
</script>

