<?php
/**
*@package pXP
*@file gen-ACTMatrizConversionDeuda.php
*@author  (ismael.valdivia)
*@date 30-11-2021 18:07:32
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTMatrizConversionDeuda extends ACTbase{

	function listarMatrizConversionDeuda(){
		$this->objParam->defecto('ordenacion','id_matriz_conversion');

		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODMatrizConversionDeuda','listarMatrizConversionDeuda');
		} else{
			$this->objFunc=$this->create('MODMatrizConversionDeuda');

			$this->res=$this->objFunc->listarMatrizConversionDeuda($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function insertarMatrizConversionDeuda(){
		$this->objFunc=$this->create('MODMatrizConversionDeuda');
		if($this->objParam->insertar('id_matriz_conversion')){
			$this->res=$this->objFunc->insertarMatrizConversionDeuda($this->objParam);
		} else{
			$this->res=$this->objFunc->modificarMatrizConversionDeuda($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function eliminarMatrizConversionDeuda(){
			$this->objFunc=$this->create('MODMatrizConversionDeuda');
		$this->res=$this->objFunc->eliminarMatrizConversionDeuda($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}


	/*Listado de partida*/
	function listarPartidaOrigen(){

		if ($this->objParam->getParametro('id_gestion') != '') {
			$this->objParam->addParametro('id_gestion',$this->objParam->getParametro('id_gestion'));
		}

		if($this->objParam->getParametro('id_partida')!=''){
	    	$this->objParam->addFiltro("parti.id_partida = ".$this->objParam->getParametro('id_partida'));
		}

		$this->objFunc=$this->create('MODMatrizConversionDeuda');
		$this->res=$this->objFunc->listarPartidaOrigen($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function listarPartidaDestino(){

		if ($this->objParam->getParametro('id_gestion_destino') != '') {
			$this->objParam->addParametro('id_gestion_destino',$this->objParam->getParametro('id_gestion_destino'));
		}

		if($this->objParam->getParametro('id_partida')!=''){
	    	$this->objParam->addFiltro("parti.id_partida = ".$this->objParam->getParametro('id_partida'));
		}
		
		$this->objFunc=$this->create('MODMatrizConversionDeuda');
		$this->res=$this->objFunc->listarPartidaDestino($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}




}

?>
