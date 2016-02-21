<?php
class Plantilla {
	
    private $plantilla, $args, $contenido;

    function __construct($plantilla, $args = null) {
        $this->plantilla = $plantilla;
        $this->args = $args;
        $this->contenido = file_get_contents($this->plantilla);
        if($args != null){
            foreach($this->args as $key =>$value){
                $this->contenido = str_replace("{".$key."}", $value, $this->contenido);
            }
        }
    }
    
    function getContenido() {
        return $this->contenido;
    }
    
    function insertPlantilla($ruta, $tag){
        $cont = file_get_contents($ruta);
        $this->contenido = str_replace("{".$tag."}", $cont, $this->contenido);
    }
    
    function insertTags($tags){
        foreach($tags as $key =>$value){
            $this->contenido = str_replace("{".$key."}", $value, $this->contenido);
        }
    }
    
    function insertTag($tag, $valor){
        $this->contenido = str_replace("{".$tag."}", $valor, $this->contenido);
    }
    
    function eraseTag($tag){
        $this->contenido = str_replace("{".$tag."}", "", $this->contenido);
    }
    
    function setPlantilla($plantilla) {
        $this->plantilla = $plantilla;
    }
    
    function alertsSiempre($mensaje, $tipo){
        $this->insertPlantilla('plantillas/_alerts.html', "alerts");
        $tags = array(
            "mensaje" => $mensaje,
            "tipo" => $tipo //success, info, danger, warning
        );
        $this->insertTags($tags);
    }
    function alertsSiempre2($mensaje, $tipo){
        $this->insertPlantilla('plantillas/_alerts2.html', "alerts2");
        $tags = array(
            "mensaje" => $mensaje,
            "tipo" => $tipo //success, info, danger, warning
        );
        $this->insertTags($tags);
    }
    
    function mostrar() {
        echo $this->contenido;
    }
    
    function insertTemaTags(User $user){
        $bd = new DataBase();
        $gestor = new ManageImagen($bd);
        $images = $gestor->getImagenes($user);
        
        $this->insertTag("alias", $user->getAlias());
        $this->insertTag("avatar", $user->getAvatar());
        $this->insertTag("descripcion", $user->getDescripcion());
        $this->insertTag("email", $user->getEmail());
        $this->insertTag("numeroFotos", count($images));
        
        $tema = $user->getPlantilla();
        if($tema == 1){
            $this->insertTemaImagenes1($images);
        }elseif($tema == 2){
            $this->insertTemaImagenes2($images);
        }else{
            $this->insertTemaImagenes3($images);
        }
    }
    
    function insertUserTags(User $user){
        $admin = "";
        if($user->getAdministrador()==1){
            $admin = 
                '<li class="sub-menu">
                    <a href="index.php?action=view&target=admin" >
                        <i class="fa fa-cog"></i>
                        <span>Administración</span>
                    </a>
                </li>';
        }
        
        $this->insertTag("admin", $admin);
        $this->insertTag("alias", $user->getAlias());
        $this->insertTag("avatar", $user->getAvatar());
        $this->insertTag("descripcion", $user->getDescripcion());
        $this->insertTag("email", $user->getEmail());
        
        $tema = $user->getPlantilla();
        $link = "<img class='img-responsive' src='./plantillas/gallery/temas/tema$tema.png'  align=''>";
        $this->insertTag("tema", $link);
        
        if($user->getPrivado()==1){
            $checked = "checked";
        }else{
            $checked = "";
        }
        $this->insertTag("checked", $checked);
    }
    
