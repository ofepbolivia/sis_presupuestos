<?php
//Adalid: reporte de acciones a mediano plazo HR 2024-01273

class RProgramaOperaciones extends ReportePDF
{
    var $dataMaster;
    var $ancho_hoja;
    var $numeracion;
    var $ancho_sin_totales;
    var $posY;
    var $gestion = '';
    var $titulo = '';

    function datosHeader($maestro)
    {
        $this->ancho_hoja = $this->getPageWidth() - PDF_MARGIN_LEFT - PDF_MARGIN_RIGHT - 10;
        $this->dataMaster = $maestro;
    }

    function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }

    function setGestion($gestion)
    {
        $this->gestion = $gestion;
    }

    function Header()
    {
        $content = '<table border="0.5" cellpadding="1" style="font-size: 11px">
            <tr>
                <td style="width: 23%; color: #444444;" rowspan="2">
                    &nbsp;<img  style="width: 120px;" src="./../../../lib/' . $_SESSION['_DIR_LOGO'] . '" alt="Logo">
                </td>		
                <td style="width: 52%; color: #444444;text-align: center" rowspan="2">
                   <h1 style="font-size: 16px">' . $this->titulo . '</h1>
                </td>
                <td style="width: 25%; color: #444444; text-align: left;height: 30px">&nbsp;&nbsp;<b>Periodo:</b> ' . $this->gestion . '</td>
            </tr>
            <tr>
                <td style="width: 25%; color: #444444; text-align: left;">&nbsp;&nbsp;<b>Página:</b> ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages() . '</td>
            </tr>
        </table>';
        $this->writeHTML($content, false, false, true, false, '');
    }

    function generarAccionMedianoPlazo()
    {
        $cantidad = 1;
        $this->setFontSubsetting(false);
        $this->AddPage();
        $this->SetFontSize(7);
        $html = '<table border="0.5" cellpadding="2" cellspacing="0">';
        $html .= '<tr style="background-color: #cccccc;font-size: 10px;text-align: center;vertical-align: middle;">
                    <td width="6%"><b>CÓDIGO</b></td>
                    <td width="26%" style="vertical-align: middle;"><b>ACCION DE MEDIANO PLAZO</b></td>
                    <td width="25%"><b>INDICADOR DE LOGRO</b></td>
                    <td width="15%"><b>PERIODO DE EJECUCIÓN</b></td>
                    <td width="10%"><b>PONDERACIÓN</b></td>
                    <td width="10%"><b>PRODUCTO</b></td>
                    <td width="8%"><b>LÍNEA BASE</b></td></tr>';
        if (is_array($this->dataMaster) && count($this->dataMaster) > 0) {
            foreach ($this->dataMaster as $row) {
                $html .= '<tr>';
                $html .= '<td>' . $row['codigo'] . '</td>';
                $html .= '<td>' . $row['descripcion'] . '</td>';
                $html .= '<td>' . $row['indicador_logro'] . '</td>';
                $html .= '<td>' . $row['periodo_ejecucion'] . '</td>';
                $html .= '<td>' . $row['ponderacion'] . '</td>';
                $html .= '<td>' . $row['producto'] . '</td>';
                $html .= '<td>' . $row['linea_base'] . '</td>';
                $html .= '</tr>';
                //$cantidad++;
            }
        } else {
            $html .= '<tr>';
            $html .= '<td colspan="8" align="center" height="30"><p>No hay registros</p></td>';
            $html .= '</tr>';
        }
        $html .= '</table>';
        $this->writeHTML($html, false, false, true, false, '');
        $this->Ln(10);
    }

    function generarAccionCortoPlazo()
    {
        $cantidad = 1;
        $this->setFontSubsetting(false);
        $this->AddPage();
        $this->SetFontSize(7);
        $html = '<table border="0.5" cellpadding="2" cellspacing="0">';
        $html .= '<tr style="background-color: #cccccc;font-size: 10px;text-align: center;vertical-align: middle;">
                    <td width="36%" colspan="3"><b>ACCIÓN DE MEDIANO PLAZO</b></td>
                    <td width="36%" colspan="3"><b>ACCIÓN DE CORTO PLAZO</b></td>
                    <td width="8%" rowspan="2"><b>CENTRO DE &#10;COSTO</b></td>
                    <td width="14%" rowspan="2"><b>PARTIDA</b></td>
                    <td width="6%" rowspan="2"><b>PONDERACIÓN</b></td></tr>
                <tr style="background-color: #cccccc;font-size: 10px;text-align: center;vertical-align: middle;">
                    <td width="6%"><b>COD.</b></td>
                    <td width="22%"><b>DESCRIPCIÓN</b></td>
                    <td width="8%"><b>PRODUCTO &#10;O RESULTADO</b></td>
                    <td width="6%"><b>COD.</b></td>
                    <td width="22%"><b>DESCRIPCIÓN</b></td>
                    <td width="8%"><b>PRODUCTO &#10;O RESULTADO</b></td>
                </tr>';
        if (is_array($this->dataMaster) && !empty($this->dataMaster)) {
            foreach ($this->dataMaster as $row) {
                $html .= '<tr>';
                $html .= '<td>' . $row['codigo_amp'] . '</td>';
                $html .= '<td>' . $row['descripcion_amp'] . '</td>';
                $html .= '<td>' . $row['producto_amp'] . '</td>';
                $html .= '<td>' . $row['codigo_acp'] . '</td>';
                $html .= '<td>' . $row['descripcion_acp'] . '</td>';
                $html .= '<td>' . $row['producto_acp'] . '</td>';
                $html .= '<td>' . $row['centro_costo'] . '</td>';
                $html .= '<td>' . $row['partida'] . '</td>';
                $html .= '<td>' . $row['ponderacion'] . '</td>';
                $html .= '</tr>';
                //$cantidad++;
            }
        } else {
            $html .= '<tr>';
            $html .= '<td colspan="9" align="center" height="30"><p>No hay registros</p></td>';
            $html .= '</tr>';
        }
        $html .= '</table>';
        // var_dump($html);
        // exit;
        $this->writeHTML($html, false, false, true, false, '');
        $this->Ln(10);
    }

    function generarArticulacionPoa()
    {
        $cantidad = 1;
        $this->setFontSubsetting(false);
        $this->AddPage();
        $this->SetFontSize(7);

        $html = '
        <table border="0.5" cellpadding="2" cellspacing="0">

            <tr style="background-color:#cccccc; font-size:10px; text-align:center; vertical-align:middle;">
                <td width="36%" colspan="3"><b>ACCIÓN DE MEDIANO PLAZO</b></td>
                <td width="36%" colspan="3"><b>ACCIÓN DE CORTO PLAZO</b></td>
                <td width="6%" rowspan="2"><b>OPERACIÓN</b></td>
                <td width="6%" rowspan="2"><b>ACTIVIDAD</b></td>
                <td width="8%" rowspan="2"><b>CENTRO DE COSTO</b></td>
                <td width="8%" rowspan="2"><b>ESTRUCTURA PROGRAMÁTICA</b></td>
            </tr>

            <tr style="background-color:#cccccc; font-size:10px; text-align:center; vertical-align:middle;">
                <td width="6%"><b>COD.</b></td>
                <td width="22%"><b>DESCRIPCIÓN</b></td>
                <td width="8%"><b>PRODUCTO O RESULTADO</b></td>

                <td width="6%"><b>COD.</b></td>
                <td width="22%"><b>DESCRIPCIÓN</b></td>
                <td width="8%"><b>PRODUCTO O RESULTADO</b></td>
            </tr>
        ';

        if (is_array($this->dataMaster) && !empty($this->dataMaster)) {

            foreach ($this->dataMaster as $row) {

                $html .= '<tr>';
                $html .= '<td>' . $row["codigo_amp"] . '</td>';
                $html .= '<td>' . $row["descripcion_amp"] . '</td>';
                $html .= '<td>' . $row["producto_amp"] . '</td>';

                $html .= '<td>' . $row["codigo_acp"] . '</td>';
                $html .= '<td>' . $row["descripcion_acp"] . '</td>';
                $html .= '<td>' . $row["producto_acp"] . '</td>';

                $html .= '<td>' . $row["operaciones"] . '</td>';
                $html .= '<td>' . $row["actividades"] . '</td>';
                $html .= '<td>' . $row["centro_costo"] . '</td>';
                $html .= '<td>' . $row["estrucutra_programatica"] . '</td>';
                $html .= '</tr>';
            }

        } else {
            $html .= '
            <tr>
                <td colspan="10" align="center" height="30"><p>No hay registros</p></td>
            </tr>';
        }

        $html .= '</table>';

        $this->writeHTML($html, false, false, true, false, '');
        $this->Ln(10);
    }

    function Footer()
    {
        $this->setY(-15);
        $ormargins = $this->getOriginalMargins();
        $this->SetTextColor(0, 0, 0);
        //set style for cell border
        $line_width = 0.85 / $this->getScaleFactor();
        $this->SetLineStyle(array('width' => $line_width, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        $ancho = round(($this->getPageWidth() - $ormargins['left'] - $ormargins['right']) / 3);
        $this->Ln(2);
        $cur_y = $this->GetY();
        //$this->Cell($ancho, 0, 'Generado por XPHS', 'T', 0, 'L');
        $this->Cell($ancho, 0, 'Usuario: ' . $_SESSION['_LOGIN'], '', 0, 'L');
        $pagenumtxt = 'Página' . ' ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages();
        $this->Cell($ancho, 0, $pagenumtxt, '', 0, 'C');
        $this->Cell($ancho, 0, $_SESSION['_REP_NOMBRE_SISTEMA'], '', 0, 'R');
        $this->Ln();
        $fecha_rep = date("d-m-Y H:i:s");
        $this->Cell($ancho, 0, "Fecha : " . $fecha_rep, '', 0, 'L');
        $this->Ln($line_width);
        $this->Ln();
    }
}

?>