<?php
/**
*@package pXP
*@file gen-ACTTechoPresupuestos.php
*@author  (admin)
*@date 09-07-2018 18:45:47
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTTechoPresupuestos extends ACTbase{    
			
	function listarTechoPresupuestos(){
		$this->objParam->defecto('ordenacion','id_techo_presupuesto');

		$this->objParam->defecto('dir_ordenacion','asc');

        if($this->objParam->getParametro('id_presupuesto')!=''){
            $this->objParam->addFiltro("tecpre.id_presupuesto = ".$this->objParam->getParametro('id_presupuesto'));
        }

		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODTechoPresupuestos','listarTechoPresupuestos');
		} else{
			$this->objFunc=$this->create('MODTechoPresupuestos');
			
			$this->res=$this->objFunc->listarTechoPresupuestos($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarTechoPresupuestos(){
		$this->objFunc=$this->create('MODTechoPresupuestos');	
		if($this->objParam->insertar('id_techo_presupuesto')){
			$this->res=$this->objFunc->insertarTechoPresupuestos($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarTechoPresupuestos($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarTechoPresupuestos(){
			$this->objFunc=$this->create('MODTechoPresupuestos');	
		$this->res=$this->objFunc->eliminarTechoPresupuestos($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>