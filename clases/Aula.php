<?php
class Aula {
    
    private $numero, $tutor;
    
    function __construct($numero=null, $tutor=null) {
        $this->numero = $numero;
        $this->tutor = $tutor;
    }

    function getNumero() {
        return $this->numero;
    }

    function getTutor() {
        return $this->tutor;
    }

    function setNumero($numero) {
        $this->numero = $numero;
    }

    function setTutor($tutor) {
        $this->tutor = $tutor;
    }
    
    function getJson(){
        $r = '{';
        foreach ($this as $indice => $valor) {
            $r .= '"' .$indice . '":' . json_encode($valor). ','; //Se codifican algunos caracteres
        }
        $r = substr($r, 0,-1);
        $r .='}';
        return $r;
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
