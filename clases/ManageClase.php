<?php
class ManageClase {
    
    private $bd = null;
    private $tabla = "clase";
    
    function __construct(DataBase $bd) {
        $this->bd = $bd;
    }

    function get($id){
        $parametros = array();
        $parametros["id"] = $id;
        $this->bd->select($this->tabla, "*", "id = :id", $parametros);
        $fila = $this->bd->getRow();
        $clase = new Clase();
        $clase->set($fila);
        return $clase;
    }
    
    function insert(Clase $clase){
        $parametros = $clase->getArray();
        return $this->bd->insert($this->tabla, $parametros, false);
    }
    
    function delete($id){
        //devolver filas borradas
        $parametros = array();
        $parametros["id"] = $id;
        return $this->bd->delete($this->tabla, $parametros);
    }
    
    function erase(Clase $clase){
        return $this->delete($clase->getId());
    }
    
    function set(Clase $clase, $pkId){
        $parametros["asignatura"] = $clase->getAsignatura();
        $parametros["dia"] = $clase->getDia();
        $parametros["inicio"] = $clase->getInicio();
        $parametros["profesor"] = $clase->getProfesor();
        $parametros["aula"] = $clase->getAula();
        $parametros["fin"] = $clase->getFin();
        $parametros["id"] = $clase->getId();
        
        $parametrosWhere = array();
        $parametrosWhere["id"]=$pkId;
        return $this->bd->update2($this->tabla, $parametros, $parametrosWhere);
    }
    
    function getList($condicion = "1=1") {
        $this->bd->select($this->tabla, "*", $condicion, array(), "dia, inicio");
        $r = array();
        while($fila = $this->bd->getRow()){
            $clase = new Clase();
            $clase->set($fila);
            $r[] = $clase;
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
}
