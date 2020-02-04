<?php
/**
*@package pXP
*@file gen-ACTPartidaEjecucion.php
*@author  (gvelasquez)
*@date 03-10-2016 15:47:23
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTPartidaEjecucion extends ACTbase{    
			
	function listarPartidaEjecucion(){
		$this->objParam->defecto('ordenacion','id_partida_ejecucion');
		$this->objParam->defecto('dir_ordenacion','asc');

        //mod-BVP 
        $this->filtros();

		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODPartidaEjecucion','listarPartidaEjecucion');
		} else{
			$this->objFunc=$this->create('MODPartidaEjecucion');
			
			$this->res=$this->objFunc->listarPartidaEjecucion($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
	
	function listarTramitesAjustables(){
		$this->objParam->defecto('ordenacion','nro_tramite');
		$this->objParam->defecto('dir_ordenacion','asc');
		
        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODPartidaEjecucion','listarTramitesAjustables');
		} else{
			$this->objFunc=$this->create('MODPartidaEjecucion');
			
			$this->res=$this->objFunc->listarTramitesAjustables($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
	
	
	
	
				
	function insertarPartidaEjecucion(){
		$this->objFunc=$this->create('MODPartidaEjecucion');	
		if($this->objParam->insertar('id_partida_ejecucion')){
			$this->res=$this->objFunc->insertarPartidaEjecucion($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarPartidaEjecucion($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarPartidaEjecucion(){
			$this->objFunc=$this->create('MODPartidaEjecucion');	
		$this->res=$this->objFunc->eliminarPartidaEjecucion($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
    }

        //INC-BVP 
	function listarDetallePartidaEjecucion(){
        
        $this->filtros();

		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODPartidaEjecucion','listarDetallePartidaEjecucion');
		} else{
			$this->objFunc=$this->create('MODPartidaEjecucion');
			
			$this->res=$this->objFunc->listarDetallePartidaEjecucion($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());        
    }

    function totalPartidaEjecucion(){
        //mod-BVP 
        $this->filtros();
		$this->objFunc=$this->create('MODPartidaEjecucion');			
		$this->res=$this->objFunc->totalPartidaEjecucion($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

    function filtros() {
        $this->objParam->getParametro('id_partida') !='' && $this->objParam->addFiltro("pareje.id_partida = ".$this->objParam->getParametro('id_partida'));
        
        $this->objParam->getParametro('id_centro_costo') !='' && $this->objParam->addFiltro("pareje.id_presupuesto = ".$this->objParam->getParametro('id_centro_costo'));        

        $this->objParam->getParametro('id_presupuesto') !='' && $this->objParam->addFiltro("pareje.id_presupuesto = ".$this->objParam->getParametro('id_presupuesto'));        
        
        $this->objParam->getParametro('id_categoria_programatica') !='' && $this->objParam->addFiltro("cat.id_categoria_programatica = ".$this->objParam->getParametro('id_categoria_programatica'));        
        
        $this->objParam->getParametro('nro_tramite') !='' && $this->objParam->addFiltro("pareje.nro_tramite ilike ''%".$this->objParam->getParametro('nro_tramite')."%''");        

        $this->objParam->getParametro('id_cp_actividad') !='' && $this->objParam->addFiltro("cat.id_cp_actividad = ".$this->objParam->getParametro('id_cp_actividad'));

        $this->objParam->getParametro('id_cp_fuente_fin') !='' && $this->objParam->addFiltro("cat.id_cp_fuente_fin = ".$this->objParam->getParametro('id_cp_fuente_fin'));

        $this->objParam->getParametro('id_cp_organismo_fin') != '' && $this->objParam->addFiltro("cat.id_cp_organismo_fin = ".$this->objParam->getParametro('id_cp_organismo_fin'));

        $this->objParam->getParametro('id_unidad_ejecutora') != '' && $this->objParam->addFiltro("cat.id_unidad_ejecutora = ".$this->objParam->getParametro('id_unidad_ejecutora')); 

        if($this->objParam->getParametro('tipo_movimiento') != ''){
            $this->objParam->getParametro('tipo_movimiento') != 'todos' && $this->objParam->addFiltro("pareje.tipo_movimiento = ''".$this->objParam->getParametro('tipo_movimiento')."''");            
        }
        if($this->objParam->getParametro('desde')!='' && $this->objParam->getParametro('hasta')!=''){
            $this->objParam->addFiltro("(pareje.fecha_reg::date  BETWEEN ''%".$this->objParam->getParametro('desde')."%''::date  and ''%".$this->objParam->getParametro('hasta')."%''::date)");
        }

        if($this->objParam->getParametro('desde')!='' && $this->objParam->getParametro('hasta')==''){
            $this->objParam->addFiltro("(pareje.fecha_reg::date  >= ''%".$this->objParam->getParametro('desde')."%''::date)");
        }

        if($this->objParam->getParametro('desde')=='' && $this->objParam->getParametro('hasta')!=''){
            $this->objParam->addFiltro("(pareje.fecha_reg::date  <= ''%".$this->objParam->getParametro('hasta')."%''::date)");
        }
    }

    function listarDetalleTramite() {
        $this->filtros();        
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODPartidaEjecucion','listarDetalleTramite');
		} else{            
			$this->objFunc=$this->create('MODPartidaEjecucion');            
            $this->res=$this->objFunc->listarDetalleTramite($this->objParam);                
            $temp = Array();
            $temp['total_comprometido'] = $this->res->extraData['total_comprometido'];
            $temp['total_ejecutado'] = $this->res->extraData['total_ejecutado'];
            $temp['total_pagado'] = $this->res->extraData['total_pagado'];
            $temp['tipo_reg'] = 'summary';                
            $this->res->total++;
            $this->res->addLastRecDatos($temp);            
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
    }

    function getProcesoWf(){
        $this->objFunc=$this->create('MODPartidaEjecucion');			
		$this->res=$this->objFunc->getProcesoWf($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
    }
    
    function listarDetallePresParDif(){
        $this->filtros2();

		if($this->objParam->getParametro('tipoReporte') =='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODPartidaEjecucion','listarDetallePresParDif');
		} else{            
			$this->objFunc=$this->create('MODPartidaEjecucion');            
            $this->res=$this->objFunc->listarDetallePresParDif($this->objParam);                
            $temp = Array();
            $temp['total_monto'] = $this->res->extraData['total_monto'];
            $temp['total_mb'] = $this->res->extraData['total_mb'];            
            $temp['total_camo'] = $this->res->extraData['total_camo'];
            $temp['total_dif'] = $this->res->extraData['total_dif'];
            $temp['tipo_reg'] = 'summary';                
            $this->res->total++;
            $this->res->addLastRecDatos($temp);            
		}        
        $this->res->imprimirRespuesta($this->res->generarJson());        
    }
    function totalDetallePresupuesto(){        
		$this->objFunc=$this->create('MODPartidaEjecucion');			
		$this->res=$this->objFunc->totalDetallePresupuesto($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
    }

    function filtros2(){
        if($this->objParam->getParametro('id_partida') !='' ){
            if($this->objParam->getParametro('id_partida') !=0){
                $this->objParam->addFiltro("pareje.id_partida = ".$this->objParam->getParametro('id_partida'));
            }
        }
        if($this->objParam->getParametro('id_presupuesto') !='' ){
            if($this->objParam->getParametro('id_presupuesto') !=0){
                $this->objParam->addFiltro("pareje.id_presupuesto = ".$this->objParam->getParametro('id_presupuesto'));
            }
        }                        

        if($this->objParam->getParametro('tipo_movimiento') != ''){
            $this->objParam->getParametro('tipo_movimiento') != 'todos' && $this->objParam->addFiltro("pareje.tipo_movimiento = ''".$this->objParam->getParametro('tipo_movimiento')."''");
        }
        if($this->objParam->getParametro('desde') !='' && $this->objParam->getParametro('hasta')!=''){
            $this->objParam->addFiltro("(pareje.fecha_reg::date  BETWEEN ''%".$this->objParam->getParametro('desde')."%''::date  and ''%".$this->objParam->getParametro('hasta')."%''::date)");
        }

        if($this->objParam->getParametro('desde') !='' && $this->objParam->getParametro('hasta')==''){
            $this->objParam->addFiltro("(pareje.fecha_reg::date  >= ''%".$this->objParam->getParametro('desde')."%''::date)");
        }

        if($this->objParam->getParametro('desde') =='' && $this->objParam->getParametro('hasta')!=''){
            $this->objParam->addFiltro("(pareje.fecha_reg::date  <= ''%".$this->objParam->getParametro('hasta')."%''::date)");
        }

        if($this->objParam->getParametro('moneda') != ''){
          $this->objParam->getParametro("moneda") != 'todos' && $this->objParam->addFiltro("pareje.id_moneda = ".$this->objParam->getParametro('moneda'));
        } 

        if($this->objParam->getParametro('diferencia') == 'si' ){        
            $this->objParam->addFiltro("
            ( round(((pareje.monto *  case when mo.id_moneda = 1 then 
            case when con.tipo_cambio = 1 then 
                            con.tipo_cambio
                        else 
                            con.tipo_cambio_2 end 
                    else 
                        case when con.id_moneda = 2 then
                                                                    
                            con.tipo_cambio
                        else 
                    con.tipo_cambio_2  end
                    end ) - pareje.monto_mb ),2) > 0.99
            or   round(((pareje.monto *  case when mo.id_moneda = 1 then 
                        case when con.tipo_cambio = 1 then 
                            con.tipo_cambio
                        else 
                            con.tipo_cambio_2 end 
                    else 
                        case when con.id_moneda = 2 then
                                                                    
                            con.tipo_cambio
                        else 
                    con.tipo_cambio_2  end
                    end ) - pareje.monto_mb ),2) < -0.99)             
            ");
        }
    }			
}

?>