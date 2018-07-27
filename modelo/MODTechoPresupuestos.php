<?php
/**
*@package pXP
*@file gen-MODTechoPresupuestos.php
*@author  (admin)
*@date 09-07-2018 18:45:47
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODTechoPresupuestos extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarTechoPresupuestos(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='pre.ft_techo_presupuestos_sel';
		$this->transaccion='PRE_TECPRE_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_techo_presupuesto','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('importe_techo_presupuesto','numeric');
		$this->captura('observaciones','varchar');
		$this->captura('id_presupuesto','int4');
		$this->captura('estado_techo_presupuesto','varchar');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_ai','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function insertarTechoPresupuestos(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='pre.ft_techo_presupuestos_ime';
		$this->transaccion='PRE_TECPRE_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('importe_techo_presupuesto','importe_techo_presupuesto','numeric');
		$this->setParametro('observaciones','observaciones','varchar');
		$this->setParametro('id_presupuesto','id_presupuesto','int4');
		$this->setParametro('estado_techo_presupuesto','estado_techo_presupuesto','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarTechoPresupuestos(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='pre.ft_techo_presupuestos_ime';
		$this->transaccion='PRE_TECPRE_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_techo_presupuesto','id_techo_presupuesto','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('importe_techo_presupuesto','importe_techo_presupuesto','numeric');
		$this->setParametro('observaciones','observaciones','varchar');
		$this->setParametro('id_presupuesto','id_presupuesto','int4');
		$this->setParametro('estado_techo_presupuesto','estado_techo_presupuesto','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarTechoPresupuestos(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='pre.ft_techo_presupuestos_ime';
		$this->transaccion='PRE_TECPRE_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_techo_presupuesto','id_techo_presupuesto','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>