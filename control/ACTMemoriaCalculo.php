<?php
/**
*@package pXP
*@file gen-ACTMemoriaCalculo.php
*@author  (admin)
*@date 01-03-2016 14:22:24
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/
require_once(dirname(__FILE__).'/../../pxp/pxpReport/DataSource.php');
require_once(dirname(__FILE__).'/../reportes/RMemoriaCalculo.php');
require_once(dirname(__FILE__).'/../reportes/RMemoriaCalculoXls.php');
require_once(dirname(__FILE__).'/../reportes/RMemoriaProgramacion.php');
require_once(dirname(__FILE__).'/../reportes/RMemoriaProgramacionXls.php');
require_once(dirname(__FILE__).'/../reportes/RMemoriaCalculaWf.php');
require_once(dirname(__FILE__).'/../reportes/RMemoriaProgramacionWf.php');
require_once(dirname(__FILE__).'/../reportes/RMemCalMensualPDF.php');
class ACTMemoriaCalculo extends ACTbase{

	function listarMemoriaCalculo(){
		$this->objParam->defecto('ordenacion','id_memoria_calculo');

		$this->objParam->defecto('dir_ordenacion','asc');

		if($this->objParam->getParametro('id_presupuesto')!=''){
            $this->objParam->addFiltro("mca.id_presupuesto = ".$this->objParam->getParametro('id_presupuesto'));
        }

		if($this->objParam->getParametro('id_objetivo')!=''){
			$this->objParam->addFiltro("mca.id_objetivo = ".$this->objParam->getParametro('id_objetivo'));
		}

		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODMemoriaCalculo','listarMemoriaCalculo');
		} else{
			$this->objFunc=$this->create('MODMemoriaCalculo');

			$this->res=$this->objFunc->listarMemoriaCalculo($this->objParam);
		}
        $temp = Array();
        $temp['importe_total'] = $this->res->extraData['total_importe'];
        $temp['tipo_reg'] = 'summary';
        $temp['id_memoria_calculo'] = 0;
        $this->res->total++;
        $this->res->addLastRecDatos($temp);

		$this->res->imprimirRespuesta($this->res->generarJson());
	}


	function insertarMemoriaCalculo(){
		$this->objFunc=$this->create('MODMemoriaCalculo');
		if($this->objParam->insertar('id_memoria_calculo')){
			$this->res=$this->objFunc->insertarMemoriaCalculo($this->objParam);
		} else{
			$this->res=$this->objFunc->modificarMemoriaCalculo($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function eliminarMemoriaCalculo(){
		$this->objFunc=$this->create('MODMemoriaCalculo');
		$this->res=$this->objFunc->eliminarMemoriaCalculo($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}


	function recuperarMemoriaCalculo(){
		$this->objFunc = $this->create('MODMemoriaCalculo');
		$cbteHeader = $this->objFunc->listarRepMemoriaCalculo($this->objParam);
		if($cbteHeader->getTipo() == 'EXITO'){
			return $cbteHeader;
		}
        else{
		    $cbteHeader->imprimirRespuesta($cbteHeader->generarJson());
			exit;
		}

    }

	function recuperarDatosGestion(){
		$this->objFunc = $this->create('sis_parametros/MODGestion');
		$cbteHeader = $this->objFunc->obtenerGestionById($this->objParam);
		if($cbteHeader->getTipo() == 'EXITO'){
			return $cbteHeader;
		}
        else{
		    $cbteHeader->imprimirRespuesta($cbteHeader->generarJson());
			exit;
		}

    }

	function recuperarDatosEmpresa(){
		$this->objFunc = $this->create('sis_parametros/MODEmpresa');
		$cbteHeader = $this->objFunc->getEmpresa($this->objParam);
		if($cbteHeader->getTipo() == 'EXITO'){
			return $cbteHeader;
		}
        else{
		    $cbteHeader->imprimirRespuesta($cbteHeader->generarJson());
			exit;
		}
	}


	function reporteMemoriaCalculo(){

				if($this->objParam->getParametro('tipo_rep') == 'general') {

					if($this->objParam->getParametro('formato_reporte')=='pdf'){
						$nombreArchivo = uniqid(md5(session_id()).'Memoria') . '.pdf';
					}
					else{
						$nombreArchivo = uniqid(md5(session_id()).'Memoria') . '.xls';
					}

					$dataSource = $this->recuperarMemoriaCalculo();
					$dataGestion = $this->recuperarDatosGestion();
					$dataEmpresa = $this->recuperarDatosEmpresa();

					//parametros basicos
					$tamano = 'LETTER';
					$orientacion = 'L';
					$titulo = 'Consolidado';


					$this->objParam->addParametro('orientacion',$orientacion);
					$this->objParam->addParametro('tamano',$tamano);
					$this->objParam->addParametro('titulo_archivo',$titulo);
					$this->objParam->addParametro('nombre_archivo',$nombreArchivo);

					//Instancia la clase de pdf
					if($this->objParam->getParametro('formato_reporte')=='pdf'){
						$reporte = new RMemoriaCalculo($this->objParam);
						$reporte->datosHeader($dataSource->getDatos(),  $dataSource->extraData,$dataGestion->getDatos(),$dataEmpresa->getDatos());
						$reporte->generarReporte();
						$reporte->output($reporte->url_archivo,'F');
					}
					else{
						$reporte = new RMemoriaCalculoXls($this->objParam);
						$reporte->datosHeader($dataSource->getDatos(),  $dataSource->extraData,$dataGestion->getDatos(),$dataEmpresa->getDatos());
						//$reporte->imprimeCabecera();
						$reporte->generarReporte();
					}



					$this->mensajeExito=new Mensaje();
					$this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se generó con éxito el reporte: '.$nombreArchivo,'control');
					$this->mensajeExito->setArchivoGenerado($nombreArchivo);
					$this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

				} else {
					$this->reporteMemoriaCalculoMensual();
				}

	}

    function recuperarProgramacion(){
		$this->objFunc = $this->create('MODMemoriaCalculo');
		$cbteHeader = $this->objFunc->listarRepProgramacion($this->objParam);
		if($cbteHeader->getTipo() == 'EXITO'){
			return $cbteHeader;
		}
        else{
		    $cbteHeader->imprimirRespuesta($cbteHeader->generarJson());
			exit;
		}

    }

/*
dev: breydi vasquez
description: modificacion y desarrollo de formato de Data
para declaracion de presupeustos BOA, en SIGEP
date: 10/09/2020
*/
    function reporteProgramacion(){

			if ($this->objParam->getParametro('tipo_reporte') == 'formulacion_presu_txt'){


				$this->objFunSeguridad=$this->create('MODMemoriaCalculo');
				$this->res = $this->objFunSeguridad->listarRepProgramacion();

				if($this->res->getTipo()=='ERROR'){
					$this->res->imprimirRespuesta($this->res->generarJson());
					exit;
				}

				$tipo_pres = $this->objParam->getParametro('tipo_pres');

				$tipo_p = null;

				if($tipo_pres == "1"){
				    $tipo_p = 'recurso';
				}else if($tipo_pres == "2"){
						$tipo_p = 'gasto';
				}else {
					throw new \Exception("Favor seleccione un solo item. GASTO O RECURSO", 1);
				}


				$nombreArchivo = $this->crearArchivoExportacionPresupuesaria($this->res, $tipo_p);


			} else {


			    if($this->objParam->getParametro('formato_reporte')=='pdf'){
					$nombreArchivo = uniqid(md5(session_id()).'Programacion').'.pdf';
					}
					else{
						$nombreArchivo = uniqid(md5(session_id()).'Programacion').'.xls';
					}

					$dataSource = $this->recuperarProgramacion();
					$dataGestion = $this->recuperarDatosGestion();
					$dataEmpresa = $this->recuperarDatosEmpresa();


					//parametros basicos
					$tamano = 'LETTER';
					$orientacion = 'L';
					$titulo = 'Consolidado';


					$this->objParam->addParametro('orientacion',$orientacion);
					$this->objParam->addParametro('tamano',$tamano);
					$this->objParam->addParametro('titulo_archivo',$titulo);
					$this->objParam->addParametro('nombre_archivo',$nombreArchivo);

					//Instancia la clase de pdf
					if($this->objParam->getParametro('formato_reporte')=='pdf'){
					    $reporte = new RMemoriaProgramacion($this->objParam);
						$reporte->datosHeader($dataSource->getDatos(),  $dataSource->extraData,$dataGestion->getDatos(),$dataEmpresa->getDatos());
					    $reporte->generarReporte();
					    $reporte->output($reporte->url_archivo,'F');
					}
					else{
						$reporte = new RMemoriaProgramacionXls($this->objParam);
						$reporte->datosHeader($dataSource->getDatos(),  $dataSource->extraData,$dataGestion->getDatos(),$dataEmpresa->getDatos());
					    $reporte->generarReporte();
					}

				}

				$this->mensajeExito=new Mensaje();
				$this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se generó con éxito el reporte: '.$nombreArchivo,'control');
				$this->mensajeExito->setArchivoGenerado($nombreArchivo);
				$this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

	}

	function crearArchivoExportacionPresupuesaria($res, $tipo) {
		$response = array();
		$data = $res -> getDatos();
		$cont = 0;


		if (count($data) <= 0) {
			throw new \Exception("No se tiene registros para el reporte", 1);
		}
		
		$cantidad = array_chunk($data, 500);

		 if ($tipo == 'gasto'){

			 foreach ($cantidad as $value) {

				 $fileName = uniqid(md5(session_id()).'FormulacionPresupuestaria'.$cont).'.txt';
				 $file = fopen("../../../reportes_generados/$fileName", 'w');

					 foreach ($value as $row) {
						 fwrite ($file,
						 "578"."|"."1"."|".
						 $row['codigo_unidad_ejecutora']."|".
						 $row['codigo_programa']."|".
						 $row['codigo_actividad']."|".
						 $row['codigo_fuente_fin']."|".
						 "230"."|".
						 $this->separaXpunto($row['codigo_partida'])."|" .
						 $row['codigo_ent_trans']."|".
						 $row['total']."\r\n");
					 }
				 $cont++;
				 	array_push($response, $fileName);
			 }

		}else{

			foreach ($cantidad as $value) {
				$fileName = uniqid(md5(session_id()).'FormulacionPresupuestaria'.$cont).'.txt';
				$file = fopen("../../../reportes_generados/$fileName", 'w');
					foreach ($value as $row) {
						fwrite ($file,
						"578"."|".
						$row['codigo_fuente_fin']."|".
						"230"."|".
						$this->separaXpunto($row['codigo_partida'])."|" .
						$row['codigo_ent_trans']."|".
						$row['total']."\r\n");
					}
				$cont++;
				 array_push($response, $fileName);
			}

		}

		return $response;
	}
	function separaXpunto($str) {
		if (is_string($str)) {
			 $arr= str_split($str);
			$response = '';
			foreach ($arr as $val) {
			    $response = $response.''.$val.'.';
			}

			if (strlen($str) == 4) {
					$response = substr($response, 0, -1);
					return $response.'0';
			}else{
					return substr($response,0, -1);
			}

		}else {
			$response = 'Formato Partida Erronea';
		}

		return $response;
	}

