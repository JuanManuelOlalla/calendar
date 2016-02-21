<?php

class DataBase {

    private $conexion, $consulta;

    function __construct() {
        try {
            $this->conexion = new PDO(
                    'mysql:host=' . Constant::SERVER . ';' .
                    'dbname=' . Constant::DATABASE, Constant::DBUSER, Constant::DBPASSWORD, array(
                PDO::ATTR_PERSISTENT => true,
                PDO::MYSQL_ATTR_INIT_COMMAND => 'set names utf8')
            );
        } catch (PDOException $e) {
            var_dump($e);
            exit();
            //Esta parte hay que completarla
        }
    }

    function close() {
        $this->conexion = null;
    }

    function getCount() {
        return $this->consulta->rowCount();
    }

    function getId() {
        return $this->conexion->lastInsertId();
    }

    function getErrorConection() {
        return $this->conexion->errorInfo();
    }

    function getError() {
        return $this->consulta->errorInfo();
    }

    function send($sql, $parametros = array()) {
        $this->consulta = $this->conexion->prepare($sql);
        foreach ($parametros as $nombreParametro => $valorParametro) {
            $this->consulta->bindValue($nombreParametro, $valorParametro);
        }
      //  echo "parametros:";
       // var_dump($parametros);
      //  echo "sql:" .$sql;
        return $this->consulta->execute();
    }

    function getRow() {
        $r = $this->consulta->fetch();
        if ($r === null) {
            $this->consulta->closeCursor();
        }
        return $r;
    }

    function count($tabla, $condicion="1 = 1", $parametros= array()){
        $sql = "select count(*) from $tabla where $condicion";
        $this->send($sql, $parametros);
        $fila = $this->getRow();
        return $fila[0];
    }
    
    function erase($tabla, $condicion, $parametros = array()) {
        //delete from TABLA where CONDICION
        $sql = "delete from $tabla where $condicion";
        if ($this->send($sql, $parametros)) {
            return $this->getCount();
        }
        return false;
    }

     function delete($tabla, $parametros = array()) {
        //delete from TABLA where CONDICION
        $camposWhere = "";
        foreach ($parametros as $nombreParametro => $valorParametro) {
            $camposWhere .= $nombreParametro . " = :" . $nombreParametro . " and ";
        }
        //$camposWhere .= "1=1";
        $camposWhere = substr($camposWhere, 0, -4);
        $sql = "delete from $tabla where $camposWhere";
        if ($this->send($sql, $parametros)) {
            return $this->getCount();
        }
        return false;
    }
    
    function insert($tabla, $parametros = array(), $auto = true) {
        //insert into TABLA values (VALORES);
        //insert into TABLA (CAMPOS) values (VALORES);
        //insert into TABLA (c1, c2) values (:c1, :c2);
        //si la tabla tiene un campo autonumerico devuelvo el id
        //si no devuelvo el numero de filas insertadas(1)
        //si error devuelvo false
        $campos = "";
        $valores = "";
        foreach ($parametros as $nombreParametro => $valorParametro) {
            $campos .= $nombreParametro . ",";
            $valores .= ":" . $nombreParametro . ",";
        }
        $campos = substr($campos, 0, -1);
        $valores = substr($valores, 0, -1);
        $sql = "insert into $tabla ($campos) values ($valores)";
        if ($this->send($sql, $parametros)) {
            return $this->getId();
        }
        return false;
    }

    function update($tabla, $parametrosSet = array(), $parametrosWhere = array()) {
        //update TABLA set VALORES where CONDICION
        //update TABLA set c1=:c1, c2=:c2 where c1=:_c1 and c3=:_c3
        $camposSet = "";
        $camposWhere = "";
        $parametros = array();
        foreach ($parametrosSet as $nombreParametro => $valorParametro) {
            $camposSet .= $nombreParametro . " = :" . $nombreParametro . ",";
            $parametros[$nombreParametro] = $valorParametro;
        }
        $camposSet = substr($camposSet, 0, -1);

        foreach ($parametrosWhere as $nombreParametro => $valorParametro) {
            $camposWhere .= $nombreParametro . " = :_" . $nombreParametro . " and ";
            $parametros["_" . $nombreParametro] = $valorParametro;
        }
        //$camposWhere .= "1=1";
        $camposWhere = substr($camposWhere, 0, -4);
        $sql = "update $tabla set $camposSet where $camposWhere";
        if ($this->send($sql, $parametros)) {
            return $this->getCount();
        }
        return -1;
    }

    function query($tabla, $proyeccion = "*", $parametros = array(), $orden = "1", $limite = "") {
        //select CAMPOS from TABLA where CONDICION order by ORDEN LIMIT 
        //select c1,c2 from TABLA where c3=:c3 and c4 = :c4 order by c2 desc,c1 limit 8,15
        $campos = "";
        foreach ($parametros as $nombreParametro => $valorParametro) {
            $campos .= $nombreParametro . " = :" . $nombreParametro . " and ";         
        }
        $campos .= "1=1";
        //$campos = substr($campos, 0, -4);
        $limit = "";
        if($limite!==""){
            $limit = "limit $limite";
        }
        $sql = "select $proyeccion from $tabla where $campos order by $orden $limit";
        return $this->send($sql, $parametros);
    }
    
    function select($tabla, $proyeccion = "*", $condicion = "1 = 1", 
                    $parametros = array(), $orden="1", $limite=""){
          $limit = "";
        if($limite!==""){
            $limit = "limit $limite";
        }
        $sql = "select $proyeccion from $tabla "
                . "where $condicion order by $orden $limit";
        return $this->send($sql, $parametros);
    }

}
