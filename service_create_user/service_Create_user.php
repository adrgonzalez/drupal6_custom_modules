<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Implementation of hook_menu().
 */
function service_create_user_menu() {
    $items['service_create_user'] = array(
        'title' => 'Service Create User',
        'access callback' => TRUE,
        'page callback' => '_service_create_user',
        'page arguments' => array(1),
        'type' => MENU_CALLBACK,
    );

    return $items;
}

function _service_create_user() {


   // echo "<br>HTTP_HOST".$_SERVER['HTTP_HOST'].' <br>';

    $user_mail = strip_tags(utf8_decode($_GET["email"]));
    $user_name = str_replace("_", " ",strip_tags(utf8_decode($_GET["nombre"])));
    $user_lastname = str_replace("_", " ",strip_tags(utf8_decode($_GET["apellido"])));
    $user_phone = str_replace("-", "", strip_tags(utf8_decode($_GET["celular"])));
    $correo1 = strip_tags(utf8_decode($_GET['amigo1']));
    $correo2 = strip_tags(utf8_decode($_GET['amigo2']));
    $correo3 = strip_tags(utf8_decode($_GET['amigo3']));
    $correo4 = strip_tags(utf8_decode($_GET['amigo4']));
    $correo5 = strip_tags(utf8_decode($_GET['amigo5']));

    if ($user_mail == '') {
        if ($correo1 <> '') {
            send_mail_Amigos($user_name, $user_lastname, $correo1);
        }
        if ($correo2 <> '') {
            send_mail_Amigos($user_name, $user_lastname, $correo2);
        }
        if ($correo3 <> '') {
            send_mail_Amigos($user_name, $user_lastname, $correo3);
        }
        if ($correo4 <> '') {
            send_mail_Amigos($user_name, $user_lastname, $correo4);
        }
        if ($correo5 <> '') {
            send_mail_Amigos($user_name, $user_lastname, $correo5);
        }

         echo "success=yes";
    } else {
        create_user($user_name, $user_lastname, $user_mail, $user_phone);
    }
}

function create_user($user_name, $user_lastname, $user_mail, $user_phone) {
    // Obtiene los roles
    $roles = user_roles();

    $user_date = array('month' => strip_tags(utf8_decode($_GET["mes"])), 'day' => strip_tags(utf8_decode($_GET["dia"])), 'year' => strip_tags(utf8_decode($_GET["anno"])));
    $user_pas = user_password(8);

    try {
        // Información del usuario
        $user = array(
            'name' => $user_mail,
            'pass' => $user_pas,
            'mail' => $user_mail,
            'status' => 1,
            'init' => $user_mail,
            'celular' => $user_phone,
            'roles' => array(array_search('some_role', $roles) => 1),
        );

        // Consultar existencia
        $existing_user = user_load(array('name' => $user['name']));
        if (!$existing_user->uid) {
            // Crear usuario
            $user = user_save(NULL, $user);
        } else {
            echo("success=exit");
            die();
        }
    } catch (Exception $e) {
        // print ('Error con el usuario: ' . $e->getMessage() . "\n");
        echo("success=exit");
        die();
    }

    $uid = db_result(db_query('SELECT max(uid) from users'));
    $code = mktime() . user_password(5) . user_password(2);
    try {
        $edit = array(
            'profile_birthdate' => $user_date,
            'profile_firstname' => $user_name,
            'profile_lastname' => $user_lastname,
            'profile_cdrByEmail' => '1',
            'profile_thirdByEmail' => '0',
            'profile_thirdBySMS' => '0',
            'profile_cdrBySMS' => '0',
            'profile_countrycode' => 'CR'
        );

        // guarda los datos del perfil
        user_save(
                (object) array('uid' => $uid), // id de usuario
                $edit, //parametros
                'Personal Information' // categoria
        );

        send_mail_Bienvenido($user_name, $user_lastname, $user_mail, $user_pas, $code);
    } catch (Exception $e) {
        // print ('Exception: ' . $e->getMessage() . "\n");
        echo("success=no");
        die();
    }

    if (!db_query("INSERT INTO {users_promotional} (uid, code, active) values($uid, '$code', 1)")) {
        echo("success=no");
        die();
    }
}

function service_create_user_method() {
    $methods[] = array(
        'id' => 'bcr',
        'name' => t('Service Create User'),
        'title' => t('Service Create User'),
        'review' => t('create user via service'),
        'callback' => '_service_create_user_callback',
        'weight' => 3,
        'checkout' => FALSE,
        'no_gateway' => TRUE,
    );

    return $methods;
}

function _service_create_user_callback() {

}

