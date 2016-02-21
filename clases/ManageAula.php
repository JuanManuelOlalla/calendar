<?php
class ManageAula {
    
    private $bd = null;
    private $tabla = "aula";
    
    function __construct(DataBase $bd) {
        $this->bd = $bd;
    }

    function get($tutor){
        $parametros = array();
        $parametros["tutor"] = $tutor;
        $this->bd->select($this->tabla, "*", "tutor = :tutor", $parametros);
        $fila = $this->bd->getRow();
        $aula = new Aula();
        $aula->set($fila);
        return $aula;
    }
    
    function insert(Aula $aula){
        $parametros = $aula->getArray();
        return $this->bd->insert($this->tabla, $parametros, false);
    }
    
    function delete($numero){
        //devolver filas borradas
        $parametros = array();
        $parametros["numero"] = $numero;
        return $this->bd->delete($this->tabla, $parametros);
    }
    
    function erase(Aula $aula){
        return $this->delete($aula->getNumero());
    }
    
    function set(Aula $aula, $pkNumero){
        $parametros["numero"] = $aula->getNumero();
        $parametros["tutor"] = $aula->getTutor();
        
        $parametrosWhere = array();
        $parametrosWhere["numero"]=$pkNumero;
        return $this->bd->update2($this->tabla, $parametros, $parametrosWhere);
    }
    
    function getList($condicion = "1=1") {
        $this->bd->select($this->tabla, "*", $condicion, array(), "numero, tutor");
        $r = array();
        while($fila = $this->bd->getRow()){
            $aula = new Aula();
            $aula->set($fila);
            $r[] = $aula;
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
        $this->bd->query($this->tabla, "numero, tutor", array(), "numero");
        $array = array();
        while ($fila = $this->bd->getRow()) {
            $array[$fila[0]] = $fila[0];
        }
        return $array;
    }
}
