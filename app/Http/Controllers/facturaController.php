<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Facturas;
use Auth;
use App\PasswrdsP;
use App\PasswrdsE;
use App\libs\Funciones;

class facturaController extends Controller
{
    public function add_factura_to_bdd(Request $request){
    			$user = JWTAuth::parseToken()->authenticate();
    			$pos=stripos($user['email'], '@');
    			$documento=substr($user['email'],0,$pos);
    			$tamaño=strlen($documento);
   	
switch ($tamaño) {

    // case 10:
    //     // $auth = Auth::guard('web');
    //     $tabla =    new PasswrdsP();
    //     break;
    
    case 13:
        // $auth = Auth::guard('usersE');
        $tabla =    new PasswrdsE();
        $datos= $tabla->select('pass_email')->where('email','=',$user['email'])->get();
        break;
}

set_time_limit(3000);

date_default_timezone_set('America/Guayaquil'); //puedes cambiar Guayaquil por tu Ciudad
setlocale(LC_TIME, 'spanish');

/* connect to gmail with your credentials */
$hostname = '{s411b.panelboxmanager.com:993/imap/ssl}INBOX';
$username = $user['email'];
$password = $datos[0]['pass_email'];

/* try to connect */
$inbox = imap_open($hostname, $username, $password) or die('No se puede conectar a Nextbook: ' . imap_last_error());

$emails = imap_search($inbox, 'ALL');

/* if any emails found, iterate through each email */
if ($emails) {
    
    $count  = 0;
    $output = '';
    
    /* put the newest emails on top */
    rsort($emails);
    
    
    /* for every email... */
    foreach ($emails as $email_number) {
        
        /* get information specific to this email */
        $message = imap_fetchbody($inbox, $email_number, 2);
        
        /* get mail structure */
        $structure = imap_fetchstructure($inbox, $email_number);
        
        //$output.= '<div class="body">'.$message.'</div>'."<br>";
        
        /* archivos adjuntos*/
        $attachments = array();
        
        /* if any attachments found... */
        if (isset($structure->parts) && count($structure->parts)) {
            for ($i = 1; $i < count($structure->parts); $i++) {
                
                if($structure->parts[$i]->ifdparameters) 
                {
                    foreach($structure->parts[$i]->dparameters as $object) 
                    {
                     if (!file_exists("./facturas/". $email_number . "-" . $object->value)) { 
                        if (strtolower(substr($object->value, -3))=="xml") {
                     //echo $object->attribute;
                            if(strtolower($object->attribute) == 'filename') 
                                {
                
                            $attachments[$i]['is_attachment'] = true;
                            $attachments[$i]['filename'] = $object->value;                             
                
                                }
                            }
                        }
                    }
                }
                if ($structure->parts[$i]->ifparameters) {
                    foreach ($structure->parts[$i]->parameters as $object) {
                        if (!file_exists("./facturas/" . $email_number . "-" . $object->value)) {
                            if (strtolower(substr($object->value, -3)) == "xml") {
                                if (strtolower($object->attribute) == 'name') {
                                    
                                    $attachments[$i]['is_attachment'] = true;
                                    $attachments[$i]['name']          = $object->value;
                                    
                                }
                            }
                        }
                    }
                }
                if ($attachments[$i]['is_attachment']) {
                    $attachments[$i]['attachment'] = imap_fetchbody($inbox, $email_number, $i + 1);
                    // echo  $attachments[$i]['attachment'];
                    /* 3 = BASE64 encoding */
                    if ($structure->parts[$i]->encoding == 3) {
                        $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                    }
                    /* 4 = QUOTED-PRINTABLE encoding */
                    elseif ($structure->parts[$i]->encoding == 4) {
                        $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                    }
                }
                
            }
        }
        
        /* iterate through each attachment and save it */
        foreach ($attachments as $attachment) {
            if ($attachment['is_attachment'] == 1) {
                $filename = $attachment['name'];
                //echo $attachment['filename'];
                if (strtolower(substr($filename, -3)) == "xml") {
                    
                    // $overview = imap_fetch_overview($inbox, $email_number, 0);
                    // $output .= '<div class="toggler ' . ($overview[0]->seen ? 'read' : 'unread') . '">' . "<br>";
                    // $output .= '<span class="subject">' . $overview[0]->subject . '</span> ' . "<br>";
                    // $output .= '<span class="from">' . $overview[0]->from . '</span>' . "<br>";
                    // $output .= '<span class="date">' . strftime(strftime("%A, %d de %B de %Y a las %H:%M:%S", strtotime($overview[0]->date))) . '</span>' . "<br>";
                    // $output .= '</div>';
                    
                    if (empty($filename))
                        $filename = $attachment['filename'];
                    
                    if (empty($filename))
                        $filename = time() . ".dat";
                    $folder = "facturas";
                    if (!is_dir($folder)) {
                        mkdir($folder);
                    }
                    if (!file_exists("./" . $folder . "/" . $email_number . "-" . $filename)) {
                        $fp = fopen("./" . $folder . "/" . $email_number . "-" . $filename, "w+");
                        fwrite($fp, $attachment['attachment']);
                        fclose($fp);
                        $tabla=new Facturas();
                        $funciones                    = new Funciones();
                        $tabla->id_factura=$funciones->generarID();
                        $tabla->nombre_fac=$attachment['name'];
                        $tabla->contenido_fac=$attachment['attachment'];
                        $tabla->id_empresa=$user['id_user'];
                        $tabla->save();
                        // $resultado = $conexion->query("INSERT INTO fac_electronica values (idfac_electronica,'" . $attachment['name'] . "','" . $attachment['attachment'] . "')") or die($conexion->error);
                        
                    }
                    
                }
                
            }
        }
    }
    
    // echo $output;
    
}

/* close the connection */
imap_close($inbox);

echo "Todos los archivos adjuntos descargados";


    }
}
