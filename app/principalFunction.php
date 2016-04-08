<?php
# funciones de utilidad general

#variable de peiciones al modulo
function formateaPeticion(){
    global $constantes_peticion;
    $uri   = $_SERVER['REQUEST_URI'];
    foreach ($constantes_peticion as $valor) {
        $peticion = str_replace($uri,'',$valor);
        return $peticion;
    }
}

#funcion para devolver partiendo desde el final de la cadena
function devuelveString($cadena=null,$busqueda=null,$case=0){
    $tamano   = strlen($cadena);
    switch ($case){
        case 1:
            $posicion = strpos($cadena, $busqueda);
            if($posicion==''){$posicion=$tamano;}
            $cadena   = substr($cadena,0,($posicion));
            break;
        case 2:
            $posicion = strpos($cadena, $busqueda);
            $cadena   = substr($cadena,($posicion + 1),($tamano - $posicion));
            break;
    }
    return $cadena;
}

#funcion para verificar la session
function validaSession(){
    if (!isset($_SESSION['usuario'])){return false;exit();}
    return true;
}

#funcion para cerrar la session
function cerrarSession(){
    session_unset();
    session_destroy();
}

#funcion para imprimir mensaje de alert a con variables
function alert_data($data){
    print "<script>alert('" .$data. "')</script>";
}

#funcion para recuperar datos segun accion de uri
function helper_user_data($metodo) {
    $count=0;$t;
    $user_data = array();
    switch ($metodo){
        case 'login':
            if($_POST) {
                if(array_key_exists('username', $_POST)) {
                    $user_data['username'] = $_POST['username'];
                }
                if(array_key_exists('password', $_POST)) {
                    $user_data['password'] = $_POST['password'];
                }
            }
            break;
        case 'nuevoRegistro':
            if($_POST) {
                foreach($_POST as $key=>$val){
                    #if(!empty($val)){
                    if(strpos($key,'password_') !== false){$val = md5($val);}
                    $user_data[$key] = $val;

                    #}
                }
                //
                // obtenemos la informacion de los adjuntos segun el numero de input file que tenga el formulario
                if(isset($_FILES) And !empty($_FILES)){
                    $pos = strpos($user_data['no_esq_tabla'], '_');
                    $nomTabla = substr($user_data['no_esq_tabla'],$pos+1,strlen($user_data['no_esq_tabla']));
                    foreach ($_FILES as $k=>$v):
                        $t=$count>0 ? $count : '';
                        if(isset($_FILES['img'.$t.'_' . $nomTabla]['name']) Or isset($_FILES['no_img'.$t.'_' . $nomTabla]['name'])):
                            $user_data["no_nombre_img".$t] = isset($_FILES['img'.$t.'_' . $nomTabla]['name']) ? $_FILES['img'.$t.'_' . $nomTabla]['name'] : $_FILES['no_img'.$t.'_' . $nomTabla]['name'];
                            $user_data["no_tamano_img".$t] = isset($_FILES['img'.$t.'_' . $nomTabla]['size']) ? $_FILES['img'.$t.'_' . $nomTabla]['size'] : $_FILES['no_img'.$t.'_' . $nomTabla]['size'];
                            $user_data["no_tmp_img".$t]    = isset($_FILES['img'.$t.'_' . $nomTabla]['tmp_name']) ? $_FILES['img'.$t.'_' . $nomTabla]['tmp_name'] : $_FILES['no_img'.$t.'_' . $nomTabla]['tmp_name'];
                        else:
                            for($i=0;$i<count($_FILES[$k]["name"]);$i++):
                                $user_data["no_imagen_base"]["nombre"][]=$_FILES[$k]["name"][$i];
                                $user_data["no_imagen_base"]["size"][]=$_FILES[$k]["size"][$i];
                                $user_data["no_imagen_base"]["tmp"][]=$_FILES[$k]["tmp_name"][$i];
                            endfor;
                        endif;
                        $count++;
                    endforeach;
                }
            }else{
                foreach($_GET as $key=>$val):
                    #if(!empty($val)){
                    if(strpos($key,'password_') !== false){$val = md5($val);}
                    $user_data[$key] = $val;
                    #}
                endforeach;
            }
            break;

    }
    return $user_data;
}