    function insertUserTagsAdmin(User $user, User $administrador){
        
        $admin = 
            '<li class="sub-menu">
                <a href="index.php?action=view&target=admin" >
                    <i class="fa fa-cog"></i>
                    <span>Administración</span>
                </a>
            </li>';
        $this->insertTag("admin", $admin);
        $this->insertTag("avatar", $administrador->getAvatar());
        $this->insertTag("alias", $administrador->getAlias());
        $tema = $administrador->getPlantilla();
        $link = "<img class='img-responsive' src='./plantillas/gallery/temas/tema$tema.png'  align=''>";
        $this->insertTag("tema", $link);
        
        $this->insertTag("avatar2", $user->getAvatar());
        $this->insertTag("alias2", $user->getAlias());
        $this->insertTag("descripcion", $user->getDescripcion());
        $this->insertTag("email", $user->getEmail());
        $this->insertTag("planti", $user->getPlantilla());
        $this->insertTag("fecha", $user->getFechalta());
        
        if($user->getPrivado()==1){
            $checked = "checked";
        }else{
            $checked = "";
        }
        $this->insertTag("checked", $checked);
        
        if($user->getAdministrador()==1){
            $checked2 = "checked";
        }else{
            $checked2 = "";
        }
        $this->insertTag("checked2", $checked2);
        
        switch ($user->getActivo()) {
            case -1:
                $this->insertTag("activo-1", "checked");
                break;
            case 0:
                $this->insertTag("activo0", "checked");
                break;
            case 1:
                $this->insertTag("activo1", "checked");
                break;
        }
        $this->insertTag("activo1", "");
        $this->insertTag("activo0", "");
        $this->insertTag("activo-1", "");
    }
    
    function getBotones(){
            $botones = 
                '<div>
                    <a href="?action=view&target=editadmin&user={email}">
                        <button type="button" class="btn btn-lg btn-warning"><i class="fa fa-eye"></i> Editar Usuario</button>
                    </a>
                </div>
                <br/>
                <div>
                    <a href="?action=view&target=user&user={email}">
                        <button type="button" class="btn btn-lg btn-primary"><i class="fa fa-eye"></i> Ver Galería</button>
                    </a>
                </div>';
            $this->insertTag("botones", $botones);
    }
    
    function insertImagenes($images, $email = null){
        $contenido = "";
        for ($i = 0; $i < count($images); $i++) {
            $imagen = $images[$i];
            $ruta = $imagen->getRuta();
            $nombre = $imagen->getNombre();
            if($email != null){
                $borrar = "index.php?action=delete&target=imagen&ruta=$ruta&nombre=$nombre&email=$email";
            }else{
                $borrar = "index.php?action=delete&target=imagen&ruta=$ruta&nombre=$nombre";
            }
             
            if($i+1==count($images) && $i%3==0 && $i!=1){
                $contenido .= "
                <div class='row mt'>    
                    <div class='col-lg-4 col-md-4 col-sm-4 col-xs-12 desc'>
                        <div class='project-wrapper'>
                            <div class='project'>
                                <div class='photo-wrapper'>
                                    <div class='photo'>
                                        <a class='fancybox' href='$borrar'><img class='img-responsive' src='$ruta' alt=''></a>
                                    </div>
                                    <div class='overlay'></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>";
            }elseif($i==0 || $i%3==0 && $i!=1){
                $contenido .= "
                <div class='row mt'>    
                    <div class='col-lg-4 col-md-4 col-sm-4 col-xs-12 desc'>
                        <div class='project-wrapper'>
                            <div class='project'>
                                <div class='photo-wrapper'>
                                    <div class='photo'>
                                        <a class='fancybox' href='$borrar'><img class='img-responsive' src='$ruta' alt=''></a>
                                    </div>
                                    <div class='overlay'></div>
                                </div>
                            </div>
                        </div>
                    </div>";
            }elseif((($i+1)%3==0 && $i!=1) || ($i+1==count($images))){
                $contenido .= "   
                    <div class='col-lg-4 col-md-4 col-sm-4 col-xs-12 desc'>
                        <div class='project-wrapper'>
                            <div class='project'>
                                <div class='photo-wrapper'>
                                    <div class='photo'>
                                        <a class='fancybox' href='$borrar'><img class='img-responsive' src='$ruta' alt=''></a>
                                    </div>
                                    <div class='overlay'></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>";
            }else{
               $contenido .= "
                    <div class='col-lg-4 col-md-4 col-sm-4 col-xs-12 desc'>
                        <div class='project-wrapper'>
                            <div class='project'>
                                <div class='photo-wrapper'>
                                    <div class='photo'>
                                        <a class='fancybox' href='$borrar'><img class='img-responsive' src='$ruta' alt=''></a>
                                    </div>
                                    <div class='overlay'></div>
                                </div>
                            </div>
                        </div>
                    </div>";
            }
        }
        $this->insertTag("imagenes", $contenido);
    }
    
