<?php

class Controlador {

    static function handle() {
        $action = Request::req("action");
        $target = Request::req("target");

        $metodo = $action . ucfirst($target);
        if (method_exists(get_class(), $metodo)) {
            self::$metodo();
        } else {
            self::viewIndex();
        }
    }

    /*     * ************************************ VIEWS ************************************* */

    private static function viewIndex() {
        $plantilla = new Plantilla('plantillas/index.html');
        $plantilla->insertPlantilla('plantillas/_login.html', "login");
        $plantilla->insertPlantilla('plantillas/_index.html', "index");
        $plantilla->insertPlantilla('plantillas/_tutoria.html', "tutoria");
        $plantilla->insertPlantilla('plantillas/_profesores.html', "profesores");
        $plantilla->insertPlantilla('plantillas/_aulas.html', "aulas");
        $plantilla->mostrar();
    }
    
    /*     * ************************************ LOGIN ************************************* */
    
    private static function loginUser() {
        header('Content-Type: application/json');
        $bd = new DataBase();
        $gestor = new ManageProfesor($bd);

        $login = Request::req("login");
        $clave = Request::req("clave");

        $profesor = $gestor->login($login, $clave);
        
        if ($profesor == false) {
            echo json_encode(array('login' => false));
        } else {
            $sesion = new Session();
            $admin = $sesion->login($profesor);
            
            echo json_encode(array('login' => true, 'admin' => $admin));
        }
        $bd->close();
    }
    
    private static function isLogueado(){
        header('Content-Type: application/json');
        $sesion = new Session();
        $logueado = $sesion->isLogged();
        if($logueado){
            $admin = $sesion->getProfesor()->getAdministrador();
            echo json_encode(array('login' => true, 'admin' => $admin));
        }else{
            echo json_encode(array('login' => false));
        }
    }
    
    private static function doLogout(){
        header('Content-Type: application/json');
        $sesion = new Session();
        $sesion->destroy();
        $ok = json_encode(array('login' => false));
        echo $ok;
    }
    
    
    /*     * ************************************ HORARIO ************************************* */
    
    private static function getNombre(){
        header('Content-Type: application/json');
        $sesion = new Session();
        $nombre = $sesion->getProfesor()->getLogin();
        echo json_encode(array('nombre' => $nombre));
    }
    
    private static function getHorario() {
        header('Content-Type: application/json');
        $sesion = new Session();
        if($sesion->isLogged()){
            $bd = new DataBase();
            $gestor = new ManageClase($bd);
            $profesor = $sesion->getProfesor()->getLogin();
            $clases = $gestor->getListJson("profesor='$profesor'");
            echo '{"clases":' .$clases. '}';
            $bd->close();
        }else{
            echo json_encode(array('clases' => false));
        }
    }
    
    private static function getDia($dia){
        switch ($dia) {
            case "Mon":
                return 1;
            case "Tue":
                return 2;
            case "Wed":
                return 3;
            case "Thu":
                return 4;
            case "Fri":
                return 5;
        }
    }
    
    private static function setClase() {
        header('Content-Type: application/json');
        $sesion = new Session();
        if($sesion->isLogged()){
            $bd = new DataBase();
            $gestor = new ManageClase($bd);
            
            $profesor = Request::req("profesor");
            if($profesor == null || $profesor == ""){
                $profesor = $sesion->getProfesor()->getLogin();
            }
            
            $diaN = substr(Request::req("inicio"), 0, 3);
            $dia = self::getDia($diaN);
            
            $pos = strpos(Request::req("inicio"), ":");
            $inicio = substr(Request::req("inicio"), $pos-2, 2);
            
            $posi = strpos(Request::req("fin"), ":");
            $fin = substr(Request::req("fin"), $posi-2, 2);
            
            $clase = new Clase(Request::req("clase"), $dia, $inicio, $profesor, Request::req("aula"), $fin);
            $r = $gestor->insert($clase);
            if($r){
                echo json_encode(array('ok' => true, 'id' => $r));
            }else{
                echo json_encode(array('ok' => false));
            }
            $bd->close();
        }else{
            echo json_encode(array('ok' => false));
        }
    }
    
    private static function deleteClase() {
        header('Content-Type: application/json');
        $sesion = new Session();
        if($sesion->isLogged()){
            $bd = new DataBase();
            $gestor = new ManageClase($bd);
            $r = $gestor->delete(Request::req("id"));
            if($r){
                echo json_encode(array('ok' => true));
            }else{
                echo json_encode(array('ok' => false));
            }
            $bd->close();
        }else{
            echo json_encode(array('ok' => false));
        }
    }
    
    
    /*     * ************************************ TUTORIA ************************************* */
    
    private static function getAula(){
        header('Content-Type: application/json');
        $sesion = new Session();
        $bd = new DataBase();
        $gestor = new ManageAula($bd);
        $tutor = $sesion->getProfesor()->getLogin();
        $aula = $gestor->get($tutor)->getNumero();
        echo json_encode(array('aula' => $aula));
    }
    
