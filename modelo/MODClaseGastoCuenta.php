<?php
/**
 *@package pXP
 *@file gen-MODClaseGastoCuenta.php
 *@author  Maylee Perez Pastpr
 *@date 22-08-2019 02:33:23
 *@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 */

class MODClaseGastoCuenta extends MODbase{

    function __construct(CTParametro $pParam){
        parent::__construct($pParam);
    }

    function listarClaseGastoCuenta(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='pre.ft_clase_gasto_cuenta_sel';
        $this->transaccion='PRE_CGCU_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        //Definicion de la lista del resultado del query
        $this->captura('id_clase_gasto_cuenta','int4');
        $this->captura('id_cuenta','int4');
        $this->captura('estado_reg','varchar');
        $this->captura('id_clase_gasto','int4');
        $this->captura('id_usuario_ai','int4');
        $this->captura('usuario_ai','varchar');
        $this->captura('fecha_reg','timestamp');
        $this->captura('id_usuario_reg','int4');
        $this->captura('id_usuario_mod','int4');
        $this->captura('fecha_mod','timestamp');
        $this->captura('usr_reg','varchar');
        $this->captura('usr_mod','varchar');
        $this->captura('desc_cuenta','varchar');
        $this->captura('id_gestion','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function insertarClaseGastoCuenta(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='pre.ft_clase_gasto_cuenta_ime';
        $this->transaccion='PRE_CGCU_INS';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_cuenta','id_cuenta','int4');
        $this->setParametro('estado_reg','estado_reg','varchar');
        $this->setParametro('id_clase_gasto','id_clase_gasto','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function modificarClaseGastoCuenta(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='pre.ft_clase_gasto_cuenta_ime';
        $this->transaccion='PRE_CGCU_MOD';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_clase_gasto_cuenta','id_clase_gasto_cuenta','int4');
        $this->setParametro('id_cuenta','id_cuenta','int4');
        $this->setParametro('estado_reg','estado_reg','varchar');
        $this->setParametro('id_clase_gasto','id_clase_gasto','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function eliminarClaseGastoCuenta(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='pre.ft_clase_gasto_cuenta_ime';
        $this->transaccion='PRE_CGCU_ELI';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_clase_gasto_cuenta','id_clase_gasto_cuenta','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    /**{developer:franklin.espinoza, date:22/12/2020, description: Clonar Cuentas Contables}**/
    function clonarCuenta(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='pre.ft_clase_gasto_cuenta_ime';
        $this->transaccion='PRE_CLOCUE_IME';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_gestion','id_gestion','int4');
        $this->setParametro('id_clase_gasto','id_clase_gasto','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    /**{developer:franklin.espinoza, date:22/12/2020, description: Clonar Cuentas Contables}**/

}
?>