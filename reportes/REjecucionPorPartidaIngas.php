<?php

// Extend the TCPDF class to create custom MultiRow
//fRnk: nuevo reporte HR00856-2024
class REjecucionPorPartidaIngas extends ReportePDF
{
    private $datos_titulo;
    private $datos_detalle;
    private $ancho_hoja;
    private $s1;
    private $t1;
    private $tg1;
    private $total;
    private $datos_entidad;
    private $ult_codigo_partida;
    private $ult_concepto;
    private $totales_segun_memoria = 0;
    private $totales_aprobado = 0;
    private $totales_ajustado = 0;
    private $totales_vigente = 0;
    private $totales_comprometido = 0;
    private $totales_ejecutado = 0;
    private $totales_pagado = 0;
    private $totales_saldoXcomprometer = 0;
    private $totales_saldoXdevengar = 0;
    private $totales_saldoXpagar = 0;
    private $totales_porcentaje_ejecucion = 0;
    private $fill;


    function datosHeader($detalle, $totales, $gestion, $dataEmpresa, $fecha_ini, $fecha_fin)
    {
        $this->ancho_hoja = $this->getPageWidth() - PDF_MARGIN_LEFT - PDF_MARGIN_RIGHT - 10;
        $this->datos_detalle = $detalle;
        $this->datos_titulo = $totales;
        $this->datos_entidad = $dataEmpresa;
        $this->datos_gestion = $gestion;
        $this->fecha_ini = $fecha_ini;
        $this->fecha_fin = $fecha_fin;
        $this->subtotal = 0;
        $this->SetMargins(7, 65, 5);
    }

