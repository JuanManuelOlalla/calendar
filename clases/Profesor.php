<?php
class Profesor {
    
    private $login, $clave, $administrador;
    
    function __construct($login=null, $clave=null, $administrador=null) {
        $this->login = $login;
        $this->clave = $clave;
        $this->administrador = $administrador;
    }
    
    function getLogin() {
        return $this->login;
    }

    function getClave() {
        return $this->clave;
    }

    function getAdministrador() {
        return $this->administrador;
    }

    function setLogin($login) {
        $this->login = $login;
    }

    function setClave($clave) {
        $this->clave = $clave;
    }

    function setAdministrador($administrador) {
        $this->administrador = $administrador;
    }
    
    function getJson(){
        $cadena = '{';
        foreach ($this as $indice => $valor) {
            $cadena .= '"'.$indice.'":"'.$valor.'",';
        }
        $cadena = substr($cadena, 0, -1);
        $cadena .= '}';
        return $cadena;
    }
    
    function set($valores, $inicio = 0){ //Generico total
        $i = 0;
        foreach ($this as $indice => $valor) {
            $this->$indice = $valores[$i+$inicio];
            $i++;
        }
    }
    
    public function __toString(){
        $r = "";
        foreach ($this as $key => $valor) {
            $r .= "$valor ";
        }
        return $r;
    }
    
    function getArray($valores=true){
        $array = array();
        foreach ($this as $key => $valor) {
            if($valores === true){
                $array[$key] =  $valor;
            }else{
                $array[$key] =  null;
            }
        }
        return $array;
    }
    
    function read(){ //añadimos a cada propiedad de la clase ($this->$key) su valor si lo pasamos con el mismo nombre
        foreach ($this as $key => $valor) {
            $this->$key = Request::req($key);
        }
    }
}
