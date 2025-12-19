<?php
/**
 * @package      pXP
 * @file         ACTReporte.php
 * @author       (fea)
 * @date         19-04-2018 16:05:34
 * @description  Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 */

require_once(dirname(__FILE__) . '/../reportes/RProgramaOperaciones.php');
require_once(dirname(__FILE__) . '/../reportes/RProgramaOperacionesXls.php');

class ACTProgramaOperacionesReporte extends ACTbase
{
    function accionMedianoPlazo()
    {
        $tipoReporte = $this->objParam->getParametro('tipo_reporte');
        $nombreArchivo = 'AccionMedianoPlazo';
        $tamano = 'LETTER';
        $orientacion = 'L';

        $titulo = 'ACCIONES DE MEDIANO PLAZO';
        $idGestion = $this->objParam->getParametro('id_gestion');
        $gestion = $this->objParam->getParametro('gestion');
        $this->objParam->addParametro('id_gestion', $idGestion);

        $idAccionMedianoPlazo = $this->objParam->getParametro('tipo_amp');
        if ($idAccionMedianoPlazo != '' && $idAccionMedianoPlazo != '0') {
            $this->objParam->addFiltro("obj.id_objetivo IN (" . $idAccionMedianoPlazo . ")");
        }

        $idPartida = $this->objParam->getParametro('id_partida');
        if ($idPartida != '' && $idPartida != '0') {
            $this->objParam->addFiltro("obj_par.id_partida IN (" . $idPartida . ")");
        }

        $idPresupuesto = $this->objParam->getParametro('id_presupuesto');
        if ($idPresupuesto != '' && $idPresupuesto != '0') {
            $this->objParam->addFiltro('obj_pres.id_presupuesto IN (' . $idPresupuesto . ')');
        }

        $this->objFunc = $this->create('MODObjetivo');
        $this->res = $this->objFunc->obtenerAccionesMedianoPlazo();

        if ($tipoReporte == 'pdf') {
            $nombreArchivo = $nombreArchivo . uniqid(md5(session_id())) . '.pdf';

            $this->objParam->addParametro('tamano', $tamano);
            $this->objParam->addParametro('orientacion', $orientacion);
            $this->objParam->addParametro('nombre_archivo', $nombreArchivo);

            $reporte = new RProgramaOperaciones($this->objParam);
            $reporte->setTitulo($titulo);
            $reporte->setGestion($gestion);
            $reporte->datosHeader($this->res->getDatos());
            $reporte->generarAccionMedianoPlazo();
            $reporte->output($reporte->url_archivo, 'F');
        } else {
            $nombreArchivo = $nombreArchivo . uniqid(md5(session_id())) . '.xls';
            $this->objParam->addParametro('nombre_archivo', $nombreArchivo);
            $this->objParam->addParametro('datos', $this->res->getDatos());
            $this->objReporte = new RProgramaOperacionesXls($this->objParam);
            $this->objReporte->setTitulo($titulo);
            $this->objReporte->setGestion($gestion);
            $this->objReporte->generarReporteAMP();
        }

        $mensajeExito = new Mensaje();
        $mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado', 'Se generó con éxito el reporte: ' . $nombreArchivo, 'control');
        $mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->res = $mensajeExito;
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function accionCortoPlazo()
    {
        $tipoReporte = $this->objParam->getParametro('tipo_reporte');
        $nombreArchivo = 'AccionCortoPlazo';
        $tamano = 'LETTER';
        $orientacion = 'L';

        $titulo = 'ACCIONES DE CORTO PLAZO';
        $idGestion = $this->objParam->getParametro('id_gestion');
        $gestion = $this->objParam->getParametro('gestion');
        $this->objParam->addParametro('id_gestion', $idGestion);

        $idAccionCortoPlazo = $this->objParam->getParametro('tipo_acp');
        if ($idAccionCortoPlazo != '' && $idAccionCortoPlazo != '0') {
            $this->objParam->addFiltro("acp.id_objetivo IN (" . $idAccionCortoPlazo . ")");
        }

        $idPartida = $this->objParam->getParametro('id_partida');
        if ($idPartida != '' && $idPartida != '0') {
            $this->objParam->addFiltro("obj_par.id_partida IN (" . $idPartida . ")");
        }

        $idPresupuesto = $this->objParam->getParametro('id_presupuesto');
        if ($idPresupuesto != '' && $idPresupuesto != '0') {
            $this->objParam->addFiltro('pre.id_centro_costo IN (' . $idPresupuesto . ')');
        }

        $this->objFunc = $this->create('MODObjetivo');
        $this->res = $this->objFunc->obtenerAccionesCortoPlazoCAMP();

        if ($tipoReporte == 'pdf') {
            $nombreArchivo = $nombreArchivo . uniqid(md5(session_id())) . '.pdf';

            $this->objParam->addParametro('tamano', $tamano);
            $this->objParam->addParametro('orientacion', $orientacion);
            $this->objParam->addParametro('nombre_archivo', $nombreArchivo);

            $reporte = new RProgramaOperaciones($this->objParam);
            $reporte->setTitulo($titulo);
            $reporte->setGestion($gestion);
            $reporte->datosHeader($this->res->getDatos());
            $reporte->generarAccionCortoPlazo();
            $reporte->output($reporte->url_archivo, 'F');
        } else {
            $nombreArchivo = $nombreArchivo . uniqid(md5(session_id())) . '.xls';
            $this->objParam->addParametro('nombre_archivo', $nombreArchivo);
            $this->objParam->addParametro('datos', $this->res->getDatos());
            $this->objReporte = new RProgramaOperacionesXls($this->objParam);
            $this->objReporte->setTitulo($titulo);
            $this->objReporte->setGestion($gestion);
            $this->objReporte->generarReporteACP();
        }
        $mensajeExito = new Mensaje();
        $mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado', 'Se generó con éxito el reporte: ' . $nombreArchivo, 'control');
        $mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->res = $mensajeExito;
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function articulacionPoa()
    {
        $tipoReporte = $this->objParam->getParametro('tipo_reporte');
        $nombreArchivo = 'POA con presupuestos';
        $tamano = 'LETTER';
        $orientacion = 'L';

        $titulo = 'ARTICULACIÓN POA CON EL PRESUPUESTO';
        $idGestion = $this->objParam->getParametro('id_gestion');
        $gestion = $this->objParam->getParametro('gestion');
        $this->objParam->addParametro('id_gestion', $idGestion);

        $this->objFunc = $this->create('MODObjetivo');
        $this->res = $this->objFunc->articulacionPoa();

        if ($tipoReporte == 'pdf') {
            $nombreArchivo = $nombreArchivo . uniqid(md5(session_id())) . '.pdf';

            $this->objParam->addParametro('tamano', $tamano);
            $this->objParam->addParametro('orientacion', $orientacion);
            $this->objParam->addParametro('nombre_archivo', $nombreArchivo);

            $reporte = new RProgramaOperaciones($this->objParam);
            $reporte->setTitulo($titulo);
            $reporte->setGestion($gestion);
            $reporte->datosHeader($this->res->getDatos());
            $reporte->generarArticulacionPoa();
            $reporte->output($reporte->url_archivo, 'F');
        } else {
            $nombreArchivo = $nombreArchivo . uniqid(md5(session_id())) . '.xls';
            $this->objParam->addParametro('nombre_archivo', $nombreArchivo);
            $this->objParam->addParametro('datos', $this->res->getDatos());
            $this->objReporte = new RProgramaOperacionesXls($this->objParam);
            $this->objReporte->setTitulo($titulo);
            $this->objReporte->setGestion($gestion);
            $this->objReporte->generarReporteAPOA();
        }
        $mensajeExito = new Mensaje();
        $mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado', 'Se generó con éxito el reporte: ' . $nombreArchivo, 'control');
        $mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->res = $mensajeExito;
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
}

?>