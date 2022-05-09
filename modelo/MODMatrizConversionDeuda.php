<?php
/**
*@package pXP
*@file gen-MODMatrizConversionDeuda.php
*@author  (ismael.valdivia)
*@date 30-11-2021 18:07:32
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODMatrizConversionDeuda extends MODbase{

	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}

	function listarMatrizConversionDeuda(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='pre.ft_matriz_conversion_deuda_sel';
		$this->transaccion='PRE_macon_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		//Definicion de la lista del resultado del query
		$this->captura('id_matriz_conversion','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('id_gestion_origen','int4');
		$this->captura('id_partida_origen','int4');
		$this->captura('id_partida_destino','int4');
		$this->captura('id_gestion_destino','int4');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_ai','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');

		$this->captura('gestion_destino','varchar');
		$this->captura('gestion_origen','varchar');
		$this->captura('desc_partida_origen','varchar');
		$this->captura('desc_partida_destino','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function insertarMatrizConversionDeuda(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='pre.ft_matriz_conversion_deuda_ime';
		$this->transaccion='PRE_macon_INS';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('id_gestion_origen','id_gestion_origen','int4');
		$this->setParametro('id_partida_origen','id_partida_origen','int4');
		$this->setParametro('id_partida_destino','id_partida_destino','int4');
		$this->setParametro('id_gestion_destino','id_gestion_destino','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function modificarMatrizConversionDeuda(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='pre.ft_matriz_conversion_deuda_ime';
		$this->transaccion='PRE_macon_MOD';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_matriz_conversion','id_matriz_conversion','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('id_gestion_origen','id_gestion_origen','int4');
		$this->setParametro('id_partida_origen','id_partida_origen','int4');
		$this->setParametro('id_partida_destino','id_partida_destino','int4');
		$this->setParametro('id_gestion_destino','id_gestion_destino','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function eliminarMatrizConversionDeuda(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='pre.ft_matriz_conversion_deuda_ime';
		$this->transaccion='PRE_macon_ELI';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_matriz_conversion','id_matriz_conversion','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function listarPartidaOrigen(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='pre.ft_matriz_conversion_deuda_sel';
		$this->transaccion='LIST_PARTIDAS_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		$this->setParametro('id_gestion','id_gestion','int4');
		//Definicion de la lista del resultado del query
		$this->captura('codigo','varchar');
		$this->captura('nombre_partida','varchar');
		$this->captura('desc_partida','varchar');
		$this->captura('id_partida','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function listarPartidaDestino(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='pre.ft_matriz_conversion_deuda_sel';
		$this->transaccion='LIST_PARTID_DEST_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		$this->setParametro('id_gestion_destino','id_gestion_destino','int4');
		//Definicion de la lista del resultado del query
		$this->captura('codigo','varchar');
		$this->captura('nombre_partida','varchar');
		$this->captura('desc_partida_destino','varchar');
		$this->captura('id_partida','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

}
?>