    function insertTemas(User $user){
        $s1 = ""; $s2 = ""; $s3 = "";
        $s = $user->getPlantilla();
        if($s==1){
            $s1 = "seleccionada";
        }elseif($s==2){
            $s2 = "seleccionada";
        }else{
            $s3 = "seleccionada";
        }
        $contenido = "";
        $contenido .= "
            <div class='row mt'>    
                <div class='col-lg-4 col-md-4 col-sm-4 col-xs-12 desc'>
                    <div class='project-wrapper'>
                        <div class='project'>
                            <div class='tema-wrapper'>
                                <div class='photo'>
                                    <a class='fancybox' href='index.php?action=edit&target=tema&s=1'>"
                                        . "<img class='img-responsive $s1' src='plantillas/gallery/temas/tema1.png' alt=''>
                                    </a>
                                </div>
                                <div class='overlay'></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class='col-lg-4 col-md-4 col-sm-4 col-xs-12 desc'>
                    <div class='project-wrapper'>
                        <div class='project'>
                            <div class='tema-wrapper'>
                                <div class='photo'>
                                    <a class='fancybox' href='index.php?action=edit&target=tema&s=2'>"
                                        . "<img class='img-responsive $s2' src='plantillas/gallery/temas/tema2.png' alt=''>
                                    </a>
                                </div>
                                <div class='overlay'></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class='col-lg-4 col-md-4 col-sm-4 col-xs-12 desc'>
                    <div class='project-wrapper'>
                        <div class='project'>
                            <div class='tema-wrapper'>
                                <div class='photo'>
                                    <a class='fancybox' href='index.php?action=edit&target=tema&s=3'>"
                                        . "<img class='img-responsive $s3' src='plantillas/gallery/temas/tema3.png' alt=''>
                                    </a>
                                </div>
                                <div class='overlay'></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>";
        $this->insertTag("temas", $contenido);
    }
    
    function insertTemaImagenes($images){
        
        $contenido = "";
        for ($i = 0; $i < count($images); $i++) {
            $ruta = $images[$i]->getRuta();
            $contenido .= "
                        <div class='reference-item' data-category='webdesign'>
                            <div class='reference'>
                                <a href='#'>
                                    <img src='$ruta' class='img-responsive' alt='' />

                                    <div class='overlay'>
                                        <h3 class='reference-title'>Project name</h3> 
                                        <p>Short project description goes here...</p>
                                    </div>
                                </a>

                                <div class='sr-only reference-description' data-images='img/main-slider1.jpg,img/main-slider2.jpg,img/main-slider3.jpg'>

                                    <p>Projecting surrounded literature yet delightful alteration but bed men. Open are from long why cold. If must snug by upon sang loud left. As me do preference entreaties compliment motionless ye literature. Day behaviour
                                        explained law remainder. Produce can cousins account you pasture. Peculiar delicate an pleasant provided do perceive.</p>

                                    <p>Sitting mistake towards his few country ask. You delighted two rapturous six depending objection happiness something the. Off nay impossible dispatched partiality unaffected. Norland adapted put ham cordial. Ladies
                                        talked may shy basket narrow see. Him she distrusts questions sportsmen. Tolerably pretended neglected on my earnestly by. Sex scale sir style truth ought.</p>

                                    <p class='buttons'>
                                        <a class='btn btn-primary' href='javascript:void(0);'><i class='fa fa-globe'></i> Visit website</a>
                                        <a class='btn btn-primary' href='javascript:void(0);'><i class='fa fa-download'></i> Download case study</a>
                                    </p>
                                </div>
                            </div>
                        </div>";
        }
        $this->insertTag("imagenes", $contenido);
    }
    
    
    function insertTemaImagenes1($images){
        $contenido = "";
        for ($i = 0; $i < count($images); $i++) {
            $imagen = $images[$i];
            $ruta = $imagen->getRuta();
            $nombre = $imagen->getNombre();
             
            if($i+1==count($images) && $i%3==0 && $i!=1){
                $contenido .= "
                <div class='row mt'>    
                    <div class='col-lg-4 col-md-4 col-sm-4 col-xs-12 desc'>
                        <div class='project-wrapper'>
                            <div class='project'>
                                <div>
                                    <div class='overFlowMio'>
                                        <a href='$ruta' ><img class='img-responsive hoverMio' src='$ruta' alt=''></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>";
            }elseif($i==0 || $i%3==0 && $i!=1){
                $contenido .= "
                <div class='row mt'>    
                    <div class='col-lg-4 col-md-4 col-sm-4 col-xs-12 desc'>
                        <div class='project-wrapper'>
                            <div class='project'>
                                <div>
                                    <div class='overFlowMio'>
                                        <a href='$ruta'><img class='img-responsive hoverMio' src='$ruta' alt=''></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>";
            }elseif((($i+1)%3==0 && $i!=1) || ($i+1==count($images))){
                $contenido .= "   
                    <div class='col-lg-4 col-md-4 col-sm-4 col-xs-12 desc'>
                        <div class='project-wrapper'>
                            <div class='project'>
                                <div>
                                    <div class='overFlowMio'>
                                        <a href='$ruta'><img class='img-responsive hoverMio' src='$ruta' alt=''></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>";
            }else{
               $contenido .= "
                    <div class='col-lg-4 col-md-4 col-sm-4 col-xs-12 desc'>
                        <div class='project-wrapper'>
                            <div class='project'>
                                <div>
                                    <div class='overFlowMio'>
                                        <a href='$ruta' ><img class='img-responsive hoverMio' src='$ruta' alt=''></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>";
            }
        }
        $this->insertTag("imagenes", $contenido);
    }
    
