<?php
/**
*@package pXP
*@file gen-MODPartidaEjecucion.php
*@author  (gvelasquez)
*@date 03-10-2016 15:47:23
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODPartidaEjecucion extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarPartidaEjecucion(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='pre.ft_partida_ejecucion_sel';
		$this->transaccion='PRE_PAREJE_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_partida_ejecucion','int4');
		$this->captura('id_int_comprobante','int4');
		$this->captura('id_moneda','int4');
        $this->captura('moneda','varchar');
		$this->captura('id_presupuesto','int4');
        $this->captura('desc_pres','varchar');
        $this->captura('codigo_cc','text');

        $this->captura('codigo_categoria','varchar');
		$this->captura('id_partida','int4');
        $this->captura('codigo','varchar');
        $this->captura('nombre_partida','varchar');
		$this->captura('nro_tramite','varchar');
		$this->captura('tipo_cambio','numeric');
		$this->captura('columna_origen','varchar');
		$this->captura('tipo_movimiento','varchar');
		$this->captura('id_partida_ejecucion_fk','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('fecha','date');
		$this->captura('monto_mb','numeric');
		$this->captura('monto','numeric');
		$this->captura('valor_id_origen','int4');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_ai','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('id_usuario_mod','int4');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		

		//Ejecuta la instruccion
        $this->armarConsulta();
        //echo($this->consulta);exit;
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}


   function listarTramitesAjustables(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='pre.ft_partida_ejecucion_sel';
		$this->transaccion='PRE_LISTRAPE_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		
		$this->setParametro('fecha_ajuste','fecha_ajuste','date');
		$this->captura('id_gestion','int4');
        $this->captura('nro_tramite','varchar');
        $this->captura('codigo','varchar');
		$this->captura('id_moneda','int4');
		$this->captura('desc_moneda','varchar');
		
		
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function insertarPartidaEjecucion(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='pre.ft_partida_ejecucion_ime';
		$this->transaccion='PRE_PAREJE_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_int_comprobante','id_int_comprobante','int4');
		$this->setParametro('id_moneda','id_moneda','int4');
		$this->setParametro('id_presupuesto','id_presupuesto','int4');
		$this->setParametro('id_partida','id_partida','int4');
		$this->setParametro('nro_tramite','nro_tramite','varchar');
		$this->setParametro('tipo_cambio','tipo_cambio','numeric');
		$this->setParametro('columna_origen','columna_origen','varchar');
		$this->setParametro('tipo_movimiento','tipo_movimiento','varchar');
		$this->setParametro('id_partida_ejecucion_fk','id_partida_ejecucion_fk','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('fecha','fecha','date');
		$this->setParametro('monto_mb','monto_mb','numeric');
		$this->setParametro('monto','monto','numeric');
		$this->setParametro('valor_id_origen','valor_id_origen','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarPartidaEjecucion(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='pre.ft_partida_ejecucion_ime';
		$this->transaccion='PRE_PAREJE_MOD';
		$this->tipo_procedimiento='IME';
                        
		//Define los parametros para la funcion
		$this->setParametro('id_partida_ejecucion','id_partida_ejecucion','int4');
		$this->setParametro('id_int_comprobante','id_int_comprobante','int4');
		$this->setParametro('id_moneda','id_moneda','int4');
		$this->setParametro('id_presupuesto','id_presupuesto','int4');
		$this->setParametro('id_partida','id_partida','int4');
		$this->setParametro('nro_tramite','nro_tramite','varchar');
		$this->setParametro('tipo_cambio','tipo_cambio','numeric');
		$this->setParametro('columna_origen','columna_origen','varchar');
		$this->setParametro('tipo_movimiento','tipo_movimiento','varchar');
		$this->setParametro('id_partida_ejecucion_fk','id_partida_ejecucion_fk','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('fecha','fecha','date');
		$this->setParametro('monto_mb','monto_mb','numeric');
		$this->setParametro('monto','monto','numeric');
		$this->setParametro('valor_id_origen','valor_id_origen','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarPartidaEjecucion(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='pre.ft_partida_ejecucion_ime';
		$this->transaccion='PRE_PAREJE_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_partida_ejecucion','id_partida_ejecucion','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
    }

    function listarDetallePartidaEjecucion(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='pre.ft_partida_ejecucion_sel';
		$this->transaccion='PRE_DETPAREJE_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        //$this->setCount(false);
                
        $this->setParametro('desde', 'desde', 'date');
        $this->setParametro('hasta', 'hasta', 'date');
        //Definicion de la lista del resultado del query
        $this->captura('moneda','varchar');
        $this->captura('desc_pres','varchar');
        $this->captura('codigo_cc','text');        
        $this->captura('codigo_categoria','varchar');
        $this->captura('nro_tramite','varchar');
        $this->captura('nombre_partida','varchar');
        $this->captura('codigo','varchar');
        $this->captura('id_presupuesto','int4');
        $this->captura('id_partida','int4');
        $this->captura('id_moneda','int4');
        $this->captura('comprometido','numeric');
		$this->captura('comprometido_mb','numeric');
        $this->captura('ejecutado','numeric');
		$this->captura('ejecutado_mb','numeric');
        $this->captura('pagado','numeric');      
		$this->captura('pagado_mb','numeric');  
        $this->captura('saldo','numeric');
		$this->captura('saldo_mb','numeric');
        $this->captura('desde', 'date');
        $this->captura('hasta', 'date');
        //$this->captura('id_proceso_wf', 'integer');
		

		//Ejecuta la instruccion
        $this->armarConsulta();
        //echo($this->consulta);exit;
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
    }
    function totalPartidaEjecucion(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='pre.ft_partida_ejecucion_sel';
		$this->transaccion='PRE_TOPAREJE_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->setCount(false);
				
        //Definicion de la lista del resultado del query
        $this->captura('comprometido','numeric');
        $this->captura('ejecutado','numeric');
        $this->captura('pagado','numeric');
		$this->captura('devengar','numeric');

		//Ejecuta la instruccion
        $this->armarConsulta();
        //echo($this->consulta);exit;
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
    }
    function listarDetalleTramite(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='pre.ft_partida_ejecucion_sel';
		$this->transaccion='TES_DENTRAM_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        //$this->setCount(false);
                
        $this->setParametro('estado_func', 'estado_func', 'varchar');

        $this->capturaCount('total_comprometido','numeric');
        $this->capturaCount('total_ejecutado','numeric');
        $this->capturaCount('total_pagado','numeric');
        //Definicion de la lista del resultado del query
        $this->captura('id_partida_ejecucion', 'int4');
        $this->captura('id_partida_ejecucion_fk', 'int4');
        $this->captura('moneda', 'varchar');
        $this->captura('comprometido', 'numeric');
        $this->captura('ejecutado', 'numeric');
        $this->captura('pagado', 'numeric');        
        $this->captura('nro_tramite', 'varchar');
        $this->captura('tipo_movimiento', 'varchar');
        $this->captura('nombre_partida', 'varchar');
        $this->captura('codigo', 'varchar');
        $this->captura('codigo_categoria', 'varchar');
        $this->captura('fecha', 'date');
        $this->captura('codigo_cc', 'varchar');
        $this->captura('usr_reg', 'varchar');
        $this->captura('usr_mod', 'varchar');
        $this->captura('fecha_reg', 'timestamp');
        $this->captura('fecha_mod', 'timestamp');    
        $this->captura('estado_reg', 'varchar');

		//Ejecuta la instruccion
        $this->armarConsulta();
        //echo($this->consulta);exit;
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
    }        			

    function getProcesoWf(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='pre.ft_partida_ejecucion_sel';
		$this->transaccion='TES_GETPRWF_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->setCount(false);
        
        $this->setParametro('nro_tramite', 'nro_tramite', 'varchar');

        $this->captura('id_proceso_wf', 'int4');

		//Ejecuta la instruccion
        $this->armarConsulta();
        //echo($this->consulta);exit;
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;        
    }

    function listarDetallePresParDif(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='pre.ft_partida_ejecucion_sel';
		$this->transaccion='PRE_DIFPAREJ_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
        
        $this->capturaCount('total_monto','numeric');
        $this->capturaCount('total_mb','numeric');        
        $this->capturaCount('total_camo','numeric');
        $this->capturaCount('total_dif','numeric');
		//Definicion de la lista del resultado del query
		$this->captura('id_partida_ejecucion','int4');				        
        $this->captura('nro_tramite','varchar');
        $this->captura('tipo_movimiento','varchar');
        $this->captura('monto','numeric');
        $this->captura('monto_mb','numeric');                
        $this->captura('tipo_cambio','numeric');				
        $this->captura('tipo_cambio2','numeric');
        $this->captura('cambio_moneda','numeric');
        $this->captura('diferencia','numeric');        
		$this->captura('fecha_reg','timestamp');
        $this->captura('usr_reg','varchar');		
        $this->captura('moneda','varchar');		
        $this->captura('desc_pres','varchar');
        $this->captura('codigo_cc','text');
        $this->captura('codigo_categoria','varchar');		
        $this->captura('codigo','varchar');
        $this->captura('nombre_partida','varchar');
        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo($this->consulta);exit;
        $this->ejecutarConsulta();
        
        //Devuelve la respuesta
        return $this->respuesta;        
    }
    function totalDetallePresupuesto(){

		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='pre.ft_partida_ejecucion_sel';
		$this->transaccion='PRE_TODETPRE_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->setCount(false);
        
        $this->setParametro('id_presupuesto', 'id_presupuesto', 'int4');
        $this->setParametro('id_partida', 'id_partida', 'int4');
        $this->setParametro('id_gestion', 'id_gestion', 'int4');
        //Definicion de la lista del resultado del query
        $this->captura('comprometido','numeric');
        $this->captura('ejecutado','numeric');
        $this->captura('pagado','numeric');		

		//Ejecuta la instruccion
        $this->armarConsulta();
        //echo($this->consulta);exit;
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
    }
}
?>