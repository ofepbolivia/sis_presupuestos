<?php
//require_once dirname(__FILE__).'/../../pxp/lib/lib_reporte/ReportePDF.php';
//require_once dirname(__FILE__).'/../../lib/tcpdf/tcpdf_barcodes_2d.php';
set_time_limit(400);

class RPresupPartidaEstadoPdf extends  ReportePDF{
    var $datos ;
    var $totales;
    var $ancho_hoja;
    var $gerencia;
    var $numeracion;
    var $ancho_sin_totales;
    var $cantidad_columnas_estaticas;
    var $total = 0;
    var $html;
    var $footer;
      //$fecha = date("d/m/Y", strtotime($record["fecha"]));

    function Header() {

      $this->setPrintFooter(false);
      //$this->setFooterData(array(0,64,0), array(0,64,128));
      //$this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
      //$this->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

      $this->SetXY(5,5);


      $cabecera = '<table Cellpadding="2">
      <thead>
          <tr>
              <th border="1" rowspan="2" style="width: 100px; height: 42px;"><img  style="width: 80px;" align="middle" src="../../../lib/imagenes/logos/logo.jpg" alt="Logo"></th>
              <th border="1" colspan="2" style="border-bottom: none; width: 830px; text-align: center; vertical-align: middle; height: 42px;"><strong>ANTEPROYECTO DE PRESUPUESTO - ESTADO: '.strtoupper($this->objParam->getParametro('estado')).'</strong></th>
          </tr>
          <tr>
              <th border="1"><span style="font-size:8px; text-align:left;"><strong>Tipo de Centro: </strong>'.strtoupper($this->objParam->getParametro('codigo_cc')).'</span></th>
              <th border="1"><span style="font-size:8px; text-align:right;"><strong>Número de Trámite: </strong>'.strtoupper($this->objParam->getParametro('nro_tramite')).'</span></th>
          </tr>
      </thead>
  </table>';  

      $this->writeHTML($cabecera, true, 0, true, 0);
      //$this->SetAutoPageBreak(true, 2);
    }

    function datosHeader($datos,$totales,$recuperar_cabecera) {

        $this->datos = $datos;
        $this->totales = $totales;
        $this->cabezera = $recuperar_cabecera;
        //var_dump( $this->datos);
    }

