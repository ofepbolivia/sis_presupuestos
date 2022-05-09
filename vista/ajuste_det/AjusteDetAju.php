<?php
/**
 *@package pXP
 *@file gen-SistemaDist.php
 *@author  maylee.perez
 *@date 16-06-2021 10:22:05
 *@description Archivo con la interfaz de usuario que permite
 *dar el visto a solicitudes de compra
 *
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.AjusteDetAju = {
        bedit: true,
        bnew: true,
        bsave: true,
        bdel: true,
        require: '../../../sis_presupuestos/vista/ajuste_det/AjusteDet.php',
        requireclase: 'Phx.vista.AjusteDet',
        title: 'Incrementos',
        nombreVista: 'AjusteDetAju',

        constructor: function(config) {
            Phx.vista.AjusteDetAju.superclass.constructor.call(this,config);
            this.init();
            var dataPadre = Phx.CP.getPagina(this.idContenedorPadre).getSelectedData()
            if(dataPadre){
                this.onEnablePanel(this, dataPadre);
            }
            else{
                this.bloquearMenus();
            }

        },
        //ll
        onReloadPage:function(m){
            this.maestro=m;
            this.store.baseParams={id_ajuste: this.maestro.id_ajuste, tipo_ajuste: 'ajuste'};

            if(this.maestro.tipo_ajuste == 'inc_comprometido'){
                this.Cmp.id_presupuesto.store.baseParams.nro_tramite = this.maestro.nro_tramite;
                this.Cmp.id_presupuesto.store.baseParams.tipo_ajuste = this.maestro.tipo_ajuste;
                this.Cmp.id_partida.store.baseParams.nro_tramite = this.maestro.nro_tramite;
                this.Cmp.id_partida.store.baseParams.tipo_ajuste = this.maestro.tipo_ajuste;

            }
            else{
                delete this.Cmp.id_presupuesto.store.baseParams.nro_tramite;
                delete this.Cmp.id_presupuesto.store.baseParams.tipo_ajuste;
                delete this.Cmp.id_partida.store.baseParams.nro_tramite;
                delete this.Cmp.id_partida.store.baseParams.tipo_ajuste;
            }

            this.Cmp.id_presupuesto.store.baseParams.id_gestion = this.maestro.id_gestion;
            this.Cmp.id_presupuesto.store.baseParams.movimiento_tipo_pres = this.maestro.movimiento;
            this.Cmp.id_partida.store.baseParams.id_gestion = this.maestro.id_gestion;
            this.Cmp.id_partida.store.baseParams.partida_rubro = this.maestro.movimiento;
            this.Cmp.id_presupuesto.modificado = true;
            this.Cmp.id_partida.modificado = true;


            this.load({params:{start:0, limit:50}});
        },

        loadValoresIniciales:function(){
            Phx.vista.AjusteDetAju.superclass.loadValoresIniciales.call(this);
            this.Cmp.id_ajuste.setValue(this.maestro.id_ajuste);
            this.Cmp.tipo_ajuste.setValue('ajuste');
        }

    };
</script>