    private static function getTutoria() {
        header('Content-Type: application/json');
        $aula = Request::req("aula");
        $sesion = new Session();
        if($sesion->isLogged()){
            $bd = new DataBase();
            $gestor = new ManageClase($bd);
            $profesores = $gestor->getListJson("aula='$aula'");
            echo '{"profesores":' .$profesores. '}';
            $bd->close();
        }else{
            echo json_encode(array('clases' => false));
        }
    }
    
    private static function setClase2() {
        header('Content-Type: application/json');
        $sesion = new Session();
        if($sesion->isLogged()){
            $bd = new DataBase();
            $gestor = new ManageClase($bd);
            
            $diaN = substr(Request::req("inicio"), 0, 3);
            $dia = self::getDia($diaN);
            
            $pos = strpos(Request::req("inicio"), ":");
            $inicio = substr(Request::req("inicio"), $pos-2, 2);
            
            $posi = strpos(Request::req("fin"), ":");
            $fin = substr(Request::req("fin"), $posi-2, 2);
            
            $clase = new Clase(Request::req("clase"), $dia, $inicio, Request::req("profesor"), Request::req("aula"), $fin);
            $r = $gestor->insert($clase);
            if($r){
                echo json_encode(array('ok' => true, 'id' => $r));
            }else{
                echo json_encode(array('ok' => false));
            }
            $bd->close();
        }else{
            echo json_encode(array('ok' => false));
        }
    }
    
    
    /*     * ************************************ ADMIN ************************************* */
    
    
    private static function getProfesores() {
        header('Content-Type: application/json');
        $sesion = new Session();
        if($sesion->isAdministrador()){
            $bd = new DataBase();
            $gestor = new ManageProfesor($bd);
            $profesores = $gestor->getListJson(); //"administrador=0"
            echo '{"profesores":' .$profesores. '}';
            $bd->close();
        }else{
            echo json_encode(array('profesores' => false));
        }
    }
    
    private static function getHorarioadmin() {
        header('Content-Type: application/json');
        $sesion = new Session();
        if($sesion->isAdministrador()){
            $bd = new DataBase();
            $gestor = new ManageClase($bd);
            $profesor = Request::req("profesor");
            $clases = $gestor->getListJson("profesor='$profesor'");
            echo '{"clases":' .$clases. '}';
            $bd->close();
        }else{
            echo json_encode(array('clases' => false));
        }
    }
    
    private static function getAulas(){
        header('Content-Type: application/json');
        $sesion = new Session();
        if($sesion->isAdministrador()){
            $bd = new DataBase();
            $gestor = new ManageAula($bd);
            $aulas = $gestor->getListJson();
            echo '{"aulas":' .$aulas. '}';
            $bd->close();
        }else{
            echo json_encode(array('profesores' => false));
        }
    }
    
    private static function getTutoriaadmin() {
        header('Content-Type: application/json');
        $sesion = new Session();
        if($sesion->isAdministrador()){
            $bd = new DataBase();
            $gestor = new ManageClase($bd);
            $aula = Request::req("aula");
            $profesores = $gestor->getListJson("aula='$aula'");
            echo '{"profesores":' .$profesores. '}';
            $bd->close();
        }else{
            echo json_encode(array('profesores' => false));
        }
    }
    
    
    /*     * ************************************ SELECTS ************************************* */
    
    private static function getAulasselect() {
        header('Content-Type: application/json');
        $sesion = new Session();
        if($sesion->isLogged()){
            $bd = new DataBase();
            $gestor = new ManageAula($bd);
            $select = Util::getSelect("aula", $gestor->getValuesSelect(), 1);
            echo json_encode(array('select' => $select));
        }else{
            echo json_encode(array('select' => false));
        }
    }
    
    private static function getProfesoresselect() {
        header('Content-Type: application/json');
        $sesion = new Session();
        if($sesion->isLogged()){
            $bd = new DataBase();
            $gestor = new ManageProfesor($bd);
            $select = Util::getSelect("profesor", $gestor->getValuesSelect(), 1);
            echo json_encode(array('select' => $select));
        }else{
            echo json_encode(array('select' => false));
        }
    }
    
    
    /*     * ************************************ SELECTS ************************************* */

    
    private static function addProfesor() {
        header('Content-Type: application/json');
        $sesion = new Session();
        if($sesion->isAdministrador()){
            $bd = new DataBase();
            $gestor = new ManageProfesor($bd);
            $profe = new Profesor(Request::req("login"), Request::req("clave"), 0);
            $r = $gestor->insert($profe);
            echo json_encode(array('profe' => $r));
        }else{
            echo json_encode(array('profe' => false));
        }
    }
    
    private static function addAula() {
        header('Content-Type: application/json');
        $sesion = new Session();
        if($sesion->isAdministrador()){
            $bd = new DataBase();
            $gestor = new ManageAula($bd);
            $aula = new Aula(Request::req("numero"), Request::req("tutor"));
            $r = $gestor->insert($aula);
            echo json_encode(array('aula' => $r));
        }else{
            echo json_encode(array('aula' => false));
        }
    }
    
    
}
