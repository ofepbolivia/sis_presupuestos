<?php
/*
dev: breydi vasquez
description: reporte Formulacion presupuestaria
date: 04/09/2020
*/
require_once dirname(__FILE__).'/../../pxp/lib/lib_reporte/ReportePDF.php';

class REjecucionCategoria extends  ReportePDF{
    var $datos ;
    var $ancho_hoja;
    var $numeracion;
    var $ancho_sin_totales;
    var $cantidad_columnas_estaticas;

    var $totales_importe = 0;
    var $totales_importe_aprobado = 0;
    var $totales_formulado = 0;
    var $totales_ajustado = 0;
    var $totales_comprometido = 0;
    var $totales_ejecutado = 0;
    var $totales_pagado = 0;
    var $totales_saldoXcomprometer = 0;
    var $totales_saldoEjecutado = 0;
    var $totales_saldoXpagar = 0;


    function datosHeader ( $detalle, $totales, $gestion,$dataEmpresa,$fecha_ini, $fecha_fin) {
      $this->SetHeaderMargin(8);
      $this->SetAutoPageBreak(TRUE, 12);
      $this->ancho_hoja = $this->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT-10;
      $this->SetMargins(5, 50, 5);

  		$this->datos_detalle = $detalle;
  		$this->datos_titulo = $totales;
  		$this->datos_entidad = $dataEmpresa;
  		$this->datos_gestion = $gestion;
  		$this->fecha_ini = $fecha_ini;
      $this->fecha_fin = $fecha_fin;
      }
    function Header() {

        $this->Ln(3);
        //cabecera del reporte

        $this->Image(dirname(__FILE__).'/../../lib/imagenes/logos/logo.jpg', 10,5,35,20);
        $this->SetFont('','B',11);
        ($this->objParam->getParametro('tipo_reporte') == 'resumen_categoria')?$title = 'RESUMEN CATEGORIA PROGRAMÁTICA':$title = 'UNIDAD EJECUTORA';
        $this->Cell(0,5, mb_strtoupper("EJECUCIÓN PRESUPUESTARIA ".$title,'UTF-8'),0,1,'C');
        $this->Cell(0,5,mb_strtoupper('BOLIVIANA DE AVIACIÓN - BOA','UTF-8'),0,1,'C');
        $this->Cell(0,5, "GESTIÓN ".$this->datos_gestion['anho'],0,1,'C');
        $this->SetFont('','B',7);
        $this->Cell(0,5,"(Expresado en Bolivianos)",0,1,'C');
        $this->Ln(2);
        $this->SetFont('','B',8);
        $this->Cell(0,4,"De: ".($this->fecha_ini). "    A: ".$this->fecha_fin,0,1,'C');
        $this->Ln();

        $this->SetFont('','B',6);
        $this->tablealigns=array('C','C','C','C','C','C','C','C','C','C','C','C','C');
        $this->tablewidths=array(30,45,18,18,18,18,18,18,18,18,18,18,15);
        $this->tablenumbers=array(0,0,0,0,0,0,0,0,0,0,0,0,0);
        $this->tableborders=array('TB','TB','TB','TB','TB','TB','TB','TB','TB','TB','TB','TB','TB');

        $this->tabletextcolor=array(array(31, 54, 86),array(31, 54, 86),array(31, 54, 86),array(31, 54, 86),array(31, 54, 86),
        array(31, 54, 86),array(31, 54, 86),array(31, 54, 86),array(31, 54, 86),array(31, 54, 86),
        array(31, 54, 86),array(31, 54, 86),array(31, 54, 86)
      );
        $this->Ln();
        if($this->objParam->getParametro('tipo_reporte') == 'resumen_categoria'){
            $title_cod_sub = 'CODIGO CAT. PRG.';
            $title_des_sub = 'DESCRIPCIÓN CAT. PRG.';
        }else{
            $title_cod_sub = 'CODIGO UD. EJE.';
            $title_des_sub = 'DESCRIPCIÓN UD. EJE.';
        }
	      $RowArray = array(
            			's0'  => $title_cod_sub,
            			's1' => $title_des_sub,
                  's2' => 'SEGÚN MEMORIA',
                  's3' => 'APROBADO',
                  's4' => 'AJUSTADO',
                  's5' => 'VIGENTE',
                  's6' => 'COMPROMETIDO',
                  's7' => 'EJECUTADO',
                  's8' => 'PAGADO',
                  's9' => 'SALDO POR COMPROMETER',
                  's10' => 'SALDO POR DEVENGAR',
                  's11' => 'SALDO POR PAGAR',
                  's12' => '% EJE');

        $this-> MultiRow($RowArray,false,1);

    }

