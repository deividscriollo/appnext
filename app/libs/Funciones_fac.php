<?php

namespace App\libs;
use App\facturas;
use App\FacturasRechazadas;
use App\Empresas;
use App\PasswrdsE;
use App\Proveedores;
use App\regpersona_empresas;
use Illuminate\Http\Request;

use App\Http\Requests;
use GuzzleHttp\Client;
use App\libs\Funciones;
//-------------------------------- extras --------------
use Mail;
use File;
use Storage;
use Zipper;

/* --------------------------------------- Funciones --------------------------------*/
class Funciones_fac
{
    
    function __construct()
    {
        set_time_limit(3000);
        date_default_timezone_set('America/Guayaquil'); //puedes cambiar Guayaquil por tu Ciudad
        setlocale(LC_TIME, 'spanish');
        $this->persona_q_registra=new regpersona_empresas();
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

public function leer($usuario,$pass,$iduser,$idsucursal){
        
/* connect to gmail with your credentials */
$hostname = '{s411b.panelboxmanager.com:993/imap/ssl/novalidate-cert}INBOX';
$username = $usuario;
$password = $pass;

/* try to connect */
$inbox = imap_open($hostname, $username, $password) or die('No se puede conectar a Nextbook: ' .  print_r(imap_errors(), true));

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
                 $res_xml = $this->save_xml_mail($attachment['attachment'],$username,$filename,$idsucursal);
                  // print_r($res_xml);
                }
                if (strtolower($file_ext[1]) == "zip") {
                 // echo "ZIP";
                 $res_zip = $this->save_zip_mail($attachment['attachment'],$username,$filename,$idsucursal);
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
$res = $client->request('POST', 'http://apiservicios.nextbook.ec/public/estado_factura', [
    'json' => ["clave"=>(string)$clave_acceso]
]);

$respuesta= json_decode($res->getBody(), true);
return $respuesta;
}


function save_xml_mail($xmlmaster,$emailuser,$doc_name,$idsucursal){
        $tblFacturas=new Facturas();
        $tblFacturas_rechazadas=new FacturasRechazadas();
        $funciones=new Funciones();
        $empresas=new Empresas();
        $passE=new PasswrdsE();
        $datosPass=$passE->select('id_user')->where('email','=',$emailuser)->get();
        $doc_name=$doc_name;
//  if (!is_dir("facturas/".$datosPass[0]['id_user'])) {
//   $old = umask(0);
// mkdir("facturas/".$datosPass[0]['id_user'], 0777);
// umask($old);    
//     }
        $xmlData_sub = new \SimpleXMLElement($xmlmaster);
if ($xmlData_sub->comprobante) {
        $xmlDatamaster = $this->uncdata($xmlData_sub->comprobante);
        $xmlDatamaster=str_replace('&lt;', '<', $xmlDatamaster);
        $xmlDatamaster=str_replace('&gt;', '>', $xmlDatamaster);
        $xmlDatamaster=str_replace('&quot;', '"', $xmlDatamaster);
        $xmlDatamaster=str_replace('&amp;', '&', $xmlDatamaster);
        $file_xml = new \SimpleXMLElement($xmlDatamaster);
}else{
        $file_xml = new \SimpleXMLElement($xmlmaster);     
}
$clave_acceso = $file_xml->infoTributaria->claveAcceso;
$ambiente = $file_xml->infoTributaria->ambiente;
$tipo_doc=$file_xml->infoTributaria[0]->codDoc;
$nombre_comercial = $file_xml->infoTributaria->nombreComercial;
$dir_matriz =$file_xml->infoTributaria->dirMatriz;
$ruc_comercial = $file_xml->infoTributaria->ruc;

$respuesta=$this->verificar_autorizacion($clave_acceso);
 //print_r($respuesta['respuesta']) ;

if (count($respuesta['respuesta']['autorizaciones'])!=0) {
    $estado=$respuesta['respuesta']['autorizaciones']['autorizacion']['estado'];

            if($estado == 'AUTORIZADO') {

             switch ((string)$tipo_doc) {
    //****************************************************** NOTA DE CREDITO
  case '04':
                  
            $xmlComp = new \SimpleXMLElement($respuesta['respuesta']['autorizaciones']['autorizacion']['comprobante']);
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
                  
            $xmlComp = new \SimpleXMLElement($respuesta['respuesta']['autorizaciones']['autorizacion']['comprobante']);
            $email = $this->getmail($xmlComp);
            $fecha_aut = $xmlComp->infoFactura->fechaEmision;                   
            $razon_social = $xmlComp->infoFactura->razonSocial;
            $dir_establecimiento =$xmlComp->infoFactura->dirEstablecimiento;
            $cod_doc = $xmlComp->infoFactura->codDoc;
            //-------------------------------------------------- totales ---------------------------
            $total = $xmlComp->infoFactura->importeTotal;
            $totales=$this->get_totales($xmlmaster);
            $subtotal_12=$totales['subtotal_12'];
            $subtotal_0=$totales['subtotal_0'];
            $subtotal_no_sujeto=$totales['subtotal_no_sujeto'];
            $subtotal_exento_iva=$totales['subtotal_exento_iva'];
            $subtotal_sin_impuestos=$totales['subtotal_sin_impuestos'];
            $descuento=$totales['descuento'];
            $ice=$totales['ice'];
            $iva_12=$totales['iva_12'];
            $propina=$totales['propina'];
            //--------------------------------------------- fin ----------------------------------------
            $datos = explode('@', $email);
            $ruc = $datos[0];         
            $identificacionComprador= $xmlComp->infoFactura->identificacionComprador;    
            //******************************** Datos Factura/************************

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
              $res =$tblFacturas->select('id_factura')->where('clave_acceso','=',(string)$clave_acceso)->where('id_empresa','=',$datosPass[0]['id_user'])->get();
              if(count($res) == 0 ){
                $tblFacturas->id_factura = $id_factura;
                $tblFacturas->num_factura = $num_fac;
                $tblFacturas->nombre_comercial = $nombre_comercial;
                $tblFacturas->Ruc_prov = $ruc_comercial;
                $tblFacturas->fecha_emision = $date_fe;
                $tblFacturas->clave_acceso = (string)$clave_acceso;
                $tblFacturas->ambiente = (string)$ambiente;
                $tblFacturas->tipo_doc = $tipo_doc;
                $tblFacturas->tipo_consumo = 'Sin-Asignar';
                $tblFacturas->total = $total;
                $tblFacturas->subtotal_12= $subtotal_12;
                $tblFacturas->subtotal_0= $subtotal_0;
                $tblFacturas->subtotal_no_sujeto= $subtotal_no_sujeto;
                $tblFacturas->subtotal_exento_iva= $subtotal_exento_iva;
                $tblFacturas->subtotal_sin_impuestos= $subtotal_sin_impuestos;
                $tblFacturas->descuento= $descuento;
                $tblFacturas->ice= $ice;
                $tblFacturas->iva_12= $iva_12;
                $tblFacturas->propina= $propina;
                $tblFacturas->estado= 1;
                $tblFacturas->contenido_fac = $respuesta['respuesta']['autorizaciones']['autorizacion']['comprobante'];
                $tblFacturas->id_empresa = $datosPass[0]['id_user'];
                $tblFacturas->id_sucursal = $idsucursal;
                $save=$tblFacturas->save();
                if ($save) {
                  // echo "OK XML--";
                  $url_destination_xml = "/".$datosPass[0]['id_user']."/".$id_factura.'.xml';
                  Storage::disk('facturas')->put($url_destination_xml, $xmlmaster);
                  // File::put($url_destination_xml,$xmlmaster);
                 
                  // $fp_fac = fopen($url_destination_xml, "wr+");
                  // fwrite($fp_fac, $xmlmaster);
                  // fclose($fp_fac);
                  //------------------------------------------------ Enviar Correo ---------------------------------------------
                  $data = [
                    'clave_acceso'=>(string)$clave_acceso,
                    'razon_social'=>$razon_social,
                    'fecha_emision'=>$fecha_aut,
                    'total'=>$total,
                    'nombre_comercial'=>$nombre_comercial
                  ];

                  $this->send_notificacion($datosPass[0]['id_user'],$data);      

                  // return array('valid' => 'true', 'methods' => 'full');
                }
                //----------------------------------------------- guardar proveedor ------------------
                $this->save_proveedor($ruc_comercial,$razon_social,$nombre_comercial,$dir_matriz,$dir_establecimiento,$datosPass[0]['id_user']);
                    }else
                        return array('respuesta' => false, 'error' => '5','methods' => 'cla-acc-existente'); // ---------- valido y listo para procesar       
                }else 
                    $this->save_fac_rechazada($xmlmaster,$emailuser,(string)$clave_acceso,'ruc-no-perteneciente');
                    // return array('respuesta' => false, 'error' => '1', 'methods' => 'ruc-no-perteneciente'); // ---------- ruc no perteneciente a esta cuenta
            }else
            $this->save_fac_rechazada($xmlmaster,$emailuser,(string)$clave_acceso,'Documento-no-autorizado');
                // return array('respuesta' => false, 'error' => '2', 'methods' => 'no-autorizado'); // ------ clave de acceso no autorizado
        }else
        $this->save_fac_rechazada($xmlmaster,$emailuser,(string)$clave_acceso,'registro-no-existente-sri');
            // return array('respuesta' => false, 'error' => '4', 'methods' => 'registro-no-existente-sri'); // ------ no disponible 
    }

function send_notificacion($id_user,$datos){
  $datos_representante=$this->persona_q_registra->where('id_empresa',$id_user)->first();
  $correo_enviar=$datos_representante->correo;
        Mail::send('email_factura_inbox', $datos, function($message)use ($correo_enviar)
            {
                $message->from("registro@facturanext.com",'Nextbook | Nueva Factura');
                $message->to($correo_enviar)->subject('Nueva Factura');
            });
}

function save_zip_mail($xmlmaster,$emailuser,$doc_name,$idsucursal){
  $funciones=new Funciones();
  $passE=new PasswrdsE();
  $tblFacturas=new Facturas();
  $tblFacturas_rechazadas=new FacturasRechazadas();
  $datosPass=$passE->select('id_user')->where('email','=',$emailuser)->get();

 // if (!is_dir("facturas/".$datosPass[0]['id_user'])) {
 //    mkdir("facturas/".$datosPass[0]['id_user']);      
 //    }
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
          if (count($respuesta['respuesta']['autorizaciones']) != 0) {
          $estado = $respuesta['respuesta']['autorizaciones']['autorizacion']['estado'];
          if($estado == 'AUTORIZADO') {

switch ((string)$tipo_doc) {
    //****************************************************** NOTA DE CREDITO
  case '04':
                  
            $xmlComp = new \SimpleXMLElement($respuesta['respuesta']['autorizaciones']['autorizacion']['comprobante']);
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
                  
            $xmlComp = new \SimpleXMLElement($respuesta['respuesta']['autorizaciones']['autorizacion']['comprobante']);
            $email = $this->getmail($xmlComp);
            $fecha_aut = $xmlComp->infoFactura->fechaEmision;
            $dir_establecimiento =$xmlComp->infoFactura->dirEstablecimiento;                   
            $razon_social = $xmlComp->infoFactura->razonSocial;
            $cod_doc = $xmlComp->infoFactura->codDoc;
            //-------------------------------------------------- totales ---------------------------
            $total = $xmlComp->infoFactura->importeTotal;
            $totales=$this->get_totales($buf);
            $subtotal_12=$totales['subtotal_12'];
            $subtotal_0=$totales['subtotal_0'];
            $subtotal_no_sujeto=$totales['subtotal_no_sujeto'];
            $subtotal_exento_iva=$totales['subtotal_exento_iva'];
            $subtotal_sin_impuestos=$totales['subtotal_sin_impuestos'];
            $descuento=$totales['descuento'];
            $ice=$totales['ice'];
            $iva_12=$totales['iva_12'];
            $propina=$totales['propina'];
            //--------------------------------------------- fin ----------------------------------------
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
                $tblFacturas->tipo_consumo = 'Sin-Asignar';
                $tblFacturas->total = $total;
                $tblFacturas->subtotal_12= $subtotal_12;
                $tblFacturas->subtotal_0= $subtotal_0;
                $tblFacturas->subtotal_no_sujeto= $subtotal_no_sujeto;
                $tblFacturas->subtotal_exento_iva= $subtotal_exento_iva;
                $tblFacturas->subtotal_sin_impuestos= $subtotal_sin_impuestos;
                $tblFacturas->descuento= $descuento;
                $tblFacturas->ice= $ice;
                $tblFacturas->iva_12= $iva_12;
                $tblFacturas->propina= $propina;
                $tblFacturas->estado= 1;
                $tblFacturas->contenido_fac = $respuesta['respuesta']['autorizaciones']['autorizacion']['comprobante'];
                $tblFacturas->id_empresa = $datosPass[0]['id_user'];
                $tblFacturas->id_empresa = $datosPass[0]['id_user'];
                $save=$tblFacturas->save();
                if ($save) {
                  // echo "OK ZIP--";
                  // File::put($path,$contents);
                  $url_destination_xml = "/".$datosPass[0]['id_user']."/".$id_factura.'.xml';
                  Storage::disk('facturas')->put($url_destination_xml, $buf);                 
                  // $fp_fac = fopen($url_destination_xml, "wr+");
                  // fwrite($fp_fac, $buf);
                  // fclose($fp_fac);
                  // return array('valid' => 'true', 'methods' => 'full');
                }

              }
            }else
            return array('respuesta' => false, 'error' => '5','methods' => 'cla-acc-existente'); // ---------- valido y listo para procesar   
            }else
             $this->save_fac_rechazada($buf,$emailuser,'no-definido','no-autorizado');
            // return array('respuesta' => false, 'error' => '2', 'methods' => 'no-autorizado'); // ------ clave de acceso no autorizado
        }else
        $this->save_fac_rechazada($buf,$emailuser,'no-definido','registro-no-existente-sri');
      }else
      $this->save_fac_rechazada("",$emailuser,'no-definido','Documento-Vacio'); //************** si el XML esta vacio
          // return array('respuesta' => false, 'error' => '4', 'methods' => 'registro-no-existente-sri'); // ------ no disponible 
           }//*************************************************** FIN si el archivo es XML******************************

        }
      }//**********while
    }/// *****if ZIP
    zip_close($zip);
    // echo "<br>".$url_destination;
    unlink($url_destination);
  }

