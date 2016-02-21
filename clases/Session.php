<?php

class Session {

    private static $iniciada = false;

    function __construct($nombre = null) {
        if (!self::$iniciada){
            if($nombre!=null){
                session_name($nombre);
            }
            session_start();
            $this->_control();
        }
        self::$iniciada = true;
    }

    private function _control() {
        $ip = $this->get('_ip');
        $cliente = $this->get('_cliente');
        if ($ip == null && $cliente == null) {
            $this->set('_ip', Server::getClientAddress());
            $this->set('_cliente', Server::getUserAgent());
        } else {
            if ($ip !== Server::getClientAddress() || $cliente !== Server::getUserAgent()) {
                $this->destroy();
            }
        }
    }
    
    function isAdministrador(){
        return $this->getProfesor()->getAdministrador() == 1;
    }
    
    function isLogged(){
        return $this->getProfesor()!=null;
    }

    function get($nombre) {
        if (isset($_SESSION[$nombre])) {
            return $_SESSION[$nombre];
        }
        return null;
    }
    
    function getProfesor(){
        return $this->get("_profesor");
    }

    function set($nombre, $valor) {
        $_SESSION[$nombre] = $valor;
    }

    function setProfesor(Profesor $profesor){
        $this->set("_profesor", $profesor);
    }
    
    function delete($nombre) {
        if (isset($_SESSION[$nombre])) {
            unset($_SESSION[$nombre]);
        }
    }

    function destroy() {
        session_destroy();
    }
    
    function login(Profesor $profesor){
        $this->setProfesor($profesor);
        return $profesor->getAdministrador();
    }

    //El segundo parametro nos manda al sitio y hacemos un exit
    function sendRedirect($destino = "index.php", $final = true){
        header("Location: $destino");
        if($final ===true){
            exit();
        }
    }
    
}
