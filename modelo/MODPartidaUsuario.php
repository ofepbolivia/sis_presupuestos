<?php
/**
*@package pXP
*@file gen-MODPartidaUsuario.php
*@author  (admin)
*@date 24-07-2018 20:34:48
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODPartidaUsuario extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarPartidaUsuario(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='pre.ft_partida_usuario_sel';
		$this->transaccion='PRE_PARUSU_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_partida_usuario','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('fecha_inicio_partida_usuario','date');
		$this->captura('fecha_fin_partida_usuario','date');
		$this->captura('estado_partida_usuario','varchar');
		$this->captura('observaciones','varchar');
		$this->captura('id_partida','int4');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_ai','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');

		$this->captura('codigo','varchar');
		$this->captura('nombre_partida','varchar');
        $this->captura('id_funcionario_resp','int4');
        $this->captura('desc_funcionario','text');
        $this->captura('id_gestion','int4');
        $this->captura('gestion','int4');


        //Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function insertarPartidaUsuario(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='pre.ft_partida_usuario_ime';
		$this->transaccion='PRE_PARUSU_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('fecha_inicio_partida_usuario','fecha_inicio_partida_usuario','date');
		$this->setParametro('fecha_fin_partida_usuario','fecha_fin_partida_usuario','date');
		$this->setParametro('estado_partida_usuario','estado_partida_usuario','varchar');
		$this->setParametro('observaciones','observaciones','varchar');
		$this->setParametro('id_partida','id_partida','int4');

        $this->setParametro('codigo','codigo','varchar');
        $this->setParametro('nombre_partida','nombre_partida','varchar');
        $this->setParametro('id_funcionario_resp','id_funcionario_resp','int4');
        $this->setParametro('desc_funcionario','desc_funcionario','text');
        $this->setParametro('id_gestion','id_gestion','int4');
        $this->setParametro('gestion','gestion','int4');


		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarPartidaUsuario(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='pre.ft_partida_usuario_ime';
		$this->transaccion='PRE_PARUSU_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_partida_usuario','id_partida_usuario','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('fecha_inicio_partida_usuario','fecha_inicio_partida_usuario','date');
		$this->setParametro('fecha_fin_partida_usuario','fecha_fin_partida_usuario','date');
		$this->setParametro('estado_partida_usuario','estado_partida_usuario','varchar');
		$this->setParametro('observaciones','observaciones','varchar');
		$this->setParametro('id_partida','id_partida','int4');

        $this->setParametro('codigo','codigo','varchar');
        $this->setParametro('nombre_partida','nombre_partida','varchar');
        $this->setParametro('id_funcionario_resp','id_funcionario_resp','int4');
        $this->setParametro('desc_funcionario','desc_funcionario','text');
        $this->setParametro('id_gestion','id_gestion','int4');
        $this->setParametro('gestion','gestion','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarPartidaUsuario(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='pre.ft_partida_usuario_ime';
		$this->transaccion='PRE_PARUSU_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_partida_usuario','id_partida_usuario','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>