    function Header()
    {
        $this->Ln(3);
        $this->Image(dirname(__FILE__) . '/../../lib' . $_SESSION['_DIR_LOGO'], 10, 5, 35, 20);
        $this->ln(5);
        $this->SetFont('', 'BU', 12);
        $this->Cell(0, 5, "EJECUCIÓN PRESUPUESTARIA POR CONCEPTO DE INGRESO/GASTO", 0, 1, 'C');
        $this->Cell(0, 5, mb_strtoupper($this->datos_entidad['nombre'], 'UTF-8'), 0, 1, 'C');
        $this->Cell(0, 5, "GESTIÓN " . $this->datos_gestion['anho'], 0, 1, 'C');
        $this->SetFont('', 'B', 7);
        $this->Cell(0, 5, "(Expresado en Bolivianos)", 0, 1, 'C');
        $this->Ln(2);
        $this->SetFont('', 'B', 8);
        $this->Cell(0, 4, "De: " . ($this->fecha_ini) . "    al " . $this->fecha_fin, 0, 1, 'C');
        $this->SetFont('', '', 10);
        $height = 5;
        $width1 = 5;
        $width_c3 = 25;
        $concepto = $this->objParam->getParametro('concepto');
        $this->Ln();
        $this->SetFont('', '', 9);
        $this->SetTextColor(0, 0, 0);
        $this->Cell($width1, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $this->Cell($width_c3, $height, "CONCEPTO INGAS: " . $concepto, 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $this->Ln();
        $this->Ln();
        $this->SetFont('', 'B', 5.5);
        $this->generarCabecera();
    }

    function generarReporte()
    {
        $this->setFontSubsetting(false);
        $this->AddPage();
        $this->generarCuerpo($this->datos_detalle);
        $this->cerrarCuadro();
    }

    function generarCabecera()
    {
        $table = '<table border="1" cellpadding="1">';
        $table .= '<tr style="text-align: center">';
        $table .= '<td width="11%">CENTRO DE COSTO</td>';
        $table .= '<td width="11%">PARTIDA</td>';
        $table .= '<td width="12%">CONCEPTO DE GASTO</td>';
        $table .= '<td width="6%">SEGÚN MEMORIA</td>';
        $table .= '<td width="6%">APROBADO</td>';
        $table .= '<td width="6%">MODIFICADO</td>';
        $table .= '<td width="6%">VIGENTE</td>';
        $table .= '<td width="7%">COMPROMETIDO</td>';
        $table .= '<td width="6%">EJECUTADO</td>';
        $table .= '<td width="6%">PAGADO</td>';
        $table .= '<td width="6.5%">SALDO POR COMPROMETER</td>';
        $table .= '<td width="6%">SALDO POR DEVENGAR</td>';
        $table .= '<td width="6%">SALDO POR PAGAR</td>';
        $table .= '<td width="4.5%">% EJE</td>';
        $table .= '</tr>';
        $table .= '</table>';
        $this->writeHTML($table, false, false, false, false, '');
    }

    function generarCuerpo($detalle)
    {
        $count = 0;
        $primero = true;
        $this->ult_codigo_partida = '';
        $this->ult_concepto = '';
        $this->fill = 0;
        $this->total = count($detalle);
        $this->s1 = 0;
        $this->t1 = 0;
        $this->tg1 = 0;
        $table = '<table border="1" cellpadding="2">';
        if (count($detalle) > 0) {
            $presupuesto_actual = $detalle[0]['id_presupuesto'];
            $row = $detalle[0]['id_presupuesto'] . $detalle[0]['codigo'] . $detalle[0]['nombre_partida'] . $detalle[0]['desc_ingas'];
        }
        foreach ($detalle as $val) {
            if ($val['id_presupuesto'] == $presupuesto_actual && ($val['id_presupuesto'] . $val['codigo'] . $val['nombre_partida'] . $val['desc_ingas'] == $row) && !$primero) {
                $count++;
            } else {
                $count = 0;
            }
            $table .= $this->imprimirLinea($val, $count);
            $this->total = $this->total - 1;
            $this->revisarfinPagina();
            $presupuesto_actual = $val['id_presupuesto'];
            $row = $val['id_presupuesto'] . $val['codigo'] . $val['nombre_partida'] . $val['desc_ingas'];
            $primero = false;
        }

        if ($this->totales_aprobado != 0) {
            $calc = (($this->totales_ejecutado / $this->totales_aprobado) * 100);
        } else {
            $calc = 0;
        }
        $this->SetFont('', '', 5);
        $por_eje = number_format((float)$calc, 2, '.', '');
        $table .= '<tr style="font-weight: bold;background-color:#e0ebff">';
        $table .= '<td width="34%" colspan="3" style="text-align:right">TOTALES</td>';
        $table .= '<td width="6%" style="text-align:right">' . number_format($this->totales_segun_memoria, 2, '.', ',') . '</td>';
        $table .= '<td width="6%" style="text-align:right">' . number_format($this->totales_aprobado, 2, '.', ',') . '</td>';
        $table .= '<td width="6%" style="text-align:right">' . number_format($this->totales_ajustado, 2, '.', ',') . '</td>';
        $table .= '<td width="6%" style="text-align:right">' . number_format($this->totales_vigente, 2, '.', ',') . '</td>';
        $table .= '<td width="7%" style="text-align:right">' . number_format($this->totales_comprometido, 2, '.', ',') . '</td>';
        $table .= '<td width="6%" style="text-align:right">' . number_format($this->totales_ejecutado, 2, '.', ',') . '</td>';
        $table .= '<td width="6%" style="text-align:right">' . number_format($this->totales_pagado, 2, '.', ',') . '</td>';
        $table .= '<td width="6.5%" style="text-align:right">' . number_format($this->totales_saldoXcomprometer, 2, '.', ',') . '</td>';
        $table .= '<td width="6%" style="text-align:right">' . number_format($this->totales_saldoXdevengar, 2, '.', ',') . '</td>';
        $table .= '<td width="6%" style="text-align:right">' . number_format($this->totales_saldoXpagar, 2, '.', ',') . '</td>';
        $table .= '<td width="4.5%" style="text-align:right">' . $por_eje . ' %</td>';
        $table .= '</tr>';
        $table .= '</table>';
        $table .= '<p></p><p style="color:red">* El presente reporte podría no reflejar datos correctos, debido a que existen Conceptos INGAS duplicados en un mismo Presupuesto, los cuales se encuentran resaltados.</p>';
        $this->writeHTML($table, false, false, false, false, '');
    }

    function imprimirLinea($val, $count)
    {
        $ingas = $val['desc_ingas'];
        $tr = '';
        $id_concepto_ingas = $this->objParam->getParametro('id_concepto_ingas');
        $ingas_arr = explode('|', $ingas);
        $id_ingas = 0;
        $desc_ingas = '';
        if (count($ingas_arr) > 0) {
            $id_ingas = $ingas_arr[0];
            $desc_ingas = $ingas_arr[1] . ' - ' . $ingas_arr[2];
        }
        if (empty($id_concepto_ingas) || $id_concepto_ingas == $id_ingas) {
            $this->SetFillColor(224, 235, 255);
            $this->SetTextColor(0);
            $this->tabletextcolor = array();
            $ajustado = $val['formulado'] - $val['importe_aprobado'];
            if ($val['importe_aprobado'] != 0) {
                $por_eje = ($val['ejecutado'] / $val['importe_aprobado']) * 100;
            } else {
                $por_eje = 0;
            }
            $por_eje = number_format((float)$por_eje, 2, '.', '');

            $sal_comprometido = $val['formulado'] - $val['comprometido'];
            $sal_ejecutado = $val['comprometido'] - $val['ejecutado'];
            $sal_pagado = $val['ejecutado'] - $val['pagado'];

            $this->SetFont('', '', 5);

            $this->totales_segun_memoria += $val['importe'];
            $this->totales_aprobado += $val['importe_aprobado'];
            $this->totales_ajustado += $ajustado;
            $this->totales_vigente += $val['formulado'];
            $this->totales_comprometido += $val['comprometido'];
            $this->totales_ejecutado += $val['ejecutado'];
            $this->totales_pagado += $val['pagado'];
            $this->totales_saldoXcomprometer += $sal_comprometido;
            $this->totales_saldoXdevengar += $sal_ejecutado;
            $this->totales_saldoXpagar += $sal_pagado;
            $this->totales_porcentaje_ejecucion += $por_eje;
            $bg = '';
            if ($this->fill) {
                $bg = 'style="background-color:#e0ebff"';
            }
            if ($count > 0) {
                $bg = 'style="background-color:#fff2cc"';
            }
            $tr .= '<tr ' . $bg . '>';
            $tr .= '<td width="11%">' . $val['codigo_cc'] . '</td>';
            $tr .= '<td width="11%">' . $val['codigo'] . ' - ' . $val['nombre_partida'] . '</td>';
            $tr .= '<td width="12%">' . $desc_ingas . '</td>';
            $tr .= '<td width="6%" style="text-align: right">' . number_format($val['importe'], 2, '.', ',') . '</td>';
            $tr .= '<td width="6%" style="text-align: right">' . number_format($val['importe_aprobado'], 2, '.', ',') . '</td>';
            $tr .= '<td width="6%" style="text-align: right">' . number_format($ajustado, 2, '.', ',') . '</td>';
            $tr .= '<td width="6%" style="text-align: right">' . number_format($val['formulado'], 2, '.', ',') . '</td>';
            $tr .= '<td width="7%" style="text-align: right">' . number_format($val['comprometido'], 2, '.', ',') . '</td>';
            $tr .= '<td width="6%" style="text-align: right">' . number_format($val['ejecutado'], 2, '.', ',') . '</td>';
            $tr .= '<td width="6%" style="text-align: right">' . number_format($val['pagado'], 2, '.', ',') . '</td>';
            $tr .= '<td width="6.5%" style="text-align: right">' . number_format($sal_comprometido, 2, '.', ',') . '</td>';
            $tr .= '<td width="6%" style="text-align: right">' . number_format($sal_ejecutado, 2, '.', ',') . '</td>';
            $tr .= '<td width="6%" style="text-align: right">' . number_format($sal_pagado, 2, '.', ',') . '</td>';
            $tr .= '<td width="4.5%" style="text-align: right">' . $por_eje . ' %</td>';
            $tr .= '</tr>';
            $this->fill = !$this->fill;
        }
        return $tr;
    }


    function revisarfinPagina()
    {
        $dimensions = $this->getPageDimensions();
        $hasBorder = false; //flag for fringe case
        $startY = $this->GetY();
        $this->getNumLines($row['cell1data'], 80);
        if (($startY + 4 * 3) + $dimensions['bm'] > ($dimensions['hk'])) {
            if ($this->total != 0) {
                $this->AddPage();
            }
        }
    }

    function cerrarCuadro()
    {
        $this->tablewidths = array(15 + 53 + 18 + 18 + 18 + 18 + 18 + 18 + 18 + 18 + 18 + 18 + 15);
        $this->tablealigns = array('L');
        $this->tablenumbers = array(0,);
        $this->tableborders = array('F');
        $RowArray = array('espacio' => '');
        $this->MultiRow($RowArray, false, 1);
    }
}

?>