#funcion para subir imagenes al servidor y validar su formato
function uploadImg($data_img=array(),$path, &$tmpName, &$colName,&$count){
    $valid_formats = array("JPEG", "JPG", "PNG", "JPEG", "BMP","WBMP","TXT","DOC","DOCX","XLS","PDF","SQL");
    if(!empty( $data_img["no_nombre_img".$count])){
        list($nomImg, $extImg) = explode(".", $data_img["no_nombre_img".$count]);
        if (in_array(strtoupper($extImg), $valid_formats)) {
            $pos = strpos($data_img['no_esq_tabla'], '_');
            $nomTabla = substr($data_img['no_esq_tabla'],$pos+1,strlen($data_img['no_esq_tabla']));
            if(isset($data_img["nom_" . $nomTabla])){
                $tmpName = str_replace(" ","_",$data_img["nom_" . $nomTabla] . $count . date("Y-m-d") . '-' . time() . "." . $extImg);
            }else{
                $tmpName = str_replace(" ","_",'adjunto'. $count . date("Y-m-d") . '-' . time() . "." . $extImg);
            }
            $colName = "img".$count."_";
            move_uploaded_file($data_img["no_tmp_img".$count], $path . $tmpName);
            $err_img = "El archivo se asocio correctamente. ";
        }else{
            $err_img = "La imagen no tiene el formato correcto debe subir archivos .png, .jpg, .bmp";
        }
        return $err_img;
    }
}

#funcion para subir archivos al servidor no obligatorios
function uploadImg_1($data_img=array(),$path, &$tmpName="",$nombre="",$ciclo=0){
    if(!empty($data_img)){
        $valid_formats = array("JPEG", "JPG", "PNG", "JPEG", "BIP","TXT","DOC","DOCX","XLS","PDF","SQL");
        list($nomImg, $extImg) = explode(".", $data_img["nombre"][$ciclo]);
        if (in_array(strtoupper($extImg), $valid_formats)) {
            $tmpName = str_replace(' ', '', trim($nombre)) . $ciclo . date("Y-m-d") . '-' . time() . "." . $extImg;
            move_uploaded_file($data_img["tmp"][$ciclo], $path . $tmpName);
        }
    }
}

#funcion para imprimir mensajes de error de acceso a los modulos
function getMenssage($tipo, $titulo, $descripcion){
    $mensage = '<div class="alert alert-'.$tipo.'">
        	<button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>'.$titulo.'</strong> '.$descripcion.'.
        </div>';
    return $mensage;
}