function save_fac_rechazada($xmlmaster,$emailuser,$clave_acceso,$razon,$id_sucursal){
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
    $tblFacturas_rechazadas->tipo_consumo = 'Sin-Asignar';
    $tblFacturas_rechazadas->razon_rechazo = $razon;
    $tblFacturas_rechazadas->contenido_fac = $xmlmaster;
    $tblFacturas_rechazadas->id_empresa = $datosPass[0]['id_user'];
    $tblFacturas_rechazadas->id_sucursal = $id_sucursal;
    $save=$tblFacturas_rechazadas->save();

}

function save_proveedor($ruc,$razon_social,$nombre_comercial,$dir_matriz,$dir_establecimiento,$id_empresa){
$tabla=new Proveedores();
$sql=$tabla->where('ruc','=',$ruc)->where('id_empresa','=',$id_empresa)->get();
    if (count($sql)==0) {
    $funciones = new Funciones();
    $tabla->id = $funciones->generarId();
    $tabla->razon_social = $razon_social;
    $tabla->nombre_comercial = $nombre_comercial;
    $tabla->ruc = $ruc;
    $tabla->dir_matriz = $dir_matriz;
    $tabla->dir_establecimiento = $dir_establecimiento;
    $tabla->id_empresa = $id_empresa;
    $tabla->estado = 1;
    $resultado = $tabla->save();
  }
}

