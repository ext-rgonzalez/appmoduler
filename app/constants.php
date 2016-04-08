<?php
#constante del controller default
const DEFAULT_CONTROLLER = 'sistema';
const FA_CONTROLLER      = 'facturacion';
const CON_CONTROLLER     = 'contabilidad';
const VAR_APP            = '?app=';
const VAR_MET            = '&met=';
#core mysql
const DEFAULT_CORE       = 'core/db_abstract_model.php';
#funciones generales
const DEFAUL_FUNCTION    = 'app/principalFunction.php';

#rutas de directorios raiz
const MODULES_PATH       = 'modules';
const VIEW_PACH          = 'view'; 
#constante de carpeta adjuntos de sistema
const SYS_DIR_ADJ     = 'modules/sistema/adjuntos/';
const FA_DIR_ADJ      = 'modules/facturacion/adjuntos/';
const HUE_DIR_ADJ     = 'modules/huella/adjuntos/';
const LC_DIR_ADJ      = 'modules/lacuchara/adjuntos/';
const DAT_DIR_ADJ     = 'modules/proyecto/adjuntos/';
const CON_DIR_ADJ     = 'modules/contabilidad/adjuntos/';
const HD_DIR_ADJ      = 'modules/helpdesk/adjuntos/';
const SINED_DIR_ADJ   = 'modules/sined/adjuntos/';
const CRM_DIR_ADJ     = 'modules/crm/adjuntos/';
const DL_DIR_ADJ      = 'modules/datolee/adjuntos/';
const ENDO_DIR_ADJ      = 'modules/endodoncia/adjuntos/';
#rutas controladores
$diccionario_general = array(
    'LINK_DES'=>array(
    'LOGOUT_TEXT'      =>'Cerrar Session',
    'EDIT_TEXT'        =>'Editar Perfil',
    'APP_TEXT'         =>'Configurar',
    'ADD_TEXT'         =>'Nuevo Usuario',
    'RETURN_TEXT'      =>'Pagina Inicio',
    'NOTIF_TEXT'       =>'Ver Todas ',
    'INFOGEN_TEXT'     =>'Empresas',
    'MSJ_TEXT'         =>'Bandeja de Entrada',
    'SOPORTE_TEXT'     =>'soporte',
    'BACKUP_TEXT'      =>'backup',
    'ESTADISTICAS_TEXT'=>'estadisticas',
    'MOD_TEXT'         =>'Modulos',
    'NAV_TEXT'         =>'Navegaci&oacute;n',
    'NOM_FORM'         =>''  
    ),
    'LINK_URL'=>array(
            'LOGOUT_LINK'      =>VAR_APP . base64_encode('sistema') . VAR_MET . base64_encode('cerrar'),
            'EDIT_LINK'        =>VAR_APP . base64_encode('sistema') . VAR_MET . base64_encode('editarPerfil'),
            'APP_LINK'         =>VAR_APP . base64_encode('sistema') . VAR_MET . base64_encode('aproved'),
            'ADD_LINK'         =>VAR_APP . base64_encode('sistema') . VAR_MET . base64_encode('usuario'),
            'RETURN_LINK'      =>VAR_APP . base64_encode('sistema') . VAR_MET . base64_encode('index'),
            'NOTIF_LINK'       =>VAR_APP . base64_encode('sistema') . VAR_MET . base64_encode('notificacion'),
            'MSJ_LINK'         =>VAR_APP . base64_encode('sistema') . VAR_MET . base64_encode('mensajes'),
            'SOPORTE_LINK'     =>VAR_APP . base64_encode('sistema') . VAR_MET . base64_encode('soporte'),
            'BACKUP_LINK'      =>VAR_APP . base64_encode('sistema') . VAR_MET . base64_encode('backup'),
            'ESTADISTICAS_LINK'=>VAR_APP . base64_encode('vsistema'). VAR_MET . base64_encode('estadisticas'),
            'MOD_LINK'         =>VAR_APP . base64_encode('sistema') . VAR_MET . base64_encode('modulos'),
            'NOM_LINK_RECOVER' =>VAR_APP . base64_encode('sistema') . VAR_MET . base64_encode('recover'),
            'NOM_LINK_INICIO'  =>VAR_APP . base64_encode('sistema') . VAR_MET . base64_encode('login')
    )
);

