<?php

namespace App\libs;
use App\Facturas;
use App\Empresas;
use App\PasswrdsE;
use Illuminate\Http\Request;

use App\Http\Requests;
use GuzzleHttp\Client;
use App\libs\Funciones;

// include_once('xmlapi.php');
/* --------------------------------------- Funciones --------------------------------*/
class Funciones_fac
{
    
    function __construct()
    {
        set_time_limit(3000);

date_default_timezone_set('America/Guayaquil'); //puedes cambiar Guayaquil por tu Ciudad
setlocale(LC_TIME, 'spanish');
    }

public static function generateValidXmlFromArray($array, $node_block='nodes', $node_name='node') {
        $xml = '<?xml version="1.0" encoding="UTF-8" ?>';

        //$xml .= '<' . $node_block . '>';    
        $xml .= self::generateXmlFromArray($array, $node_name);
        //$xml .= '</' . $node_block . '>';
        return $xml;
    }
    private static function generateXmlFromArray($array, $node_name) {
        $xml = '';
        if(is_array($array) || is_object($array)) {
          foreach ($array as $key=>$value) {
              if (is_numeric($key)) {
                  $key = $node_name;
              }
              $xml .= '<' . $key . '>' . self::generateXmlFromArray($value, $node_name) . '</' . $key . '>';          
          }
        }else {
          $xml = htmlspecialchars($array, ENT_QUOTES);
        }
        return $xml;
    }
    public function uncdata($xml){    
    $state = 'out';
    $a = str_split($xml);
    $new_xml = '';
    foreach ($a AS $k => $v) {        
      switch ( $state ) {
        case 'out':
          if ( '<' == $v ) {
            $state = $v;
          } else {
            $new_xml .= $v;
          }
          break;
        case '<':
          if ( '!' == $v  ) {
            $state = $state . $v;
          } else {
            $new_xml .= $state . $v;
            $state = 'out';
          }
          break;
        case '<!':
          if ( '[' == $v  ) {
            $state = $state . $v;
          } else {
            $new_xml .= $state . $v;
            $state = 'out';
          }
          break;
        case '<![':
          if ( 'C' == $v  ) {
            $state = $state . $v;
          } else {
            $new_xml .= $state . $v;
            $state = 'out';
          }
          break;
        case '<![C':
          if ( 'D' == $v  ) {
            $state = $state . $v;
          } else {
            $new_xml .= $state . $v;
            $state = 'out';
          }
          break;
        case '<![CD':
          if ( 'A' == $v  ) {
              $state = $state . $v;
          } else {
              $new_xml .= $state . $v;
              $state = 'out';
          }
          break;
        case '<![CDA':
          if ( 'T' == $v  ) {
              $state = $state . $v;
          } else {
              $new_xml .= $state . $v;
              $state = 'out';
          }
          break;
        case '<![CDAT':
          if ( 'A' == $v  ) {
              $state = $state . $v;
          } else {
              $new_xml .= $state . $v;
              $state = 'out';
          }
          break;
        case '<![CDATA':
          if ( '[' == $v  ) {


              $cdata = '';
              $state = 'in';
          } else {
              $new_xml .= $state . $v;
              $state = 'out';
          }
          break;
        case 'in':
          if ( ']' == $v ) {
              $state = $v;
          } else {
              $cdata .= $v;
          }
          break;
        case ']':
          if (  ']' == $v  ) {
              $state = $state . $v;
          } else {
              $cdata .= $state . $v;
              $state = 'in';
          }
          break;
        case ']]':
          if (  '>' == $v  ) {
              $new_xml .= str_replace('>','&gt;',
                          str_replace('>','&lt;',
                          str_replace('"','&quot;',
                          str_replace('&','&amp;',
                          $cdata))));
              $state = 'out';
          } else {
              $cdata .= $state . $v;
              $state = 'in';
          }
          break;        
        }
      }    

    return trim($new_xml);
  }

public function getmail($xml){
        $email = '';
        for ($i=0; $i < sizeof($xml->infoAdicional->campoAdicional); $i++) {    
            if(strtolower($xml->infoAdicional->campoAdicional[$i]->attributes()) == 'email' ) {
                $email = $xml->infoAdicional->campoAdicional[$i];
            }
        }
        return $email;
    }
  public  function getruc($email){
        $class = new Empresas();
        $respuesta = $class->select('Ruc')->where('email','=',$email)->get();
        $ruc = $respuesta['ruc'];
        
        return $ruc;
    }

public function leer($usuario,$pass,$iduser){
        
/* connect to gmail with your credentials */
$hostname = '{s411b.panelboxmanager.com:993/imap/ssl}INBOX';
$username = $usuario;
$password = $pass;

/* try to connect */
$inbox = imap_open($hostname, $username, $password) or die('No se puede conectar a Nextbook: ' . imap_last_error());

$emails = imap_search($inbox, 'UNSEEN');

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
        $cont=0;
        
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
                             $cont++;
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
                                    $cont++;
                                }
                            }
                        }
                    }
                }
                // echo "<br>".strtolower(substr($object->value, -3));
                if ($cont!=0) {
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
            imap_clearflag_full($inbox, $email_number, "\\Seen");
        }
        
        /* iterate through each attachment and save it */
        foreach ($attachments as $attachment) {
            if ($attachment['is_attachment'] == 1) {
                $filename = $attachment['name'];
                $file_ext = explode('.', $filename);
                // echo strtolower($file_ext[1]);
                if (strtolower($file_ext[1]) == "xml") {
                 
                 $res = $this->save_xml_mail($attachment['attachment'],$username);
                                // print_r($res);   
                    // if (empty($filename))
                    //     $filename = $attachment['filename'];
                    
                    // if (empty($filename))
                    //     $filename = time() . ".dat";
                    // $folder = "facturas";
                    // if (!is_dir($folder)) {
                    //     mkdir($folder);
                    // }
                    // if (!file_exists("./" . $folder . "/" . $email_number . "-" . $filename)) {
                    //     $fp = fopen("./" . $folder . "/" . $email_number . "-" . $filename, "w+");
                    //     fwrite($fp, $attachment['attachment']);
                    //     fclose($fp);
                    //     $tabla=new Facturas();
                    //     $funciones                    = new Funciones();
                    //     $tabla->id_factura=$funciones->generarID();
                    //     $tabla->nombre_fac=$attachment['name'];
                    //     $tabla->contenido_fac=$attachment['attachment'];
                    //     $tabla->id_empresa=$iduser;
                    //     $tabla->save();
                        
                    // }
                }
                
            }
        }
    }
    
    // echo $output;
    
}