#cargamos las variables necesarias para la vista
function setVariables($controller,$Objvista,$metodo,$form,$tipoView,$vista = Null,$condicion = Null,$camposCombo=array(),$camposChek=array(),$ciclo=1,$camposComboEsp=array(),$codSubMenu=null,$met="N",$tablaAux=null,$selecionAux=null){
    $controller->get_config_usuario(Session::get('cod')) ;
    $Objvista->_empresa          = $controller->_empresa;
    $Objvista->_notificacion     = array('des_notificacion'=>$controller->_notificacion[0]['des_notificacion']);
    $Objvista->_tarea            = array('des_tarea'=>$controller->_tarea[0]['des_tarea']);
    /*$var = "";
    for($t=0;$t<count($controller->_mensajes);$t++){$var .= $controller->_mensajes[$t]['des_mensajes'];}
    $Objvista->_mensajes         = array ('des_mensajes'=>$var);
    $Objvista->_numMensajes      = $controller->_numMensajes;*/
    $var = "";
    for($t=0;$t<count($controller->_menu);$t++){$var .= $controller->_menu[$t]['menu'];}
    $Objvista->_menu             = array ('menu'=>$var);
    $Objvista->_menuHeader       = $controller->_menuHeader;
    $Objvista->_menuShorcut      = $controller->_menuShorcut;
    #traemos el formulario de usuario.
    switch($tipoView){
        case 1:
            $controller->get_form($metodo,Session::get('cod'),$form,0,$ciclo,$codSubMenu,$met);
            break;
        case 2:
            $controller->get_table($metodo,Session::get('cod'),$form,$vista,$condicion,$met,$tablaAux,$selecionAux);
            break;
        case 3:
            $controller->get_form($metodo,Session::get('cod'),$form,1,$ciclo,$codSubMenu,$met);
            break;
    }
    #traemos los archivos relacionados al formulario
    $controller->get_datos('fbDevuelveArchivos('.$form.',1) as ARCHIVOSCSS');
    $Objvista->_archivos_css = $controller->_data;
    $controller->get_datos('fbDevuelveArchivos('.$form.',2) as ARCHIVOSSCRIPT');
    $Objvista->_archivos_js  = $controller->_data;
    #armamos la tabla con los datos de las propiedades del sitema
    $var = "";
    $_rowFinal  = '';
    $_rowFinal1 = '';
    $_rowFinal2 = '';
    $_colFinal  = '';
    $_tabla     = '';
    $_clase     = "";
    //var_dump($controller->_cTabla);exit;
    for($t=0;$t<count($controller->_cTabla);$t++){
        isset($controller->_cTabla[$t]["clase_estado"]) ? $_clase=$controller->_cTabla[$t]["clase_estado"] : $_clase="";
        foreach($controller->_cTabla[$t] as $p=>$v){
            $_row = '<td style="font-size:8pt;" id="'.$p.'">' . $v . '</td>';
            $_rowFinal .=  $_row;
        }
        $_rowFinal1 .= '<tr class="'. $_clase .'">' . $_rowFinal . '</tr>';
        $_rowFinal = '';
    }

    for($t=0;$t<count($controller->_tabla);$t++){
        $_tabla = $controller->_tabla[$t]['tabla'] . $_rowFinal1;
    }
    $_formulario="";
    //var_dump($camposCombo);exit;
    if(!empty($camposCombo)){
        foreach ($camposCombo as $k=>$va){
            if(is_array($va)){
                foreach($va as $k1=>$va1){
                    if(is_array($va1)){
                        foreach($va1 as $k2=>$va2){
                            if(is_array($va2)){
                                foreach($va2 as $k3=>$va3){
                                    $campo3=key($va2);
                                    $_formulario = str_replace('<option data-selected="'.$campo3.'" value="'.$va3.'"', '<option data-selected="'.$campo3.'" selected value="'.$va3.'"', $controller->_formulario[0]["formulario"]);
                                    $controller->_formulario[0]["formulario"] = $_formulario;
                                    next($va2);
                                }
                            }else{
                                $campo2=key($va1);
                                $_formulario = str_replace('<option data-selected="'.$campo2.'" value="'.$va2.'"', '<option data-selected="'.$campo2.'" selected value="'.$va2.'"', $controller->_formulario[0]["formulario"]);
                                $controller->_formulario[0]["formulario"] = $_formulario;
                                next($va1);
                            }
                        }
                    }else{
                        $campo1=key($va);
                        $_formulario = str_replace('<option data-selected="'.$campo1.'" value="'.$va1.'"', '<option data-selected="'.$campo1.'"  selected value="'.$va1.'"', $controller->_formulario[0]["formulario"]);
                        $controller->_formulario[0]["formulario"] = $_formulario;
                        next($va);
                    }
                }
            }else{
                $campo=key($camposCombo);
                $_formulario = str_replace('<option data-selected="'.$campo.'" value="'.$va.'"', '<option data-selected="'.$campo.'" selected  value="'.$va.'"', $controller->_formulario[0]["formulario"]);
                $controller->_formulario[0]["formulario"] = $_formulario;
                next($camposCombo);
            }
        }
    }
    //var_dump($camposChek);exit();
    if(!empty($camposChek)){
        foreach ($camposChek as $k=>$va){
            $campo = '"'.$va.'"';
            $_formulario = str_replace("name=$campo", "name=$campo checked=checked", $controller->_formulario[0]["formulario"]);
            $controller->_formulario[0]["formulario"] = $_formulario;
        }
    }
    if(!empty($camposComboEsp)){
        $u=0;
        foreach($camposComboEsp as $k =>$v){
            foreach($v as $k1=>$v1){
                $_formulario = str_replace('<option data-array='.$u.' data-selected="'.$k1.'" value="'.$v1.'"', '<option data-array='.$u.' data-selected="'.$k.'" selected value="'.$v1.'"', $controller->_formulario[0]["formulario"]);
                $controller->_formulario[0]["formulario"] = $_formulario;
                $_formularioTxt = str_replace('name="no_'.$k1.'[]" data-array='.$u.' value="{'.$k1.'}"', 'name="no_'.$k1.'[]" data-array='.$u.' value="'.$v1.'"', $controller->_formulario[0]["formulario"]);
                $controller->_formulario[0]["formulario"] = $_formularioTxt;
                $_formularioTxtAr = str_replace('name="no_'.$k1.'[]" data-array='.$u.'>{'.$k1.'}', 'name="no_'.$k1.'[]" data-array='.$u.'>'.$v1, $controller->_formulario[0]["formulario"]);
                $controller->_formulario[0]["formulario"] = $_formularioTxtAr;
                $selected = $v1==1 ? ' checked="checked"' : '';
                $_formularioCheck = str_replace('name="no_'.$k1.'[]" data-array='.$u.' value="1"', 'name="no_'.$k1.'[]" data-array='.$u.' value="1" ' . $selected, $controller->_formulario[0]["formulario"]);
                $controller->_formulario[0]["formulario"] = $_formularioCheck;
                $_formularioDiv = str_replace('" data-ref="'.$k1.'" data="'.$v1.'" id="btoAccion"', ' active1" data-ref="'.$k1.'"  data="'.$v1.'" id="btoAccion"', $controller->_formulario[0]["formulario"]);
                $controller->_formulario[0]["formulario"] = $_formularioDiv;
            }$u=$u+1;
        }
    }
    $Objvista->_tabla                = array('tabla'=>$_tabla);
    $Objvista->_formulario           = $controller->_formulario;
    $Objvista->_formulario_ayuda     = $controller->_formulario_ayuda;
    $Objvista->_formulario_modal     = $controller->_formulario_modal;
    $Objvista->_boton                = $controller->_boton;
}

