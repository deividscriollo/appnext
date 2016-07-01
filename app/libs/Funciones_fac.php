<?php

namespace App\libs;
use App\Facturas;
use App\FacturasRechazadas;
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
                          str_replace('<','&lt;',
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
                $attachments[$i] = array(
                            'is_attachment' => false,
                            'filename' => '',
                            'name' => '',
                            'attachment' => ''
                        );
                // if($structure->parts[$i]->ifdparameters) 
                // {
                //     foreach($structure->parts[$i]->dparameters as $object) 
                //     {
                //      if (!file_exists("./facturas/". $email_number . "-" . $object->value)) {
                //         if (strtolower(substr($object->value, -3))=="xml"||strtolower(substr($object->value, -3))=="zip") {
                //      //echo $object->attribute;
                //             if(strtolower($object->attribute) == 'filename') 
                //                 {
                //             $attachments[$i]['is_attachment'] = true;
                //             $attachments[$i]['filename'] = $object->value;                             
                //              $cont++;
                //                 }
                //             }
                //         }
                //     }
                // }
                if ($structure->parts[$i]->ifparameters) {
                    foreach ($structure->parts[$i]->parameters as $object) {
                        if (!file_exists("./facturas/" . $email_number . "-" . $object->value)) {
                            if (strtolower(substr($object->value, -3)) == "xml"||strtolower(substr($object->value, -3))=="zip") {
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
                 // echo "XML";
                 $res_xml = $this->save_xml_mail($attachment['attachment'],$username,$filename);
                  // print_r($res_xml);
                }
                if (strtolower($file_ext[1]) == "zip") {
                 // echo "ZIP";
                 $res_zip = $this->save_zip_mail($attachment['attachment'],$username,$filename);
                  // print_r($res_zip);
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
function verificar_autorizacion($clave_acceso){
        $client = new Client;
$res = $client->request('POST', 'http://localhost/appserviciosnext/public/estado_factura', [
    'json' => ["clave"=>(string)$clave_acceso]
]);

$respuesta= json_decode($res->getBody(), true);
return $respuesta;
}


    function save_xml_mail($xmlmaster,$emailuser,$doc_name){
        $tblFacturas=new Facturas();
        $tblFacturas_rechazadas=new FacturasRechazadas();
        $funciones=new Funciones();
        $empresas=new Empresas();
        $passE=new PasswrdsE();
        $datosPass=$passE->select('id_user')->where('email','=',$emailuser)->get();
        $doc_name=$doc_name;
 if (!is_dir("facturas/".$datosPass[0]['id_user'])) {
    mkdir("facturas/".$datosPass[0]['id_user']);      
    }
        $xmlData_sub = new \SimpleXMLElement($xmlmaster);
if ($xmlData_sub->comprobante) {
        $xmlDatamaster = $this->uncdata($xmlData_sub->comprobante);
        $file_xml = new \SimpleXMLElement($xmlDatamaster);
}else{
        $file_xml = new \SimpleXMLElement($xmlmaster);     
}
$clave_acceso = $file_xml->infoTributaria->claveAcceso;
$ambiente = $file_xml->infoTributaria->ambiente;
$tipo_doc=$file_xml->infoTributaria[0]->codDoc;
$nombre_comercial = $file_xml->infoTributaria->nombreComercial;
$ruc_comercial = $file_xml->infoTributaria->ruc;


$respuesta=$this->verificar_autorizacion($clave_acceso);
// print_r($respuesta) ;

if (count($respuesta[0]['autorizaciones'])!=0) {
    $estado=$respuesta[0]['autorizaciones']['autorizacion']['estado'];

            if($estado == 'AUTORIZADO') {

             switch ((string)$tipo_doc) {
    //****************************************************** NOTA DE CREDITO
  case '04':
                  
            $xmlComp = new \SimpleXMLElement($respuesta[0]['autorizaciones']['autorizacion']['comprobante']);
            $email = $xmlComp->infoAdicional->campoAdicional;
            $fecha_aut = $xmlComp->infoNotaCredito->fechaEmision;                   
            $razon_social = $xmlComp->infoNotaCredito->razonSocial;
            $cod_doc = $xmlComp->infoNotaCredito->codDoc;
            $total = $xmlComp->infoNotaCredito->valorModificacion;
            $datos = explode('@', $email);
            $ruc = $datos[0];         
            $identificacionComprador= $xmlComp->infoNotaCredito->identificacionComprador;    
            //******************************** Datos Nota de Credito/************************

            $num_fac = $xmlComp->infoTributaria->estab. '-'.$xmlComp->infoTributaria->ptoEmi. '-'.$xmlComp->infoTributaria->secuencial;
            $var_fe = $xmlComp->infoNotaCredito->fechaEmision;
            $tipo_doc = $xmlComp->infoTributaria->codDoc;
            $date_fe = str_replace('/', '-', $var_fe);
            $date_fe = date('Y-m-d', strtotime($date_fe));
            $id_factura = $funciones->generarID();
           // print_r($ruc_comercial);

    break;  

     case '01':
                  
            $xmlComp = new \SimpleXMLElement($respuesta[0]['autorizaciones']['autorizacion']['comprobante']);
            $email = $this->getmail($xmlComp);
            $fecha_aut = $xmlComp->infoFactura->fechaEmision;                   
            $razon_social = $xmlComp->infoFactura->razonSocial;
            $cod_doc = $xmlComp->infoFactura->codDoc;
            $total = $xmlComp->infoFactura->importeTotal;
            $datos = explode('@', $email);
            $ruc = $datos[0];         
            $identificacionComprador= $xmlComp->infoFactura->identificacionComprador;    
            //******************************** Datos Nota de Credito/************************

            $num_fac = $xmlComp->infoTributaria->estab. '-'.$xmlComp->infoTributaria->ptoEmi. '-'.$xmlComp->infoTributaria->secuencial;
            $var_fe = $xmlComp->infoFactura->fechaEmision;
            $tipo_doc = $xmlComp->infoTributaria->codDoc;
            $date_fe = str_replace('/', '-', $var_fe);
            $date_fe = date('Y-m-d', strtotime($date_fe));
            $id_factura = $funciones->generarID();
    break;  
}
// echo "RUC= ".$cod_doc."identificacionComprador=".$identificacionComprador;
               if($ruc != $identificacionComprador) {
              
              $id_fact = $funciones->generarID();
              $datosPass=$passE->select('id_user')->where('email','=',$emailuser)->get();
              $res =$tblFacturas->select('id_factura')->where('clave_acceso','=',(string)$clave_acceso)->get();
              if(count($res) == 0 ){
                $tblFacturas->id_factura = $id_factura;
                $tblFacturas->num_factura = $num_fac;
                $tblFacturas->nombre_comercial = $nombre_comercial;
                $tblFacturas->Ruc_prov = $ruc_comercial;
                $tblFacturas->fecha_emision = $date_fe;
                $tblFacturas->clave_acceso = (string)$clave_acceso;
                $tblFacturas->ambiente = (string)$ambiente;
                $tblFacturas->tipo_doc = $tipo_doc;
                $tblFacturas->tipo_consumo = '-------';
                $tblFacturas->total = $total;
                $tblFacturas->contenido_fac = $respuesta[0]['autorizaciones']['autorizacion']['comprobante'];
                $tblFacturas->id_empresa = $datosPass[0]['id_user'];
                $save=$tblFacturas->save();
                if ($save) {
                  // echo "OK XML--";
                  $url_destination_xml = "facturas/".$datosPass[0]['id_user']."/".$id_factura.'.xml';                 
                  $fp_fac = fopen($url_destination_xml, "wr+");
                  fwrite($fp_fac, $xmlmaster);
                  fclose($fp_fac);
                  // return array('valid' => 'true', 'methods' => 'full');
                }
                    }else
                        return array('valid' => 'false', 'error' => '5','methods' => 'cla-acc-existente'); // ---------- valido y listo para procesar       
                }else 
                    $this->save_fac_rechazada($xmlmaster,$emailuser,(string)$clave_acceso,'ruc-no-perteneciente');
                    // return array('valid' => 'false', 'error' => '1', 'methods' => 'ruc-no-perteneciente'); // ---------- ruc no perteneciente a esta cuenta
            }else
            $this->save_fac_rechazada($xmlmaster,$emailuser,(string)$clave_acceso,'Documento-no-autorizado');
                // return array('valid' => 'false', 'error' => '2', 'methods' => 'no-autorizado'); // ------ clave de acceso no autorizado
        }else
        $this->save_fac_rechazada($xmlmaster,$emailuser,(string)$clave_acceso,'registro-no-existente-sri');
            // return array('valid' => 'false', 'error' => '4', 'methods' => 'registro-no-existente-sri'); // ------ no disponible 
    }

function save_zip_mail($xmlmaster,$emailuser,$doc_name){
  $funciones=new Funciones();
  $passE=new PasswrdsE();
  $tblFacturas=new Facturas();
  $tblFacturas_rechazadas=new FacturasRechazadas();
  $datosPass=$passE->select('id_user')->where('email','=',$emailuser)->get();

 if (!is_dir("facturas/".$datosPass[0]['id_user'])) {
    mkdir("facturas/".$datosPass[0]['id_user']);      
    }
    $id=$funciones->generarID();
    $url_destination = "facturas/".$datosPass[0]['id_user']."/".$id.'.zip';                 
    $fp = fopen($url_destination, "wr+");
    fwrite($fp, $xmlmaster);
    fclose($fp);

    $zip = zip_open($url_destination);
    if ($zip) {
      while ($zip_entry = zip_read($zip)) {
        // $fp = fopen("facturas/".$datosPass[0]['id_user']."/".zip_entry_name($zip_entry), "w");
        if (zip_entry_open($zip, $zip_entry, "r")) {
          
            $nombre_archivo= zip_entry_name($zip_entry);
            $tipo_archivo= explode(".", $nombre_archivo);
            $tipo_archivo= $tipo_archivo[1];
            $filesize=zip_entry_filesize($zip_entry);
        if ($tipo_archivo=="xml") {//********************INICIO si el archivo es XML******************************
        //********************************* SI XML NO ESTA VACIO*********************
          if ($filesize!=0) {
        $buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
        $xmlData_sub = new \SimpleXMLElement($buf);
          if ($xmlData_sub->comprobante) {
                  $xmlDatamaster = $xmlData_sub->comprobante;
                  $file_xml = new \SimpleXMLElement($xmlDatamaster);
          }else{
                  $file_xml = new \SimpleXMLElement($xmlData_sub);
                  // $clave_acceso = $file_xml->infoTributaria->claveAcceso;
          }
          $clave_acceso = $file_xml->infoTributaria->claveAcceso;
          $ambiente = $file_xml->infoTributaria->ambiente;
          $tipo_doc=$file_xml->infoTributaria[0]->codDoc;
        $respuesta=$this->verificar_autorizacion($clave_acceso);

          // print_r($respuesta);
          if (count($respuesta[0]['autorizaciones']) != 0) {
          $estado = $respuesta[0]['autorizaciones']['autorizacion']['estado'];
          if($estado == 'AUTORIZADO') {

switch ((string)$tipo_doc) {
    //****************************************************** NOTA DE CREDITO
  case '04':
                  
            $xmlComp = new \SimpleXMLElement($respuesta[0]['autorizaciones']['autorizacion']['comprobante']);
            $email = $xmlComp->infoAdicional->campoAdicional;
            $fecha_aut = $xmlComp->infoNotaCredito->fechaEmision;                   
            $razon_social = $xmlComp->infoNotaCredito->razonSocial;
            $cod_doc = $xmlComp->infoNotaCredito->codDoc;
             $total = $xmlComp->infoNotaCredito->importeTotal;
            $datos = explode('@', $email);
            $ruc = $datos[0];         
            $identificacionComprador= $xmlComp->infoNotaCredito->identificacionComprador;    
            //******************************** Datos Nota de Credito/************************

            $num_fac = $xmlComp->infoTributaria->estab. '-'.$xmlComp->infoTributaria->ptoEmi. '-'.$xmlComp->infoTributaria->secuencial;
            $var_fe = $xmlComp->infoNotaCredito->fechaEmision;
            $nombre_comercial = $xmlComp->infoFactura->nombreComercial;
            $ruc_comercial = $xmlComp->infoFactura->ruc;
            $tipo_doc = $xmlComp->infoTributaria->codDoc;
            $date_fe = str_replace('/', '-', $var_fe);
            $date_fe = date('Y-m-d', strtotime($date_fe));
            $id_factura = $funciones->generarID();
    break;  

     case '01':
                  
            $xmlComp = new \SimpleXMLElement($respuesta[0]['autorizaciones']['autorizacion']['comprobante']);
            $email = $this->getmail($xmlComp);
            $fecha_aut = $xmlComp->infoFactura->fechaEmision;                   
            $razon_social = $xmlComp->infoFactura->razonSocial;
            $cod_doc = $xmlComp->infoFactura->codDoc;
             $total = $xmlComp->infoFactura->importeTotal;
            $datos = explode('@', $email);
            $ruc = $datos[0];         
            $identificacionComprador= $xmlComp->infoFactura->identificacionComprador;    
            //******************************** Datos Nota de Credito/************************

            $num_fac = $xmlComp->infoTributaria->estab. '-'.$xmlComp->infoTributaria->ptoEmi. '-'.$xmlComp->infoTributaria->secuencial;
            $var_fe = $xmlComp->infoFactura->fechaEmision;
            $nombre_comercial = $xmlComp->infoFactura->nombreComercial;
            $ruc_comercial = $xmlComp->infoFactura->ruc;
            $tipo_doc = $xmlComp->infoTributaria->codDoc;
            $date_fe = str_replace('/', '-', $var_fe);
            $date_fe = date('Y-m-d', strtotime($date_fe));
            $id_factura = $funciones->generarID();
    break;  
}

     if($ruc != $identificacionComprador) {

              $id_fact = $funciones->generarID();
              $datosPass=$passE->select('id_user')->where('email','=',$emailuser)->get();
              $res =$tblFacturas->select('id_factura')->where('clave_acceso','=',(string)$clave_acceso)->get();
              if(count($res) == 0 ){
                $tblFacturas->id_factura = $id_factura;
                $tblFacturas->num_factura = $num_fac;
                $tblFacturas->nombre_comercial = (string)$nombre_comercial;
                $tblFacturas->Ruc_prov = (string)$ruc_comercial;
                $tblFacturas->fecha_emision = $date_fe;
                $tblFacturas->clave_acceso = (string)$clave_acceso;
                $tblFacturas->ambiente = (string)$ambiente;
                $tblFacturas->tipo_doc = $tipo_doc;
                $tblFacturas->tipo_consumo = '-------';
                $tblFacturas->total = $total;
                $tblFacturas->contenido_fac = $respuesta[0]['autorizaciones']['autorizacion']['comprobante'];
                $tblFacturas->id_empresa = $datosPass[0]['id_user'];
                $save=$tblFacturas->save();
                if ($save) {
                  // echo "OK ZIP--";
                  $url_destination_xml = "facturas/".$datosPass[0]['id_user']."/".$id_factura.'.xml';                 
                  $fp_fac = fopen($url_destination_xml, "wr+");
                  fwrite($fp_fac, $buf);
                  fclose($fp_fac);
                  // return array('valid' => 'true', 'methods' => 'full');
                }

              }
            }else
            return array('valid' => 'false', 'error' => '5','methods' => 'cla-acc-existente'); // ---------- valido y listo para procesar   
            }else
             $this->save_fac_rechazada($buf,$emailuser,'no-definido','no-autorizado');
            // return array('valid' => 'false', 'error' => '2', 'methods' => 'no-autorizado'); // ------ clave de acceso no autorizado
        }else
        $this->save_fac_rechazada($buf,$emailuser,'no-definido','registro-no-existente-sri');
      }else
      $this->save_fac_rechazada("",$emailuser,'no-definido','Documento-Vacio'); //************** si el XML esta vacio
          // return array('valid' => 'false', 'error' => '4', 'methods' => 'registro-no-existente-sri'); // ------ no disponible 
           }//*************************************************** FIN si el archivo es XML******************************

        }
      }//**********while
    }/// *****if ZIP
    zip_close($zip);
    // echo "<br>".$url_destination;
    unlink($url_destination);
  }

function save_fac_rechazada($xmlmaster,$emailuser,$clave_acceso,$razon){
  $tblFacturas_rechazadas=new FacturasRechazadas();
  $passE=new PasswrdsE();
  $datosPass=$passE->select('id_user')->where('email','=',$emailuser)->get();

 $funciones=new Funciones();

    $tblFacturas_rechazadas->id_factura_r = $funciones->generarID();
    // $tblFacturas_rechazadas->num_factura = "no-disponible";
    // $tblFacturas_rechazadas->nombre_comercial = "no-disponible";
    // $tblFacturas_rechazadas->Ruc_prov = "no-disponible";
    // $tblFacturas_rechazadas->fecha_emision = "no-disponible";
    // $tblFacturas_rechazadas->clave_acceso = "no-disponible";
    // $tblFacturas_rechazadas->ambiente = "no-disponible";
    // $tblFacturas_rechazadas->tipo_doc = "no-disponible";
    // $tblFacturas_rechazadas->total = "no-disponible";
    $tblFacturas_rechazadas->clave_acceso = $clave_acceso;
    $tblFacturas_rechazadas->tipo_consumo = '-------';
    $tblFacturas_rechazadas->razon_rechazo = $razon;
    $tblFacturas_rechazadas->contenido_fac = $xmlmaster;
    $tblFacturas_rechazadas->id_empresa = $datosPass[0]['id_user'];
    $save=$tblFacturas_rechazadas->save();

}

function save_xml_file($xmlmaster,$emailuser,$doc_name,$tipo_consumo){
        $tblFacturas=new Facturas();
        $tblFacturas_rechazadas=new FacturasRechazadas();
        $funciones=new Funciones();
        $empresas=new Empresas();
        $passE=new PasswrdsE();
        $datosPass=$passE->select('id_user')->where('email','=',$emailuser)->get();
        $doc_name=$doc_name;
 if (!is_dir("facturas/".$datosPass[0]['id_user'])) {
    mkdir("facturas/".$datosPass[0]['id_user']);      
    }
        $xmlData_sub = new \SimpleXMLElement($xmlmaster);
if ($xmlData_sub->comprobante) {
        $xmlDatamaster = $this->uncdata($xmlData_sub->comprobante);
        $file_xml = new \SimpleXMLElement($xmlDatamaster);
}else{
        $file_xml = new \SimpleXMLElement($xmlmaster);     
}
$clave_acceso = $file_xml->infoTributaria->claveAcceso;
$ambiente = $file_xml->infoTributaria->ambiente;
$tipo_doc=$file_xml->infoTributaria[0]->codDoc;
$nombre_comercial = $file_xml->infoTributaria->nombreComercial;
$ruc_comercial = $file_xml->infoTributaria->ruc;


$respuesta=$this->verificar_autorizacion($clave_acceso);
// print_r($respuesta) ;

if (count($respuesta[0]['autorizaciones'])!=0) {
    $estado=$respuesta[0]['autorizaciones']['autorizacion']['estado'];

            if($estado == 'AUTORIZADO') {

             switch ((string)$tipo_doc) {
    //****************************************************** NOTA DE CREDITO
  case '04':
                  
            $xmlComp = new \SimpleXMLElement($respuesta[0]['autorizaciones']['autorizacion']['comprobante']);
            $email = $xmlComp->infoAdicional->campoAdicional;
            $fecha_aut = $xmlComp->infoNotaCredito->fechaEmision;                   
            $razon_social = $xmlComp->infoNotaCredito->razonSocial;
            $cod_doc = $xmlComp->infoNotaCredito->codDoc;
            $total = $xmlComp->infoNotaCredito->valorModificacion;
            $datos = explode('@', $email);
            $ruc = $datos[0];         
            $identificacionComprador= $xmlComp->infoNotaCredito->identificacionComprador;    
            //******************************** Datos Nota de Credito/************************

            $num_fac = $xmlComp->infoTributaria->estab. '-'.$xmlComp->infoTributaria->ptoEmi. '-'.$xmlComp->infoTributaria->secuencial;
            $var_fe = $xmlComp->infoNotaCredito->fechaEmision;
            $tipo_doc = $xmlComp->infoTributaria->codDoc;
            $date_fe = str_replace('/', '-', $var_fe);
            $date_fe = date('Y-m-d', strtotime($date_fe));
            $id_factura = $funciones->generarID();
           // print_r($ruc_comercial);

    break;  

     case '01':
                  
            $xmlComp = new \SimpleXMLElement($respuesta[0]['autorizaciones']['autorizacion']['comprobante']);
            $email = $this->getmail($xmlComp);
            $fecha_aut = $xmlComp->infoFactura->fechaEmision;                   
            $razon_social = $xmlComp->infoFactura->razonSocial;
            $cod_doc = $xmlComp->infoFactura->codDoc;
            $total = $xmlComp->infoFactura->importeTotal;
            $datos = explode('@', $email);
            $ruc = $datos[0];         
            $identificacionComprador= $xmlComp->infoFactura->identificacionComprador;    
            //******************************** Datos Nota de Credito/************************

            $num_fac = $xmlComp->infoTributaria->estab. '-'.$xmlComp->infoTributaria->ptoEmi. '-'.$xmlComp->infoTributaria->secuencial;
            $var_fe = $xmlComp->infoFactura->fechaEmision;
            $tipo_doc = $xmlComp->infoTributaria->codDoc;
            $date_fe = str_replace('/', '-', $var_fe);
            $date_fe = date('Y-m-d', strtotime($date_fe));
            $id_factura = $funciones->generarID();
    break;  
}
// echo "RUC= ".$cod_doc."identificacionComprador=".$identificacionComprador;
               if($ruc != $identificacionComprador) {
              
              $id_fact = $funciones->generarID();
              $datosPass=$passE->select('id_user')->where('email','=',$emailuser)->get();
              $res =$tblFacturas->select('id_factura')->where('clave_acceso','=',(string)$clave_acceso)->get();
              if(count($res) == 0 ){
                $tblFacturas->id_factura = $id_factura;
                $tblFacturas->num_factura = $num_fac;
                $tblFacturas->nombre_comercial = $nombre_comercial;
                $tblFacturas->Ruc_prov = $ruc_comercial;
                $tblFacturas->fecha_emision = $date_fe;
                $tblFacturas->clave_acceso = (string)$clave_acceso;
                $tblFacturas->ambiente = (string)$ambiente;
                $tblFacturas->tipo_doc = $tipo_doc;
                $tblFacturas->tipo_consumo = $tipo_consumo;
                $tblFacturas->total = $total;
                $tblFacturas->contenido_fac = $respuesta[0]['autorizaciones']['autorizacion']['comprobante'];
                $tblFacturas->id_empresa = $datosPass[0]['id_user'];
                $save=$tblFacturas->save();
                if ($save) {
                  // echo "OK XML--";
                  $url_destination_xml = "facturas/".$datosPass[0]['id_user']."/".$id_factura.'.xml';                 
                  $fp_fac = fopen($url_destination_xml, "wr+");
                  fwrite($fp_fac, $xmlmaster);
                  fclose($fp_fac);
                  // return array('valid' => 'true', 'methods' => 'full');
                }
                    }else
                        return array('valid' => 'false', 'error' => '5','methods' => 'cla-acc-existente'); // ---------- valido y listo para procesar       
                }else 
                    $this->save_fac_rechazada($xmlmaster,$emailuser,(string)$clave_acceso,'ruc-no-perteneciente');
                    // return array('valid' => 'false', 'error' => '1', 'methods' => 'ruc-no-perteneciente'); // ---------- ruc no perteneciente a esta cuenta
            }else
            $this->save_fac_rechazada($xmlmaster,$emailuser,(string)$clave_acceso,'Documento-no-autorizado');
                // return array('valid' => 'false', 'error' => '2', 'methods' => 'no-autorizado'); // ------ clave de acceso no autorizado
        }else
        $this->save_fac_rechazada($xmlmaster,$emailuser,(string)$clave_acceso,'registro-no-existente-sri');
            // return array('valid' => 'false', 'error' => '4', 'methods' => 'registro-no-existente-sri'); // ------ no disponible 
    }


}

?>