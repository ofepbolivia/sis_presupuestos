<?php
class REjecucionPorPartidaXls
{
	private $docexcel;
	private $objWriter;
	private $nombre_archivo;
	private $hoja;
	private $columnas=array();
	private $fila;
	private $equivalencias=array();
	
	private $indice, $m_fila, $titulo;
	private $swEncabezado=0; //variable que define si ya se imprime el encabezado
	private $objParam;
	public  $url_archivo;
	
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

	//Variables para el calculo de totales por columna.
    var $totales_segun_memoria = 0;
    var $totales_aprobado = 0;
    var $totales_ajustado = 0;
    var $totales_vigente = 0;
    var $totales_comprometido = 0;
    var $totales_ejecutado = 0;
    var $totales_pagado = 0;
    var $totales_saldoXcomprometer = 0;
    var $totales_saldoXdevengar = 0;
    var $totales_saldoXpagar = 0;
    var $totales_porcentaje_ejecucion = 0;
	
	
	
	function __construct(CTParametro $objParam){
		$this->objParam = $objParam;
		$this->url_archivo = "../../../reportes_generados/".$this->objParam->getParametro('nombre_archivo');
		//ini_set('memory_limit','512M');
		set_time_limit(400);
		$cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
		$cacheSettings = array('memoryCacheSize'  => '10MB');
		PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

		$this->docexcel = new PHPExcel();
		$this->docexcel->getProperties()->setCreator("PXP")
							 ->setLastModifiedBy("PXP")
							 ->setTitle($this->objParam->getParametro('titulo_archivo'))
							 ->setSubject($this->objParam->getParametro('titulo_archivo'))
							 ->setDescription('Reporte "'.$this->objParam->getParametro('titulo_archivo').'", generado por el framework PXP')
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Report File");
							 
		$this->docexcel->setActiveSheetIndex(0);
		
		$this->docexcel->getActiveSheet()->setTitle($this->objParam->getParametro('titulo_archivo'));
		
		$this->equivalencias=array(0=>'A',1=>'B',2=>'C',3=>'D',4=>'E',5=>'F',6=>'G',7=>'H',8=>'I',
								9=>'J',10=>'K',11=>'L',12=>'M',13=>'N',14=>'O',15=>'P',16=>'Q',17=>'R',
								18=>'S',19=>'T',20=>'U',21=>'V',22=>'W',23=>'X',24=>'Y',25=>'Z',
								26=>'AA',27=>'AB',28=>'AC',29=>'AD',30=>'AE',31=>'AF',32=>'AG',33=>'AH',
								34=>'AI',35=>'AJ',36=>'AK',37=>'AL',38=>'AM',39=>'AN',40=>'AO',41=>'AP',
								42=>'AQ',43=>'AR',44=>'AS',45=>'AT',46=>'AU',47=>'AV',48=>'AW',49=>'AX',
								50=>'AY',51=>'AZ',
								52=>'BA',53=>'BB',54=>'BC',55=>'BD',56=>'BE',57=>'BF',58=>'BG',59=>'BH',
								60=>'BI',61=>'BJ',62=>'BK',63=>'BL',64=>'BM',65=>'BN',66=>'BO',67=>'BP',
								68=>'BQ',69=>'BR',70=>'BS',71=>'BT',72=>'BU',73=>'BV',74=>'BW',75=>'BX',
								76=>'BY',77=>'BZ');		
									
	}
	
	function datosHeader ( $detalle, $totales, $gestion,$dataEmpresa) {
		//var_dump($detalle);exit;
		$this->datos_detalle = $detalle;
		$this->datos_titulo = $totales;
		$this->datos_entidad = $dataEmpresa;
		$this->datos_gestion = $gestion;
		
		
	}