//funcion para devolver la configuracion del formulario segun parametros
function fbRetornaConfigForm(){
    return "title_formulario_config as TITLE,fbBase64_encode(nom_form_formulario_config) as NOM_FORM,
                fbBase64_encode(controller_formulario_config) as FORM_CONTROLLER,view_form_formulario_config as VIEW_FORM,
                fbBase64_encode(metodo_formulario_config) as FORM_MET,fbBase64_encode(arg_formulario_config) as FORM_ARG,
                fbBase64_encode(CONCAT('1',',',num_form_formulario_config,',',met_new_formulario_config)) as CONFIG_FORM_NEW,
                fbBase64_encode(CONCAT('3',',',num_form_formulario_config,',',met_edti_formulario_config)) as CONFIG_FORM_EDIT,
                fbBase64_encode(CONCAT(tipview_formulario_config,',',num_form_ant_formulario_config,',',view_form_ant_formulario_config)) as CONFIG_FORM_BACK,
                FBBASE64_ENCODE(CONCAT('2',',',num_form_formulario_config,',',met_table_formulario_config)) AS CONFIG_TABLE,
                fbBase64_encode(form_ant_formulario_config) as NOM_FORM_ANT, num_form_ant_formulario_config as NUM_FORM_ANT,
                view_form_ant_formulario_config as VIEW_FORM_ANT,form_formulario_config as FORM,
                met_edti_formulario_config as MET_EDIT,met_new_formulario_config as MET_NEW,
                target_formulario_config as TARGET";
}
//Funcion para formatear el array post y obtener los campos para la transaccion en el base de datos
function fbFormateaPost($user_data=array(),&$tblCol=null,&$tblVal=null){
    foreach ($user_data as $col => $dat):
        if (strpos(substr($col, 0, 3), 'no_') === false And strpos(substr($col, 0, 4), 'noo_') === false) :
            $tblCol = $tblCol . $col . ',';
            $tblVal = $tblVal . "'" . $dat . "'" . ',';
        endif;
    endforeach;
}
//Funcion para formatear el array post y obtener los campos para la transaccion en el base de datos
function fbFormateaNoPost($user_data=array(),&$tblCol=null,&$tblVal=null){
    foreach ($user_data as $col => $dat):
        if (substr($col, 0, 4)== 'noo_') :
            $tblCol = $tblCol . str_replace('noo_', '', $col). ',';
            $tblVal = $tblVal . "'" . $dat . "'" . ',';
        endif;
    endforeach;
}
//Funcion para enviar emails segun el case
function  sendEmail($modulo,$template="",$data=array(),$view=null,$dataIn=array(),$ruta=null,$aux=null,$adj="",&$_return=null){

    if($ruta==null){
        require_once ROOT . 'libs/PHPMailer-master/PHPMailerAutoload.php';
        require_once ROOT . 'libs/PHPMailer-master/class.smtp.php';
    }else{
        require_once $ruta . 'libs/PHPMailer-master/PHPMailerAutoload.php';
        require_once $ruta . 'libs/PHPMailer-master/class.smtp.php';
    }
    $mail = new PHPMailer(true);
    $index = isset($dataIn[1]) and is_array($dataIn[1]) ? 1 : 0;
    $mail->Username   = $index==1 ? $dataIn[$index][0]["email_envio_config"]    : $dataIn[$index]["email_envio_config"];
    $mail->Password   = $index==1 ? $dataIn[$index][0]["pass_envio_config"]     : $dataIn[$index]["pass_envio_config"];
    $mail->From       = $index==1 ? $dataIn[$index][0]["email_envio_config"]    : $dataIn[$index]["email_envio_config"];
    $mail->FromName   = $index==1 ? $dataIn[$index][0]["from_config"]           : $dataIn[$index]["from_config"];
    $mail->Host       = $index==1 ? $dataIn[$index][0]["host_envio_config"]     : $dataIn[$index]["host_envio_config"];
    $mail->Port       = $index==1 ? $dataIn[$index][0]["port_envio_config"]     : $dataIn[$index]["port_envio_config"];
    $mail->SMTPSecure = $index==1 ? $dataIn[$index][0]["tipo_sec_envio_config"] : $dataIn[$index]["tipo_sec_envio_config"];
    $mail->Subject    = $index==1 ? str_replace('{NRO_TIQUET}',$aux,$dataIn[$index][0]["asunto_config"]) : str_replace('{NRO_TIQUET}',$aux,$dataIn[$index]["asunto_config"]);
    switch ($template){
        //email para notificar asignacion de tiquet
        case 1:
            $html = $view->get_template($modulo,'forms/view_email',"");
            $html = $view->render_dinamic_data($modulo, $html, $data);
            try {
                $mail->IsSMTP();
                $mail->Mailer = 'smtp';
                $mail->SMTPAuth = true;
                $mail->Body = $html;
                $mail->IsHTML(true);
                $mail->AddAddress($data["to"]);
                $mail->Send();
            } catch (phpmailerException $e) {
                echo $e->errorMessage();
            } catch (Exception $e) {
                echo $e->getMessage();
            }
            break;
        //email para notificar la recepcion del tiquet
        case 2:
            $html = $view->get_template($modulo,'forms/view_email',1);
            $html = $view->render_dinamic_data($modulo, $html, $data);
            try {
                $mail->IsSMTP();
                $mail->Mailer = 'smtp';
                $mail->SMTPAuth = true;
                $mail->Body = $html;
                $mail->IsHTML(true);
                $mail->AddAddress($data["to"]);
                $mail->Send();
            } catch (phpmailerException $e) {
                echo $e->errorMessage();
            } catch (Exception $e) {
                echo $e->getMessage();
            }
            break;
        case 3:
            $html = $view->get_template($modulo,'forms/view_email',"");
            $html = $view->render_dinamic_data($modulo, $html, $data);
            try {
                $mail->IsSMTP();
                //$mail->Mailer = 'smtp';
                $mail->SMTPAuth = true;
                $mail->Body = $html;
                $mail->IsHTML(true);
                $mail->AddAddress($data["to"]);
                $mail->AddAttachment($adj,$adj);
                $mail->Send();
                $_return = 0;
            } catch (phpmailerException $e) {
                $_return = 1;
            } catch (Exception $e) {
                $_return = 1;
            }
            break;
    }
}

