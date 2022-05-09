<?php
/*
dev: breydi vasquez
description: reporte Formulacion presupuestaria
date: 04/09/2020
*/
require_once dirname(__FILE__).'/../../pxp/lib/lib_reporte/ReportePDF.php';

class RFormPresupPDF extends  ReportePDF{
    var $datos ;
    var $ancho_hoja;
    var $numeracion;
    var $ancho_sin_totales;
    var $cantidad_columnas_estaticas;

    function Header() {
        $white = array('LTRB' =>array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 255, 255)));
        $black = array('T' =>array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        $responsable = $this->objParam->getParametro('responsable');
        $obs = $this->objParam->getParametro('obs');
        $fecha_crea = $this->objParam->getParametro('fecha_crea');
        $ID = $this->objParam->getParametro('id_formulacion_presu');
        $usu_creacion = $this->objParam->getParametro('usu_creacion');

        $this->Ln(3);
        //cabecera del reporte
        $this->Image(dirname(__FILE__).'/../../lib/imagenes/logos/logo.jpg', 10,5,35,20);
        // $this->ln(5);
        $this->SetFont('','B',11);
        $this->Cell(0,5,"FORMULACIÓN PRESUPUESTARIA",0,1,'C');
        $this->Cell(0,5, "Expresado en Bolvianos",0,1,'C');
        $this->Ln(10);

        // $this->SetFont('','B',5);

        $tbl = '<table border="0" style="font-size: 7pt;">
                <tr>
                    <td width="50%"><span style="color:#1F3656;">Responsable: </span>'.$responsable.'</td>
                    <td width="40%"><span style="color:#1F3656;">N° Identificador: </span>'.$ID.'</td>
                </tr>
                <tr>
                    <td width="50%"><span style="color:#1F3656;">Usuario de Registro: </span>'.$usu_creacion.'</td>
                    <td width="40%"><span style="color:#1F3656;">Fecha Creación: </span>'.date('d/m/Y', strtotime($fecha_crea)).'</td>
                </tr>
                <tr>
                    <td width="100%" height="20px"><span style="color:#1F3656;">Descripción: </span>'.$obs.'</td>
                </tr>
                </table>
                ';

        $this->writeHTML ($tbl);
        $this->Ln(-3.5);
        // <td width="4%"><b>N° CONTRATO</b></td>
        // <td width="5%"><b>PROVEEDOR</b></td>
        // <td width="5%"><b>HOJA DE RESPALDO</b></td>
        $header = '<table border="1" style="font-size: 5pt;color:#1F3656;"><tr align="center;">
                        <td width="2%"><b>N°</b></td>
                        <td width="7%"><b>CENTRO DE COSTOS</b></td>
                        <td width="7%"><b>CONCEPTO  INGRESO GASTO</b></td>
                        <td width="7%"><b>PARTIDA</b></td>
                        <td width="20%"><b>JUSTIFICACIÓN</b></td>
                        <td width="4%"><b>ENERO</b></td>
                        <td width="4%"><b>FEBRERO</b></td>
                        <td width="4%"><b>MARZO</b></td>
                        <td width="4%"><b>ABRIL</b></td>
                        <td width="4%"><b>MAYO</b></td>
                        <td width="4%"><b>JUNIO</b></td>
                        <td width="4%"><b>JULIO</b></td>
                        <td width="4%"><b>AGOSTO</b></td>
                        <td width="5%"><b>SEPTIEMBRE</b></td>
                        <td width="4%"><b>OCTUBRE</b></td>
                        <td width="5%"><b>NOVIEMBRE</b></td>
                        <td width="5%"><b>DICIEMBRE</b></td>
                        <td width="5%"><b>Importe Total</b></td>
                      </tr></table>
                     ';
        $this->writeHTML ($header, true, false,false, false, 'C');
    }

    function setDatos($datos) {

        $this->datos = $datos;
        $this->SetHeaderMargin(8);
        $this->SetAutoPageBreak(TRUE, 12);
        $this->ancho_hoja = $this->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT-10;
        $this->SetMargins(3, 48.8, 2);
        // $por_eje = number_format((float)$calc, 2, '.', '');
        // var_dump( $this->datos);exit;
    }

    function  generarReporte()
    {

        $this->AddPage();
        $this->Ln();
        $contador = 1;
        $totales = 0;
        foreach ($this->datos as $record) {
            $this->SetTextColor(0);
            $this->SetFont('','',4);
                                     // 1   2     3     4     5     6     7      8    E     F     M     A     M      J    JU   AG    SE      O     N    D
            // $conf_par_tablewidths=array(6, 20.3, 20.5, 20.6, 20.5, 11.8, 14.5, 14.8, 11.6, 11.8, 11.7, 11.7, 11.7, 11.8, 11.7, 11.7, 14.6, 11.7, 14.7, 14.7, 14.6);
                                      // 1   2     3     4     5    E     F     M     A     M      J    JU   AG    SE      O     N    D
            $conf_par_tablewidths=array(6, 20.3, 20.4, 20.4, 58.4, 11.7, 11.7, 11.7, 11.7, 11.6, 11.8, 11.7, 11.6, 14.6, 11.7, 14.6, 14.6, 14.6);
            $conf_par_tablealigns=array('C','L','C','L','C','R','R','R','R','R','R','R','R','R','R','R','R','R');
            $conf_par_tablenumbers=array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
            $conf_tableborders=array('LTBR','LTBR','LTBR','LTBR','LTBR','LTBR','LTBR','LTBR','LTBR','LTBR','LTBR','LTBR','LTBR','LTBR','LTBR','LTBR','LTBR','LTBR');

            $this->tablewidths=$conf_par_tablewidths;
            $this->tablealigns=$conf_par_tablealigns;
            $this->tablenumbers=$conf_par_tablenumbers;
            $this->tableborders=$conf_tableborders;

            $RowArray = array(
                's0' => $contador,
                's1' => $record["codigo_cc"],
                's2' => $record["nombre_ingas"],
                's3' => $record["nombre_partida"],
                's4' => $record["justificacion"],
                // 's5' => $record["nro_contrato"],
                // 's6' => $record["proveedor"],
                // 's7' => $record["hoja_respaldo"],
                's5' => number_format($record["periodo_enero"], 2, ',', '.'),
                's6' => number_format($record["periodo_febrero"], 2, ',', '.'),
                's7' => number_format($record["periodo_marzo"], 2, ',', '.'),
                's8' => number_format($record["periodo_abril"], 2, ',', '.'),
                's9' => number_format($record["periodo_mayo"], 2, ',', '.'),
                's10' => number_format($record["periodo_junio"], 2, ',', '.'),
                's11' => number_format($record["periodo_julio"], 2, ',', '.'),
                's12' => number_format($record["periodo_agosto"], 2, ',', '.'),
                's13' => number_format($record["periodo_septiembre"], 2, ',', '.'),
                's14' => number_format($record["periodo_octubre"], 2, ',', '.'),
                's15' => number_format($record["periodo_noviembre"], 2, ',' ,'.'),
                's16' => number_format($record["periodo_diciembre"], 2, ',', '.'),
                's17' => number_format($record["importe_total"], 2, ',', '.')

            );

            $this-> MultiRow($RowArray);
            $contador++;
            $totales += $record['importe_total'];

        }
        $this->Ln();
        $RowArray = array(
           's0' => '',
           's1' => 'TOTAL',
           's2' => number_format($totales, 2, ',', '.'),

          );

        $this->SetFont('','B',8);
        $this->tablewidths=array(220, 29, 40);
        $this->tableborders=array('','','B');
        $this->tablealigns=array('','R', 'R');
        $this->tablenumbers=array(0,0,0);
        $this-> MultiRow($RowArray);

    }
}
?>
