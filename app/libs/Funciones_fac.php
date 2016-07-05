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
use Codedge\Fpdf\Fpdf\FPDF;


include_once('fpdf/barcode.inc.php');
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

    // --------------------------------------GENERAR PDF----------------------
public function gen_pdf($xmlmaster,$iduser,$idfac){

$xmlData = new \SimpleXMLElement($xmlmaster);
if(!is_object($xmlData)){
    $xmlString = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $xmlmaster);
    $xmlAut = new \SimpleXMLElement($xmlString);         
    
    $nroAut = $xmlAut->soapBody->ns2autorizacionComprobanteResponse->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->numeroAutorizacion;
    $fechAut = $xmlAut->soapBody->ns2autorizacionComprobanteResponse->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->fechaAutorizacion;
    $ambi = $xmlAut->soapBody->ns2autorizacionComprobanteResponse->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->ambiente;

    $xmlAut = utf8_decode($xmlAut->soapBody->ns2autorizacionComprobanteResponse->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->comprobante);                
  }else{    
    $nroAut = $xmlData->numeroAutorizacion; 
    $fechAut = $xmlData->fechaAutorizacion; 
    $ambi = utf8_decode($xmlData->ambiente);  
    $xmlAut = $this->uncdata($xmlData->comprobante); 
  }         

  $xmlAut =  new \SimpleXMLElement($xmlAut); 
  // print_r($xmlAut);              
  
  if($xmlAut->infoTributaria->tipoEmision == 1){
    $emi = 'Normal';  
  }else{
    $emi = 'Indisponibilidad del Sistema';  
  }

     $fpdf= new FPDF('P','mm','a4');
        $fpdf->AddPage();
        $fpdf->SetMargins(10,0,0,0);        
        $fpdf->AliasNbPages();
        $fpdf->SetAutoPageBreak(true, 10);
        $fpdf->SetFont('Arial','B',16);

        if($xmlAut->infoTributaria->codDoc == '01'){
    $doc = "FACTURA";     
    $fpdf->Rect(3, 8, 100, 43 , 'D');//1 empresa imagen
      $fpdf->Text(5, 50, substr(utf8_decode($xmlAut->infoTributaria->razonSocial),0,48).'..');//NOMBRE proveedor
      $fpdf->Text(5, 50, substr(utf8_decode($xmlAut->infoTributaria->razonSocial),0,48).'..');//NOMBRE proveedor
      //////////////////////1/////////////////////////

    $fpdf->Rect(3, 53, 100, 45 , 'D');//2 datos personales 
    $fpdf->SetY(55);
    $fpdf->SetX(4);  
    $fpdf->multiCell( 98, 5, $xmlAut->infoTributaria->razonSocial,0 );//NOMBRE proveedor 
    $fpdf->SetY(66);
    $fpdf->SetX(4);  
    $fpdf->multiCell( 98, 5, 'Dir Matriz: ' .utf8_decode($xmlAut->infoTributaria->dirMatriz),0 );//   direccion  
    $fpdf->Text(5, 86, utf8_decode('Contribuyente Especial Resolución Nro: '.$xmlAut->infoFactura->contribuyenteEspecial));//contribuyente
    $fpdf->Text(5, 93, utf8_decode('Obligado a llevar Contabilidad: '.$xmlAut->infoFactura->obligadoContabilidad));//obligado

    $est = $xmlAut->infoTributaria->estab . '-'. $xmlAut->infoTributaria->ptoEmi . '-'. $xmlAut->infoTributaria->secuencial;
    
      $fpdf->Rect(106, 8, 102, 90 , 'D');//3 DATOS EMPRESA
      $fpdf->Text(108, 15, 'RUC: '. $xmlAut->infoTributaria->ruc);//ruc
      $fpdf->Text(108, 22, $doc);//tipo comprobante
      $fpdf->Text(108, 29, 'No. ' . $est);//tipo comprobante
      $fpdf->Text(108, 36, utf8_decode('NÚMERO DE AUTORIZACIÓN'));//nro autorizacion TEXT
      $fpdf->Text(108, 43, $nroAut);//nro autorizacion
      $fpdf->Text(108, 50, utf8_decode('FECHA Y HORA DE AUTORIZACIÓN'));//fecha y hora de autorizacion TEXT
      $fpdf->Text(108, 57, $fechAut);//nro autorizacion
      $fpdf->Text(108, 64, utf8_decode('AMBIENTE: '. utf8_encode($ambi)));//ambiente
      $fpdf->Text(108, 71, utf8_decode('EMISIÓN: '. $emi));//tipo de emision
      $fpdf->Text(108, 80, utf8_decode('CLAVE DE ACCESO: '));//tipo de emision
      $code_number = $xmlAut->infoTributaria->claveAcceso;//////cpdigo de barras    
    new barCodeGenrator($code_number,1,'temp.gif', 475, 60, true);///img codigo barras    
    $fpdf->Image('temp.gif',108,82,96,15);       
      /////////////////////////////Datos Factura///////////////////
      $fpdf->Rect(3, 101, 205, 20 , 'D');////3 INFO TRIBUTARIA
      $fpdf->SetY(101);
      $fpdf->SetX(3);
    $fpdf->multiCell( 130, 6, utf8_decode('Razón Social / Nombres y Apellidos: ' . $xmlAut->infoFactura->razonSocialComprador ),0 );//NOMBRE cliente 
      $fpdf->Text(135, 105, utf8_decode('RUC / CI: ' . $xmlAut->infoFactura->identificacionComprador ));//ruc cliente
      $fpdf->Text(5, 117, utf8_decode('Fecha de Emisión: ' . $xmlAut->infoFactura->fechaEmision ));//fecha de emision cliente
      $fpdf->Text(136, 117, utf8_decode('Guía de Remisión: ' .$xmlAut->infoFactura->guiaRemision ));//guia remision 

      if(is_object($xmlAut->detalles->detalle[0]->detallesAdicionales->detAdicional[2])){
      print_r($xmlAut->detalles->detalle[0]->detallesAdicionales->detAdicional[1]->attributes()->nombre); 
    }
    
    //  //////////////////detalles factura/////////////
      // $fpdf->SetFont('Amble-Regular','',8);               
      $fpdf->SetY(125);
    $fpdf->SetX(3);
    $fpdf->multiCell( 20, 10, utf8_decode('Cod. Principal'),1 );
    $fpdf->SetY(125);
    $fpdf->SetX(23);
    $fpdf->multiCell( 21, 10, utf8_decode('Cod. Auxiliar'),1 );
    $fpdf->SetY(125);
    $fpdf->SetX(44);
    $fpdf->multiCell( 12, 10, utf8_decode('Cant.'),1 );
    $fpdf->SetY(125);
    $fpdf->SetX(56);
    $fpdf->multiCell( 60, 10, utf8_decode('Descripción'),1 );
    $fpdf->SetY(125);
    $fpdf->SetX(116);
    $fpdf->multiCell( 15, 5, utf8_decode('Detalle Adicional'),1 );
    $fpdf->SetY(125);
    $fpdf->SetX(131);
    $fpdf->multiCell( 15, 5, utf8_decode('Detalle Adicional'),1 );
    $fpdf->SetY(125);
    $fpdf->SetX(146);
    $fpdf->multiCell( 15, 5, utf8_decode('Detalle Adicional'),1 );
    $fpdf->SetY(125);
    $fpdf->SetX(161);
    $fpdf->multiCell( 16, 5, utf8_decode('Precio Unitario'),1 );
    $fpdf->SetY(125);
    $fpdf->SetX(177);
    $fpdf->multiCell( 16, 10, utf8_decode('Descuento'),1 );
    $fpdf->SetY(125);
    $fpdf->SetX(193);
    $fpdf->multiCell( 15, 5, utf8_decode('Precio Total'),1 );            
   
    for ($i=0; $i < sizeof($xmlAut->detalles->detalle); $i++) { 
      $fpdf->SetX(3);
      $fpdf->Cell(20, 6, utf8_decode($xmlAut->detalles->detalle[$i]->codigoPrincipal),1,0, 'C',0);                   
      $fpdf->Cell(21, 6, utf8_decode($xmlAut->detalles->detalle[$i]->codigoAuxiliar),1,0, 'C',0);
      $fpdf->Cell(12, 6, utf8_decode($xmlAut->detalles->detalle[$i]->cantidad),1,0, 'C',0); 
      $fpdf->Cell(60, 6, substr(utf8_decode($xmlAut->detalles->detalle[$i]->descripcion),0,36),1,0, 'L',0);  
      if(is_object($xmlAut->detalles->detalle[$i]->detallesAdicionales->detAdicional[0])){
        $fpdf->Cell(15, 6, substr(utf8_decode($xmlAut->detalles->detalle[$i]->detallesAdicionales->detAdicional[0]->attributes()),0,9),1,0, 'C',0);                          
      }else{
        $fpdf->Cell(15, 6, '',1,0, 'C',0);                                   
      }
      if(is_object($xmlAut->detalles->detalle[$i]->detallesAdicionales->detAdicional[1])){
        $fpdf->Cell(15, 6, substr(utf8_decode($xmlAut->detalles->detalle[$i]->detallesAdicionales->detAdicional[1]->attributes()),0,9),1,0, 'C',0);                          
      }else{
        $fpdf->Cell(15, 6, '',1,0, 'C',0);                                   
      }
      if(is_object($xmlAut->detalles->detalle[$i]->detallesAdicionales->detAdicional[2])){        
        $fpdf->Cell(15, 6, substr(utf8_decode($xmlAut->detalles->detalle[$i]->detallesAdicionales->detAdicional[2]->attributes()),0,9),1,0, 'C',0);                  
      }else{
        $fpdf->Cell(15, 6, '',1,0, 'C',0);                             
      }     
      $fpdf->Cell(16, 6, utf8_decode($xmlAut->detalles->detalle[$i]->precioUnitario),1,0, 'C',0);                  
      $fpdf->Cell(16, 6, utf8_decode($xmlAut->detalles->detalle[$i]->descuento),1,0, 'C',0);                   
      $fpdf->Cell(15, 6, utf8_decode($xmlAut->detalles->detalle[$i]->precioTotalSinImpuesto),1,1, 'C',0);                                  
    
    }
    /////////////////pie de pagina//////////
    // $fpdf->SetFont('Amble-Regular','',9);              
    $fpdf->Ln(5);
    $fpdf->SetX(3);
    
      $fpdf->Rect($fpdf->GetX(), $fpdf->GetY(), 115, 0 , 'D');////3 INFO TRIBUTARIA
      $fpdf->Rect($fpdf->GetX() + 115, $fpdf->GetY(), 90, 0 , 'D');////3 INFO TRIBUTARIA
    $y =  $fpdf->GetY();
    $x =  $fpdf->GetX();
    $y_1 =  $fpdf->GetY();
    $x_1 =  $fpdf->GetX();
    $fpdf->Text($x_1 + 3, $y_1 + 3, utf8_decode('INFORMACIÓN ADICIONAL'));//informacion adicional  
    $fpdf->Ln(3);
    $y = $y + 6;    
    $tam = 0;
    $tam =  $tam + 6;
    for ($i=0; $i < sizeof($xmlAut->infoAdicional->campoAdicional); $i++) {     
      $fpdf->SetX(5);
      $fpdf->MultiCell( 105, 5, utf8_decode($xmlAut->infoAdicional->campoAdicional[$i]->attributes() . ' : ' . $xmlAut->infoAdicional->campoAdicional[$i]),0 );            
      $y = $y + 6;
      $tam =  $tam + 6;
    } 
    $fpdf->Rect($x_1, $y_1, 110, $tam , 'D');////4 TOTALES
    $y_1 = $y_1;
    $fpdf->SetY($y_1);
    $fpdf->SetX(115);    
    $fpdf->Cell(70, 5, utf8_decode('SUBTOTAL 12 %'),1,0, 'L',0);                              
    $tam = sizeof($xmlAut->infoFactura->totalConImpuestos->totalImpuesto);
    $cont = 0;
    for($i = 0; $i < $tam;$i++){
      if($xmlAut->infoFactura->totalConImpuestos->totalImpuesto[$i]->codigoPorcentaje == 2){
        $fpdf->Cell(23, 5, $xmlAut->infoFactura->totalConImpuestos->totalImpuesto[$i]->baseImponible,1,1, 'L',0);                                      
        $fpdf->SetX(115);  
        $cont = 1;
      }   
    }
    if($cont == 0){
      $fpdf->Cell(23, 5, '0.00',1,1, 'L',0);// CODIGO 1                                            
      $fpdf->SetX(115);  
    }         
    $cont = 0;    
    $fpdf->Cell(70, 5, utf8_decode('SUBTOTAL 0 %'),1,0, 'L',0);                                       
    for($i = 0; $i < $tam;$i++){
      if($xmlAut->infoFactura->totalConImpuestos->totalImpuesto[$i]->codigoPorcentaje == 0){
        $fpdf->Cell(23, 5, $xmlAut->infoFactura->totalConImpuestos->totalImpuesto[$i]->baseImponible,1,1, 'L',0);                                                      
        $fpdf->SetX(115);
        $cont = 1;
      }     
    }
    if($cont == 0){
      $fpdf->Cell(23, 5, '0.00',1,1, 'L',0);///CODIGO 2      
      $fpdf->SetX(115);  
    }         
    $cont = 0;
    $fpdf->Cell(70, 5, utf8_decode('SUBTOTAL No sujeto de IVA'),1,0, 'L',0);                
    for($i = 0; $i < $tam;$i++){
      if($xmlAut->infoFactura->totalConImpuestos->totalImpuesto[$i]->codigoPorcentaje == 6){
        $fpdf->Cell(23, 5, $xmlAut->infoFactura->totalConImpuestos->totalImpuesto[$i]->baseImponible,1,1, 'L',0);        
        $fpdf->SetX(115);
        $cont = 1;
      }     
    }   
    if($cont == 0){
      $fpdf->Cell(23, 5, '0.00',1,1, 'L',0);// CODIGO 2      
      $fpdf->SetX(115);  
    }         
    $cont = 0;
    $fpdf->Cell(70, 5, utf8_decode('SUBTOTAL Exento de IVA'),1,0, 'L',0);                         
    for($i = 0; $i < $tam;$i++){
      if($xmlAut->infoFactura->totalConImpuestos->totalImpuesto[$i]->codigoPorcentaje == 7){
        $fpdf->Cell(23, 5, $xmlAut->infoFactura->totalConImpuestos->totalImpuesto[$i]->baseImponible,1,1, 'L',0);                        
        $fpdf->SetX(115);
        $cont = 1;
      }     
    }
    if($cont == 0){
      $fpdf->Cell(23, 5, '0.00',1,1, 'L',0);// CODIGO 2            
      $fpdf->SetX(115);  
      $cont = 1;
    }         
    $cont = 0;
    $fpdf->Cell(70, 5, utf8_decode('SUBTOTAL SIN IMPUESTOS'),1,0, 'L',0);                             
    $fpdf->Cell(23, 5, utf8_decode($xmlAut->infoFactura->totalSinImpuestos),1,1, 'L',0);                                 
    $fpdf->SetX(115);

    $fpdf->Cell(70, 5, utf8_decode('DESCUENTOS'),1,0, 'L',0);                                    
    $fpdf->Cell(23, 5, utf8_decode($xmlAut->infoFactura->totalDescuento),1,1, 'L',0);                                          
    $fpdf->SetX(115);

    $fpdf->Cell(70, 5, utf8_decode('ICE'),1,0, 'L',0);                                         
    
    for($i = 0; $i < $tam;$i++){
      if($xmlAut->infoFactura->totalConImpuestos->totalImpuesto[$i]->codigoPorcentaje >= 3000 && $xmlAut->infoFactura->totalConImpuestos->totalImpuesto[$i]->codigoPorcentaje < 4000 ){
        $fpdf->Cell(23, 5, $xmlAut->infoFactura->totalConImpuestos->totalImpuesto[$i]->valor,1,1, 'L',0);                
        $fpdf->SetX(115);
        $cont = 1;
      }     
    }   
    if($cont == 0){
      $fpdf->Cell(23, 5, '0.00',1,1, 'L',0);// CODIGO 2            
      $fpdf->SetX(115);
      $cont = 1;  
    }         
    $cont = 0;
    
    $fpdf->Cell(70, 5, utf8_decode('IVA 12 %'),1,0, 'L',0);                                        
    for($i = 0; $i < $tam;$i++){
      if($xmlAut->infoFactura->totalConImpuestos->totalImpuesto[$i]->codigoPorcentaje == 2){
        $fpdf->Cell(23, 5, $xmlAut->infoFactura->totalConImpuestos->totalImpuesto[$i]->valor,1,1, 'L',0);                                  
        $fpdf->SetX(115);
        $cont = 1;
      }     
    }
    if($cont == 0){
      $fpdf->Cell(23, 5, '0.00',1,1, 'L',0);// CODIGO 2                          
      $fpdf->SetX(115);  
      $cont = 1;
    }         

    $cont = 0;  

    $fpdf->Cell(70, 5, utf8_decode('IRBPNR'),1,0, 'L',0);              
    for($i = 0; $i < $tam;$i++){
      if($xmlAut->infoFactura->totalConImpuestos->totalImpuesto[$i]->codigoPorcentaje >= 5000){
        $fpdf->Cell(23, 5, $xmlAut->infoFactura->totalConImpuestos->totalImpuesto[$i]->valor,1,1, 'L',0);                                                      
        $fpdf->SetX(115);
        $cont = 1;
      }     
    } 
    if($cont == 0){
      $fpdf->Cell(23, 5, '0.00',1,1, 'L',0);// CODIGO 2                                
      $fpdf->SetX(115);  
      $cont = 1;
    }         
    $cont = 0;      
    $fpdf->Cell(70, 5, utf8_decode('PROPINA'),1,0, 'L',0);                   
    $fpdf->Cell(23, 5,utf8_decode($xmlAut->infoFactura->propina),1,1, 'L',0);                                                                
    $fpdf->SetX(115);

    $fpdf->Cell(70, 5, utf8_decode('VALOR TOTAL'),1,0, 'L',0);                       
    $fpdf->Cell(23, 5,utf8_decode($xmlAut->infoFactura->importeTotal),1,1, 'L',0);        
  }

  if($xmlAut->infoTributaria->codDoc == '04'){
    // echo "NOTA DE CRÉDITO";
    $doc = "NOTA DE CRÉDITO";     
    $fpdf->Rect(3, 8, 100, 43 , 'D');//1 empresa imagen
      $fpdf->Text(5, 50, utf8_decode($xmlAut->infoTributaria->razonSocial));//NOMBRE proveedor
      $fpdf->Text(5, 50, utf8_decode($xmlAut->infoTributaria->razonSocial));//NOMBRE proveedor
      //////////////////////1/////////////////////////

      $fpdf->Rect(3, 53, 100, 45 , 'D');//2 datos personales 
    $fpdf->SetY(55);
    $fpdf->SetX(4);  
      $fpdf->multiCell( 98, 5, $xmlAut->infoTributaria->razonSocial,0 );//NOMBRE proveedor 
    $fpdf->SetY(66);
    $fpdf->SetX(4);  
    $fpdf->multiCell( 98, 5, utf8_decode('Dir Matriz: ' .$xmlAut->infoTributaria->dirMatriz),0 );//   direccion  
    $fpdf->Text(5, 86, utf8_decode('Contribuyente Especial Resolución Nro: '.$xmlAut->infoFactura->contribuyenteEspecial));//contribuyente
    $fpdf->Text(5, 93, utf8_decode('Obligado a llevar Contabilidad: '.$xmlAut->infoFactura->obligadoContabilidad));//obligado

    $est = $xmlAut->infoTributaria->estab . '-'. $xmlAut->infoTributaria->ptoEmi . '-'. $xmlAut->infoTributaria->secuencial;
    
      $fpdf->Rect(106, 8, 102, 90 , 'D');//3 DATOS EMPRESA
      $fpdf->Text(108, 15, 'RUC: '. $xmlAut->infoTributaria->ruc);//ruc
      $fpdf->Text(108, 22, $doc);//tipo comprobante
      $fpdf->Text(108, 29, 'No. ' . $est);//tipo comprobante
      $fpdf->Text(108, 36, utf8_decode('NÚMERO DE AUTORIZACIÓN'));//nro autorizacion TEXT
      $fpdf->Text(108, 43, $nroAut);//nro autorizacion
      $fpdf->Text(108, 50, utf8_decode('FECHA Y HORA DE AUTORIZACIÓN'));//fecha y hora de autorizacion TEXT
      $fpdf->Text(108, 57, $fechAut);//nro autorizacion
      $fpdf->Text(108, 64, utf8_decode('AMBIENTE: '. $ambi));//ambiente
      $fpdf->Text(108, 71, utf8_decode('EMISIÓN: '. $emi));//tipo de emision
      $fpdf->Text(108, 80, utf8_decode('CLAVE DE ACCESO: '));//tipo de emision
      $code_number = $xmlAut->infoTributaria->claveAcceso;//////cpdigo de barras    
    new barCodeGenrator($code_number,1,'temp.gif', 475, 60, true);///img codigo barras    
    $fpdf->Image('temp.gif',108,82,96,15);       
      /////////////////////////////Datos Factura///////////////////
      $fpdf->Rect(3, 101, 205, 44 , 'D');////3 INFO TRIBUTARIA
      $fpdf->SetY(101);
    $fpdf->SetX(3);
    $fpdf->multiCell( 130, 6, utf8_decode('Razón Social / Nombres y Apellidos: ' . $xmlAut->infoNotaCredito->razonSocialComprador ),0 );//NOMBRE cliente 
      $fpdf->Text(135, 105, utf8_decode('Identificación: ' . $xmlAut->infoNotaCredito->identificacionComprador ));//ruc cliente
      $fpdf->Text(5, 117, utf8_decode('Fecha de Emisión: ' . $xmlAut->infoNotaCredito->fechaEmision ));//fecha de emision cliente      
    $fpdf->Line(5,122,205,122);
    //01 factura ver en la base de datos $xmlAut->infoNotaCredito->codDocModificado
    $fpdf->Text(5, 128, utf8_decode('Comprobante que se modifica: ' .  'FACTURA'));//
    $fpdf->Text(150, 128, utf8_decode($xmlAut->infoNotaCredito->numDocModificado ));//
      $fpdf->Text(5, 136, utf8_decode('Fecha Emisión (Comprobante a modificar): ' . $xmlAut->infoNotaCredito->fechaEmisionDocSustento));//
      $fpdf->Text(5, 143, utf8_decode('Razón de Modificación: ' . $xmlAut->infoNotaCredito->motivo));//
      
       //////////////////detalles factura/////////////
      // $fpdf->SetFont('Amble-Regular','',8);               
      $fpdf->SetY(145);
    $fpdf->SetX(3);
    $fpdf->multiCell( 15, 10, utf8_decode('Código'),1 );
    $fpdf->SetY(145);
    $fpdf->SetX(18);
    $fpdf->multiCell( 13, 5, utf8_decode('Código Auxiliar'),1 );
    $fpdf->SetY(145);
    $fpdf->SetX(31);
    $fpdf->multiCell( 14, 10, utf8_decode('Cantidad'),1 );
    $fpdf->SetY(145);
    $fpdf->SetX(45);
    $fpdf->multiCell( 71, 10, utf8_decode('Descripción'),1 );
    $fpdf->SetY(145);
    $fpdf->SetX(116);
    $fpdf->multiCell( 15, 5, utf8_decode('Detalle Adicional'),1 );
    $fpdf->SetY(145);
    $fpdf->SetX(131);
    $fpdf->multiCell( 15, 5, utf8_decode('Detalle Adicional'),1 );
    $fpdf->SetY(145);
    $fpdf->SetX(146);
    $fpdf->multiCell( 15, 5, utf8_decode('Detalle Adicional'),1 );
    $fpdf->SetY(145);
    $fpdf->SetX(161);
    $fpdf->multiCell( 16, 10, utf8_decode('Descuento'),1 );
    $fpdf->SetY(145);
    $fpdf->SetX(177);
    $fpdf->multiCell( 16, 5, utf8_decode('Precio Unitario'),1 );
    $fpdf->SetY(145);
    $fpdf->SetX(193);
    $fpdf->multiCell( 15, 5, utf8_decode('Precio Total'),1 );             
    $desc = 0;
      for ($i=0; $i < sizeof($xmlAut->detalles->detalle); $i++) { 
      $fpdf->SetX(3);
      $fpdf->Cell(15, 6, utf8_decode($xmlAut->detalles->detalle[$i]->codigoInterno),1,0, 'C',0);                   
      $fpdf->Cell(13, 6, utf8_decode($xmlAut->detalles->detalle[$i]->codigoAdicional),1,0, 'C',0);
      $fpdf->Cell(14, 6, utf8_decode($xmlAut->detalles->detalle[$i]->cantidad),1,0, 'C',0); 
      $fpdf->Cell(71, 6, substr(utf8_decode($xmlAut->detalles->detalle[$i]->descripcion),0,46),1,0, 'L',0);                  
      $fpdf->Cell(15, 6, substr(utf8_decode($xmlAut->detalles->detalle[$i]->detallesAdicionales->detAdicional),0,9),1,0, 'C',0);                   
      $fpdf->Cell(15, 6, substr(utf8_decode($xmlAut->detalles->detalle[$i]->detallesAdicionales->detAdicional),0,9),1,0, 'C',0);                   
      $fpdf->Cell(15, 6, substr(utf8_decode($xmlAut->detalles->detalle[$i]->detallesAdicionales->detAdicional),0,9),1,0, 'C',0);                   
      $fpdf->Cell(16, 6, utf8_decode($xmlAut->detalles->detalle[$i]->precioUnitario),1,0, 'C',0);                  
      $fpdf->Cell(16, 6, utf8_decode($xmlAut->detalles->detalle[$i]->descuento),1,0, 'C',0);                   
      $fpdf->Cell(15, 6, utf8_decode($xmlAut->detalles->detalle[$i]->precioTotalSinImpuesto),1,1, 'C',0);                                  
      $desc = $desc + $xmlAut->detalles->detalle[$i]->descuento;
    }
    /////////////////pie de pagina//////////
    // $fpdf->SetFont('Amble-Regular','',9);              
    $fpdf->Ln(4);
    $fpdf->SetX(3);
    
      $fpdf->Rect($fpdf->GetX(), $fpdf->GetY(), 115, 0 , 'D');////3 INFO TRIBUTARIA
      $fpdf->Rect($fpdf->GetX() + 115, $fpdf->GetY(), 90, 0 , 'D');////3 INFO TRIBUTARIA
    $y =  $fpdf->GetY();
    $x =  $fpdf->GetX();
    $y_1 =  $fpdf->GetY();
    $x_1 =  $fpdf->GetX();
    $fpdf->Text($x_1 + 3, $y_1 + 3, utf8_decode('INFORMACIÓN ADICIONAL'));//informacion adicional  
    $fpdf->Ln(3);
    $y = $y + 6;    
    $tam = 0;
    $tam =  $tam + 6;
    for ($i=0; $i < sizeof($xmlAut->infoAdicional->campoAdicional); $i++) {     
      $fpdf->SetX(5);
      $fpdf->MultiCell( 105, 5, utf8_decode($xmlAut->infoAdicional->campoAdicional[$i]->attributes() . ' : ' . $xmlAut->infoAdicional->campoAdicional[$i]),0 );            
      $y = $y + 6;
      $tam =  $tam + 6;
    } 
    ////////////////////////////////////////////////////
    $fpdf->Rect($x_1, $y_1, 110, $tam , 'D');////4 TOTALES
    $y_1 = $y_1;
    $fpdf->SetY($y_1);
    $fpdf->SetX(115);
    $fpdf->multiCell( 70, 5, utf8_decode('SUBTOTAL 12 %'),1 );
    $fpdf->SetY($y_1);
    $fpdf->SetX(115 + 70);
    $fpdf->multiCell( 23, 5, $xmlAut->infoNotaCredito->totalConImpuestos->totalImpuesto[0]->baseImponible,1 , 'C');
    $y_1 = $y_1 + 5;
    $fpdf->SetY($y_1);
    $fpdf->SetX(115);
    $fpdf->multiCell( 70, 5, utf8_decode('SUBTOTAL 0 %'),1 );
    $fpdf->SetY($y_1);
    $fpdf->SetX(115 + 70);
    $fpdf->multiCell( 23, 5, $xmlAut->infoNotaCredito->totalConImpuestos->totalImpuesto[0]->baseImponible,1 , 'C');
    $y_1 = $y_1 + 5;
    $fpdf->SetY($y_1);
    $fpdf->SetX(115);
    $fpdf->multiCell( 70, 5, utf8_decode('SUBTOTAL No sujeto de IVA'),1 );
    $fpdf->SetY($y_1);
    $fpdf->SetX(115 + 70);
    $fpdf->multiCell( 23, 5, $xmlAut->infoNotaCredito->totalConImpuestos->totalImpuesto[1]->baseImponible,1, 'C' );
    $y_1 = $y_1 + 5;
    $fpdf->SetY($y_1);
    $fpdf->SetX(115);
    $fpdf->multiCell( 70, 5, utf8_decode('SUBTOTAL Exento de IVA'),1 );
    $fpdf->SetY($y_1);
    $fpdf->SetX(115 + 70);
    $fpdf->multiCell( 23, 5, $xmlAut->infoNotaCredito->totalConImpuestos->totalImpuesto[1]->baseImponible,1 , 'C');
    $y_1 = $y_1 + 5;
    $fpdf->SetY($y_1);
    $fpdf->SetX(115);
    $fpdf->multiCell( 70, 5, utf8_decode('SUBTOTAL SIN IMPUESTOS'),1 );
    $fpdf->SetY($y_1);
    $fpdf->SetX(115 + 70);
    $fpdf->multiCell( 23, 5, utf8_decode($xmlAut->infoNotaCredito->totalSinImpuestos),1 , 'C');
    $y_1 = $y_1 + 5;
    $fpdf->SetY($y_1);
    $fpdf->SetX(115);
    $fpdf->multiCell( 70, 5, utf8_decode('TOTAL DESCUENTOS'),1 );
    $fpdf->SetY($y_1);
    $fpdf->SetX(115 + 70);
    $fpdf->multiCell( 23, 5, $desc,1 , 'C');
    $y_1 = $y_1 + 5;
    $fpdf->SetY($y_1);
    $fpdf->SetX(115);
    $fpdf->multiCell( 70, 5, utf8_decode('ICE'),1);
    $fpdf->SetY($y_1);
    $fpdf->SetX(115 + 70);
    if($xmlAut->infoNotaCredito->ice == ''){
      $ice = '0.00';
    }else{
      $ice = $xmlAut->infoNotaCredito->ice;
    }
    $fpdf->multiCell( 23, 5, utf8_decode($ice),1 ,'C' );
    $y_1 = $y_1 + 5;
    $fpdf->SetY($y_1);
    $fpdf->SetX(115);
    $fpdf->multiCell( 70, 5, utf8_decode('IVA 12 %'),1 );
    $fpdf->SetY($y_1);
    $fpdf->SetX(115 + 70);
    $fpdf->multiCell( 23, 5,  $xmlAut->infoNotaCredito->totalConImpuestos->totalImpuesto[1]->valor,1 , 'C');
    $y_1 = $y_1 + 5;
    $fpdf->SetY($y_1);
    $fpdf->SetX(115);
    $fpdf->multiCell( 70, 5, utf8_decode('IRBPNR'),1 );
    $fpdf->SetY($y_1);
    $fpdf->SetX(115 + 70);
    $fpdf->multiCell( 23, 5, $xmlAut->infoNotaCredito->totalConImpuestos->totalImpuesto[1]->valor,1,'C' );
    $y_1 = $y_1 + 5;
    $fpdf->SetY($y_1);
    $fpdf->SetX(115);
    $fpdf->multiCell( 70, 5, utf8_decode('VALOR TOTAL'),1 );
    $fpdf->SetY($y_1);
    $fpdf->SetX(115 + 70);
    $fpdf->multiCell( 23, 5, utf8_decode($xmlAut->infoNotaCredito->valorModificacion),1 ,'C');

    ////////////////////////////////////////////

  }
  if($xmlAut->infoTributaria->codDoc == '05'){
    $doc = "NOTA DE DÉBITO";      
    $fpdf->Rect(3, 8, 100, 43 , 'D');//1 empresa imagen
      $fpdf->Text(5, 50, utf8_decode($xmlAut->infoTributaria->razonSocial));//NOMBRE proveedor
      $fpdf->Text(5, 50, utf8_decode($xmlAut->infoTributaria->razonSocial));//NOMBRE proveedor
      //////////////////////1/////////////////////////

      $fpdf->Rect(3, 53, 100, 45 , 'D');//2 datos personales 
    $fpdf->SetY(55);
    $fpdf->SetX(4);  
      $fpdf->multiCell( 98, 5, $xmlAut->infoTributaria->razonSocial,0 );//NOMBRE proveedor 
    $fpdf->SetY(66);
    $fpdf->SetX(4);  
    $fpdf->multiCell( 98, 5, utf8_decode('Dir Matriz: ' .$xmlAut->infoTributaria->dirMatriz),0 );//   direccion  
    $fpdf->Text(5, 86, utf8_decode('Contribuyente Especial Resolución Nro: '.$xmlAut->infoFactura->contribuyenteEspecial));//contribuyente
    $fpdf->Text(5, 93, utf8_decode('Obligado a llevar Contabilidad: '.$xmlAut->infoFactura->obligadoContabilidad));//obligado

    $est = $xmlAut->infoTributaria->estab . '-'. $xmlAut->infoTributaria->ptoEmi . '-'. $xmlAut->infoTributaria->secuencial;
    
      $fpdf->Rect(106, 8, 102, 90 , 'D');//3 DATOS EMPRESA
      $fpdf->Text(108, 15, 'RUC: '. $xmlAut->infoTributaria->ruc);//ruc
      $fpdf->Text(108, 22, $doc);//tipo comprobante
      $fpdf->Text(108, 29, 'No. ' . $est);//tipo comprobante
      $fpdf->Text(108, 36, utf8_decode('NÚMERO DE AUTORIZACIÓN'));//nro autorizacion TEXT
      $fpdf->Text(108, 43, $nroAut);//nro autorizacion
      $fpdf->Text(108, 50, utf8_decode('FECHA Y HORA DE AUTORIZACIÓN'));//fecha y hora de autorizacion TEXT
      $fpdf->Text(108, 57, $fechAut);//nro autorizacion
      $fpdf->Text(108, 64, utf8_decode('AMBIENTE: '. $ambi));//ambiente
      $fpdf->Text(108, 71, utf8_decode('EMISIÓN: '. $emi));//tipo de emision
      $fpdf->Text(108, 80, utf8_decode('CLAVE DE ACCESO: '));//tipo de emision
      $code_number = $xmlAut->infoTributaria->claveAcceso;//////cpdigo de barras    
    new barCodeGenrator($code_number,1,'temp.gif', 475, 60, true);///img codigo barras    
    $fpdf->Image('temp.gif',108,82,96,15);       
      /////////////////////////////Datos Factura///////////////////
      $fpdf->Rect(3, 101, 205, 40 , 'D');////3 INFO TRIBUTARIA
      $fpdf->SetY(101);
    $fpdf->SetX(3);
    $fpdf->multiCell( 130, 6, utf8_decode('Razón Social / Nombres y Apellidos: ' . $xmlAut->infoNotaDebito->razonSocialComprador ),0 );//NOMBRE cliente  
      $fpdf->Text(135, 105, utf8_decode('Identificación: ' . $xmlAut->infoNotaDebito->identificacionComprador ));//ruc cliente
      $fpdf->Text(5, 117, utf8_decode('Fecha de Emisión: ' . $xmlAut->infoNotaDebito->fechaEmision ));//fecha de emision cliente     
    $fpdf->Line(5,122,205,122);
    //01 factura ver en la base de datos $xmlAut->infoNotaDebito->codDocModificado
    $fpdf->Text(5, 128, utf8_decode('Comprobante que se modifica: ' .  'FACTURA'));//ruc cliente
    $fpdf->Text(150, 128, utf8_decode($xmlAut->infoNotaDebito->numDocModificado ));//ruc cliente
      $fpdf->Text(5, 136, utf8_decode('Fecha Emisión: ' . $xmlAut->infoNotaDebito->fechaEmisionDocSustento));//ruc cliente
      //detalles nota debito
      $fpdf->SetFont('Amble-Regular','',12);               
      $fpdf->SetY(141);
    $fpdf->SetX(3);
    $fpdf->multiCell( 127, 8, utf8_decode('RAZÓN DE LA MODIFICACIÓN'),1,'C' );
    $fpdf->SetY(141);
    $fpdf->SetX(130);
    $fpdf->multiCell( 78, 8, utf8_decode('VALOR DE LA MODIFICACIÓN'),1, 'C' );
    // $fpdf->SetFont('Amble-Regular','',9);               
    for ($i=0; $i < sizeof($xmlAut->motivos->motivo); $i++) { 
      $fpdf->SetX(3);
      $fpdf->Cell(127, 6, utf8_decode($xmlAut->motivos->motivo[$i]->razon),1,0, 'L',0);                  
      $fpdf->Cell(78, 6, utf8_decode($xmlAut->motivos->motivo[$i]->valor),1,0, 'R',0);                                         
    }
      /////////////////pie de pagina//////////
    // $fpdf->SetFont('Amble-Regular','',9);              
    $fpdf->Ln(8);
    $fpdf->SetX(3);
    
      $fpdf->Rect($fpdf->GetX(), $fpdf->GetY(), 115, 0 , 'D');////3 INFO TRIBUTARIA
      $fpdf->Rect($fpdf->GetX() + 115, $fpdf->GetY(), 90, 0 , 'D');////3 INFO TRIBUTARIA
    $y =  $fpdf->GetY();
    $x =  $fpdf->GetX();
    $y_1 =  $fpdf->GetY();
    $x_1 =  $fpdf->GetX();
    $fpdf->Text('', '', utf8_decode('INFORMACIÓN ADICIONAL'));//informacion adicional  
    $fpdf->Ln(3);
    $y = $y + 6;    
    $tam = 0;
    $tam =  $tam + 6;
    for ($i=0; $i < sizeof($xmlAut->infoAdicional->campoAdicional); $i++) {     
      $fpdf->SetX(5);
      $fpdf->MultiCell( 105, 5, utf8_decode($xmlAut->infoAdicional->campoAdicional[$i]->attributes() . ' : ' . $xmlAut->infoAdicional->campoAdicional[$i]),0 );            
      $y = $y + 6;
      $tam =  $tam + 6;
    } 
    $fpdf->Rect($x_1, $y_1, 110, $tam , 'D');////4 TOTALES
    $y_1 = $y_1;
    $fpdf->SetY($y_1);
    $fpdf->SetX(115);
    $fpdf->multiCell( 70, 5, utf8_decode('SUBTOTAL 12 %'),1 );
    $fpdf->SetY($y_1);
    $fpdf->SetX(115 + 70);
    $fpdf->multiCell( 23, 5, $xmlAut->infoNotaDebito->totalSinImpuestos,1 );
    $y_1 = $y_1 + 5;
    $fpdf->SetY($y_1);
    $fpdf->SetX(115);
    $fpdf->multiCell( 70, 5, utf8_decode('SUBTOTAL 0 %'),1 );
    $fpdf->SetY($y_1);
    $fpdf->SetX(115 + 70);
    $fpdf->multiCell( 23, 5, '0.00',1 );
    $y_1 = $y_1 + 5;
    $fpdf->SetY($y_1);
    $fpdf->SetX(115);
    $fpdf->multiCell( 70, 5, utf8_decode('SUBTOTAL No sujeto de IVA'),1 );
    $fpdf->SetY($y_1);
    $fpdf->SetX(115 + 70);
    $fpdf->multiCell( 23, 5, '0.00',1 );
    $y_1 = $y_1 + 5;
    $fpdf->SetY($y_1);
    $fpdf->SetX(115);
    $fpdf->multiCell( 70, 5, utf8_decode('SUBTOTAL Exento IVA'),1 );
    $fpdf->SetY($y_1);
    $fpdf->SetX(115 + 70);
    $fpdf->multiCell( 23, 5, utf8_decode($xmlAut->infoNotaDebito->totalSinImpuestos),1 );
    $y_1 = $y_1 + 5;
    $fpdf->SetY($y_1);
    $fpdf->SetX(115);
    $fpdf->multiCell( 70, 5, utf8_decode('SUBTOTAL SIN IMPUESTOS'),1 );
    $fpdf->SetY($y_1);
    $fpdf->SetX(115 + 70);
    $fpdf->multiCell( 23, 5, utf8_decode($xmlAut->infoNotaDebito->totalSinImpuestos),1 );
    $y_1 = $y_1 + 5;
    $fpdf->SetY($y_1);
    $fpdf->SetX(115);    
    $fpdf->multiCell( 70, 5, utf8_decode('VALOR ICE'),1 );
    $fpdf->SetY($y_1);
    $fpdf->SetX(115 + 70);
    if($xmlAut->infoNotaDebito->ice == ''){
      $ice = '0.00';
    }else{
      $ice = $xmlAut->infoNotaDebito->ice;
    }
    $fpdf->multiCell( 23, 5, utf8_decode($ice),1 );
    $y_1 = $y_1 + 5;
    $fpdf->SetY($y_1);
    $fpdf->SetX(115);
    $fpdf->multiCell( 70, 5, utf8_decode('IVA 12 %'),1 );
    $fpdf->SetY($y_1);
    $fpdf->SetX(115 + 70);
    $fpdf->multiCell( 23, 5, $xmlAut->infoNotaDebito->impuestos->impuesto->valor ,1 );//REVISAR CON OTROS DATOS
    $y_1 = $y_1 + 5;
    $fpdf->SetY($y_1);
    $fpdf->SetX(115);        
    $fpdf->multiCell( 70, 5, utf8_decode('VALOR TOTAL'),1 );
    $fpdf->SetY($y_1);
    $fpdf->SetX(115 + 70);
    $fpdf->multiCell( 23, 5, utf8_decode($xmlAut->infoNotaDebito->valorTotal),1 );
    

  }
  $filename=public_path().'/facturas/'.$iduser.'/'.$idfac.".pdf";
  $fpdf->Output($filename,'F');

}

}

?>