function recuperaEmailHelpDesk($dataIn=array(),&$data=array()){
    $hostname = $dataIn[0]["host_recepcion_config"];
    $username = $dataIn[0]["email_recepcion_config"];
    $password = $dataIn[0]["pass_recepcion_config"];
    date_default_timezone_set("America/Bogota");
    $inbox = imap_open($hostname,$username,$password) or die('Cannot connect to host: ' . imap_last_error());
    $emails = imap_search($inbox,'SUBJECT '. $dataIn[0]["cad_asunto_config"] );
    if($emails):
        $output = '';$i=0;
        rsort($emails);
        foreach($emails as $email_number):
            $dataInsert=array();$sql='';$insert=false;
            $overview = imap_fetch_overview($inbox,$email_number,0);
            $overview[0]->seen ? $insert=false : $insert=true;
            $dataInsert["email_servicio"]= formatearString($overview[0]->from, '<', '>');
            $dataInsert["nom_servicio"]  = trim(devuelveString($overview[0]->from, '<', 1));
            $dataInsert["cod_empresa"]   = $dataIn[0]["cod_empresa"];
            $dataInsert["cod_estado"]    = 'SAA';
            $dataInsert['fec_servicio']  = date("Y.m.d H:i:s");
            $dataInsert['des_servicio']  = message($inbox, $email_number);
            $dataInsert["cod_prioridad"] = "3";
            if($insert){
                $sql.= "INSERT INTO hd_servicio(cod_servicio,des_servicio,fec_servicio,cod_estado,email_servicio,nom_servicio,cod_empresa,cod_prioridad)";
                $sql.= "                 VALUES({cod_servicio},'".$dataInsert["des_servicio"]."','".$dataInsert["fec_servicio"]."','".$dataInsert["cod_estado"]."',";
                $sql.= "                        '".$dataInsert["email_servicio"]."','".$dataInsert["nom_servicio"]."','".$dataInsert["cod_empresa"]."',";
                $sql.= "                        '".$dataInsert["cod_prioridad"]."')";
            }
            $data[$i]["query"]    =$sql;
            $data[$i]["from"]=$dataInsert["email_servicio"];
            $data[$i]["nombre"]=$dataInsert["nom_servicio"];
            $i++;

        endforeach;
    endif;
    imap_close($inbox);
}
//Funcion para decodificar los mensajes
function decode_qprint($str){
    $str = preg_replace("/\=([A-F][A-F0-9])/","%$1",$str);
    $str = urldecode($str);
    $str = utf8_encode($str);
    return $str;
}
//funcion para leer el mensaje
function message($connection,$number){
    $info = imap_fetchstructure($connection, $number, 0);
    if($info -> encoding == 3){
        $message = base64_decode(imap_fetchbody($connection, $number, 1));
    }elseif($info -> encoding == 4){
        $message = imap_qprint(imap_fetchbody($connection, $number, 1));
    }else{
        $message = imap_fetchbody($connection, $number, 1);
    }
    //$message = imap_fetchbody($this -> connection, $number, 2);
    return decode_qprint($message);
}
//function para obtener cadenas dentro de caracteres o llaves ejemplo: <zeta>, obtendra la palabra zeta
//enviando los contenedores que en este caso con < > y la cadena completa.
function formatearString($cadena='',$busqueda='',$busqueda_1=''){
    $tamano     = strlen($cadena);
    $posicion   = strpos($cadena, $busqueda)+1;
    $posicion_1 = strpos($cadena, $busqueda_1);
    return substr($cadena,$posicion,($posicion_1-$posicion));
}