/*fin breydi.vasquez*/

	function listarMemoriaCalculoXPartida(){

			$this->objParam->defecto('ordenacion','id_concepto_ingas');
			$this->objParam->defecto('dir_ordenacion','asc');


			if($this->objParam->getParametro('tipo')!=''){

				if($this->objParam->getParametro('tipo')=='Bien'){
					$this->objParam->addFiltro("conig.tipo =''Bien''");
				}
				if($this->objParam->getParametro('tipo')=='Servicio'){
					$this->objParam->addFiltro("conig.tipo =''Servicio''");
				}
			}

			if($this->objParam->getParametro('movimiento')!=''){
				if(  $this->objParam->getParametro('movimiento') == 'ingreso_egreso'){
					$this->objParam->addFiltro("conig.movimiento in (''ingreso'',''gasto'')");
				}
				else{
					$this->objParam->addFiltro("conig.movimiento =''".$this->objParam->getParametro('movimiento')."''");
				}

			}

			if($this->objParam->getParametro('id_gestion')!=''){
				$this->objParam->addFiltro("par.id_gestion =".$this->objParam->getParametro('id_gestion'));
			}

			if($this->objParam->getParametro('requiere_ot')!=''){
				$this->objParam->addFiltro("conig.requiere_ot =''".$this->objParam->getParametro('requiere_ot')."''");
			}

			if($this->objParam->getParametro('id_concepto_ingas')!=''){
				$this->objParam->addFiltro("conig.id_concepto_ingas =''".$this->objParam->getParametro('id_concepto_ingas')."''");
			}



			if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
				$this->objReporte = new Reporte($this->objParam,$this);
				$this->res = $this->objReporte->generarReporteListado('MODMemoriaCalculo','listarMemoriaCalculoXPartida');
			} else{
				$this->objFunc=$this->create('MODMemoriaCalculo');

				$this->res=$this->objFunc->listarMemoriaCalculoXPartida($this->objParam);
			}
			$this->res->imprimirRespuesta($this->res->generarJson());
	}


	function recuperarMemoriaCalculoWf(){
        $this->objFunc = $this->create('MODMemoriaCalculo');
        $cbteHeader = $this->objFunc->listarMemoriaCalculoWf($this->objParam);
        if($cbteHeader->getTipo() == 'EXITO'){
            return $cbteHeader;
        }
        else{
            $cbteHeader->imprimirRespuesta($cbteHeader->generarJson());
            exit;
        }

    }
    function reporteMemoriaCalculoWf(){
        $nombreArchivo = uniqid(md5(session_id()).'Memoria') . '.pdf';
        $dataSource = $this->recuperarMemoriaCalculoWf();
        $dataEmpresa = $this->recuperarDatosEmpresa();
        //parametros basicos
        $tamano = 'LETTER';
        $orientacion = 'L';
        $titulo = 'Consolidado';
        $this->objParam->addParametro('orientacion',$orientacion);
        $this->objParam->addParametro('tamano',$tamano);
        $this->objParam->addParametro('titulo_archivo',$titulo);
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);
        $reporte = new RMemoriaCalculaWf($this->objParam);
        $reporte->datosHeader($dataSource->getDatos(),  $dataSource->extraData,$dataEmpresa->getDatos());
        $reporte->generarReporte();
        $reporte->output($reporte->url_archivo,'F');
        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
    }
    function recuperarProgramacionWf(){
        $this->objFunc = $this->create('MODMemoriaCalculo');
        $cbteHeader = $this->objFunc->listarRepProgramacionWf($this->objParam);
        if($cbteHeader->getTipo() == 'EXITO'){
            return $cbteHeader;
        }
        else{
            $cbteHeader->imprimirRespuesta($cbteHeader->generarJson());
            exit;
        }

    }
    function reporteProgramacionWf(){

       $nombreArchivo = uniqid(md5(session_id()).'Programacion').'.pdf';
        $dataSource = $this->recuperarProgramacionWf();
        $dataEmpresa = $this->recuperarDatosEmpresa();
        //parametros basicos
        $tamano = 'LETTER';
        $orientacion = 'L';
        $titulo = 'Consolidado';
        $this->objParam->addParametro('orientacion',$orientacion);
        $this->objParam->addParametro('tamano',$tamano);
        $this->objParam->addParametro('titulo_archivo',$titulo);
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);

        $reporte = new RMemoriaProgramacionWf($this->objParam);
        $reporte->datosHeader($dataSource->getDatos(),  $dataSource->extraData,$dataEmpresa->getDatos());
        $reporte->generarReporte();
        $reporte->output($reporte->url_archivo,'F');
        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

    }

	function reporteMemoriaCalculoMensual(){

		if($this->objParam->getParametro('tipo_rep') == null){
			$this->objParam->addParametro('tipo_rep','');
		}

		$this->objFunc = $this->create('MODMemoriaCalculo');
		$cbteHeader = $this->objFunc->listarMemoriaCalculoMensual($this->objParam);

		$nombreArchivo = uniqid(md5(session_id()).'[MemoriaCalculo-Mensual]') . '.pdf';
		$dataSource = $cbteHeader;
		$dataEmpresa = $this->recuperarDatosEmpresa();
		//parametros basicos
		$tamano = 'A3';
		$orientacion = 'L';
		$titulo = 'Consolidado';
		$this->objParam->addParametro('orientacion',$orientacion);
		$this->objParam->addParametro('tamano',$tamano);
		$this->objParam->addParametro('titulo_archivo',$titulo);
		$this->objParam->addParametro('nombre_archivo',$nombreArchivo);
		$reporte = new RMemCalMensualPDF($this->objParam);
		$reporte->datosHeader($dataSource->getDatos(),  $dataSource->extraData,$dataEmpresa->getDatos());
		$reporte->generarReporte();
		$reporte->output($reporte->url_archivo,'F');
		$this->mensajeExito=new Mensaje();
		$this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se generó con éxito el reporte: '.$nombreArchivo,'control');
		$this->mensajeExito->setArchivoGenerado($nombreArchivo);
		$this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
	}
}

?>
