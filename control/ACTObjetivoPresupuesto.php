<?php
/**
 * @package pXP
 * @file gen-ACTObjetivoPresupuesto.php
 * @author  (franklin.espinoza)
 * @date 27-07-2017 16:10:48
 * @description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 */

class ACTObjetivoPresupuesto extends ACTbase
{
    function listarObjetivoPresupuesto()
    {
        $this->objParam->defecto('ordenacion', 'id_objetivo_presupuesto');

        $this->objParam->defecto('dir_ordenacion', 'asc');
        if ($this->objParam->getParametro('tipoReporte') == 'excel_grid' || $this->objParam->getParametro('tipoReporte') == 'pdf_grid') {
            $this->objReporte = new Reporte($this->objParam, $this);
            $this->res = $this->objReporte->generarReporteListado('MODObjetivoPresupuesto', 'listarObjetivoPresupuesto');
        } else {
            $this->objFunc = $this->create('MODObjetivoPresupuesto');

            $this->res = $this->objFunc->listarObjetivoPresupuesto($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function insertarObjetivoPresupuesto()
    {
        $this->objFunc = $this->create('MODObjetivoPresupuesto');
        if ($this->objParam->insertar('id_objetivo_presupuesto')) {
            $this->res = $this->objFunc->insertarObjetivoPresupuesto($this->objParam);
        } else {
            $this->res = $this->objFunc->modificarObjetivoPresupuesto($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function eliminarObjetivoPresupuesto()
    {
        $this->objFunc = $this->create('MODObjetivoPresupuesto');
        $this->res = $this->objFunc->eliminarObjetivoPresupuesto($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function obtenerCentroCostosPorObjetivos()
    {
        $this->objParam->addFiltro("vcc.id_gestion = " . $this->objParam->getParametro('id_gestion'));
        if ($this->objParam->getParametro('id_objetivo') != '0' && $this->objParam->getParametro('id_objetivo') != '') {
            $this->objParam->addFiltro("tobj.id_objetivo_fk IN (" . $this->objParam->getParametro('id_objetivo') . ")");
        }
        $this->objFunc = $this->create('MODObjetivoPresupuesto');
        $this->res = $this->objFunc->obtenerCentroCostosPorObjetivos($this->objParam);
        $datos = $this->res->getDatos();
        array_unshift($datos, array(
            'id_presupuesto' => '0',
            'desc_presupuesto' => 'Todos'
        ));
        $this->res->setDatos($datos);

        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function obtenerCentroCostosPorAccionescp()
    {
        $this->objParam->addFiltro("vcc.id_gestion = " . $this->objParam->getParametro('id_gestion'));
        if ($this->objParam->getParametro('id_objetivo') != '0' && $this->objParam->getParametro('id_objetivo') != '') {
            $this->objParam->addFiltro("tobj.id_objetivo IN (" . $this->objParam->getParametro('id_objetivo') . ")");
        }
        $this->objFunc = $this->create('MODObjetivoPresupuesto');
        $this->res = $this->objFunc->obtenerCentroCostosPorAccionescp($this->objParam);
        $datos = $this->res->getDatos();
        array_unshift($datos, array(
            'id_presupuesto' => '0',
            'desc_presupuesto' => 'Todos'
        ));
        $this->res->setDatos($datos);

        $this->res->imprimirRespuesta($this->res->generarJson());
    }
}

?>