function RandomString($length=500,$uc=true,$n=true,$sc=true){
    $an = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ.-";
    $su = strlen($an) - 1;
    return  substr($an, rand(0, $su), 1) .
    substr($an, rand(0, $su), 1) .
    substr($an, rand(0, $su), 1) .
    substr($an, rand(0, $su), 1) .
    substr($an, rand(0, $su), 1) .
    substr($an, rand(0, $su), 1);
}

//Funcion para imprimir archivos pdf segun parametros
function PrintPdf($data=array(),$dataParametros=array()){
    require_once ROOT . 'libs/class.fpdf.php';
    require_once ROOT . 'libs/class.fpdf.php';
    $pdf = new PDF('L','mm','A3');
    $pdf->AliasNbPages();
    $pdf->AddPage('L','A5');
    $pdf->Cell(30, 20,'');
    $pdf->SetFont('Arial','',8);
    //$pdf->Cell(130,6,'INFORME ACADEMICO Y DISCIPLINARIO '.$data["lectivo"].' '.$data["periodo"],0,0,'C',false);
    $pdf->Ln(10);
    $pdf->Output();
}

function armaTextAnidado($data=array(),$case=0){
    $count=0;
    $cadReturn="";
    if(!empty($data)):
        for($i=0;$i<count($data);$i++):
            $cadArray="";
            foreach($data[$i] as $key=>$val):
                $cadArray .= $case==0 ? $key.' : '.$val.'    ' : $val.',';
            endforeach;
            $cadReturn .=  $case==0 ? $cadArray."\n\n" : $cadArray;
        endfor;
    endif;
    return $cadReturn;
}

