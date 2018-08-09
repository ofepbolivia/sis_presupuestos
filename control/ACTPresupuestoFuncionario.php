<?php
/**
*@package pXP
*@file gen-ACTPresupuestoUsuario.php
*@author  (admin)
*@date 29-02-2016 03:25:38
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTPresupuestoFuncionario extends ACTbase{    
			
	function listarPresupuestoFuncionario(){
		$this->objParam->defecto('ordenacion','id_presupuesto_funcionario');

		$this->objParam->defecto('dir_ordenacion','asc');
		
		if($this->objParam->getParametro('id_presupuesto')!=''){
            $this->objParam->addFiltro("pf.id_presupuesto = ".$this->objParam->getParametro('id_presupuesto'));    
        }
		
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODPresupuestoFuncionario','listarPresupuestoFuncionario');
		} else{
			$this->objFunc=$this->create('MODPresupuestoFuncionario');
			
			$this->res=$this->objFunc->listarPresupuestoFuncionario($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function listarCentroCostoFuncionarios(){
		$this->objFunc=$this->create('MODPresupuestoFuncionario');
		$this->res=$this->objFunc->listarCentroCostoFuncionarios($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarPresupuestoFuncionario(){
		$this->objFunc=$this->create('MODPresupuestoFuncionario');	
		if($this->objParam->insertar('id_presupuesto_funcionario')){
			$this->res=$this->objFunc->insertarPresupuestoFuncionario($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarPresupuestoFuncionario($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarPresupuestoFuncionario(){
			$this->objFunc=$this->create('MODPresupuestoFuncionario');	
		$this->res=$this->objFunc->eliminarPresupuestoFuncionario($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
	
///BVP 
	function listarFuncionarioPresupuesto(){
		$this->objParam->defecto('ordenacion','id_presupuesto');

		$this->objParam->defecto('dir_ordenacion','asc');
		
		if($this->objParam->getParametro('id_funcionario')!=''){
            $this->objParam->addFiltro("f.id_funcionario = ".$this->objParam->getParametro('id_funcionario'));    
        }
        if($this->objParam->getParametro('id_gestion') != ''){
            $this->objParam->addFiltro("vcc.id_gestion = ".$this->objParam->getParametro('id_gestion')." ");

        }	

		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODPresupuestoFuncionario','listarFuncionarioPresupuesto');
		} else{
			$this->objFunc=$this->create('MODPresupuestoFuncionario');
			
			$this->res=$this->objFunc->listarFuncionarioPresupuesto($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
    function listarPresupuestoFun(){
        $this->objParam->defecto('ordenacion','id_presupuesto');
        $this->objParam->defecto('dir_ordenacion','asc');
		
		if($this->objParam->getParametro('id_funcionario')!=''){
			$this->objParam->addFiltro(" vp.id_gestion =".$this->objParam->getParametro('id_gestion')." and vp.codigo_cc is not null and 
			 vp.id_presupuesto not in 
									(select  prf.id_presupuesto
									from pre.tpresupuesto_funcionario prf
									inner join pre.vpresupuesto_cc vpe on vpe.id_presupuesto=prf.id_presupuesto
									where vpe.id_gestion= ".$this->objParam->getParametro('id_gestion')." 
									and prf.id_funcionario= ".$this->objParam->getParametro('id_funcionario').")");
		}
        $this->objFunc=$this->create('MODPresupuestoFuncionario');
        $this->res=$this->objFunc->listarPresupuestoFun($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
	function insertarPresupuestoFun(){
		$this->objFunc=$this->create('MODPresupuestoFuncionario');	
		if($this->objParam->insertar('id_presupuesto_funcionario')){
			$this->res=$this->objFunc->insertarPresupuestoFun($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarPresupuestoFun($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}	
			
}

?>