    function  generarReporte()
    {

        $this->AddPage();
        $this->Ln(2);
        $this->SetFont('','',5.5);
        $contador = 1;
        $totales = 0;
        $this->tableborders=array('LRT','LRT','LRT','LRT','LRT','LRT','LRT','LRT','LRT','LRT','LRT','LRT','LRT');
        $this->tablewidths=array(30,45,18,18,18,18,18,18,18,18,18,18,15);
        $this->tablenumbers=array(0,0,0,0,0,0,0,0,0,0,0,0,0);
        $this->tablealigns=array('L','L','R','R','R','R','R','R','R','R','R','R','R');
        $this->tabletextcolor=array();
        foreach ($this->datos_detalle as $val) {

        $sal_comprometido = $val['formulado'] - $val['comprometido'];
    		$sal_ejecutado = $val['comprometido'] - $val['ejecutado'];
    		$sal_pagado = $val['ejecutado'] - $val['pagado'];

        $RowArray = array(
                    's0' => $val['codigo_categoria'],
                    's1' => $val['descripcion_cate'],
                    's2' => number_format($val['importe'], 2, ',', '.'),
                    's3' => number_format($val['importe_aprobado'], 2, ',', '.'),
                    's4' => number_format($val['ajustado'], 2, ',', '.'),
                    's5' => number_format($val['formulado'], 2, ',', '.'),
                    's6' => number_format($val['comprometido'], 2, ',', '.'),
                    's7' => number_format($val['ejecutado'], 2, ',', '.'),
                    's8' => number_format($val['pagado'], 2, ',', '.'),
                    's9' => number_format($sal_comprometido, 2, ',', '.'),
                    's10' => number_format($sal_ejecutado, 2, ',', '.'),
                    's11' => number_format($sal_pagado, 2, ',', '.'),
                    's12' => $val['porc_ejecucion'].' %'
                  );

        $this-> MultiRow($RowArray);

        $this->totales_importe += $val['importe'];
        $this->totales_importe_aprobado += $val['importe_aprobado'];
        $this->totales_formulado += $val['formulado'];
        $this->totales_ajustado += $val['ajustado'];
        $this->totales_comprometido += $val['comprometido'];
        $this->totales_ejecutado += $val['ejecutado'];
        $this->totales_pagado += $val['pagado'];
        $this->totales_saldoXcomprometer += $sal_comprometido;
        $this->totales_saldoEjecutado += $sal_ejecutado;
        $this->totales_saldoXpagar += $sal_pagado;

        }

        if($this->totales_importe_aprobado != 0){
            $calc = (($this->totales_ejecutado / $this->totales_importe_aprobado)*100);
        }else{
            $calc = 0 ;
        }
        $por_eje = number_format((float)$calc, 2, '.', '');

        $RowArray = array(
         's0'  => 'TOTAL',
         's1'  => '578 BOLIVIANA DE AVIACIÓN',
         's2'  => number_format($this->totales_importe, 2, ',', '.'),
         's3'  => number_format($this->totales_importe_aprobado, 2, ',', '.'),
         's4'  => number_format($this->totales_ajustado, 2, ',', '.'),
         's5'  => number_format($this->totales_formulado, 2, ',', '.'),
         's6'  => number_format($this->totales_comprometido, 2, ',', '.'),
         's7'  => number_format($this->totales_ejecutado, 2, ',', '.'),
         's8'  => number_format($this->totales_pagado, 2, ',', '.'),
         's9'  => number_format($this->totales_saldoXcomprometer, 2, ',', '.'),
         's10' => number_format($this->totales_saldoEjecutado, 2, ',', '.'),
         's11' => number_format($this->totales_saldoXpagar, 2, ',', '.'),
         's12' => $por_eje. ' %');

        $this->SetFont('','B',5);
        $this->tableborders=array('LTB','RTB','LRTB','LRTB','LRTB','LRTB','LRTB','LRTB','LRTB','LRTB','LRTB','LRTB');
        $this->tablewidths=array(30,45,18,18,18,18,18,18,18,18,18,18,15);
        $this->tablenumbers=array(0,0,0,0,0,0,0,0,0,0,0,0);
        $this->tablealigns=array('C','L','R','R','R','R','R','R','R','R','R','R','R');

        $this-> MultiRow($RowArray,$fill);

    }
}
?>