// Funcion para contar el numero de concurrencias de un string dentro de un texto
function contarCoincidencias($data=array(),$clave=""){
    $cadena="";
    foreach($data as $k=>$v):$cadena.=$k; endforeach;
    return substr_count($cadena, $clave);
}
// Funcion para convertir objetos en notacion json a array php para procesarolos posteriormente
function objeto_a_array($data) {
    if (is_object($data)) {
        $data = get_object_vars($data);
    }

    if (is_array($data)) {
        return array_map(__FUNCTION__, $data);
    }
    else {
        return $data;
    }
}
//funcion para convertir imagenes de tipo bmp en jpg
function bmp2gd($src, $dest = false){
    if(!($src_f = fopen($src, "rb"))){
        return false;
    }
    if(!($dest_f = fopen($dest, "wb"))){
        return false;
    }
    $header = unpack("vtype/Vsize/v2reserved/Voffset", fread( $src_f, 14));
    $info = unpack("Vsize/Vwidth/Vheight/vplanes/vbits/Vcompression/Vimagesize/Vxres/Vyres/Vncolor/Vimportant",
        fread($src_f, 40));
    extract($info);
    extract($header);
    if($type != 0x4D42){
        return false;
    }

    $palette_size = $offset - 54;
    $ncolor = $palette_size / 4;
    $gd_header = "";

    $gd_header .= ($palette_size == 0) ? "\xFF\xFE" : "\xFF\xFF";
    $gd_header .= pack("n2", $width, $height);
    $gd_header .= ($palette_size == 0) ? "\x01" : "\x00";
    if($palette_size) {
        $gd_header .= pack("n", $ncolor);
    }

    $gd_header .= "\xFF\xFF\xFF\xFF";

    fwrite($dest_f, $gd_header);

    if($palette_size){
        $palette = fread($src_f, $palette_size);
        $gd_palette = "";
        $j = 0;
        while($j < $palette_size){
            $b = $palette{$j++};
            $g = $palette{$j++};
            $r = $palette{$j++};
            $a = $palette{$j++};
            $gd_palette .= "$r$g$b$a";
        }
        $gd_palette .= str_repeat("\x00\x00\x00\x00", 256 - $ncolor);
        fwrite($dest_f, $gd_palette);
    }
    $scan_line_size = (($bits * $width) + 7) >> 3;
    $scan_line_align = ($scan_line_size & 0x03) ? 4 - ($scan_line_size & 0x03) : 0;

    for($i = 0, $l = $height - 1; $i < $height; $i++, $l--){
        fseek($src_f, $offset + (($scan_line_size + $scan_line_align) * $l));
        $scan_line = fread($src_f, $scan_line_size);
        if($bits == 24){
            $gd_scan_line = "";
            $j = 0;
            while($j < $scan_line_size){
                $b = $scan_line{$j++};
                $g = $scan_line{$j++};
                $r = $scan_line{$j++};
                $gd_scan_line .= "\x00$r$g$b";
            }
        }elseif($bits == 8){
            $gd_scan_line = $scan_line;
        }elseif($bits == 4){
            $gd_scan_line = "";
            $j = 0;
            while($j < $scan_line_size){
                $byte = ord($scan_line{$j++});
                $p1 = chr($byte >> 4);
                $p2 = chr($byte & 0x0F);
                $gd_scan_line .= "$p1$p2";
            }
            $gd_scan_line = substr($gd_scan_line, 0, $width);
        }elseif($bits == 1){
            $gd_scan_line = "";
            $j = 0;
            while($j < $scan_line_size){
                $byte = ord($scan_line{$j++});
                $p1 = chr((int) (($byte & 0x80) != 0));
                $p2 = chr((int) (($byte & 0x40) != 0));
                $p3 = chr((int) (($byte & 0x20) != 0));
                $p4 = chr((int) (($byte & 0x10) != 0));
                $p5 = chr((int) (($byte & 0x08) != 0));
                $p6 = chr((int) (($byte & 0x04) != 0));
                $p7 = chr((int) (($byte & 0x02) != 0));
                $p8 = chr((int) (($byte & 0x01) != 0));
                $gd_scan_line .= "$p1$p2$p3$p4$p5$p6$p7$p8";
            }
            $gd_scan_line = substr($gd_scan_line, 0, $width);
        }
        fwrite($dest_f, $gd_scan_line);
    }
    fclose($src_f);
    fclose($dest_f);
    return true;
}

