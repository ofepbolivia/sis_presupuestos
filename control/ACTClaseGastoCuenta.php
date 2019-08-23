<?php
/**
 *@package pXP
 *@file gen-ACTClaseGastoCuenta.php
 *@author  Maylee perez Pastor
 *@date 22-08-2019 02:33:23
 *@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 */

class ACTClaseGastoCuenta extends ACTbase{

    function listarClaseGastoCuenta(){
        $this->objParam->defecto('ordenacion','id_clase_gasto_cuenta');

        $this->objParam->defecto('dir_ordenacion','asc');

        if($this->objParam->getParametro('id_clase_gasto')!=''){
            $this->objParam->addFiltro("cgc.id_clase_gasto = ".$this->objParam->getParametro('id_clase_gasto'));
        }

        if($this->objParam->getParametro('id_gestion')!=''){
            $this->objParam->addFiltro("cuen.id_gestion = ".$this->objParam->getParametro('id_gestion'));
        }


        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODClaseGastoCuenta','listarClaseGastoCuenta');
        } else{
            $this->objFunc=$this->create('MODClaseGastoCuenta');

            $this->res=$this->objFunc->listarClaseGastoCuenta($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function insertarClaseGastoCuenta(){
        $this->objFunc=$this->create('MODClaseGastoCuenta');
        if($this->objParam->insertar('id_clase_gasto_cuenta')){
            $this->res=$this->objFunc->insertarClaseGastoCuenta($this->objParam);
        } else{
            $this->res=$this->objFunc->modificarClaseGastoCuenta($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function eliminarClaseGastoCuenta(){
        $this->objFunc=$this->create('MODClaseGastoCuenta');
        $this->res=$this->objFunc->eliminarClaseGastoCuenta($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

}

?>