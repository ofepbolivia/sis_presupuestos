<?php
//require_once dirname(__FILE__).'/../../pxp/lib/lib_reporte/ReportePDF.php';
//require_once dirname(__FILE__).'/../../lib/tcpdf/tcpdf_barcodes_2d.php';
set_time_limit(400);

class RPresupPartidaPdf extends  ReportePDF{
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

      //if ($this->objParam->getParametro('desde') == '' && $this->objParam->getParametro('hasta') != '') {
      //  $cabecera_datos = ' <td style="width: 200px; text-align: center; vertical-align: middle; height: 42px;"><strong>Departamento:</strong> Contabilidad <br /><strong>Desde: </strong>01/01/'.$this->objParam->getParametro('gestion').' <strong>Hasta:</strong> '.$this->objParam->getParametro('hasta').'<br /><strong>Gesti&oacute;n:</strong>'.$this->objParam->getParametro('gestion').'</td>
      //                  ';
      //} elseif ($this->objParam->getParametro('hasta') == '' && $this->objParam->getParametro('desde') != '') {
      // $cabecera_datos = '
      //                    <td style="width: 200px; text-align: center; vertical-align: middle; height: 42px;"><strong>Departamento:</strong> Contabilidad <br /><strong>Desde: </strong>'.$this->objParam->getParametro('desde').' <strong>Hasta:</strong> 31/12/'.$this->objParam->getParametro('gestion').'<br /><strong>Gesti&oacute;n:</strong> '.$this->objParam->getParametro('gestion').'</td>
      //
      //                        ';
      //  } elseif ($this->objParam->getParametro('hasta') != '' && $this->objParam->getParametro('desde') != '') {
      //    $cabecera_datos = '
      //                        <td style="width: 200px; text-align: center; vertical-align: middle; height: 42px;"><strong>Departamento:</strong> Contabilidad <br /><strong>Desde: </strong>'.$this->objParam->getParametro('desde').' <strong>Hasta:</strong> '.$this->objParam->getParametro('hasta').'<br /><strong>Gesti&oacute;n:</strong> '.$this->objParam->getParametro('gestion').'</td>
      //                      ';
      //  } elseif ($this->objParam->getParametro('hasta') == '' && $this->objParam->getParametro('desde') == '') {
      //    $cabecera_datos = '
      //                        <td style="width: 200px; text-align: center; vertical-align: middle; height: 42px;"><strong>Departamento:</strong> Contabilidad <br /><strong>Desde: </strong> 01/01/'.$this->objParam->getParametro('gestion').' <strong>Hasta:</strong> 31/12/'.$this->objParam->getParametro('gestion').'<br /><strong>Gesti&oacute;n:</strong> '.$this->objParam->getParametro('gestion').'</td>
      //                      ';
      //  }

      $cabecera = '<table style="height: 20px;" cellspacing="0" cellpadding="2">
      <thead>
          <tr>
              <th border="1" rowspan="2" style="width: 100px; height: 26px;"><img  style="width: 80px;" align="middle" src="../../../lib/imagenes/logos/logo.jpg" alt="Logo"></th>
              <th border="1" colspan="2" style="border-bottom: none; width: 610px; text-align: center; vertical-align: middle; height: 26px;"><strong>PRESUPUESTO ESTADO: '.strtoupper($this->objParam->getParametro('estado')).'</strong></th>
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
        $tabla_datos = '
                          <table style="text-align: center; font-size: 8px;" border="0" cellspacing="0" cellpadding="1">
                            <thead>
                                <tr style="font-size: 8px; background-color:#c6c6c6;">
                                    <td style="border:1px solid black; width: 20px;"><strong>#</strong></td>
                                    <td style="border:1px solid black; width: 290px;"><strong>Partida</strong></td>
                                    <td style="border:1px solid black; width: 100px;"><strong>Importe Según Memoria</strong></td>
                                    <td style="border:1px solid black; width: 100px;"><strong>Importe Aprobado</strong></td>
                                    <td style="border:1px solid black; width: 60px;"><strong>Estado Reg.</strong></td>
                                    <td style="border:1px solid black; width: 70px;"><strong>Fecha creación:</strong></td>
                                    <td style="border:1px solid black; width: 70px;"><strong>Creado por:</strong></td>
                                </tr>
                            </thead> ';

                    $contador=1;
          foreach( $this->datos as $record){
                        $tabla_datos .='<tbody>
                        <tr nobr="true">
                        <td style="border:1px solid black; width: 20px; text-align: center; vertical-align: middle;">'.$contador.'</td>
                        <td style="border:1px solid black; width: 290px; text-align: left; vertical-align: middle;">'.$record["desc_partida"].'</td>
                        <td style="border:1px solid black; width: 100px; text-align: right; vertical-align: middle;">'.number_format($record["importe"],2).'</td>
                        <td style="border:1px solid black; width: 100px; text-align: right; vertical-align: middle;">'.number_format($record["importe_aprobado"],2).'</td>
                        <td style="border:1px solid black; width: 60px; text-align: left; vertical-align: middle;">'.$record["estado_reg"].'</td>
                        <td style="border:1px solid black; width: 70px; text-align: left; vertical-align: middle;">'.substr($record["fecha_reg"],0,10).'</td>
                        <td style="border:1px solid black; width: 70px; text-align: left; vertical-align: middle;">'.$record["usr_reg"].'</td>
                        </tr>
                        </tbody>
                        ';
                        $contador++;

            }
          /****************************************************************************************************************************************/
            $tabla_datos .= '
                            <tr style="font-size: 9px;">
                            <td style="width: 20px;"><strong></strong></td>
                            <td style="width: 290px; text-align:right;"><strong>Totales:</strong></td>
                            <td style="border:1px solid black; width: 100px; text-align:right;"><strong>'.number_format($this->totales["total_importe"],2).'</strong></td>
                            <td style="border:1px solid black; width: 100px; text-align:right;"><strong>'.number_format($this->totales["total_importe_aprobado"],2).'</strong></td>
                            <td style="width: 60px;"><strong></strong></td>
                            <td style="width: 70px;"><strong></strong></td>
                            <td style="width: 70px;"><strong></strong></td>
                        </tr>
                          </table>';
          //<td style="border-right:2px solid white; border-bottom: 2px solid white;">'.$total.'</td>
          //$this->writeHTML($tabla_datos);
          //$this->writeHTMLCell('', '', '', '', $tabla_datos, 0, 0, 0, true, '', true);
       
          $this->writeHTML($tabla_datos, true, false, true, false, '');
          //$this->writeHTML($tabla_totales, true, false, true, false, '');
          //
    }
}
?>
