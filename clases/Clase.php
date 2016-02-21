<?php
class Clase {
    private $asignatura, $dia, $inicio, $profesor, $aula, $fin, $id;
    
    function __construct($asignatura=null, $dia=null, $inicio=null, $profesor=null, $aula=null, $fin=null, $id=null) {
        $this->asignatura = $asignatura;
        $this->dia = $dia;
        $this->inicio = $inicio;
        $this->profesor = $profesor;
        $this->aula = $aula;
        $this->fin = $fin;
        $this->id = $id;
    }
    
    function getAsignatura() {
        return $this->asignatura;
    }

    function getDia() {
        return $this->dia;
    }

    function getInicio() {
        return $this->inicio;
    }

    function getProfesor() {
        return $this->profesor;
    }

    function getAula() {
        return $this->aula;
    }
    
    function getFin() {
        return $this->fin;
    }
    
    function getId(){
        return $this->id;
    }

    function setAsignatura($asignatura) {
        $this->asignatura = $asignatura;
    }

    function setDia($dia) {
        $this->dia = $dia;
    }

    function setInicio($inicio) {
        $this->inicio = $inicio;
    }

    function setProfesor($profesor) {
        $this->profesor = $profesor;
    }

    function setAula($aula) {
        $this->aula = $aula;
    }

    function setFin($fin) {
        $this->fin = $fin;
    }
    
    function setId($id){
        $this->id = $id;
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