    function insertTemaImagenes2($images){
        $contenido = "";
        for ($i = 0; $i < count($images); $i++) {
            $imagen = $images[$i];
            $ruta = $imagen->getRuta();
            $nombre = $imagen->getNombre();
             
            if($i+1==count($images) && $i%3==0 && $i!=1){
                $contenido .= "
                <div class='row'>
                    <div class='col-sm-4'>
                      <div class='box overFlowMio'><a href='$ruta' title='' data-toggle='lightbox' data-gallery='portfolio' data-title='Portfolio image 1' data-footer='Some footer information'><img src='$ruta' alt='' class='img-responsive hoverMio'></a></div>
                    </div>
                </div>";
            }elseif($i==0 || $i%3==0 && $i!=1){
                $contenido .= "
                <div class='row'>
                    <div class='col-sm-4'>
                      <div class='box overFlowMio'><a href='$ruta' title='' data-toggle='lightbox' data-gallery='portfolio' data-title='Portfolio image 1' data-footer='Some footer information'><img src='$ruta' alt='' class='img-responsive hoverMio'></a></div>
                    </div>";
            }elseif((($i+1)%3==0 && $i!=1) || ($i+1==count($images))){
                $contenido .= "
                    <div class='col-sm-4'>
                      <div class='box overFlowMio'><a href='$ruta' title='' data-toggle='lightbox' data-gallery='portfolio' data-title='Portfolio image 1' data-footer='Some footer information'><img src='$ruta' alt='' class='img-responsive hoverMio'></a></div>
                    </div>
                </div>";
            }else{
               $contenido .= "
                    <div class='col-sm-4'>
                      <div class='box overFlowMio'><a href='$ruta' title='' data-toggle='lightbox' data-gallery='portfolio' data-title='Portfolio image 1' data-footer='Some footer information'><img src='$ruta' alt='' class='img-responsive hoverMio'></a></div>
                    </div>";
            }
        }
        $this->insertTag("imagenes", $contenido);
    }
    
    function insertTemaImagenes3($images){
        $contenido = "";
        for ($i = 0; $i < count($images); $i++) {
            $imagen = $images[$i];
            $ruta = $imagen->getRuta();
            $nombre = $imagen->getNombre();
             if($i == 0){
                 
            $contenido .= "
                <div class='item active'>
                    <img src='$ruta' alt='$nombre'>
                </div>";
             }else{
            $contenido .= "
                <div class='item'>
                    <img src='$ruta' alt='$nombre'>
                </div>";
             }
        }
        $this->insertTag("imagenes", $contenido);
    }
    
