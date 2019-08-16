<?php
// Extend the TCPDF class to create custom MultiRow
class REjecucion extends  ReportePDF {
	var $datos_titulo;
	var $datos_detalle;
	var $ancho_hoja;
	var $gerencia;
	var $numeracion;
	var $ancho_sin_totales;
	var $cantidad_columnas_estaticas;
	var $s1;
	var $t1;
	var $tg1;
	var $total;
	var $datos_entidad;
	var $datos_periodo;
	var $ult_codigo_partida;
	var $ult_concepto;
    var $fecha_ini;

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
    var $desc= '';
            		
	function datosHeader ( $detalle, $totales, $gestion,$dataEmpresa,$fecha_ini, $fecha_fin) {
		$this->ancho_hoja = $this->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT-10;
		$this->datos_detalle = $detalle;
		$this->datos_titulo = $totales;
		$this->datos_entidad = $dataEmpresa;
		$this->datos_gestion = $gestion;
		$this->fecha_ini = $fecha_ini;
        $this->fecha_fin = $fecha_fin;
        $this->desc = $detalle[0]['desc_cat'];

		$this->subtotal = 0;
		$this->SetMargins(7, 60, 5);


	}
	
	function Header() {
		
		$white = array('LTRB' =>array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 255, 255)));
        $black = array('T' =>array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        
		if ($this->objParam->getParametro('tipo_reporte') == 'centro_costo'){ 
            $this->Ln(3);
        }else{
            $this->Ln(3);
        }
		//formato de fecha
		
		//cabecera del report
		$this->Image(dirname(__FILE__).'/../../lib/imagenes/logos/logo.jpg', 10,5,35,20);
        $this->ln(5);

        switch ($this->objParam->getParametro('tipo_reporte')) {
            case 'categoria': $tit = 'CATEGORÍA'; 
                break;
            case 'programa': $tit = 'PROGRAMA'; 
                break;
            case 'presupuesto': $tit = 'PRESUPUESTO'; 
                break;
            case 'proyecto': $tit = 'PROYECTO';
                break;
            case 'actividad': $tit = 'ACTIVIDAD';
                break;
            case 'orga_financ': $tit = 'ORGANISMO FINANCIADOR';
                break;
            case 'fuente_financ': $tit = 'FUENTE DE FINANCIAMIENTO';
                break;
            case 'unidad_ejecutora': $tit = 'UNIDAD EJECUTORA';
                break;
        }
		if($this->objParam->getParametro('tipo_reporte') == 'centro_costo'){
            $this->SetFont('','BU',12);		
            $this->Cell(0,5,"EJECUCIÓN PRESUPUESTARIA A NIVEL CENTRO DE COSTO",0,1,'C');
            $this->Cell(0,5,mb_strtoupper($this->datos_entidad['nombre'],'UTF-8'),0,1,'C');
            $this->SetFont('','B',8);
            $this->Cell(0,3,"De: ".($this->fecha_ini). "       A: ".$this->fecha_fin,0,1,'C');            
            $this->SetFont('','B',7);            
            $this->Cell(0,5,"(Expresado en Bolivianos)",0,1,'C');              
            $this->SetFont('','',10);             
            $this->Ln(2);
        }else{
            $this->SetFont('','BU',12);		
            $this->Cell(0,5,"EJECUCIÓN PRESUPUESTARIA POR ".$tit,0,1,'C');
            $this->Cell(0,5,mb_strtoupper($this->datos_entidad['nombre'],'UTF-8'),0,1,'C');
            $this->Cell(0,5,"GESTIÓN ".$this->datos_gestion['anho'],0,1,'C');
            //$this->Ln();
            $this->SetFont('','B',7);
            $this->Cell(0,5,"(Expresado en Bolivianos)",0,1,'C');		
            $this->Ln(2);
            //
            $this->SetFont('','B',8);
            $this->Cell(0,4,"De: ".($this->fecha_ini). "    A: ".$this->fecha_fin,0,1,'C');
            //$this->Ln(0);
    
            $this->SetFont('','',10);
        }
        
        $concepto = $this->objParam->getParametro('concepto');
        $subtitulo = $this->objParam->getParametro('subtitulo');        
        //var_dump($subtitulo);exit;
		$height = 3;
        $width1 = 5;
		$esp_width = 10;
        $width_c1= 50;
        $width_c2= 130;        
        $width3 = 40;
        $width4 = 75;
		$fuente = 10;
        $this->Ln();        
        
        switch ($this->objParam->getParametro('tipo_reporte')) {
            case 'categoria': $tmp = 'CATEGORÍA'; $concepto = $subtitulo; $width_c1=25; $fuente=8;
                break;
            case 'programa': $tmp = 'PROGRAMA'; $width_c1=25;
                break;
            case 'presupuesto': $tmp = 'PRESUPUESTO'; $width_c1=30;
                break;
            case 'proyecto': $tmp = 'PROYECTO'; $concepto = $subtitulo; $width_c1=25;
                break;
            case 'actividad': $tmp = 'ACTIVIDAD'; $concepto = $subtitulo; $width_c1=25;
                break;
            case 'orga_financ': $tmp = 'ORGANISMO FINANCIADOR'; $concepto = $subtitulo; 
                break;
            case 'fuente_financ': $tmp = 'FUENTE DE FINANCIAMIENTO'; $concepto = $subtitulo;$width_c1=55;
                break;
            case 'unidad_ejecutora': $tmp = 'UNIDAD EJECUTORA'; $concepto = $subtitulo;$width_c1=40;
                break;
        }
        if ($this->objParam->getParametro('tipo_reporte') == 'centro_costo'){ 
            $this->Cell($width1, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
            $this->Cell($width_c1, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
            $this->SetFont('', '');
            $this->SetFillColor(0,0,0, false);
            $this->Cell($width_c2, $height, '', '', 0, 'L', false, '', 0, false, 'T', 'C');                        
            $this->Ln();                      
            $this->Ln();
            //$tmp = 'CATEGORÍA PROGRAMATICA';
            //$concepto = 'Administración Central BoA';            
        }
        else if($this->objParam->getParametro('tipo_reporte') == 'presupuesto'){
            $this->Ln(-2);
            $this->Cell($width1, 1, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');                        
            $this->SetTextColor(0, 0, 0);                       
            $this->Cell($width_c1, 1, $tmp.": ", 0, 0, 'L', false, '', 0, false, 'T', 'C');
            $this->SetFont('', 'B');
            
            $this->SetTextColor(0, 0, 255);            
            $this->Cell($width_c2, 1, $concepto, 0, 0, 'L', false, '', 0, false, 'T', 'C');
            $this->Ln();
            $this->SetFont('', '');
            $this->Cell($width1, 1, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');                        
            $this->SetTextColor(0, 0, 0);                       
            $this->Cell($width_c1, 1, "CATEGORIA: ", 0, 0, 'L', false, '', 0, false, 'T', 'C');
            $this->SetFont('', 'B');
            
            $this->SetTextColor(0, 0, 255);            
            $this->Cell($width_c2, 1, $this->desc, 0, 0, 'L', false, '', 0, false, 'T', 'C');                        
            $this->Ln(7);
        }
        else{
			$this->SetFont('', '',$fuente);	
            $this->Cell($width1, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');                        
            $this->SetTextColor(0, 0, 0);                       
            $this->Cell($width_c1, $height, $tmp.": ", 0, 0, 'L', false, '', 0, false, 'T', 'C');
            $this->SetFont('', 'B',$fuente);
            $this->SetFillColor(192,192,192, true);
            $this->SetTextColor(0, 0, 255);            
            $this->Cell($width_c2, $height, $concepto, 0, 0, 'L', false, '', 0, false, 'T', 'C');            
            $this->Ln();      
            $this->Ln();
        }
		
		$this->SetFont('','B',6);
		$this->generarCabecera();		
		
	}
   
   function generarReporte() {
		$this->setFontSubsetting(false);
		$this->AddPage();
		$this->generarCuerpo($this->datos_detalle);		
		$this->cerrarCuadro();	
		
		
	} 
    function generarCabecera(){
    	
		//armca caecera de la tabla		        
        $this->tablenumbers=array(0,0,0,0,0,0,0,0,0,0,0,0,0);
        $nro = '';
        $partida = '';
        if($this->objParam->getParametro('tipo_reporte') == 'centro_costo'){
            $partida = 'CENTRO DE COSTO (UNIDAD ORGANIZACIONAL)';
            $modificado = 'MODIFICADO';
            $this->tablealigns=array('L','C','C','C','C','C','C','C','C','C','C','C','C');
            $this->tablewidths=array(6,53,19,19,19,19,20,19,19,19,19,19,15);
            $this->tableborders=array('TBL','TBR','TBRL','TBRL','TBRL','TBRL','TBRL','TBRL','TBRL','TBRL','TBRL','TBRL','TBRL');			
            $this->Ln(6);
        }else{
            $nro = 'COD';
            $partida = 'PARTIDA';
            $modificado = 'AJUSTADO';
            $this->tablealigns=array('C','C','C','C','C','C','C','C','C','C','C','C','C');
            $this->tablewidths=array(15,53,18,18,18,18,18,18,18,18,18,18,15);
            $this->tableborders=array('TB','TB','TB','TB','TB','TB','TB','TB','TB','TB','TB','TB','TB');
        }
        $this->tabletextcolor=array();
		
	    $RowArray = array(
            			's0'  => $nro,
            			's1' => $partida,   
                        's2' => 'SEGÚN MEMORIA',        
                        's3' => 'APROBADO',
                        's4' => $modificado,            
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
	
	function generarCuerpo($detalle){
		
		$count = 1;
		$sw = 0;
		$sw1 = 0;
		$this->ult_codigo_partida = '';
		$this->ult_concepto = '';
		$fill = 0;
		
		$this->total = count($detalle);
		
		$this->s1 = 0;
		$this->t1 = 0;
        $this->tg1 = 0;		
        
		if($this->objParam->getParametro('tipo_reporte') == 'centro_costo'){            
            foreach ($detalle as $val) {                
                $this->imprimirCentroCosto($val,$count,$fill);
                $fill = !$fill;
                $count = $count + 1;
                $this->total = $this->total -1;
                $this->revisarfinPagina();
            }
            if($this->totales_importe_aprobado != 0){        
                $calc = (($this->totales_ejecutado / $this->totales_importe_aprobado)*100);
            }else{
                $calc = 0 ;
            }
            $por_eje = number_format((float)$calc, 2, '.', '');
            $RowArray = array(
             's1'  => '',  
             's2'  => 'TOTALES',   
             's3'  => $this->totales_importe,
             's4'  => $this->totales_importe_aprobado,
             's5'  => $this->totales_formulado,
             's6'  => $this->totales_ajustado,
             's7'  => $this->totales_comprometido,
             's8'  => $this->totales_ejecutado,
             's9'  => $this->totales_pagado,
             's10' => $this->totales_saldoXcomprometer,
             's11' => $this->totales_saldoEjecutado,
             's12' => $this->totales_saldoXpagar,
             's13' => $por_eje. ' %');            
                
            $this->SetFont('','B',6);
            $this->tablealigns=array('R','C','R','R','R','R','R','R','R','R','R','R','C');
            $this->tablenumbers=array(0,0,2,2,2,2,2,2,2,2,2,2,0);
    
            $this-> MultiRow($RowArray,$fill,1);


        }else{            
            foreach ($detalle as $val) {                    
                $this->imprimirLinea($val,$count,$fill);
                $fill = !$fill;
                $count = $count + 1;
                $this->total = $this->total -1;
                $this->revisarfinPagina();
                
            }
        }
		
		
		
	}	
	
	function imprimirLinea($val,$count,$fill){		
		$this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $tab = '';
		$this->tabletextcolor=$conf_tabletextcolor;
		$sal_comprometido = $val['formulado'] - $val['comprometido'];
		$sal_ejecutado = $val['comprometido'] - $val['ejecutado'];
		$sal_pagado = $val['ejecutado'] - $val['pagado'];
		
		
		if ($val['nivel_partida'] == 0){
			 $this->SetFont('','BU',6);
			 $this->tableborders=array('LRTB','LRTB','LRTB','LRTB','LRTB','LRTB','LRTB','LRTB','LRTB','LRTB','LRTB','LRTB','LRTB');
			 $conf_par_tablewidths=array(15,53,18,18,18,18,18,18,18,18,18,18,15);
		     $this->tablealigns=array('L','L','R','R','R','R','R','R','R','R','R','R','R');
		     $this->tablenumbers=array(0,0,2,2,2,2,2,2,2,2,2,2,0);
			 $RowArray = array(
            			's1' =>  $tab.$val['codigo_partida'],
            			's2' => $tab.$val['nombre_partida'],
                        's3' => $val['importe'],
                        's4' => $val['importe_aprobado'],
						's5' => $val['ajustado'],
                        's6' => $val['formulado'],
                        's7' => $val['comprometido'],
                        's8' => $val['ejecutado'],
						's9' => $val['pagado'],
						's10' => $sal_comprometido,
                        's11' => $sal_ejecutado,
						's12' => $sal_pagado,
                        's13' => $val['porc_ejecucion'].' %');
			
        }
		
		if ($val['nivel_partida'] == 1){
			 //$this->ln();
			 $this->SetFont('','BU',6);
			 $this->tableborders=array('LR','LR','LR','LR','LR','LR','LR','LR','LR','LR','LR','LR','LR');
			 $this->tablewidths=array(15,53,18,18,18,18,18,18,18,18,18,18,15);
		     $this->tablealigns=array('L','L','R','R','R','R','R','R','R','R','R','R','R');
		     $this->tablenumbers=array(0,0,2,2,2,2,2,2,2,2,2,2,0);
			 
			 $tab = '';
			 $RowArray = array(
            			's1' =>  $tab.$val['codigo_partida'],
            			's2' => $tab.$val['nombre_partida'],
                        's3' => $val['importe'],
                        's4' => $val['importe_aprobado'],
						's5' => $val['ajustado'],
                        's6' => $val['formulado'],
                        's7' => $val['comprometido'],
                        's8' => $val['ejecutado'],
						's9' => $val['pagado'],
						's10' => $sal_comprometido,
                        's11' => $sal_ejecutado,
						's12' => $sal_pagado,
                        's13' => $val['porc_ejecucion'].' %');
			 
		}
		
		
		
		if ($val['nivel_partida'] == 2){
			 $this->SetFont('','',6);
			 $this->tableborders=array('LR','L','R','LR','LR','LR','LR','LR','LR','LR','LR','LR','LR','LR');
			 $this->tablewidths=array(15,3,50,18,18,18,18,18,18,18,18,18,18,15);
		     $this->tablealigns=array('L','L','L','R','R','R','R','R','R','R','R','R','R','R');
		     $this->tablenumbers=array(0,0,0,2,2,2,2,2,2,2,2,2,2,0);
			 $tab = "\t\t";
			 $RowArray = array(
            			's1' =>  $tab.$val['codigo_partida'],
            			's2.0' => '',
                        's2' => $val['nombre_partida'],
                        's3' => $val['importe'],
                        's4' => $val['importe_aprobado'],
						's5' => $val['ajustado'],
                        's6' => $val['formulado'],
                        's7' => $val['comprometido'],
                        's8' => $val['ejecutado'],
						's9' => $val['pagado'],
						's10' => $sal_comprometido,
                        's11' => $sal_ejecutado,
						's12' => $sal_pagado,
                        's13' => $val['porc_ejecucion'].' %');
			
		}
       if ($val['nivel_partida'] == 3){
			  $this->SetFont('','',6);
		      $this->tableborders=array('LR','L','R','LR','LR','LR','LR','LR','LR','LR','LR','LR','LR','LR');
			  $this->tablewidths=array(15,5,48,18,18,18,18,18,18,18,18,18,18,15);
		      $this->tablealigns=array('L','L','L','R','R','R','R','R','R','R','R','R','R','R');
		      $this->tablenumbers=array(0,0,0,2,2,2,2,2,2,2,2,2,2,0);
			 
		     $tab = "\t\t\t";
			 $RowArray = array(
            			's1' =>  $tab.$val['codigo_partida'],
            			's2.0' => '',
                        's2' => $val['nombre_partida'],
                        's3' => $val['importe'],
                        's4' => $val['importe_aprobado'],
						's5' => $val['ajustado'],
                        's6' => $val['formulado'],
                        's7' => $val['comprometido'],
                        's8' => $val['ejecutado'],
						's9' => $val['pagado'],
						's10' => $sal_comprometido,
                        's11' => $sal_ejecutado,
						's12' => $sal_pagado,
                        's13' => $val['porc_ejecucion'].' %');
			 
		}
      if ($val['nivel_partida'] > 3){
			  $this->SetFont('','',6);
		      $this->tableborders=array('LR','L','R','LR','LR','LR','LR','LR','LR','LR','LR','LR','LR','LR');
			  $this->tablewidths=array(15,7,46,18,18,18,18,18,18,18,18,18,18,15);
		      $this->tablealigns=array('L','L','L','R','R','R','R','R','R','R','R','R','R','R');
		      $this->tablenumbers=array(0,0,0,2,2,2,2,2,2,2,2,2,2,0);
			 
		     $tab = "\t\t\t\t";
			 $RowArray = array(
            			's1' =>  $tab.$val['codigo_partida'],
            			's2.0' => '',
                        's2' => $val['nombre_partida'],
                        's3' => $val['importe'],
                        's4' => $val['importe_aprobado'],
						's5' => $val['ajustado'],
                        's6' => $val['formulado'],
                        's7' => $val['comprometido'],
                        's8' => $val['ejecutado'],
						's9' => $val['pagado'],
						's10' => $sal_comprometido,
                        's11' => $sal_ejecutado,
						's12' => $sal_pagado,
                        's13' => $val['porc_ejecucion'].' %');
			 
		}
		$this-> MultiRow($RowArray,$fill,1);
		
	}


    function revisarfinPagina(){
		$dimensions = $this->getPageDimensions();
		$hasBorder = false; //flag for fringe case		
		$startY = $this->GetY();
		$this->getNumLines($row['cell1data'], 80);
		
		if (($startY + 4 * 3) + $dimensions['bm'] > ($dimensions['hk'])) {
		    if($this->total!= 0){
				$this->AddPage();
			}
		} 
	}
	
	
   
 
  
  function cerrarCuadro(){
  	
	   
	   	    //si noes inicio termina el cuardro anterior
	   	   
			$this->tablewidths=array(15+53+18+18+18+18+18+18+18+18+18+18+15);
            $this->tablealigns=array('L');
            $this->tablenumbers=array(0,);
            $this->tableborders=array('T');		
	        $RowArray = array('espacio' => '');     
	        $this-> MultiRow($RowArray,false,1);
			
	
  }  
  function imprimirCentroCosto($val,$count,$fill){          

    $sal_comprometido = $val['ajustado'] - $val['comprometido'];
    $sal_ejecutado = $val['comprometido'] - $val['ejecutado'];
    $sal_pagado = $val['ejecutado'] - $val['pagado'];    
    $this->tableborders=array('LRTB','LRTB','LRTB','LRTB','LRTB','LRTB','LRTB','LRTB','LRTB','LRTB','LRTB','LRTB','LRTB');
    $this->tablewidths=array(6,53,19,19,19,19,20,19,19,19,19,19,15);    
    $this->tablenumbers=array(0,0,2,2,2,2,2,2,2,2,2,2,0);
    $nombre_partida = '';         
     
    if($val['nombre_partida'] == 'TOTAL'){        
        $nombre_partida = $val['nombre_partida']." ".$val['categoria'];
        $this->SetFont('','B',6);
        $this->tablealigns=array('R','C','R','R','R','R','R','R','R','R','R','R','C');
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
    }else{  
        $nombre_partida = $val['nombre_partida'];
        $this->SetFont('','',5);
        $this->tablealigns=array('R','L','R','R','R','R','R','R','R','R','R','R','C');
    }
        $RowArray = array(
            's1' => $val['codigo_partida'],
            's2' => $nombre_partida,
            's3' => $val['importe'],
            's4' => $val['importe_aprobado'],
            's5' => $val['formulado'], 
            's6' => $val['ajustado'],
            's7' => $val['comprometido'],
            's8' => $val['ejecutado'],
            's9' => $val['pagado'],
            's10' => $sal_comprometido,
            's11' => $sal_ejecutado,
            's12' => $sal_pagado,
			's13' => $val['porc_ejecucion'].' %');            
			
    $this-> MultiRow($RowArray,false,1);    
  }
}
?>