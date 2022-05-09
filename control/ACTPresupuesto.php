<?php
/**
*@package pXP
*@file gen-ACTPresupuesto.php
*@author  Gonzalo Sarmiento Sejas
*@date 26-11-2012 21:35:35
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/
/*
require_once(dirname(__FILE__).'/../../sis_mantenimiento/reportes/pxpReport/ReportWriter.php');
require_once(dirname(__FILE__).'/../../sis_mantenimiento/reportes/RPresupuesto.php');
require_once(dirname(__FILE__).'/../../sis_mantenimiento/reportes/pxpReport/DataSource.php');
require_once(dirname(__FILE__).'/../../sis_mantenimiento/reportes/pxpReport/DataSource.php');
*/
require_once(dirname(__FILE__).'/../../sis_presupuestos/reportes/RCertificacionPresupuestaria.php');
require_once(dirname(__FILE__).'/../../sis_presupuestos/reportes/RPoaPDF.php');
require_once(dirname(__FILE__).'/../../sis_presupuestos/reportes/RNotaIntern.php');
require_once(dirname(__FILE__).'/../../sis_presupuestos/reportes/RCertificacionPresupuestariaMod.php');

include_once(dirname(__FILE__).'/../../lib/lib_general/ExcelInput.php');
require_once(dirname(__FILE__).'/../reportes/RFormPresupPDF.php');

require_once(dirname(__FILE__).'/../../sis_presupuestos/reportes/RInformacionPresupuestaria.php');

class ACTPresupuesto extends ACTbase{

