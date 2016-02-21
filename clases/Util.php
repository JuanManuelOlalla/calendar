<?php

class Util {
    
    static function getSelect($name, $parametros, $valorSeleccionado=null, $blanco=false, $atributos="", $id=null ){
        //<select name='x' id='y' z >
       // var_dump($parametros);
        if($id!==null){
            $id="id = '$id'";
        }else{
            $id="id = '$name'";
        }
        $r = "<select name='$name' $id $atributos >\n";
        if($blanco===true){
            $r .= "<option value=''>&nbsp;</option>\n";
        }
        foreach ($parametros as $indice => $valor) {
            $selected = "";
            if($valorSeleccionado !== null && $indice===$valorSeleccionado){
                $selected = "selected";
            }
            $r .= "<option $selected value='$indice'>$valor</option>\n";
        }
        $r .= "</select>\n";
        return $r;
    }
    
}