function send_mail_Bienvenido($user_name, $user_lastname, $user_mail, $user_pas, $code) {

    $values = "
<html>
<head>
	<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
	<title>Titi Online</title>
</head>
<body>
<div style='text-align:center; margin: 0 auto;'>
<table style='margin: 0 auto; width:560px;' cellpadding='0' cellspacing='0'>
<tr>
	<td>
        <tr width='560px' height='20px'>
            <td colspan='2'>
                <img src='http://www.titionline.com/sites/all/themes/titi_theme/images/mail_borde.jpg'/>
            </td>
        </tr>
        <tr  width='560px'  height='540px'>
         	<td  height='100%' style='border-left:solid 1px #603814; padding-left:20px; font-family: Arial; margin-right:-10px;  font-size:14px; color:#603813;'>
            <p  style='text-align:right; margin-top:50px;'><a href='http://www.titionline.com'><img src='http://www.titionline.com/sites/all/themes/titi_theme/images/mail_logo.jpg' border='0' /></a></p>
            <br/>
            <p style='color:#666; font-size:24px; margin-left:160px;'>¡Bienvenido!</p>
            <p style='text-align:left !important;'>Hola <font style='text-transform:capitalize; font-weight:bold;'>$user_name $user_lastname</font><br/>
            Has sido registrado exitosamente en <b>Titionline</b><br/>
            Tu usuario es: $user_mail<br/>
            Tu Contraseña es: $user_pas<br/><br/>
            Pronto podrás ingresar al sitio
             <a href='http://www.titionline.com' title='Titi Online' target='_blank'>www.titionline.com</a><br/> y empezar a descargar.</p><br/>

                <table style='background:#F15B26; color:#FFF; font-family: Arial; margin-left:100px; text-align:center; font-size:13px;  padding:4px;  width:250px; height: 50px;'>
                    <tr>
                    	<td>*Obtendrás 20% de descuento en tu primera descarga con este cupón <br/> <b> $code </b></td>
                    </tr>
                </table>
                <center>
                	<br/><p >*Válido únicamente durante la semana de lanzamiento del 2 de diciembre al 9 de diciembre del 2011.</p>
                       <p><b>Muy importante:</b> Recuerda  guardar este correo electrónico con tu cupón de descuento. </p>
                </center>
            </td>
            <td  height='100%' style='border-right:solid 1px #603814;'><img src='http://www.titionline.com/sites/all/themes/titi_theme/images/mail_canasta.png'  border='0' /></td>
        </tr>
        <tr width='560px' height='50px'>
            <td colspan='2'> <a href='http://www.titionline.com'><img src='http://www.titionline.com/sites/all/themes/titi_theme/images/mail_naranja.png' border='0' /></a></td>
        </tr>
    </td>
</tr>
</table>
</div>
</body>
</html>
";

    // $receiver = "sergio.fernandez@orbelink.com";
    $sender = "servicioalcliente@titionline.com";
//'S3rv1@2011'

    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/HTML; charset=UTF-8' . "\r\n";
    $headers .= "From: $sender\r\n";
    $headers .= "Reply-To: $sender\r\n";
    // $headers .= "X-Mailer: PHP/" . phpversion();
    // header('Content-type: application/javascript');
    if (mail($user_mail, "Bienvenido a TitiOnline", $values, $headers)) {
        echo "success=yes";
    } else {
        echo "success=no";
    }
}

function send_mail_Amigos($user_name, $user_lastname, $user_mail) {
    $values = "
<html>
<head>
	<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
	<title>Titi Online</title>
</head>
<body>
<div style='text-align:center; margin: 0 auto;'>
<table style='margin: 0 auto; width:560px;'>
<tr>
	<td>
        <tr width='560px' height='20px'>
            <td colspan='2'>
                <img src='http://www.titionline.com/sites/all/themes/titi_theme/images/mail_borde.jpg'/>
            </td>
        </tr>
        <tr  width='560px'  height='540px'>
            <td height='100%' style='border-left:solid 1px #603814; padding-left:20px; font-family: Arial; margin-right:-10px;  font-size:14px; color:#603813;'>
            <p  style='text-align:right; margin-top:50px;'><a href='http://www.titionline.com'><img src='http://www.titionline.com/sites/all/themes/titi_theme/images/mail_logo.jpg' border='0' /></a></p>
            <br/>
            <p style='color:#666; font-size:24px; margin-left:160px;'><b>INVITACIÓN</b></p>
            <p style='text-align:left !important;'>
            <font style='text-transform:capitalize; font-weight:bold;'>$user_name $user_lastname</font><br/>
             Te invito a que te Registres y formes parte de una <br/>
            REVOLUCIÓN en contenidos digitales.<br/><br/><br/>
            	INGRESA A <a href='http://www.titionline.com' title='Titi Online' target='_blank'>www.titionline.com</a> y descubre todo lo bueno <br/> para TÍ, a un solo click de DISTANCIA.</p><br/>
              <table style='font-style:italic;  color:#603813; font-family: Arial; margin-left:100px; text-align:center; font-size:13px;  padding:4px;  width:250px; height: 50px;'>
                    <tr>
            	<td>
                ¡Una nueva era digital ha llegado a Costa Rica,
                    <label style='font-size:16px; font-weight:bold;'>disfrútalo ya!</label>
                    </td>
               </tr>
                </table>
                <br/>  <br/>
                <table style='background:#56C2E2; color:#FFF; font-family: Arial; margin-left:100px; text-align:center; font-size:12px;  padding:4px;  width:250px; height: 50px;'>
                    <tr>
                    	<td>REGÍSTRATE <a href='http://www.titionline.com' title='Titi Online' target='_blank' style='color:#326486 !important; font-weight:bold;'>AQUÍ</a> Y ADQUIERE UN 20% DE DESCUENTO EN TUS DESCARGAS</td>
                    </tr>
                </table>

            </td>
            <td height='100%' style='border-right:solid 1px #603814;'><img src='http://www.titionline.com/sites/all/themes/titi_theme/images/mail_canasta.png'  border='0' /></td>
        </tr>
        <tr width='560px' height='50px'>
            <td colspan='2'> <a href='http://www.titionline.com'><img src='http://www.titionline.com/sites/all/themes/titi_theme/images/mail_naranja.png' border='0' /></a></td>
        </tr>
    </td>
</tr>
</table>
</div>
</body>
</html>
";

    $receiver = "sergio.fernandez@orbelink.com";
    $sender = "servicioalcliente@titionline.com";

    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/HTML; charset=UTF-8' . "\r\n";
    $headers .= "From: $sender\r\n";
    $headers .= "Reply-To: $sender\r\n";
    // $headers .= "X-Mailer: PHP/" . phpversion();
    header('Content-type: text/json');

    mail($user_mail, "Conoce TitiOnline", $values, $headers);
   /* if () {
        echo "success=yes";
    } else {
        echo "success=no";
    }*/
    return"";
}
?>