    function  generarReporte()
    {

    $this->AddPage();
    $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $this->SetXY(5,22);

          //$this->writeHTML ($tabla_datos_cabeza);
        /*******************************************************************************************************************************************/
          $this->SetX(5);
          $this->SetMargins(5, 23, 0);

        /************************************Creamos la estructura para el detalle******************************************************************/
        $tabla_datos = '<br><br><br><table style="text-align: center; font-size: 9px;" border="0" cellspacing="0" cellpadding="2">
                            <thead>
                                <tr style="font-size: 9px; background-color:#c6c6c6;">
                                    <td style="border:1px solid black; width: 15px;"><strong>#</strong></td>
                                    <td style="border:1px solid black; width: 190px;"><strong>Partida</strong></td>
                                    <td style="border:1px solid black; width: 75px;"><strong>Segun Memoria</strong></td>
                                    <td style="border:1px solid black; width: 65px;"><strong>Aprobado</strong></td>
                                    <td style="border:1px solid black; width: 65px;"><strong>Ajustes</strong></td>
                                    <td style="border:1px solid black; width: 65px;"><strong>Vigente</strong></td>
                                    <td style="border:1px solid black; width: 75px;"><strong>Comprometido</strong></td>
                                    <td style="border:1px solid black; width: 65px;"><strong>Ejecutado</strong></td>
                                    <td style="border:1px solid black; width: 65px;"><strong>Pagado</strong></td>
                                    <td style="border:1px solid black; width: 65px;"><strong>% Ejecución</strong></td>
                                    <td style="border:1px solid black; width: 60px;"><strong>Estado Reg.</strong></td>
                                    <td style="border:1px solid black; width: 60px;"><strong>Fecha de Creación</strong></td>
                                    <td style="border:1px solid black; width: 60px;"><strong>Creado Por</strong></td>
                                </tr>
                            </thead> ';

          $contador=1;
          foreach( $this->datos as $record){
                        $tabla_datos 
                        .='<tbody>
                        <tr nobr="true">
                        
                        <td style="border:1px solid black; width: 15px; text-align: center; vertical-align: middle;">'.$contador.'</td>
                        <td style="border:1px solid black; width: 190px; text-align: left; vertical-align: middle;">'.$record["desc_partida"].'</td>
                        <td style="border:1px solid black; width: 75px; text-align: right; vertical-align: middle;">'.number_format($record["importe"],2).'</td>
                        <td style="border:1px solid black; width: 65px; text-align: right; vertical-align: middle;">'.number_format($record["importe_aprobado"],2).'</td>
                        <td style="border:1px solid black; width: 65px; text-align: right; vertical-align: middle;">'.number_format($record["ajustado"],2).'</td>
                        <td style="border:1px solid black; width: 65px; text-align: right; vertical-align: middle;">'.number_format($record["formulado"],2).'</td>
                        <td style="border:1px solid black; width: 75px; text-align: right; vertical-align: middle;">'.number_format($record["comprometido"],2).'</td>
                        <td style="border:1px solid black; width: 65px; text-align: right; vertical-align: middle;">'.number_format($record["ejecutado"],2).'</td>
                        <td style="border:1px solid black; width: 65px; text-align: right; vertical-align: middle;">'.number_format($record["pagado"],2).'</td>
                        <td style="border:1px solid black; width: 65px; text-align: right; vertical-align: middle;">'.number_format($record["porc_ejecucion"],2).'</td>
                        <td style="border:1px solid black; width: 60px; text-align: right; vertical-align: middle;">'.$record["estado_reg"].'</td>
                        <td style="border:1px solid black; width: 60px; text-align: right; vertical-align: middle;">'.substr($record["fecha_reg"],0,10).'</td>
                        <td style="border:1px solid black; width: 60px; text-align: right; vertical-align: middle;">'.$record["usr_reg"].'</td>
                        </tr>
                        </tbody>
                        ';
                        $contador++;

            }
          /****************************************************************************************************************************************/
            $ajustado = $this->totales["total_importe_aprobado"] - $this->totales["total_importe_formulado"];
            $tabla_datos .= 
                            '<tr style="font-size: 9px;">
                            <td style="width: 15px;"><strong></strong></td>
                            <td style="width: 190px; text-align:right;"><strong>Totales:</strong></td>
                            <td style="border:1px solid black; width: 75px; text-align:right;"><strong>'.number_format($this->totales["total_importe"],2).'</strong></td>
                            <td style="border:1px solid black; width: 65px; text-align:right;"><strong>'.number_format($this->totales["total_importe_aprobado"],2).'</strong></td>
                            <td style="border:1px solid black; width: 65px; text-align:right;"><strong>'.number_format($ajustado,2).'</strong></td>
                            <td style="border:1px solid black; width: 65px; text-align:right;"><strong>'.number_format($this->totales["total_importe_formulado"],2).'</strong></td>
                            <td style="border:1px solid black; width: 75px; text-align:right;"><strong>'.number_format($this->totales["total_importe_comprometido"],2).'</strong></td>
                            <td style="border:1px solid black; width: 65px; text-align:right;"><strong>'.number_format($this->totales["total_importe_ejecutado"],2).'</strong></td>
                            <td style="border:1px solid black; width: 65px; text-align:right;"><strong>'.number_format($this->totales["total_importe_pagado"],2).'</strong></td>
                            <td style="border:1px solid black; width: 65px; text-align:right;"><strong>'.number_format($this->totales["importe"],2).'</strong></td>
                            <td style="width: 60px;"><strong></strong></td>
                            <td style="width: 60px;"><strong></strong></td>
                            <td style=" width: 60px;"><strong></strong></td>
                        </tr>
                            </table>';
          //<td style="border-right:2px solid white; border-bottom: 2px solid white;">'.$total.'</td>
          //$this->writeHTML($tabla_datos);
          //$this->writeHTMLCell('', '', '', '', $tabla_datos, 0, 0, 0, true, '', true);
          $this->writeHTML($tabla_datos, true, false, true, false, '');
    }
}
?>