/* close the connection */
imap_close($inbox);

// if (!isset($cont)) {
//  return "Facturas añadidas: 0";
// }else{
//  return "Facturas añadidas:".$cont;
// }
    }

    function save_xml_mail($xmlmaster,$emailuser){

        $xmlData_sub = new \SimpleXMLElement($xmlmaster);
        $xmlDatamaster = $this->uncdata($xmlData_sub->comprobante);
        $file_xml = new \SimpleXMLElement($xmlDatamaster);
        $clave_acceso = $file_xml->infoTributaria->claveAcceso;
        // $respuesta = $getsri->estado_factura_electronica($clave_acceso);
        // $respuesta = Request::create('http://192.168.1.34/appserviciosnext/public/estado_factura', 'POST', ['clave' => '1503201601109172437100120020010000269744392556014']);
        $client = new Client;

$res = $client->request('POST', 'http://192.168.1.24/appserviciosnext/public/estado_factura', [
    'json' => ["clave"=>"1503201601109172437100120020010000269744392556014"]
]);

$respuesta= json_decode($res->getBody(), true);

       // print_r($respuesta[0]['autorizaciones']);
if (count($respuesta[0]['autorizaciones'])!=0) {
    $estado=$respuesta[0]['autorizaciones']['autorizacion']['estado'];

            if($estado == 'AUTORIZADO') {
                $funciones=new Funciones();
                $empresas=new Empresas();
                $passE=new PasswrdsE();
                $id_fac = $funciones->generarID();    
                $xmlComp = new \SimpleXMLElement($estado=$respuesta[0]['autorizaciones']['autorizacion']['comprobante']);
                $email = $this->getmail($xmlComp);
                $fecha_aut = $xmlComp->infoFactura->fechaEmision;                                    
                $hora = date('G:i');
                $razon_social = $xmlComp->infoTributaria->razonSocial;
                $cod_doc = $xmlComp->infoTributaria->codDoc;
                $datos = explode('@', $email);
                $ruc = $datos[0];
                // echo $fecha_aut;
                if($ruc != $xmlComp->infoFactura->identificacionComprador || substr($ruc, 0,10)  != $xmlComp->infoFactura->identificacionComprador) {
                if($ruc == $xmlComp->infoFactura->identificacionComprador || substr($ruc, 0,10)  == $xmlComp->infoFactura->identificacionComprador) {
                    $id_prov = $funciones->generarID();
                    $fecha_adj = date('G:i');
                    $id_fact = $funciones->generarID();
                    $datosPass=$passE->select('id_user')->where('email','=',$emailuser)->get();
                    // $datosE=$empresas->select('Ruc')->where('id_empresa','=',$datosPass[0]['id_user'])->get();
                    // echo $datosE[0]['Ruc'];
                        $id_factura=$funciones->generarID();
                        $tabla=new Facturas();
                        $tabla->id_factura=$id_factura;
                        $tabla->nombre_fac=$id_factura.'.xml';
                        $tabla->contenido_fac=$estado=$respuesta[0]['autorizaciones']['autorizacion']['comprobante'];
                        $tabla->id_empresa=$datosPass[0]['id_user'];
                        $tabla->save();
                         if (!is_dir("facturas/".$datosPass[0]['id_user'])) {
                         mkdir("facturas/".$datosPass[0]['id_user']);      
                          }
                         $url_destination = "facturas/".$datosPass[0]['id_user']."/".$id_factura.'.xml';                    
                         $fp = fopen($url_destination, "wr+");   
                         // $xml = $this->generateValidXmlFromArray($respuesta[0]['autorizaciones']['autorizacion']['comprobante']);  
                         $xml =$xmlmaster;                           
                         fwrite($fp, $xml);
                         fclose($fp);
                        return array('valid' => 'true', 'methods' => 'full'); // ---------- valido y listo para procesar
                    }else
                        return array('valid' => 'false', 'error' => '5','methods' => 'cla-acc-existente'); // ---------- valido y listo para procesar       
                }else 
                    return array('valid' => 'false', 'error' => '1', 'methods' => 'ruc-no-perteneciente'); // ---------- ruc no perteneciente a esta cuenta
            }else
                return array('valid' => 'false', 'error' => '2', 'methods' => 'no-autorizado'); // ------ clave de acceso no autorizado
        }else
            return array('valid' => 'false', 'error' => '4', 'methods' => 'registro-no-existente-sri'); // ------ no disponible 
    }

}
?>