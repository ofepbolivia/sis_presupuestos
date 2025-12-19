<?php
/**
 * @package pXP
 * @file gen-ACTObjetivoPartida.php
 * @author  (franklin.espinoza)
 * @date 24-07-2017 13:34:28
 * @description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 */

class ACTObjetivoPartida extends ACTbase
{

    function listarObjetivoPartida()
    {

        $this->objParam->defecto('ordenacion', 'id_objetivo_partida');

        $this->objParam->defecto('dir_ordenacion', 'asc');

        /*if($this->objParam->getParametro('id_objetivo')!=''){
            $this->objParam->addFiltro("obj_part.id_objetivo = ".$this->objParam->getParametro('id_objetivo'));
        }*/

        if ($this->objParam->getParametro('tipoReporte') == 'excel_grid' || $this->objParam->getParametro('tipoReporte') == 'pdf_grid') {
            $this->objReporte = new Reporte($this->objParam, $this);
            $this->res = $this->objReporte->generarReporteListado('MODObjetivoPartida', 'listarObjetivoPartida');
        } else {
            $this->objFunc = $this->create('MODObjetivoPartida');

            $this->res = $this->objFunc->listarObjetivoPartida($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function insertarObjetivoPartida()
    {
        $this->objFunc = $this->create('MODObjetivoPartida');
        if ($this->objParam->insertar('id_objetivo_partida')) {
            $this->res = $this->objFunc->insertarObjetivoPartida($this->objParam);
        } else {
            $this->res = $this->objFunc->modificarObjetivoPartida($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function eliminarObjetivoPartida()
    {
        $this->objFunc = $this->create('MODObjetivoPartida');
        $this->res = $this->objFunc->eliminarObjetivoPartida($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function obtenerPartidasPorObjetivos()
    {
        $this->objParam->addFiltro("tg.id_gestion = " . $this->objParam->getParametro('id_gestion'));

        if ($this->objParam->getParametro('id_objetivo') != '0' && $this->objParam->getParametro('id_objetivo') != '') {
            $this->objParam->addFiltro("tobj.id_objetivo_fk IN (" . $this->objParam->getParametro('id_objetivo') . ")");
        }
        $this->objFunc = $this->create('MODObjetivoPartida');
        $this->res = $this->objFunc->obtenerPartidasPorObjetivos($this->objParam);

        $datos = $this->res->getDatos();
        array_unshift($datos, array(
            'id_partida' => '0',
            'codigo' => '0',
            'desc_partida' => 'Todos'
        ));
        $this->res->setDatos($datos);

        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function obtenerPartidasPorAccionescp()
    {
        $this->objParam->addFiltro("tg.id_gestion = " . $this->objParam->getParametro('id_gestion'));

        if ($this->objParam->getParametro('id_objetivo') != '0' && $this->objParam->getParametro('id_objetivo') != '') {
            $this->objParam->addFiltro("tobj.id_objetivo IN (" . $this->objParam->getParametro('id_objetivo') . ")");
        }
        $this->objFunc = $this->create('MODObjetivoPartida');
        $this->res = $this->objFunc->obtenerPartidasPorAccionescp($this->objParam);

        $datos = $this->res->getDatos();
        array_unshift($datos, array(
            'id_partida' => '0',
            'codigo' => '0',
            'desc_partida' => 'Todos'
        ));
        $this->res->setDatos($datos);

        $this->res->imprimirRespuesta($this->res->generarJson());
    }
}

?>