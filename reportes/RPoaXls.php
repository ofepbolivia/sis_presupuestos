<?php

class RPoaXls
{
    private $docexcel;
    private $objWriter;
    private $nombre_archivo;
    private $hoja;
    private $columnas = array();
    private $fila;
    private $equivalencias = array();

    private $indice, $m_fila, $titulo;
    private $swEncabezado = 0; //variable que define si ya se imprimi� el encabezado
    private $objParam;
    public $url_archivo;

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
    var $id_gestion;

    function __construct(CTParametro $objParam)
    {
        $this->objParam = $objParam;
        $this->url_archivo = "../../../reportes_generados/" . $this->objParam->getParametro('nombre_archivo');
        $this->id_gestion = $this->objParam->getParametro('id_gestion');
        //ini_set('memory_limit','512M');
        set_time_limit(400);
        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array('memoryCacheSize' => '10MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

        $this->docexcel = new PHPExcel();
        $this->docexcel->getProperties()->setCreator("PXP")
            ->setLastModifiedBy("PXP")
            ->setTitle('POA')
            ->setSubject('POA')
            ->setDescription('Reporte "POA", generado por el framework PXP')
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Report File");

        $this->docexcel->setActiveSheetIndex(0);

        $this->docexcel->getActiveSheet()->setTitle('POA');

        $this->equivalencias = array(0 => 'A', 1 => 'B', 2 => 'C', 3 => 'D', 4 => 'E', 5 => 'F', 6 => 'G', 7 => 'H', 8 => 'I',
            9 => 'J', 10 => 'K', 11 => 'L', 12 => 'M', 13 => 'N', 14 => 'O', 15 => 'P', 16 => 'Q', 17 => 'R',
            18 => 'S', 19 => 'T', 20 => 'U', 21 => 'V', 22 => 'W', 23 => 'X', 24 => 'Y', 25 => 'Z',
            26 => 'AA', 27 => 'AB', 28 => 'AC', 29 => 'AD', 30 => 'AE', 31 => 'AF', 32 => 'AG', 33 => 'AH',
            34 => 'AI', 35 => 'AJ', 36 => 'AK', 37 => 'AL', 38 => 'AM', 39 => 'AN', 40 => 'AO', 41 => 'AP',
            42 => 'AQ', 43 => 'AR', 44 => 'AS', 45 => 'AT', 46 => 'AU', 47 => 'AV', 48 => 'AW', 49 => 'AX',
            50 => 'AY', 51 => 'AZ',
            52 => 'BA', 53 => 'BB', 54 => 'BC', 55 => 'BD', 56 => 'BE', 57 => 'BF', 58 => 'BG', 59 => 'BH',
            60 => 'BI', 61 => 'BJ', 62 => 'BK', 63 => 'BL', 64 => 'BM', 65 => 'BN', 66 => 'BO', 67 => 'BP',
            68 => 'BQ', 69 => 'BR', 70 => 'BS', 71 => 'BT', 72 => 'BU', 73 => 'BV', 74 => 'BW', 75 => 'BX',
            76 => 'BY', 77 => 'BZ');

    }

    function datosHeader($detalle)
    {
        $this->datos_detalle = $detalle;
    }

    function imprimeCabecera()
    {
        $styleTitulos = array(
            'font' => array(
                'bold' => true,
                'size' => 18,
                'name' => 'Calibri'
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'FFFFFF'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );
        $styleTitulos_principal = array(
            'font' => array(
                'bold' => true,
                'size' => 38,
                'name' => 'Calibri',
                'color' => array(
                    'rgb' => 'A0FCD8'
                ),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );
        $styleTitulosSubCabezera = array(
            'font' => array(
                'bold' => true,
                'name' => 'Calibri'
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'C0C0C0'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );

        $bordes = array(
            'borders' => array(
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),

        );
        $bordes_titulo_infe = array(
            'borders' => array(
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),

            ),

        );
        //titulos
        $logo_ = dirname(__FILE__) . '/../../lib' . $_SESSION['_DIR_LOGO']; //fRnk
        if (strpos($logo_, 'png') !== false)
            $gdImage = imagecreatefrompng($logo_);
        else
            $gdImage = imagecreatefromjpeg($logo_);
        // Add a drawing to the worksheetecho date('H:i:s') . " Add a drawing to the worksheet\n";
        $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
        $objDrawing->setImageResource($gdImage);
        $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
        $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
        $objDrawing->setHeight(50);
        $objDrawing->setCoordinates('A1');
        $objDrawing->setWorksheet($this->docexcel->getActiveSheet());

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, 1, 'PROGRAMACIÓN DE OPERACIONES');
        $this->docexcel->getActiveSheet()->mergeCells('A1:I1');
        $this->docexcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleTitulos);

        $this->docexcel->getActiveSheet()->getStyle('A3:L3')->applyFromArray($bordes_titulo_infe);
        $this->docexcel->getActiveSheet()->mergeCells('A2:I2');
        $this->docexcel->getActiveSheet()->mergeCells('A3:I3');

        $cone = new conexion();
        $link = $cone->conectarpdo();
        $consulta = $link->prepare("
            SELECT gestion 
            FROM param.tgestion 
            WHERE id_gestion = :id_gestion
        ");

        $consulta->execute([
            ':id_gestion' => $this->id_gestion
        ]);

        $data = $consulta->fetchAll(PDO::FETCH_ASSOC);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, 1, 'GESTIÓN: ' . $data[0]['gestion']);
        $this->docexcel->getActiveSheet()->mergeCells('J1:L1');
        $this->docexcel->getActiveSheet()->mergeCells('J2:L3');
        $this->docexcel->getActiveSheet()->mergeCells('J3:L3');
        $this->docexcel->getActiveSheet()->getStyle('J1')->applyFromArray($styleTitulos);

        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0, 4, 'Objetivo')->getColumnDimension('A')->setWidth(50);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1, 4, 'Tipo')->getColumnDimension('B')->setWidth(15);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2, 4, 'Indicador de Logro')->getColumnDimension('C')->setWidth(50);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3, 4, 'Periodo de Ejecución')->getColumnDimension('D')->setWidth(25);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4, 4, 'Ponderación (%)')->getColumnDimension('E')->setWidth(10);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5, 4, 'Producto')->getColumnDimension('F')->setWidth(50);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6, 4, 'Línea Base')->getColumnDimension('G')->setWidth(10);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7, 4, 'Fecha Inicio')->getColumnDimension('H')->setWidth(15);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8, 4, 'Fecha Fin')->getColumnDimension('I')->setWidth(15);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9, 4, 'Medio de Verificación')->getColumnDimension('J')->setWidth(25);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10, 4, 'Transaccional')->getColumnDimension('K')->setWidth(15);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11, 4, 'Creado por')->getColumnDimension('L')->setWidth(15);

        $this->docexcel->getActiveSheet()->getStyle('A4:L4')->applyFromArray($styleTitulosSubCabezera);
    }

    function imprimeDatos()
    {
        $datos = $this->datos_detalle;

        $fila = 5;
        $contador = 1;

        foreach ($datos as $value) {
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0, $fila, $value['descripcion']);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1, $fila, $value['tipo_objetivo']);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2, $fila, $value['indicador_logro']);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3, $fila, $value['periodo_ejecucion']);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4, $fila, $value['ponderacion']);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5, $fila, $value['producto']);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6, $fila, $value['linea_base']);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7, $fila, $value['fecha_inicio']);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8, $fila, $value['fecha_fin']);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9, $fila, $value['unidad_verificacion']);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10, $fila, $value['sw_transaccional']);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11, $fila, $value['usr_reg']);

            //$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(14,$fila,"=SUM(C".$fila.":N".$fila.")");

            $fila++;
            $contador++;
        }

        //************************************************Fin Detalle***********************************************

    }

    function generarReporte()
    {
        $this->imprimeDatos();
        $this->docexcel->setActiveSheetIndex(0);
        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);
    }

}

?>