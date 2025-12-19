<?php
require_once dirname(__FILE__).'/../../pxp/lib/lib_reporte/ReportePDF.php';
require_once(dirname(__FILE__) . '/../../lib/tcpdf/tcpdf_barcodes_2d.php');
class RPoaPdf extends  ReportePDF{
    var $datos ;
    var $id_gestion ;
    var $ancho_hoja;

    function Header() {
        $this->Ln(3);

        $cone = new conexion();
        $link = $cone->conectarpdo();$consulta = $link->prepare("
            SELECT gestion 
            FROM param.tgestion 
            WHERE id_gestion = :id_gestion
        ");

        $consulta->execute([
            ':id_gestion' => $this->id_gestion
        ]);

        $data = $consulta->fetchAll(PDO::FETCH_ASSOC);

        //cabecera del reporte
        $this->Image(dirname(__FILE__).'/../../lib'.$_SESSION['_DIR_LOGO'], 20,5,40,20);
        $this->ln(5);

        $this->SetFont('','B',12);
        $this->Cell(0,5,"PROGRAMACIÓN DE OPERACIONES",0,1,'C');
        $this->Cell(0,5,"GESTIÓN - ".$data[0]['gestion'],0,1,'C');
        $this->Ln(5);
    }

    function setDatos($datos, $gestion) {

        $this->datos = $datos;
        $this->id_gestion = $gestion;
    }

    function  generarReporteH()
    {
        $this->AddPage('L');
        $this->setPageOrientation('L');

        $this->SetMargins(15, 40, 15);
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        $obj_institucion = '';
        $obj_gestion = '';
        $obj_operacion = '';
        $id_obj_fk = '';
        $id_padre = '';
        $bandera = true;
        $bandera_head = true;
        $contador = 0;
        $this->Ln(7.5);
        $cont_lineas = $this->getY();
        $tbl = '';

        $tbl .= '<table border="1" style="font-size: 7pt;"><tr align="center">
            <td width="17%"><b>Objetivo</b></td>
            <td width="6%"><b>Tipo</b></td>
            <td width="8%"><b>Indicador de Logro</b></td>
            <td width="7%"><b>Periodo de Ejecución</b></td>
            <td width="7%"><b>Ponderación (%)</b></td>
            <td width="12%"><b>Producto</b></td>
            <td width="7%"><b>Línea Base</b></td>
            <td width="6%"><b>Fecha Inicio</b></td>
            <td width="6%"><b>Fecha Fin</b></td>
            <td width="7%"><b>Medio de Verificación</b></td>
            <td width="6%"><b>Transaccional</b></td>
            <td width="6%"><b>Creado por</b></td>
            </tr>
            ';

        foreach( $this->datos as $record) {
            $tbl .= '<tr>
                    <td width="17%">'.$record['descripcion'].'</td>
                    <td width="6%">'.$record['tipo_objetivo'].'</td>
                    <td width="8%">'.$record['indicador_logro'].'</td>
                    <td width="7%">'.$record['periodo_ejecucion'].'</td>
                    <td width="7%">'.$record['ponderacion'].'</td>
                    <td width="12%">'.$record['producto'].'</td>
                    <td width="7%">'.$record['linea_base'].'</td>
                    <td width="6%">'.implode('/', array_reverse(explode('-', $record['fecha_inicio']))).'</td>
                    <td width="6%">'.implode('/', array_reverse(explode('-', $record['fecha_fin']))).'</td>
                    <td width="7%">'.$record['unidad_verificacion'].'</td>
                    <td width="6%">'.$record['sw_transaccional'].'</td>
                    <td width="6%">'.$record['usr_reg'].'</td>
                  </tr>
                 ';
        }
        $tbl .='</table>';
        $this->writeHTML ($tbl);
    }


    function  generarReporte()
    {
        $this->AddPage('P');
        $this->setPageOrientation('P');

        $this->SetMargins(15, 40, 15);
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        $obj_institucion = '';
        $obj_gestion = '';
        $obj_operacion = '';
        $id_obj_fk = '';
        $id_padre = '';
        $bandera = true;
        $bandera_head = true;
        $contador = 0;
        $this->Ln(7.5);
        $cont_lineas = $this->getY();
        $tbl = '';

        $tbl .= '<table border="1" style="font-size: 7pt;"><tr align="center">
            <td width="17%"><b>Objetivo</b></td>
            <td width="6%"><b>Tipo</b></td>
            <td width="8%"><b>Indicador de Logro</b></td>
            <td width="7%"><b>Periodo de Ejecución</b></td>
            <td width="7%"><b>Ponderación (%)</b></td>
            <td width="12%"><b>Producto</b></td>
            <td width="7%"><b>Línea Base</b></td>
            <td width="6%"><b>Fecha Inicio</b></td>
            <td width="6%"><b>Fecha Fin</b></td>
            <td width="7%"><b>Medio de Verificación</b></td>
            <td width="6%"><b>Transaccional</b></td>
            <td width="6%"><b>Creado por</b></td>
            </tr>
            ';

        foreach( $this->datos as $record) {
            $tbl .= '<tr>
                    <td width="17%">'.$record['descripcion'].'</td>
                    <td width="6%">'.$record['tipo_objetivo'].'</td>
                    <td width="8%">'.$record['indicador_logro'].'</td>
                    <td width="7%">'.$record['periodo_ejecucion'].'</td>
                    <td width="7%">'.$record['ponderacion'].'</td>
                    <td width="12%">'.$record['producto'].'</td>
                    <td width="7%">'.$record['linea_base'].'</td>
                    <td width="6%">'.implode('/', array_reverse(explode('-', $record['fecha_inicio']))).'</td>
                    <td width="6%">'.implode('/', array_reverse(explode('-', $record['fecha_fin']))).'</td>
                    <td width="7%">'.$record['unidad_verificacion'].'</td>
                    <td width="6%">'.$record['sw_transaccional'].'</td>
                    <td width="6%">'.$record['usr_reg'].'</td>
                  </tr>
                 ';
        }
        $tbl .='</table>';
        $this->writeHTML ($tbl);
    }

    function generarImagen($nom, $car, $ofi){
        $cadena_qr = 'Nombre: '.$nom. "\n" . 'Cargo: '.$car."\n".'Oficina: '.$ofi ;
        $barcodeobj = new TCPDF2DBarcode($cadena_qr, 'QRCODE,M');
        $png = $barcodeobj->getBarcodePngData($w = 8, $h = 8, $color = array(0, 0, 0));
        $im = imagecreatefromstring($png);
        $nom = preg_replace('([^A-Za-z0-9])', '_', $nom);
        if ($im !== false) {
            header('Content-Type: image/png');
            imagepng($im, dirname(__FILE__) . "/../../reportes_generados/" . $nom . ".png");
            imagedestroy($im);

        } else {
            echo 'A ocurrido un Error.';
        }
        $url_archivo = dirname(__FILE__) . "/../../reportes_generados/" . $nom . ".png";

        return $url_archivo;
    }

}
?>