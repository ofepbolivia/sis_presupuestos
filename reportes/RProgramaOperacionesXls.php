<?php

//fRnk: nuevo reporte Estructura UO HR01765-2024
class RProgramaOperacionesXls
{
    private $docexcel;
    private $objWriter;
    private $objParam;
    public $url_archivo;
    var $gestion = '';
    var $titulo = '';

    function __construct(CTParametro $objParam)
    {
        $this->objParam = $objParam;
        $this->url_archivo = "../../../reportes_generados/" . $this->objParam->getParametro('nombre_archivo');
        set_time_limit(400);
        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array('memoryCacheSize' => '10MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        $this->docexcel = new PHPExcel();
        $this->docexcel->getProperties()->setCreator($_SESSION['_TITULO_SIS_CORTO'])
            ->setLastModifiedBy($_SESSION['_TITULO_SIS_CORTO'])
            ->setTitle($this->objParam->getParametro('titulo_archivo'))
            ->setSubject($this->objParam->getParametro('titulo_archivo'))
            ->setDescription('Reporte "' . $this->objParam->getParametro('titulo_archivo'))
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Report File");
        $this->docexcel->setActiveSheetIndex(0);
    }

    function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }

    function setGestion($gestion)
    {
        $this->gestion = $gestion;
    }

    function imprimeDatosAMP()
    {
        $this->docexcel->getActiveSheet()->setTitle($this->titulo);
        $sheet = $this->docexcel->getActiveSheet();
        $datos = $this->objParam->getParametro('datos');
        $this->createSheetAMP($sheet, $datos, 'T');
    }

    function imprimeDatosACP()
    {
        $this->docexcel->getActiveSheet()->setTitle($this->titulo);
        $sheet = $this->docexcel->getActiveSheet();
        $datos = $this->objParam->getParametro('datos');
        $this->createSheetACP($sheet, $datos, 'T');
    }

    function imprimeDatosAPOA()
    {
        $this->docexcel->getActiveSheet()->setTitle('DATOS POA');
        $sheet = $this->docexcel->getActiveSheet();
        $datos = $this->objParam->getParametro('datos');
        $this->createSheetAPOA($sheet, $datos, 'T');
    }

    function createSheetAMP($sheet, $datos, $type)
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
        $logo_ = dirname(__FILE__) . '/../../lib' . $_SESSION['_DIR_LOGO'];
        if (strpos($logo_, 'png') !== false)
            $gdImage = imagecreatefrompng($logo_);
        else
            $gdImage = imagecreatefromjpeg($logo_);