function save_xml_file($xmlmaster,$emailuser,$doc_name,$tipo_consumo,$id_sucursal){
        $tblFacturas=new Facturas();
        $tblFacturas_rechazadas=new FacturasRechazadas();
        $funciones=new Funciones();
        $empresas=new Empresas();
        $passE=new PasswrdsE();
        $datosPass=$passE->select('id_user')->where('email','=',$emailuser)->get();
        $doc_name=$doc_name;
 // if (!is_dir("facturas/".$datosPass[0]['id_user'])) {
 //    mkdir("facturas/".$datosPass[0]['id_user']);      
 //    }
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
$dir_matriz =$file_xml->infoTributaria->dirMatriz;
$ruc_comercial = $file_xml->infoTributaria->ruc;
$razon_social = $file_xml->infoTributaria->razonSocial;


$respuesta=$this->verificar_autorizacion($clave_acceso);
// print_r($respuesta) ;

if (count($respuesta['respuesta']['autorizaciones'])!=0) {
    $estado=$respuesta['respuesta']['autorizaciones']['autorizacion']['estado'];

            if($estado == 'AUTORIZADO') {

             switch ((string)$tipo_doc) {
    //****************************************************** NOTA DE CREDITO
  case '04':
                  
            $xmlComp = new \SimpleXMLElement($respuesta['respuesta']['autorizaciones']['autorizacion']['comprobante']);
            $email = $xmlComp->infoAdicional->campoAdicional;
            $fecha_aut = $xmlComp->infoNotaCredito->fechaEmision;                   
            $razon_social = $xmlComp->infoNotaCredito->razonSocial;
            $dir_establecimiento =$xmlComp->infoNotaCredito->dirEstablecimiento;
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
            $xmlComp = new \SimpleXMLElement($respuesta['respuesta']['autorizaciones']['autorizacion']['comprobante']);
            $email = $this->getmail($xmlComp);
            $fecha_aut = $xmlComp->infoFactura->fechaEmision;                   
            //$razon_social = $xmlComp->infoFactura->razonSocial;
            $dir_establecimiento =$xmlComp->infoFactura->dirEstablecimiento;
            $cod_doc = $xmlComp->infoFactura->codDoc;
            //-------------------------------------------------- totales ---------------------------
            $total = $xmlComp->infoFactura->importeTotal;
            $totales=$this->get_totales($xmlmaster);
            $subtotal_12=$totales['subtotal_12'];
            $subtotal_0=$totales['subtotal_0'];
            $subtotal_no_sujeto=$totales['subtotal_no_sujeto'];
            $subtotal_exento_iva=$totales['subtotal_exento_iva'];
            $subtotal_sin_impuestos=$totales['subtotal_sin_impuestos'];
            $descuento=$totales['descuento'];
            $ice=$totales['ice'];
            $iva_12=$totales['iva_12'];
            $propina=$totales['propina'];
            //--------------------------------------------- fin ----------------------------------------
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
              $res =$tblFacturas->select('id_factura')->where('clave_acceso','=',(string)$clave_acceso)->where('id_empresa','=',$datosPass[0]['id_user'])->get();
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
                $tblFacturas->subtotal_12= $subtotal_12;
                $tblFacturas->subtotal_0= $subtotal_0;
                $tblFacturas->subtotal_no_sujeto= $subtotal_no_sujeto;
                $tblFacturas->subtotal_exento_iva= $subtotal_exento_iva;
                $tblFacturas->subtotal_sin_impuestos= $subtotal_sin_impuestos;
                $tblFacturas->descuento= $descuento;
                $tblFacturas->ice= $ice;
                $tblFacturas->iva_12= $iva_12;
                $tblFacturas->propina= $propina;
                $tblFacturas->estado= 1;
                $tblFacturas->contenido_fac = $respuesta['respuesta']['autorizaciones']['autorizacion']['comprobante'];
                $tblFacturas->id_empresa = $datosPass[0]['id_user'];
                $tblFacturas->id_empresa = $datosPass[0]['id_user'];
                $tblFacturas->id_sucursal = $id_sucursal;
                $save=$tblFacturas->save();
                if ($save) {
                  // echo "OK XML--";
                  $url_destination_xml = "/".$datosPass[0]['id_user']."/".$id_factura.'.xml'; 
                  Storage::disk('facturas')->put($url_destination_xml, $xmlmaster);                
                  // $fp_fac = fopen($url_destination_xml, "wr+");
                  // fwrite($fp_fac, $xmlmaster);
                  // fclose($fp_fac);
                  // return array('valid' => 'true', 'methods' => 'full');
                }
                //----------------------------------------------- guardar proveedor ------------------
                $this->save_proveedor($ruc_comercial,$razon_social,$nombre_comercial,$dir_matriz,$dir_establecimiento,$datosPass[0]['id_user']);
                 //------------------------------------------------ Enviar Correo ---------------------------------------------
                  $data = [
                    'clave_acceso'=>(string)$clave_acceso,
                    'razon_social'=>$razon_social,
                    'fecha_emision'=>$fecha_aut,
                    'total'=>$total,
                    'nombre_comercial'=>$nombre_comercial
                  ];
                $this->send_notificacion($datosPass[0]['id_user'],$data);
                    }else
                        return array('respuesta' => false, 'error' => '5','methods' => 'cla-acc-existente'); // ---------- valido y listo para procesar       
                }else 
                    $this->save_fac_rechazada($xmlmaster,$emailuser,(string)$clave_acceso,'ruc-no-perteneciente');
                    // return array('respuesta' => false, 'error' => '1', 'methods' => 'ruc-no-perteneciente'); // ---------- ruc no perteneciente a esta cuenta
            }else
            $this->save_fac_rechazada($xmlmaster,$emailuser,(string)$clave_acceso,'Documento-no-autorizado');
                // return array('respuesta' => false, 'error' => '2', 'methods' => 'no-autorizado'); // ------ clave de acceso no autorizado
        }else
        $this->save_fac_rechazada($xmlmaster,$emailuser,(string)$clave_acceso,'registro-no-existente-sri');
            // return array('respuesta' => false, 'error' => '4', 'methods' => 'registro-no-existente-sri'); // ------ no disponible 
    }
