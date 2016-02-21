<?php
class ManageProfesor {
    
    private $bd = null;
    private $tabla = "profesor";
    
    function __construct(DataBase $bd) {
        $this->bd = $bd;
    }

    function get($login){
        $parametros = array();
        $parametros["login"] = $login;
        $this->bd->select($this->tabla, "*", "login = :login", $parametros);
        $fila = $this->bd->getRow();
        $profesor = new Profesor();
        $profesor->set($fila);
        return $profesor;
    }
    
    function delete($login){
        //devolver filas borradas
        $parametros = array();
        $parametros["login"] = $login;
        return $this->bd->delete($this->tabla, $parametros);
    }
    
    function erase(Profesor $profesor){
        return $this->delete($user->getLogin());
    }
    
    function set(Profesor $profesor, $pkLogin){
        $parametros["login"] = $profesor->getEmail();
        $parametros["clave"] = $user->getClave();
        $parametros["administrador"] = $user->getAdministrador();
        
        $parametrosWhere = array();
        $parametrosWhere["login"]=$pkLogin;
        return $this->bd->update2($this->tabla, $parametros, $parametrosWhere);
    }
    
    function setSin(Profesor $profesor, $pkLogin){
        $parametros["login"] = $profesor->getEmail();
        $parametros["administrador"] = $user->getAdministrador();
        
        $parametrosWhere = array();
        $parametrosWhere["login"]=$pkLogin;
        return $this->bd->update2($this->tabla, $parametros, $parametrosWhere);
    }
    
    function insert(Profesor $profesor){
        $parametros = $profesor->getArray();
        $parametros['clave'] = sha1($parametros['clave']);
        return $this->bd->insert($this->tabla, $parametros, false);
    }
    
    function login($login, $clave) {
        $sql = "select clave from profesor where login=:login;";
        $parametros["login"] = $login;
        $this->bd->send($sql, $parametros);
        $claveEncontrada = $this->bd->getRow()[0];
        if ($claveEncontrada == sha1($clave)) {
            return $this->get($login);
        }
        return false;
    }
    
    function getList($condicion = "1=1") {
        $this->bd->select($this->tabla, "*", $condicion, array(), "login, administrador");
        $r = array();
        while($fila = $this->bd->getRow()){
            $profesor = new Profesor();
            $profesor->set($fila);
            $r[] = $profesor;
        }
        return $r;
    }
    
    function getListJson($condicion ="1=1"){
        $lista = $this->getList($condicion);
        $r = "[ ";
        foreach ($lista as $objeto){
            $r .= $objeto->getJson() . ",";
        }
        $r = substr($r, 0, -1) . "]";
        return $r;
    }
    
    function getValuesSelect() {
        $this->bd->query($this->tabla, "login, administrador", array(), "login");
        $array = array();
        while ($fila = $this->bd->getRow()) {
            $array[$fila[0]] = $fila[0];
        }
        return $array;
    }
    
}