	function listarPresupuesto(){
		$this->objParam->defecto('ordenacion','id_presupuesto');

		$this->objParam->defecto('dir_ordenacion','asc');
		$this->objParam->addParametro('id_funcionario_usu',$_SESSION["ss_id_funcionario"]);
        $this->objParam->addParametro('tipo_interfaz',$this->objParam->getParametro('tipo_interfaz'));

        if(strtolower($this->objParam->getParametro('estado'))=='borrador'){
             $this->objParam->addFiltro("(pre.estado in (''borrador''))");
        }
		if(strtolower($this->objParam->getParametro('estado'))=='en_proceso'){
             $this->objParam->addFiltro("(pre.estado not in (''borrador'',''aprobado''))");
        }
		if(strtolower($this->objParam->getParametro('estado'))=='finalizados'){
             $this->objParam->addFiltro("(pre.estado in (''aprobado''))");
        }

		if($this->objParam->getParametro('id_gestion')!=''){
	    	$this->objParam->addFiltro("vcc.id_gestion = ".$this->objParam->getParametro('id_gestion'));
		}

		if($this->objParam->getParametro('codigos_tipo_pres')!=''){
	    	$this->objParam->addFiltro("(pre.tipo_pres::integer in (".$this->objParam->getParametro('codigos_tipo_pres').") or pre.tipo_pres is null or pre.tipo_pres = '''')");
		}

		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam, $this);
			$this->res = $this->objReporte->generarReporteListado('MODPresupuesto','listarPresupuesto');
		} else{
			$this->objFunc=$this->create('MODPresupuesto');
			$this->res=$this->objFunc->listarPresupuesto();
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}


	function listarPresupuestoRest(){
		$this->objParam->addParametro('gestion',$this->objParam->getParametro('gestion'));
		$this->objFunc=$this->create('MODPresupuesto');
		$this->res=$this->objFunc->listarPresupuestoRest();
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

    function listarPresupuestoCmb(){
		$this->objParam->defecto('ordenacion','id_presupuesto');

		$this->objParam->defecto('dir_ordenacion','asc');
		$this->objParam->addParametro('id_funcionario_usu',$_SESSION["ss_id_funcionario"]);



        if($this->objParam->getParametro('estado')!=''){
	    	$this->objParam->addFiltro("estado = ''".$this->objParam->getParametro('estado')."''");
		}


		if($this->objParam->getParametro('id_gestion')!=''){
	    	$this->objParam->addFiltro("id_gestion = ".$this->objParam->getParametro('id_gestion'));
		}

		if($this->objParam->getParametro('codigos_tipo_pres')!=''){
	    	$this->objParam->addFiltro("(tipo_pres::integer in (".$this->objParam->getParametro('codigos_tipo_pres').") or tipo_pres is null or tipo_pres = '''')");
		}

		if($this->objParam->getParametro('movimiento_tipo_pres')!=''){
	    	//$this->objParam->addFiltro("movimiento_tipo_pres = ''".$this->objParam->getParametro('movimiento_tipo_pres')."''");
            $this->objParam->addFiltro("((movimiento_tipo_pres = ''".$this->objParam->getParametro('movimiento_tipo_pres')."'' or movimiento_tipo_pres=''gasto'')or movimiento_tipo_pres=''recurso'')" );
            //$this->objParam->addFiltro("(movimiento_tipo_pres = ''".$this->objParam->getParametro('movimiento_tipo_pres')."'') or( movimiento_tipo_pres=''gasto'' and movimiento_tipo_pres=''recurso'')" );
            //$this->objParam->addFiltro("(movimiento_tipo_pres = ''".$this->objParam->getParametro('movimiento_tipo_pres')."'') or( movimiento_tipo_pres=''gasto'' and movimiento_tipo_pres=''recurso'') and (movimiento_tipo_pres=''gasto'')" );
            //$this->objParam->addFiltro("(movimiento_tipo_pres = ''".$this->objParam->getParametro('movimiento_tipo_pres')."'' == ''recurso-gasto'') " );
        }


		if($this->objParam->getParametro('sw_oficial')!=''){
	    	$this->objParam->addFiltro("sw_oficial = ''".$this->objParam->getParametro('sw_oficial')."''");
		}

		if($this->objParam->getParametro('sw_consolidado')!=''){
	    	$this->objParam->addFiltro("sw_consolidado = ''".$this->objParam->getParametro('sw_consolidado')."''");
		}

		if($this->objParam->getParametro('tipo_ajuste')!='' &&
		   $this->objParam->getParametro('nro_tramite')!='' &&
		   $this->objParam->getParametro('id_gestion')!=''){

	    	  $this->objParam->addFiltro("id_presupuesto in (select x.id_presupuesto from pre.vpartida_ejecucion_check x where   x.id_gestion =  ".$this->objParam->getParametro('id_gestion')." and  x.nro_tramite = ''".$this->objParam->getParametro('nro_tramite')."'')");


		}





		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam, $this);
			$this->res = $this->objReporte->generarReporteListado('MODPresupuesto','listarPresupuestoCmb');
		} else{
			$this->objFunc=$this->create('MODPresupuesto');
			$this->res=$this->objFunc->listarPresupuestoCmb();
		}

       if($this->objParam->getParametro('_adicionar')!=''){

			$respuesta = $this->res->getDatos();

			array_unshift ( $respuesta, array(  'id_presupuesto'=>'0',
		                                'codigo_cc'=>'Todos',
									    'descripcion'=>'Todos',
										'desc_tipo_presupuesto'=>'Todos',
										'estado'=>'Todos',
										'desc_tipo_presupuesto'=>'Todos',
										'movimiento_tipo_pres'=>'Todos',
										'tipo'=>'Todos'));
		    //var_dump($respuesta);
			$this->res->setDatos($respuesta);
		}

		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function insertarPresupuesto(){
		$this->objFunc=$this->create('MODPresupuesto');
		if($this->objParam->insertar('id_presupuesto')){
			$this->res=$this->objFunc->insertarPresupuesto();
		} else{
			$this->res=$this->objFunc->modificarPresupuesto();
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function eliminarPresupuesto(){
		$this->objFunc=$this->create('MODPresupuesto');
		$this->res=$this->objFunc->eliminarPresupuesto();
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

    function reportePresupuesto(){
        $dataSource = new DataSource();
        $idPresupuesto = $this->objParam->getParametro('id_presupuesto');
        $this->objParam->addParametroConsulta('id_presupuesto',$idPresupuesto);
        $this->objParam->addParametroConsulta('ordenacion','id_presupuesto');
        $this->objParam->addParametroConsulta('dir_ordenacion','ASC');
        $this->objParam->addParametroConsulta('cantidad',1000);
        $this->objParam->addParametroConsulta('puntero',0);
        $this->objFunc = $this->create('MODPresupuesto');
        $resultPresupuesto = $this->objFunc->reportePresupuesto();
        $datosPresupuesto = $resultPresupuesto->getDatos();

        $dataSource->putParameter('moneda', $datosPresupuesto[0]['moneda']);

        $presupuestoDataSource = new DataSource();
        $presupuestoDataSource->setDataSet($resultPresupuesto->getDatos());
        $dataSource->putParameter('presupuestoDataSource', $presupuestoDataSource);

        //build the report
        $reporte = new RPresupuesto();
        $reporte->setDataSource($dataSource);
        $nombreArchivo = 'ReportePresupuesto.pdf';
        $reportWriter = new ReportWriter($reporte, dirname(__FILE__).'/../../reportes_generados/'.$nombreArchivo);
        $reportWriter->writeReport(ReportWriter::PDF);

        $mensajeExito = new Mensaje();
        $mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado',
                                        'Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->res = $mensajeExito;
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function graficaPresupuesto(){
        $idPresupuesto = $this->objParam->getParametro('id_presupuesto');

        $this->objParam->defecto('ordenacion','id_presupuesto');
        $this->objParam->defecto('id_presupuesto',$idPresupuesto);
        $this->objParam->defecto('dir_ordenacion','asc');
        $this->objParam->defecto('cantidad',1000);
        $this->objParam->defecto('puntero',0);

        $this->objFunc=$this->create('MODPresupuesto');
        $this->objFunc->setCount(false);
        $this->res=$this->objFunc->reportePresupuesto();
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

	function clonarPresupuestosGestion(){
		$this->objFunc=$this->create('MODPresupuesto');
		$this->res=$this->objFunc->clonarPresupuestosGestion();
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function iniciarTramite(){
		$this->objParam->addParametro('id_funcionario_usu',$_SESSION["ss_id_funcionario"]);
		$this->objFunc=$this->create('MODPresupuesto');
		$this->res=$this->objFunc->iniciarTramite();
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function siguienteEstadoPresupuesto(){
        $this->objFunc=$this->create('MODPresupuesto');
        $this->res=$this->objFunc->siguienteEstadoPresupuesto($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

   function anteriorEstadoPresupuesto(){
        $this->objFunc=$this->create('MODPresupuesto');
        $this->res=$this->objFunc->anteriorEstadoPresupuesto($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }


	//Reporte Certificación Presupuestaria (FEA) 13/07/2017
	function reporteCertificacionP (){
		$this->objFunc=$this->create('MODPresupuesto');
        $dataSource=$this->objFunc->reporteCertificacionP();
		$this->dataSource=$dataSource->getDatos();
		$nombreArchivo = uniqid(md5(session_id()).'[Reporte-CertificaciónPresupuestaria]').'.pdf';
		$this->objParam->addParametro('orientacion','P');
		$this->objParam->addParametro('tamano','LETTER');
		$this->objParam->addParametro('nombre_archivo',$nombreArchivo);

		$this->objReporte = new RCertificacionPresupuestaria($this->objParam);
		$this->objReporte->setDatos($this->dataSource);
		$this->objReporte->generarReporte();
		$this->objReporte->output($this->objReporte->url_archivo,'F');


		$this->mensajeExito=new Mensaje();
		$this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado', 'Se generó con éxito el reporte: '.$nombreArchivo,'control');
		$this->mensajeExito->setArchivoGenerado($nombreArchivo);
		$this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
	}

    //Reporte POA (FEA) 31/07/2017
    function reportePOA (){
        $this->objFunc=$this->create('MODPresupuesto');
        $dataSource=$this->objFunc->reportePOA();
        $this->dataSource=$dataSource->getDatos();

        $nombreArchivo = uniqid(md5(session_id()).'[Reporte-POA]').'.pdf';
        $this->objParam->addParametro('orientacion','L');
        $this->objParam->addParametro('tamano','LETTER');
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);

        $this->objReporte = new RPoaPDF($this->objParam);
        $this->objReporte->setDatos($this->dataSource);
        $this->objReporte->generarReporte();
        $this->objReporte->output($this->objReporte->url_archivo,'F');


        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado', 'Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
    }
    function notaInterna(){
        $this->objFunc=$this->create('MODPresupuesto');
        $dataSource=$this->objFunc->listarNotaInterna();
        $this->dataSource=$dataSource->getDatos();
        //var_dump($this->dataSource);exit;
        $nombreArchivo = uniqid(md5(session_id()).'[Reporte-POA]').'.pdf';
        $this->objParam->addParametro('orientacion','P');
        $this->objParam->addParametro('tamano','LETTER');
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);

        $this->objReporte = new RNotaIntern($this->objParam);
        $this->objReporte->setDatos($this->dataSource);
        $this->objReporte->generarReporte();
        $this->objReporte->output($this->objReporte->url_archivo,'F');


        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado', 'Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
    }

    //(franklin.espinoza) 23/5/2019 Certificacion Presupuestaria Modificada
    function reporteCertificacionPMod (){
        $this->objFunc=$this->create('MODPresupuesto');
        $dataSource=$this->objFunc->reporteCertificacionPMod();
        $this->dataSource=$dataSource->getDatos();

        $nombreArchivo = uniqid(md5(session_id()).'[Reporte-CertificaciónPresupuestariaMod]').'.pdf';
        $this->objParam->addParametro('orientacion','P');
        $this->objParam->addParametro('tamano','LETTER');
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);

        $this->objReporte = new RCertificacionPresupuestariaMod($this->objParam);
        $this->objReporte->setDatos($this->dataSource);
        $this->objReporte->generarReporte();
        $this->objReporte->output($this->objReporte->url_archivo,'F');


        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado', 'Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
    }

    // (may) aprueba todos los registros para pasar al estado aprobado
    function pasarTodosAprobado(){
        //$this->objParam->addParametro('id_funcionario_usu',$_SESSION["ss_id_funcionario"]);
        $this->objFunc=$this->create('MODPresupuesto');
        $this->res=$this->objFunc->pasarTodosAprobado();
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    //(may) subir detalle de la formulacion presupuestaria
    function subirDetalleFormulacion(){
        //validar extension del archivo

        //$id_solicitud = $this->objParam->getParametro('id_solicitud');
        $id_responsable = $this->objParam->getParametro('id_funcionario');
        $observaciones = $this->objParam->getParametro('observaciones');
        $id_gestion = $this->objParam->getParametro('id_gestion');

        $codigoArchivo = $this->objParam->getParametro('codigo');

        $arregloFiles = $this->objParam->getArregloFiles();
        $ext = pathinfo($arregloFiles['archivo']['name']);
        $nombreArchivo = $ext['filename'];
        $extension = $ext['extension'];

        $error = 'no';
        $mensaje_completo = '';

        //validar errores unicos del archivo: existencia, copia y extension
        if(isset($arregloFiles['archivo']) && is_uploaded_file($arregloFiles['archivo']['tmp_name'])){

            //procesa Archivo
            $archivoExcel = new ExcelInput($arregloFiles['archivo']['tmp_name'], $codigoArchivo);
            $archivoExcel->recuperarColumnasExcel();
            //var_dump('llegactr', $archivoExcel->recuperarColumnasExcel());

            $arrayArchivo = $archivoExcel->leerColumnasArchivoExcel();
            // var_dump('llegactr',$arrayArchivo);exit;
            foreach ($arrayArchivo as $fila) {

                //if ($fila['centro_costo'] == '' || $fila['centro_costo'] == NULL || $fila['centro_costo'] == ' ' || $fila['centro_costo'] == '  ') {
                if ($fila['centro_costo'] !== '' &&  $fila['centro_costo'] !== ' ' && $fila['centro_costo'] !== '  ' && $fila['centro_costo'] !== null) {

                    //var_dump('llegactr2', $fila['centro_costo']);
                    $centro_costo = $fila['centro_costo'] ;

                    if ($fila['concepto_gasto'] == '' || $fila['concepto_gasto'] == NULL) {
                        $concepto_gasto = '';
                    }else{
                        $concepto_gasto = $fila['concepto_gasto'] ;
                    }

                    if ($fila['nro_contrato'] == '' || $fila['nro_contrato'] == NULL) {
                        $nro_contrato = '';
                    }else{
                        $nro_contrato = $fila['nro_contrato'] ;
                    }
                    if ($fila['proveedor'] == '' || $fila['proveedor'] == NULL) {
                        $proveedor = '';
                    }else{
                        $proveedor = $fila['proveedor'] ;
                    }
                    if ($fila['hoja_respaldo'] == '' || $fila['hoja_respaldo'] == NULL) {
                        $hoja_respaldo = '';
                    }else{
                        $hoja_respaldo = $fila['hoja_respaldo'] ;
                    }
                    if ($fila['periodo_enero'] == '' || $fila['periodo_enero'] == NULL) {
                        $periodo_enero = 0;
                    }else{
                        $periodo_enero = $fila['periodo_enero'] ;
                    }
                    if ($fila['periodo_febrero'] == '' || $fila['periodo_febrero'] == NULL) {
                        $periodo_febrero = 0;
                    }else{
                        $periodo_febrero = $fila['periodo_febrero'] ;
                    }
                    if ($fila['periodo_marzo'] == '' || $fila['periodo_marzo'] == NULL) {
                        $periodo_marzo = 0;
                    }else{
                        $periodo_marzo = $fila['periodo_marzo'] ;
                    }
                    if ($fila['periodo_abril'] == '' || $fila['periodo_abril'] == NULL) {
                        $periodo_abril = 0;
                    }else{
                        $periodo_abril = $fila['periodo_abril'] ;
                    }
                    if ($fila['periodo_mayo'] == '' || $fila['periodo_mayo'] == NULL) {
                        $periodo_mayo = 0;
                    }else{
                        $periodo_mayo = $fila['periodo_mayo'] ;
                    }
                    if ($fila['periodo_junio'] == '' || $fila['periodo_junio'] == NULL) {
                        $periodo_junio = 0;
                    }else{
                        $periodo_junio = $fila['periodo_junio'] ;
                    }
                    if ($fila['periodo_julio'] == '' || $fila['periodo_julio'] == NULL) {
                        $periodo_julio = 0;
                    }else{
                        $periodo_julio = $fila['periodo_julio'] ;
                    }
                    if ($fila['periodo_agosto'] == '' || $fila['periodo_agosto'] == NULL) {
                        $periodo_agosto = 0;
                    }else{
                        $periodo_agosto = $fila['periodo_agosto'] ;
                    }
                    if ($fila['periodo_septiembre'] == '' || $fila['periodo_septiembre'] == NULL) {
                        $periodo_septiembre = 0;
                    }else{
                        $periodo_septiembre = $fila['periodo_septiembre'] ;
                    }
                    if ($fila['periodo_octubre'] == '' || $fila['periodo_octubre'] == NULL) {
                        $periodo_octubre = 0;
                    }else{
                        $periodo_octubre = $fila['periodo_octubre'] ;
                    }
                    if ($fila['periodo_noviembre'] == '' || $fila['periodo_noviembre'] == NULL) {
                        $periodo_noviembre = 0;
                    }else{
                        $periodo_noviembre = $fila['periodo_noviembre'] ;
                    }
                    if ($fila['periodo_diciembre'] == '' || $fila['periodo_diciembre'] == NULL) {
                        $periodo_diciembre = 0;
                    }else{
                        $periodo_diciembre = $fila['periodo_diciembre'] ;
                    }




                            $this->objParam->addParametro('id_responsable', $id_responsable);
                            $this->objParam->addParametro('observaciones', $observaciones);
                            $this->objParam->addParametro('id_gestion', $id_gestion);

                            $this->objParam->addParametro('centro_costo', $centro_costo);
                            $this->objParam->addParametro('concepto_gasto', html_entity_decode(preg_replace('/_x([0-9a-fA-F]{4})_/','&#x$1;', $concepto_gasto)));
                            //$this->objParam->addParametro('concepto_gasto', $concepto_gasto);
                            $this->objParam->addParametro('partida', ' ');
                            $this->objParam->addParametro('justificacion', $fila['justificacion']);
                            $this->objParam->addParametro('nro_contrato', $nro_contrato );
                            $this->objParam->addParametro('proveedor', $proveedor);
                            $this->objParam->addParametro('hoja_respaldo', $hoja_respaldo);
                            $this->objParam->addParametro('periodo_enero', $periodo_enero);
                            $this->objParam->addParametro('periodo_febrero', $periodo_febrero);
                            $this->objParam->addParametro('periodo_marzo', $periodo_marzo);
                            $this->objParam->addParametro('periodo_abril', $periodo_abril);
                            $this->objParam->addParametro('periodo_mayo', $periodo_mayo);
                            $this->objParam->addParametro('periodo_junio', $periodo_junio);
                            $this->objParam->addParametro('periodo_julio', $periodo_julio);
                            $this->objParam->addParametro('periodo_agosto', $periodo_agosto);
                            $this->objParam->addParametro('periodo_septiembre', $periodo_septiembre);
                            $this->objParam->addParametro('periodo_octubre', $periodo_octubre);
                            $this->objParam->addParametro('periodo_noviembre', $periodo_noviembre);
                            $this->objParam->addParametro('periodo_diciembre', $periodo_diciembre);
                            $this->objParam->addParametro('importe_total', $periodo_enero+$periodo_febrero+$periodo_marzo+$periodo_abril+$periodo_mayo+$periodo_junio+$periodo_julio+$periodo_agosto+
                                $periodo_septiembre+$periodo_octubre+$periodo_noviembre+$periodo_diciembre);

                    //var_dump('llegaarray', $this->objParam);



                        $this->objFunc = $this->create('sis_presupuestos/MODPresupuesto');

                        $this->res = $this->objFunc->insertarDetalleFormulacion($this->objParam);
                }
                        if($this->res->getTipo()=='ERROR'){
                            //(may)para que las filas parametrizadas del excel no cuenten como un registro porque da null y error en esa fila

                            $error = 'error';
                            $mensaje_completo = $this->res->getMensajeTec();
                        }

            }

            $file_path = $arregloFiles['archivo']['name'];

        } else {
            $mensaje_completo = "No se subio ningun archivo seleccionado";
            $error = 'error_fatal';
        }


        //armar respuesta en error fatal
        if ($error == 'error_fatal') {

            $this->mensajeRes=new Mensaje();
            $this->mensajeRes->setMensaje('ERROR','ACTPresupuesto.php',$mensaje_completo,
                $mensaje_completo,'control');
            //si no es error fatal proceso el archivo
        }
        //15-07-2020 (may) modificacion no mostraba el incidente desde la bd en producccion
        if ($error == 'error') {

            $this->mensajeRes=new Mensaje();

            $this->mensajeRes->setMensaje('ERROR','ACTPresupuesto.php','Ocurrieron los siguientes incidentes: ' . $mensaje_completo,
                $mensaje_completo,'control');

        }else if ($error == 'no') {
            $this->mensajeRes=new Mensaje();
            $this->mensajeRes->setMensaje('EXITO','ACTPresupuesto.php','El archivo fue ejecutado con éxito',
                'El archivo fue ejecutado con éxito','control');
        }

        //devolver respuesta
        $this->mensajeRes->imprimirRespuesta($this->mensajeRes->generarJson());
        //return $this->respuesta;
    }

    function eliminarDetalleFormulacion(){
        $this->objFunc=$this->create('MODPresupuesto');
        $this->res=$this->objFunc->eliminarDetalleFormulacion();
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function listarFormulacionPresu(){
        $this->objParam->defecto('ordenacion','id_formulacion_presu');
        $this->objParam->defecto('dir_ordenacion','asc');
       // $this->objParam->addParametro('id_funcionario_usu',$_SESSION["ss_id_funcionario"]);

        if($this->objParam->getParametro('id_gestion')!=''){
            $this->objParam->addFiltro("fp.id_gestion = ".$this->objParam->getParametro('id_gestion'));
        }


        if ($this->objParam->getParametro('tipoReporte') == 'excel_grid' || $this->objParam->getParametro('tipoReporte') == 'pdf_grid') {
            $this->objReporte = new Reporte($this->objParam, $this);
            $this->res = $this->objReporte->generarReporteListado('MODPresupuesto', 'listarFormulacionPresu');
        } else {
            $this->objFunc = $this->create('MODPresupuesto');

            $this->res = $this->objFunc->listarFormulacionPresu($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    function listarFormulacionPresuDet(){
        $this->objParam->defecto('ordenacion','id_formulacion_presu');
        $this->objParam->defecto('dir_ordenacion','asc');

        if($this->objParam->getParametro('id_formulacion_presu')!=''){
            $this->objParam->addFiltro("fpd.id_formulacion_presu = ".$this->objParam->getParametro('id_formulacion_presu'));
        }


        if ($this->objParam->getParametro('tipoReporte') == 'excel_grid' || $this->objParam->getParametro('tipoReporte') == 'pdf_grid') {
            $this->objReporte = new Reporte($this->objParam, $this);
            $this->res = $this->objReporte->generarReporteListado('MODPresupuesto', 'listarFormulacionPresuDet');
        } else {
            $this->objFunc = $this->create('MODPresupuesto');

            $this->res = $this->objFunc->listarFormulacionPresuDet($this->objParam);
        }

        $temp = Array();
        $temp['hoja_respaldo'] ='TOTAL';
        $temp['importe_total'] = $this->res->extraData['importe_total'];

        $temp['tipo_reg'] = 'summary';
        $temp['id_centro_costo'] = 0;

        $this->res->total++;
        $this->res->addLastRecDatos($temp);


        $this->res->imprimirRespuesta($this->res->generarJson());
    }

		/*****
		date: 03/09/2020
		dev: breydi vasquez
		description: Reporte Forulacion presupuestaria
		****/
		function reportePDFPresupuestaria () {
			$this->objParam->defecto('ordenacion','id_formulacion_presu');
			$this->objParam->defecto('dir_ordenacion','asc');
			$this->objParam->defecto('cantidad', 1000000);
			$this->objParam->defecto('puntero', 0);

			$this->objParam->addFiltro("fpd.id_formulacion_presu = ".$this->objParam->getParametro('id_formulacion_presu'));

			$this->objFunc=$this->create('MODPresupuesto');
			$this->res=$this->objFunc->listarFormulacionPresuDet($this->objParam);

			//obtener titulo del reporte
			$titulo = 'FormulacionPresupuestaria';
			//Genera el nombre del archivo (aleatorio + titulo)
			$nombreArchivo=uniqid(md5(session_id()).$titulo);


			$nombreArchivo.='.pdf';
			$this->objParam->addParametro('orientacion','L');
			$this->objParam->addParametro('tamano','LETTER	');
			$this->objParam->addParametro('nombre_archivo',$nombreArchivo);
			//Instancia la clase de pdf
			$this->objReporteFormato=new RFormPresupPDF($this->objParam);
			$this->objReporteFormato->setDatos($this->res->datos);
			$this->objReporteFormato->generarReporte();
			$this->objReporteFormato->output($this->objReporteFormato->url_archivo,'F');


			$this->mensajeExito=new Mensaje();
			$this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado',
					'Se generó con éxito el reporte: '.$nombreArchivo,'control');
			$this->mensajeExito->setArchivoGenerado($nombreArchivo);
			$this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

		}

    //(MAY)16/11/2020 Reporte Informacion Presupuestaria
    function reporteInformacionP (){
        $this->objFunc=$this->create('MODPresupuesto');
        $dataSource=$this->objFunc->reporteInformacionP();
        $this->dataSource=$dataSource->getDatos();

        $nombreArchivo = uniqid(md5(session_id()).'[Reporte-InformaciónPresupuestaria]').'.pdf';
        $this->objParam->addParametro('orientacion','P');
        $this->objParam->addParametro('tamano','LETTER');
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);

        $this->objReporte = new RInformacionPresupuestaria($this->objParam);
        $this->objReporte->setDatos($this->dataSource);
        $this->objReporte->generarReporte();
        $this->objReporte->output($this->objReporte->url_archivo,'F');


        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado', 'Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
    }


}

?>