    function imprimeCabecera()
    {
//        $this->docexcel->createSheet();
        $styleTitulos1 = array(
            'font' => array(
                'bold' => true,
                'size' => 12,
                'name' => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );

        $styleTitulos3 = array(
            'font' => array(
                'bold' => true,
                'size' => 11,
                'name' => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),

        );

        //titulos

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, 2, 'EJECUCIÓN PRESUPUESTARIA POR PARTIDA');
        $this->docexcel->getActiveSheet()->getStyle('A2:M2')->applyFromArray($styleTitulos1);
        $this->docexcel->getActiveSheet()->mergeCells('A2:M2');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, 3, 'Del: ' . $this->objParam->getParametro('fecha_ini') . '   Al: ' . $this->objParam->getParametro('fecha_fin'));
        $this->docexcel->getActiveSheet()->getStyle('A3:M3')->applyFromArray($styleTitulos3);
        $this->docexcel->getActiveSheet()->mergeCells('A3:M3');

    }
			
	function imprimeDatos(){
		$datos = $this->datos_detalle;
		
		$config = $this->objParam->getParametro('config');
		$columnas = 0;
		
		
		$styleTitulos = array(
							      'font'  => array(
							          'bold'  => true,
							          'size'  => 8,
							          'name'  => 'Arial'
							      ),
							      'alignment' => array(
							          'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
							          'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
							      ),
								   'fill' => array(
								      'type' => PHPExcel_Style_Fill::FILL_SOLID,
								      'color' => array('rgb' => 'c5d9f1')
								   ),
								   'borders' => array(
								         'allborders' => array(
								             'style' => PHPExcel_Style_Border::BORDER_THIN
								         )
                                     ));
        $styleSubtitulo  = array(
                                    'font' => array(
                                        'bold' => true, 
                                        'size' => 12),
                                    'fill' => array(                                        
                                        'color' => array( 'rgb' => '#00008B')
                                    )
                                );                                     

        $concepto  = $this->objParam->getParametro('concepto_partida');
        $concepto1 = $this->objParam->getParametro('concepto');
        $subtitulo = $this->objParam->getParametro('subtitulo');
        $cod_cat = $this->datos_detalle[0]['cod_cat'];        

        $str_concepto = ($subtitulo!='')?$subtitulo:$concepto1;
        
        $tipo_repo = ''; 
        switch ($this->objParam->getParametro('tipo_reporte')) {
        case 'categoria': $tipo_repo = 'CATEGORÍA: '; 
            break;
        case 'programa': $tipo_repo = 'PROGRAMA: '; 
            break;
        case 'presupuesto': $tipo_repo = 'PRESUPUESTO: '; 
            break;
        case 'proyecto': $tipo_repo = 'PROYECTO: ';
            break;
        case 'actividad': $tipo_repo = 'ACTIVIDAD: ';
            break;
        case 'orga_financ': $tipo_repo = 'ORGANISMO FINANCIADOR: ';
            break;
        case 'fuente_financ': $tipo_repo = 'FUENTE DE FINANCIAMIENTO: ';
            break;
        case 'unidad_ejecutora': $tipo_repo = 'UNIDAD EJECUTORA: ';
            break;
        case 'centro_costo': $tipo_repo = ''; $str_concepto = '';
            break;            
        } 
        
        $this->docexcel->getActiveSheet()->getStyle('B5:I5')->applyFromArray($styleSubtitulo);
        $this->docexcel->getActiveSheet()->mergeCells('B5:D5');
        $this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[0])->setWidth(20);  
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,5,"PARTIDA: ".$concepto);
        $this->docexcel->getActiveSheet()->getStyle('B6:I6')->applyFromArray($styleSubtitulo);
        $this->docexcel->getActiveSheet()->mergeCells('B6:I6');
        $this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[0])->setWidth(10);  
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,6,$tipo_repo.$str_concepto);
        if( $this->objParam->getParametro('tipo_reporte')=='presupuesto' ){
            $this->docexcel->getActiveSheet()->getStyle('B7:I7')->applyFromArray($styleSubtitulo);
            $this->docexcel->getActiveSheet()->mergeCells('B7:I7');
            $this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[0])->setWidth(10);  
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,7,'CATEGORIA: '.$cod_cat);            
        }

        $fi = 8;
        $this->docexcel->getActiveSheet()->getStyle('B8:M8')->applyFromArray($styleTitulos);        
		//*************************************Cabecera*****************************************
		//$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[0])->setWidth(20);		
		//$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,1,'Código');
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[1])->setWidth(50);
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$fi,'PRESUPUESTO');
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[2])->setWidth(20);
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$fi,'SEGÚN MEMORIA');
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[3])->setWidth(20);
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,$fi,'APROBADO');
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[4])->setWidth(20);
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,$fi,'MODIFICADO');//Ajustado
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[5])->setWidth(20);
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,$fi,'VIGENTE');
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[6])->setWidth(20);
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,$fi,'COMPROMETIDO');
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[7])->setWidth(20);
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,$fi,'EJECUTADO');
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[8])->setWidth(20);
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8,$fi,'PAGADO');
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[9])->setWidth(22);
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9,$fi,'SALDO POR COMPROMETER');
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[10])->setWidth(20);
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10,$fi,'SALDO POR EJECUTAR');
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[11])->setWidth(20);
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11,$fi,'SALDO POR PAGAR');
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[12])->setWidth(20);
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(12,$fi,'% EJECUCIÓN');
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[13])->setWidth(20);
		//*************************************Fin Cabecera*****************************************
		
		$fila = 9;
		$contador = 1;
		
		/////////////////////***********************************Detalle***********************************************
		foreach($datos as $value) {
			
			if($value['importe_aprobado']!=0){
				$por_eje = ($value['ejecutado']/$value['importe_aprobado'])*100;
			}
			else{
				$por_eje = 0;
			}
			$por_eje = number_format((float)$por_eje, 2, '.', '');

            $ajustado = $value['formulado'] - $value['importe_aprobado'];
            $sal_comprometido = $value['formulado'] - $value['comprometido'];
            $sal_ejecutado = $value['comprometido'] - $value['ejecutado'];
            $sal_pagado = $value['ejecutado'] - $value['pagado'];

            $styleTitulos = array(
                'font'  => array(
                    'bold'  => true,
                    'size'  => 8,
                    'name'  => 'Arial'
                ),
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ));

            if ($value['id_presupuesto'] == 0 ){
                $styleCat = array(
                    'font'  => array(
                        'bold'  => true,
                        'size'  => 8,
                        'name'  => 'Arial'
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                    ),
                     'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'c5d9f1')
                     ),
                     'borders' => array(
                           'allborders' => array(
                               'style' => PHPExcel_Style_Border::BORDER_THIN
                           )
                       ));                                
                       $this->docexcel->getActiveSheet()->getStyle('B'.$fila.':'.'M'.$fila)->applyFromArray($styleCat);
            }else{
                $this->docexcel->getActiveSheet()->getStyle('B'.$fila.':'.'M'.$fila)->applyFromArray($styleTitulos);
            }            
            $this->docexcel->getActiveSheet()->getStyle('C:M')->getNumberFormat()->setFormatCode('#,##0.00');

			//$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$fila,$value['codigo_partida']);
			$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$fila,$value['codigo_cc']);
			$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$fila,$value['importe']);
			$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,$fila,$value['importe_aprobado']);//D
			$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,$fila,$ajustado);//"=F".$fila."-D".$fila
			$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,$fila,$value['formulado']);//F
			$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,$fila,$value['comprometido']);//G
			$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,$fila,$value['ejecutado']);//H
			$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8,$fila,$value['pagado']);//I
			$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9,$fila,$sal_comprometido);//"=F".$fila."-G".$fila
			$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10,$fila,$sal_ejecutado);//"=G".$fila."-H".$fila
			$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11,$fila,$sal_pagado);//"=H".$fila."-I".$fila
			$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(12,$fila,$por_eje);

            $this->totales_segun_memoria += $value['importe'];
            $this->totales_aprobado += $value['importe_aprobado'];
            $this->totales_ajustado += $ajustado;
            $this->totales_vigente += $value['formulado'];
            $this->totales_comprometido += $value['comprometido'];
            $this->totales_ejecutado += $value['ejecutado'];
            $this->totales_pagado += $value['pagado'];
            $this->totales_saldoXcomprometer += $sal_comprometido;
            $this->totales_saldoXdevengar += $sal_ejecutado;
            $this->totales_saldoXpagar += $sal_pagado;
            $this->totales_porcentaje_ejecucion += $por_eje;
						
			//$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(14,$fila,"=SUM(C".$fila.":N".$fila.")");
			
			$fila++;
			$contador++;
		}

        $styleTitulos = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 8,
                'name'  => 'Arial'
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'FACC2E')
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ));

        $this->docexcel->getActiveSheet()->getStyle('B'.$fila.':'.'M'.$fila)->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->getStyle('C:M')->getNumberFormat()->setFormatCode('#,##0.00');

        $por_eje = number_format((float)$this->totales_porcentaje_ejecucion, 2, '.', '');
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$fila,'TOTALES');
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$fila,$this->totales_segun_memoria);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,$fila,$this->totales_aprobado);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,$fila,$this->totales_ajustado);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,$fila,$this->totales_vigente);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,$fila,$this->totales_comprometido);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,$fila,$this->totales_ejecutado);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8,$fila,$this->totales_pagado);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9,$fila,$this->totales_saldoXcomprometer);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10,$fila,$this->totales_saldoXdevengar);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11,$fila,$this->totales_saldoXpagar);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(12,$fila,$por_eje);
		//************************************************Fin Detalle***********************************************
		
	}

	
	
	function generarReporte(){
		
		$this->imprimeDatos();
		
		//echo $this->nombre_archivo; exit;
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$this->docexcel->setActiveSheetIndex(0);
		$this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
		$this->objWriter->save($this->url_archivo);		
		
		
	}	
	

}

?>