function ImageCreateFromBmp($filename){
    $tmp_name = tempnam("/tmp", "GD");
    if(bmp2gd($filename, $tmp_name)){
        $img = imagecreatefromgd($tmp_name);
        unlink($tmp_name);
        return $img;
    }
    return false;
}

function ImagenProporcion($ruta_imagen=null,$nombre_img=null,$rutaDestino=null){
    $miniatura_ancho_maximo = 300;
    $miniatura_alto_maximo  = 450;
    $validacion             = 3;
    $transparente           = null;
    $imageMarco            = null;

    $info_imagen  = getimagesize($ruta_imagen);
    $imagen_ancho = $info_imagen[0];
    $imagen_alto  = $info_imagen[1];
    $imagen_tipo  = $info_imagen['mime'];

    $proporcion_imagen = $imagen_ancho / $imagen_alto;
    $proporcion_miniatura = $miniatura_ancho_maximo / $miniatura_alto_maximo;

    $validacion = ($imagen_ancho < $imagen_alto) ? $validacion=1 : $validacion;
    $validacion = ($imagen_ancho > $imagen_alto) ? $validacion=2 : $validacion;
    $validacion = ($imagen_ancho == $imagen_alto) ? $validacion=3 : $validacion;

    switch ($validacion) {
        case 1:
            $miniatura_ancho = $miniatura_ancho_maximo+70;
            $miniatura_alto  = $miniatura_alto_maximo;
            break;
        case 2:
            $miniatura_ancho = ( ($miniatura_ancho_maximo * $proporcion_imagen)+50 ) > 450 ? 450 : ( $miniatura_ancho_maximo * $proporcion_imagen ) +50;
            $miniatura_alto  = ($miniatura_alto_maximo / $proporcion_imagen)+80;
            break;
        case 3:
            $miniatura_ancho = $miniatura_ancho_maximo;
            $miniatura_alto  = $miniatura_alto_maximo;
            break;
    }

    switch ( $imagen_tipo ){
        case "image/jpg":
        case "image/jpeg":
            $imagen = imagecreatefromjpeg( $ruta_imagen );
            break;
        case "image/png":
            $imagen = imagecreatefrompng( $ruta_imagen );
            break;
        case "image/gif":
            $imagen = imagecreatefromgif( $ruta_imagen );
            break;
    }

    $lienzo = imagecreatetruecolor( $miniatura_ancho, $miniatura_alto );
    $fondo_lienzo = imagecolorallocate($lienzo, 255, 255, 255);
    imagefilledrectangle($lienzo, 0, 0, $miniatura_ancho, $miniatura_alto, $fondo_lienzo);
    imagecopyresampled($lienzo, $imagen, 0, 0, 0, 0, $miniatura_ancho, $miniatura_alto, $imagen_ancho, $imagen_alto);

    $imageMarco = imageCreateTrueColor(  450, 450 );
    $fondo = imagecolorallocate($imageMarco, 255, 255, 255);
    imagefilledrectangle($imageMarco, 0, 0, 450, 450, $fondo);
    imageCopyResampled( $imageMarco, $lienzo, 0, 0, 0, 0, $miniatura_ancho, $miniatura_alto, 450, 450 );

    $lienzo = $imageMarco;

    imagejpeg($lienzo, $rutaDestino.$nombre_img, 80);

    return $nombre_img;
}
?>