        $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
        $objDrawing->setImageResource($gdImage);
        $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
        $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
        $objDrawing->setHeight(60);
        $objDrawing->setCoordinates('A1');
        $objDrawing->setWorksheet($this->docexcel->getActiveSheet());

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, 1, $this->titulo);
        $this->docexcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('B1:E2');

        $this->docexcel->getActiveSheet()->getStyle('A3:E3')->applyFromArray($bordes_titulo_infe);
        $this->docexcel->getActiveSheet()->mergeCells('A2:E2');
        $this->docexcel->getActiveSheet()->mergeCells('A3:E3');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, 1, 'GESTIÓN: ' . $this->gestion);
        $this->docexcel->getActiveSheet()->mergeCells('F1:G1');
        $this->docexcel->getActiveSheet()->mergeCells('F2:G3');
        $this->docexcel->getActiveSheet()->mergeCells('F3:G3');
        $this->docexcel->getActiveSheet()->getStyle('F1')->applyFromArray($styleTitulos);

        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0, 4, 'CÓDIGO')->getColumnDimension('A')->setWidth(15);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1, 4, 'ACCION DE MEDIANO PLAZO')->getColumnDimension('B')->setWidth(50);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2, 4, 'INDICADOR DE LOGRO')->getColumnDimension('C')->setWidth(50);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3, 4, 'PERIODO DE EJECUCIÓN')->getColumnDimension('D')->setWidth(25);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4, 4, 'PONDERACIÓN')->getColumnDimension('E')->setWidth(20);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5, 4, 'PRODUCTO')->getColumnDimension('F')->setWidth(50);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6, 4, 'LÍNEA BASE')->getColumnDimension('G')->setWidth(20);

        $this->docexcel->getActiveSheet()->getStyle('A4:G4')->applyFromArray($styleTitulosSubCabezera);

        $fila = 5;
        $contador = 1;

        foreach ($datos as $value) {
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0, $fila, $value['codigo']);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1, $fila, $value['descripcion']);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2, $fila, $value['indicador_logro']);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3, $fila, $value['periodo_ejecucion']);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4, $fila, $value['ponderacion']);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5, $fila, $value['producto']);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6, $fila, $value['linea_base']);

            $fila++;
            $contador++;
        }
    }

    function createSheetACP($sheet, $datos, $type)
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
        $logo_ = dirname(__FILE__) . '/../../lib' . $_SESSION['_DIR_LOGO'];
        if (strpos($logo_, 'png') !== false)
            $gdImage = imagecreatefrompng($logo_);
        else
            $gdImage = imagecreatefromjpeg($logo_);

        $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
        $objDrawing->setImageResource($gdImage);
        $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
        $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
        $objDrawing->setHeight(60);
        $objDrawing->setCoordinates('A1');
        $objDrawing->setWorksheet($this->docexcel->getActiveSheet());

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, 1, $this->titulo);
        $this->docexcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('B1:G2');

        $this->docexcel->getActiveSheet()->getStyle('A3:I3')->applyFromArray($bordes_titulo_infe);
        $this->docexcel->getActiveSheet()->mergeCells('A2:I2');
        $this->docexcel->getActiveSheet()->mergeCells('A3:I3');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, 1, 'GESTIÓN: ' . $this->gestion);
        $this->docexcel->getActiveSheet()->mergeCells('H1:I2');
        $this->docexcel->getActiveSheet()->getStyle('H1')->applyFromArray($styleTitulos);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, 4, 'ACCIÓN DE MEDIANO PLAZO');
        $this->docexcel->getActiveSheet()->mergeCells('A4:C4');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, 4, 'ACCION DE CORTO PLAZO');
        $this->docexcel->getActiveSheet()->mergeCells('D4:F4');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, 4, 'CENTRO DE COSTO');
        $this->docexcel->getActiveSheet()->mergeCells('G4:G5');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, 4, 'PARTIDA');
        $this->docexcel->getActiveSheet()->mergeCells('H4:H5');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, 4, 'PONDERACIÓN');
        $this->docexcel->getActiveSheet()->mergeCells('I4:I5');

        $this->docexcel->getActiveSheet()->getStyle('A4:I4')->applyFromArray($styleTitulosSubCabezera);

        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0, 5, 'CÓDIGO')->getColumnDimension('A')->setWidth(15);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1, 5, 'DESCRIPCIÓN')->getColumnDimension('B')->setWidth(50);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2, 5, 'PRODUCTO O RESULTADO')->getColumnDimension('C')->setWidth(30);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3, 5, 'CÓDIGO')->getColumnDimension('D')->setWidth(15);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4, 5, 'DESCRIPCIÓN')->getColumnDimension('E')->setWidth(50);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5, 5, 'PRODUCTO O RESULTADO')->getColumnDimension('F')->setWidth(30);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6, 5, 'CENTRO DE COSTO')->getColumnDimension('G')->setWidth(30);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7, 5, 'PARTIDA')->getColumnDimension('H')->setWidth(50);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8, 5, 'PONDERACIÓN')->getColumnDimension('I')->setWidth(20);

        $this->docexcel->getActiveSheet()->getStyle('A5:I5')->applyFromArray($styleTitulosSubCabezera);

        $fila = 6;
        $contador = 1;

        foreach ($datos as $value) {
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0, $fila, $value['codigo_amp']);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1, $fila, $value['descripcion_amp']);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2, $fila, $value['producto_amp']);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3, $fila, $value['codigo_acp']);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4, $fila, $value['descripcion_acp']);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5, $fila, $value['producto_acp']);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6, $fila, $value['centro_costo']);
            $value['partida'] = html_entity_decode($value['partida'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $value['partida'] = mb_convert_encoding($value['partida'], 'UTF-8', 'auto');
            $value['partida'] = str_replace('<br>', "\n", $value['partida']);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7, $fila, $value['partida']);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8, $fila, $value['ponderacion']);

            $fila++;
            $contador++;
        }
    }

    function createSheetAPOA($sheet, $datos, $type)
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
        $logo_ = dirname(__FILE__) . '/../../lib' . $_SESSION['_DIR_LOGO'];
        if (strpos($logo_, 'png') !== false)
            $gdImage = imagecreatefrompng($logo_);
        else
            $gdImage = imagecreatefromjpeg($logo_);

        $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
        $objDrawing->setImageResource($gdImage);
        $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
        $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
        $objDrawing->setHeight(60);
        $objDrawing->setCoordinates('A1');
        $objDrawing->setWorksheet($this->docexcel->getActiveSheet());

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, 1, $this->titulo);
        $this->docexcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('B1:H2');

        $this->docexcel->getActiveSheet()->getStyle('A3:J3')->applyFromArray($bordes_titulo_infe);
        $this->docexcel->getActiveSheet()->mergeCells('A2:J2');
        $this->docexcel->getActiveSheet()->mergeCells('A3:J3');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, 1, 'GESTIÓN: ' . $this->gestion);
        $this->docexcel->getActiveSheet()->mergeCells('I1:J2');
        $this->docexcel->getActiveSheet()->getStyle('I1')->applyFromArray($styleTitulos);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, 4, 'ACCIÓN DE MEDIANO PLAZO');
        $this->docexcel->getActiveSheet()->mergeCells('A4:C4');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, 4, 'ACCION DE CORTO PLAZO');
        $this->docexcel->getActiveSheet()->mergeCells('D4:F4');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, 4, 'OPERACIÓN');
        $this->docexcel->getActiveSheet()->mergeCells('G4:G5');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, 4, 'ACTIVIDAD');
        $this->docexcel->getActiveSheet()->mergeCells('H4:H5');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, 4, 'CENTRO DE COSTO');
        $this->docexcel->getActiveSheet()->mergeCells('I4:I5');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, 4, 'ESTRUCTURA PROGRAMATICA');
        $this->docexcel->getActiveSheet()->mergeCells('J4:J5');

        $this->docexcel->getActiveSheet()->getStyle('A4:J4')->applyFromArray($styleTitulosSubCabezera);

        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0, 5, 'CÓDIGO')->getColumnDimension('A')->setWidth(15);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1, 5, 'DESCRIPCIÓN')->getColumnDimension('B')->setWidth(50);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2, 5, 'PRODUCTO O RESULTADO')->getColumnDimension('C')->setWidth(30);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3, 5, 'CÓDIGO')->getColumnDimension('D')->setWidth(15);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4, 5, 'DESCRIPCIÓN')->getColumnDimension('E')->setWidth(50);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5, 5, 'PRODUCTO O RESULTADO')->getColumnDimension('F')->setWidth(30);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6, 5, 'OPERACIÓN')->getColumnDimension('G')->setWidth(30);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7, 5, 'ACTIVIDAD')->getColumnDimension('H')->setWidth(50);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8, 5, 'CENTRO DE COSTO')->getColumnDimension('I')->setWidth(40);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9, 5, 'ESTRUCTURA PROGRAMATICA')->getColumnDimension('J')->setWidth(40);

        $this->docexcel->getActiveSheet()->getStyle('A5:J5')->applyFromArray($styleTitulosSubCabezera);

        $fila = 6;
        $contador = 1;

        foreach ($datos as $value) {
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0, $fila, $value['codigo_amp']);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1, $fila, $value['descripcion_amp']);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2, $fila, $value['producto_amp']);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3, $fila, $value['codigo_acp']);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4, $fila, $value['descripcion_acp']);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5, $fila, $value['producto_acp']);
            $value['operaciones'] = html_entity_decode($value['operaciones'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $value['operaciones'] = mb_convert_encoding($value['operaciones'], 'UTF-8', 'auto');
            $value['operaciones'] = str_replace('<br>', "\n", $value['operaciones']);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6, $fila, $value['operaciones']);
            $value['actividades'] = html_entity_decode($value['actividades'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $value['actividades'] = mb_convert_encoding($value['actividades'], 'UTF-8', 'auto');
            $value['actividades'] = str_replace('<br>', "\n", $value['actividades']);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7, $fila, $value['actividades']);
            $value['centro_costo'] = html_entity_decode($value['centro_costo'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $value['centro_costo'] = mb_convert_encoding($value['centro_costo'], 'UTF-8', 'auto');
            $value['centro_costo'] = str_replace('<br>', "\n", $value['centro_costo']);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8, $fila, $value['centro_costo']);
            $value['estrucutra_programatica'] = html_entity_decode($value['estrucutra_programatica'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $value['estrucutra_programatica'] = mb_convert_encoding($value['estrucutra_programatica'], 'UTF-8', 'auto');
            $value['estrucutra_programatica'] = str_replace('<br>', "\n", $value['estrucutra_programatica']);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9, $fila, $value['estrucutra_programatica']);
            $fila++;
            $contador++;
        }
    }

    function generarReporteAMP()
    {
        $this->docexcel->setActiveSheetIndex(0);
        $this->imprimeDatosAMP();
        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);
    }

    function generarReporteACP()
    {
        $this->docexcel->setActiveSheetIndex(0);
        $this->imprimeDatosACP();
        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);
    }

    function generarReporteAPOA()
    {
        $this->docexcel->setActiveSheetIndex(0);
        $this->imprimeDatosAPOA();
        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);
    }
}

?>