<?php
/**
*@package pXP
*@file gen-MODPresupuestoObjetivo.php
*@author  (franklin.espinoza)
*@date 18-07-2017 15:29:59
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODPresupuestoObjetivo extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarPresupuestoObjetivo(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='pre.ft_presupuesto_objetivo_sel';
		$this->transaccion='PRE_PRE_OBJ_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->capturaCount('importe_total','numeric');
		$this->captura('id_presupuesto_objetivo','int4');
		$this->captura('id_presupuesto','int4');
		$this->captura('id_objetivo','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('id_usuario_ai','int4');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('usuario_ai','varchar');
		$this->captura('fecha_mod','timestamp');
		$this->captura('id_usuario_mod','int4');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		$this->captura('descripcion','varchar');
		$this->captura('importe_total','numeric');
		$this->captura('codigo','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}

	function listarObjetivosCombo(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='pre.ft_presupuesto_objetivo_sel';
		$this->transaccion='PRE_COMB_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		//Definicion de la lista del resultado del query
		$this->captura('id_presupuesto_objetivo','int4');
		$this->captura('id_presupuesto','int4');
		$this->captura('id_objetivo','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('id_usuario_ai','int4');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('usuario_ai','varchar');
		$this->captura('fecha_mod','timestamp');
		$this->captura('id_usuario_mod','int4');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		$this->captura('descripcion','varchar');
		$this->captura('importe_total','numeric');
		$this->captura('codigo','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function insertarPresupuestoObjetivo(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='pre.ft_presupuesto_objetivo_ime';
		$this->transaccion='PRE_PRE_OBJ_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_presupuesto','id_presupuesto','int4');
		$this->setParametro('id_objetivo','id_objetivo','varchar');
		$this->setParametro('estado_reg','estado_reg','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarPresupuestoObjetivo(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='pre.ft_presupuesto_objetivo_ime';
		$this->transaccion='PRE_PRE_OBJ_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_presupuesto_objetivo','id_presupuesto_objetivo','int4');
		$this->setParametro('id_presupuesto','id_presupuesto','int4');
		$this->setParametro('id_objetivo','id_objetivo','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarPresupuestoObjetivo(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='pre.ft_presupuesto_objetivo_ime';
		$this->transaccion='PRE_PRE_OBJ_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_presupuesto_objetivo','id_presupuesto_objetivo','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function listarObjetivo(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='pre.ft_objetivo_sel';
		$this->transaccion='PRE_OBJ_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		//Definicion de la lista del resultado del query

		$this->captura('id_objetivo','int4');
		$this->captura('id_objetivo_fk','int4');
		$this->captura('nivel_objetivo','int4');
		$this->captura('sw_transaccional','varchar');
		$this->captura('cantidad_verificacion','numeric');
		$this->captura('unidad_verificacion','varchar');
		$this->captura('ponderacion','numeric');
		$this->captura('fecha_inicio','date');
		$this->captura('tipo_objetivo','varchar');
		$this->captura('descripcion','varchar');
		$this->captura('linea_base','varchar');
		$this->captura('estado_reg','varchar');
		$this->captura('id_parametros','int4');
		$this->captura('indicador_logro','varchar');
		$this->captura('id_gestion','int4');
		$this->captura('codigo','varchar');
		$this->captura('periodo_ejecucion','varchar');
		$this->captura('producto','varchar');
		$this->captura('fecha_fin','date');
		$this->captura('fecha_reg','timestamp');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_reg','int4');
		$this->captura('id_usuario_ai','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('id_usuario_mod','int4');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		$this->captura('detalle_descripcion','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		//var_dump($this->consulta); exit;
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function verificarObjetivos(){
		$this->procedimiento='pre.ft_presupuesto_objetivo_ime';
		$this->transaccion='PRE_LIST_OBJ_VAL';
		$this->tipo_procedimiento='IME';

		$this->setParametro('id_objetivo','id_objetivo','varchar');

		$this->armarConsulta();
		$this->ejecutarConsulta();
		//var_dump($this->respuesta); exit;
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>