//----------------------------------------------- Generar codigo de barras ---------------------------------------
  public function gen_codigo_barras($clave_acceso){
    new barCodeGenrator($clave_acceso,1,'temp.gif', 475, 60, true);///img codigo barras  
  }
//----------------------------------------------- obtener totales ------------------------------------------------
  public function get_totales($xml){

        $xmlData = new \SimpleXMLElement($xml);
        if (count($xmlData->infoTributaria->ambiente)!=0) {
        $xmlData = new \SimpleXMLElement($xml);
        $tipoambiente=(string)$xmlData->infoTributaria->ambiente;
        }else{
        $xmlDatamaster=$xmlData->comprobante;
        $xmlDatamaster=str_replace(array('<![CDATA[',']]>'), '', $xmlDatamaster);
        $xmlData = new \SimpleXMLElement($xmlDatamaster);
        }

     $tam = sizeof($xmlData->infoFactura->totalConImpuestos->totalImpuesto);
//---------------------------------------- base 12-------------------------------------------
    $cont = 0;
    for($i = 0; $i < $tam;$i++){
      if($xmlData->infoFactura->totalConImpuestos->totalImpuesto[$i]->codigoPorcentaje == 2){
        $subtotal_12=(string)$xmlData->infoFactura->totalConImpuestos->totalImpuesto[$i]->baseImponible;
        $cont = 1;
      }   
    }
     if($cont == 0){
        $subtotal_12="0.00";
    }
    //---------------------------------------- base 0-------------------------------------------
     $cont = 0;                                        
    for($i = 0; $i < $tam;$i++){
      if($xmlData->infoFactura->totalConImpuestos->totalImpuesto[$i]->codigoPorcentaje == 0){
        $subtotal_0=(string)$xmlData->infoFactura->totalConImpuestos->totalImpuesto[$i]->baseImponible;
        $cont = 1;
      }     
    }
    if($cont == 0){
    $subtotal_0="0.00";
    } 
    //------------------------- No sujeto IVA ------------------------------------------------
    $cont = 0;              
    for($i = 0; $i < $tam;$i++){
      if($xmlData->infoFactura->totalConImpuestos->totalImpuesto[$i]->codigoPorcentaje == 6){
        $subtotal_no_sujeto=(string)$xmlData->infoFactura->totalConImpuestos->totalImpuesto[$i]->baseImponible;      
        $cont = 1;
      }     
    }   
    if($cont == 0){
      $subtotal_no_sujeto="0.00";
    }
    //------------------------------------------- Exento de IVA ------------------------------
    $cont = 0;                        
    for($i = 0; $i < $tam;$i++){
      if($xmlData->infoFactura->totalConImpuestos->totalImpuesto[$i]->codigoPorcentaje == 7){
        $subtotal_exento_iva=(string)$xmlData->infoFactura->totalConImpuestos->totalImpuesto[$i]->baseImponible;
        $cont = 1;
      }     
    }
    if($cont == 0){
        $subtotal_exento_iva="0.00";
      $cont = 1;
    }
    //----------------------------------------------- Subtotal sin inpuestos ----------------------------
    $cont = 0;                          
    $subtotal_sin_impuestos=(string)$xmlData->infoFactura->totalSinImpuestos;

    //----------------------------------------------- Descuentos ----------------------------                                   
    $descuentos=(string)$xmlData->infoFactura->totalDescuento;                                          
    //----------------------------------------------------- ICE ------------------------------                                     
    for($i = 0; $i < $tam;$i++){
      if($xmlData->infoFactura->totalConImpuestos->totalImpuesto[$i]->codigoPorcentaje >= 3000 && $xmlData->infoFactura->totalConImpuestos->totalImpuesto[$i]->codigoPorcentaje < 4000 ){
        $ice=(string)$xmlData->infoFactura->totalConImpuestos->totalImpuesto[$i]->valor;
        $cont = 1;
      }     
    }   
    if($cont == 0){
      $ice="0.00";
      $cont = 1;  
    }
//------------------------------------------ IVA 12 -----------------------------
    $cont = 0;                                    
    for($i = 0; $i < $tam;$i++){
      if($xmlData->infoFactura->totalConImpuestos->totalImpuesto[$i]->codigoPorcentaje == 2){
        $iva_12=(string)$xmlData->infoFactura->totalConImpuestos->totalImpuesto[$i]->valor;
        $cont = 1;
      }     
    }
    if($cont == 0){
      $iva_12="0.00";
      $cont = 1;
    }
        //------------------------------------------------------ PROPINA -------------------------------
        $propina=(string)$xmlData->infoFactura->propina;
        //--------------------------------------- VALOR TOTAL ---------------------
        $valor_total=(string)$xmlData->infoFactura->importeTotal;

        $totales=[
        'subtotal_12'=>$subtotal_12,
        'subtotal_0'=>$subtotal_0,
        'subtotal_no_sujeto'=>$subtotal_no_sujeto,
        'subtotal_exento_iva'=>$subtotal_exento_iva,
        'subtotal_sin_impuestos'=>$subtotal_sin_impuestos,
        'descuento'=> $descuentos,
        'ice'=> $ice,
        'iva_12'=> $iva_12,
        'propina'=>$propina,
        'valor_total'=>$valor_total];
        return $totales;

  }


}

?>