$diccionario = array(
    'error' => array(
            'TITLE'=>'402 ERROR',
            'ERR'=>array(
                    'ERR_404'     =>'404',
                    'DES_ERR_404' =>'Pagina no encontrada - Moduler Admin'
    ),
    'LINK_DES'=>array(
                'LINK_ERR_DES'=>'Regresar'
        ),
        'LINK_URL'=>array(
                'LINK_ERR_URL'=>'javascript: history.go(-1)',
        'FORM_CONTROLLER'=>'sistema'
        ),
        'FORM'=>''

    ),
    'login' => array(
        'TITLE'=>'Inicio de Sesi&oacute;n',
        'ERR'=>array(),
        'LINK_DES'=>'',
        'LINK_URL'=>array(
                'SET_LOGIN'=>VAR_APP . 'sistema' .VAR_MET. 'login',
                'FORM_CONTROLLER'=>'sistema'

        ),
        'FORM'=>'forms/view_login'
    ),
    'index' => array(
        'TITLE'=>'Dashboard',
        'ERR'=>array(),
        'LINK_DES'=>array(
                'NOM_FORM'         =>'form_usuario',
        'FORM_CONTROLLER'=>'sistema'  
        ),
        'LINK_URL'=>'',
        'FORM'=>'forms/view_dashboard'
    ),
    'indexLacuchara' => array(
        'TITLE'=>'Dashboard La Cuchara',
        'ERR'=>array(),
        'LINK_DES'=>array(
                'NOM_FORM'         =>'form_usuario',
        'FORM_CONTROLLER'=>'lacuchara' 
        ),
        'LINK_URL'=>'',
        'FORM'=>'forms/view_dashboard'
    ),
    'indexFacturacion' => array(
        'TITLE'=>'Dashboard Facturacion',
        'ERR'=>array(),
        'LINK_DES'=>array(
                'NOM_FORM'         =>'form_usuario',
        'FORM_CONTROLLER'=>'facturacion' 
        ),
        'LINK_URL'=>'',
        'FORM'=>'forms/view_dashboard'
    ),
    'indexContabildiad' => array(
        'TITLE'=>'Dashboard Contabilidad',
        'ERR'=>array(),
        'LINK_DES'=>array(
                'NOM_FORM'         =>'form_usuario',
        'FORM_CONTROLLER'=>'contabilidad' 
        ),
        'LINK_URL'=>'',
        'FORM'=>'forms/view_dashboard'
    ),
    'indexHelpDesk' => array(
        'TITLE'=>'Dashboard HelpDesk',
        'ERR'=>array(),
        'LINK_DES'=>array(
                'NOM_FORM'         =>'form_usuario',
        'FORM_CONTROLLER'=>'helpdesk' 
        ),
        'LINK_URL'=>'',
        'FORM'=>'forms/view_dashboard'
    )
);
#constantes de la lista de notificaciones
const INI_NOT            = '<li><a href="#" id="btoAccion" ';
const MED_NOT            = '><span class="padding-10 unread"><em class="badge padding-5 no-border-radius bg-color-blueLight pull-left margin-right-5"><i class="fa fa-bell fa-fw fa-2x"></i></em><span>'; 
const FIN_NOT            = '</span></span></a></li>'; 
#constante de la lista de mensajes
const INI_MSJ            = '<li><a href="#" id="btoAccion" ';
const MED_MSJ            = '><span class="padding-10 unread"><em class="badge padding-5 no-border-radius bg-color-blueLight pull-left margin-right-5"><i class="fa fa-user fa-fw fa-2x"></i></em><span>'; 
const MED_MSJ_1          = '<br><span class="pull-right font-xs text-muted"><i>';
const MED_MSJ_2          = '</span>'; 
const FIN_MSJ            = '</span>'; 
#constante para la navegacion de usuario
const INI_NAV            = '<li><a href="#';
const MED_NAV            = '"><i class="';
const MED_NAV_1          = '"></i><span class="menu-item-parent">';
const FIN_NAV            = '</span></a>';
const INI_SNAV           = '<ul>';
const FIN_SNAV           = '</ul></li>';
?>
