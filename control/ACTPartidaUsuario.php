<?php
/**
*@package pXP
*@file gen-ACTPartidaUsuario.php
*@author  (admin)
*@date 24-07-2018 20:34:48
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTPartidaUsuario extends ACTbase{    
			
	function listarPartidaUsuario(){
		$this->objParam->defecto('ordenacion','id_partida_usuario');


        if($this->objParam->getParametro('id_gestion')!=''){
            $this->objParam->addFiltro("parusu.id_gestion = ".$this->objParam->getParametro('id_gestion'));
        }

		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODPartidaUsuario','listarPartidaUsuario');
		} else{
			$this->objFunc=$this->create('MODPartidaUsuario');
			
			$this->res=$this->objFunc->listarPartidaUsuario($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarPartidaUsuario(){
		$this->objFunc=$this->create('MODPartidaUsuario');	
		if($this->objParam->insertar('id_partida_usuario')){
			$this->res=$this->objFunc->insertarPartidaUsuario($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarPartidaUsuario($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarPartidaUsuario(){
			$this->objFunc=$this->create('MODPartidaUsuario');	
		$this->res=$this->objFunc->eliminarPartidaUsuario($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>