    function insertUsuarios($usuarios){
        $contenido = "";
        for ($i = 0; $i < count($usuarios); $i++) {
            $user = $usuarios[$i];
            $alias = $user->getAlias();
            $email = $user->getEmail();
            $avatar = $user->getAvatar();
             // index.php?action=view&target=user&user=$email
            if($i+1==count($usuarios) && $i%3==0 && $i!=1){
                $contenido .= "
                <div class='row'>
                    <div class='col-sm-4'>
                      <div class='overFlowMio'><a href='index.php?action=view&target=user&user=$email' title='$alias' data-toggle='lightbox' data-gallery='portfolio' data-title='Portfolio image 1' data-footer='Some footer information'><img src='plantillas/index/user/avatar/$avatar' alt='$alias' class='img-responsive hoverMio img-circle'></a></div>
                    </div>
                </div>";
            }elseif($i==0 || $i%3==0 && $i!=1){
                $contenido .= "
                <div class='row'>
                    <div class='col-sm-4'>
                      <div class=' overFlowMio'><a href='index.php?action=view&target=user&user=$email' title='$alias' data-toggle='lightbox' data-gallery='portfolio' data-title='Portfolio image 1' data-footer='Some footer information'><img src='plantillas/index/user/avatar/$avatar' alt='$alias' class='img-responsive hoverMio img-circle'></a></div>
                    </div>";
            }elseif((($i+1)%3==0 && $i!=1) || ($i+1==count($usuarios))){
                $contenido .= "
                    <div class='col-sm-4'>
                      <div class=' overFlowMio'><a href='index.php?action=view&target=user&user=$email' title='$alias' data-toggle='lightbox' data-gallery='portfolio' data-title='Portfolio image 1' data-footer='Some footer information'><img src='plantillas/index/user/avatar/$avatar' alt='$alias' class='img-responsive hoverMio img-circle'></a></div>
                    </div>
                </div>";
            }else{
               $contenido .= "
                    <div class='col-sm-4'>
                      <div class=' overFlowMio'><a href='index.php?action=view&target=user&user=$email' title='$alias' data-toggle='lightbox' data-gallery='portfolio' data-title='Portfolio image 1' data-footer='Some footer information'><img src='plantillas/index/user/avatar/$avatar' alt='$alias' class='img-responsive hoverMio img-circle'></a></div>
                    </div>";
            }
        }
        $this->insertTag("usuarios", $contenido);
    }
    
    function insertUsuariosAdmin($usuarios){
        $contenido = "";
        for ($i = 0; $i < count($usuarios); $i++) {
            $user = $usuarios[$i];
            $alias = $user->getAlias();
            $email = $user->getEmail();
            $avatar = $user->getAvatar();
             // index.php?action=view&target=user&user=$email
            if($i+1==count($usuarios) && $i%3==0 && $i!=1){
                $contenido .= "
                <div class='row'>
                    <div class='col-sm-4'>
                      <div class='overFlowMio'><a href='index.php?action=view&target=editadmin&user=$email' title='$alias' data-toggle='lightbox' data-gallery='portfolio' data-title='Portfolio image 1' data-footer='Some footer information'><img src='plantillas/index/user/avatar/$avatar' alt='$alias' class='img-responsive hoverMio img-circle'></a></div>
                    </div>
                </div>";
            }elseif($i==0 || $i%3==0 && $i!=1){
                $contenido .= "
                <div class='row'>
                    <div class='col-sm-4'>
                      <div class=' overFlowMio'><a href='index.php?action=view&target=editadmin&user=$email' title='$alias' data-toggle='lightbox' data-gallery='portfolio' data-title='Portfolio image 1' data-footer='Some footer information'><img src='plantillas/index/user/avatar/$avatar' alt='$alias' class='img-responsive hoverMio img-circle'></a></div>
                    </div>";
            }elseif((($i+1)%3==0 && $i!=1) || ($i+1==count($usuarios))){
                $contenido .= "
                    <div class='col-sm-4'>
                      <div class=' overFlowMio'><a href='index.php?action=view&target=editadmin&user=$email' title='$alias' data-toggle='lightbox' data-gallery='portfolio' data-title='Portfolio image 1' data-footer='Some footer information'><img src='plantillas/index/user/avatar/$avatar' alt='$alias' class='img-responsive hoverMio img-circle'></a></div>
                    </div>
                </div>";
            }else{
               $contenido .= "
                    <div class='col-sm-4'>
                      <div class=' overFlowMio'><a href='index.php?action=view&target=editadmin&user=$email' title='$alias' data-toggle='lightbox' data-gallery='portfolio' data-title='Portfolio image 1' data-footer='Some footer information'><img src='plantillas/index/user/avatar/$avatar' alt='$alias' class='img-responsive hoverMio img-circle'></a></div>
                    </div>";
            }
        }
        $this->insertTag("usuarios", $contenido);
    }

}