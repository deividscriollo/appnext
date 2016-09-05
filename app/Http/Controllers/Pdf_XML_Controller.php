<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
// -------------------------- Autenticacion ----------
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
//------------------------------ Funciones -----------
use App\libs\Funciones_fac;
//------------------------------- extras ---------------
use GuzzleHttp\Client;
use File;
use Storage;
use Zipper;


class Pdf_XML_Controller extends Controller
{
    
    public function __construct(Request $request)
        {
        // ------------------------------------ Modelos ----------------------
        // $this->tablaPersonareg = new regpersona_empresas();
        // $this->tablaPass = new PasswrdsE();
        // --------------------------------------- Autenticacion --------------------
        $this->user = JWTAuth::parseToken()->authenticate();
        //----------------------------------- Funciones -------------------------------
        $this->Funciones_fac=new Funciones_fac();
        //------------------------------------ paths -------------------------------
        $this->storagePath  = Storage::disk('facturas')->getDriver()->getAdapter()->getPathPrefix();
        }

    public function generar_pdf(Request $request) 
    {
        $iddocumento=$request->input('iddocumento');
        if (!File::exists($this->storagePath.'/'.$this->user['id_user'].'/'.$iddocumento.'.pdf'))
        {
        $data = $this->getData($iddocumento);
        $date = date('Y-m-d');
        $invoice = "2222";
        $view =  \View::make('invoice', compact('data', 'date', 'invoice'))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->save($this->storagePath.'/'.$this->user['id_user'].'/'.$iddocumento.'.pdf');
        }
        return response()->json(['respuesta'=>true,'url'=>'http://192.168.0.101/appnext/storage/app/facturas/'.$this->user['id_user'].'/'.$iddocumento.'.pdf']);
    }
 
    public function getData($iddocumento) 
    {
        //-------------------------------------------- CONSULTAR AUTORIZACION ---------------------------------------
            // $client = new Client;
            // $res = $client->request('POST', 'http://localhost/appserviciosnext/public/estado_factura', ['json' => ['token'=>$token,'clave' => $iddocumento]]);
            // $respuesta = json_decode($res->getBody() , true);

        // -------------------------------------------GENERAR PDF ---------------------------------------------------
        $xml = $this->storagePath.'/'.$this->user['id_user'].'/'.$iddocumento.".xml";
        $xml = file_get_contents($xml);

        $xmlData = new \SimpleXMLElement($xml);
        if (count($xmlData->infoTributaria->ambiente)!=0) {
        $xmlData = new \SimpleXMLElement($xml);
        $tipoambiente=(string)$xmlData->infoTributaria->ambiente;
        }else{
        $xmlDatamaster=$xmlData->comprobante;
        $xmlDatamaster=str_replace(array('<![CDATA[',']]>'), '', $xmlDatamaster);
        $xmlData = new \SimpleXMLElement($xmlDatamaster);
        }

        $tipoambiente=(string)$xmlData->infoTributaria->ambiente;
        $tipoambiente='2'; 
        switch ($tipoambiente) {
                case '2':
                $ambiente="PRODUCCION";
                break;
                case '1':
                $ambiente="PRUEBAS";
                break;
        }

if($xmlData->infoTributaria->tipoEmision == 1){
    $emision = 'Normal';  
  }else{
    $emision = 'Indisponibilidad del Sistema';  
  }
  //----------------------------------------------------------- Generar codigo de barras -----------------------
    $code_number = $xmlData->infoTributaria->claveAcceso;
    $this->Funciones_fac->gen_codigo_barras($code_number);

        $cabecera=[
        'razonSocial'=>(string)$xmlData->infoTributaria->razonSocial,
        'dirMatriz'=>(string)$xmlData->infoTributaria->dirMatriz,
        'contribuyenteEspecial'=>(string)$xmlData->infoFactura->contribuyenteEspecial,
        'obligadoContabilidad'=>(string)$xmlData->infoFactura->obligadoContabilidad,
        'ruc'=>(string)$xmlData->infoTributaria->ruc,
        'nromFactura'=>(string)$xmlData->infoTributaria->estab. '-'.$xmlData->infoTributaria->ptoEmi. '-'.$xmlData->infoTributaria->secuencial,
        'ambiente'=>$ambiente,
        'tipoEmision'=>$emision,
        'claveAcceso'=>(string)$xmlData->infoTributaria->claveAcceso,
        'fechaEmision'=>(string)$xmlData->infoFactura->fechaEmision
        ];

        $cliente=[
        'cedula'=>(string)$xmlData->infoFactura->identificacionComprador,
        'direccion'=>(string)$xmlData->infoAdicional->campoAdicional[0],
        'nombres_apellidos'=>(string)$xmlData->infoFactura->razonSocialComprador
        ];

        $detalles=[];
        for ($i=0; $i < sizeof($xmlData->detalles->detalle); $i++) { 
            $detalles[$i]['codigoPrincipal']=(string)$xmlData->detalles->detalle[$i]->codigoPrincipal;
            $detalles[$i]['codigoAuxiliar']=(string)$xmlData->detalles->detalle[$i]->codigoAuxiliar;
            $detalles[$i]['cantidad']=(string)$xmlData->detalles->detalle[$i]->cantidad;
            $detalles[$i]['descripcion']=(string)$xmlData->detalles->detalle[$i]->descripcion;
            $detalles[$i]['precioUnitario']=(string)$xmlData->detalles->detalle[$i]->precioUnitario;
            $detalles[$i]['descuento']=(string)$xmlData->detalles->detalle[$i]->descuento;
            $detalles[$i]['precioTotalSinImpuesto']=(string)$xmlData->detalles->detalle[$i]->precioTotalSinImpuesto;
        }

        //--------------------------------------- VALOR TOTAL ---------------------
        $valor_total=(string)$xmlData->infoFactura->importeTotal;

        $totales=$this->Funciones_fac->get_totales($xml);

        $totales=[
        'subtotal_12'=>$totales['subtotal_12'],
        'subtotal_0'=>$totales['subtotal_0'],
        'subtotal_no_sujeto'=>$totales['subtotal_no_sujeto'],
        'subtotal_exento_iva'=>$totales['subtotal_exento_iva'],
        'subtotal_sin_impuestos'=>$totales['subtotal_sin_impuestos'],
        'descuento'=> $totales['descuento'],
        'ice'=> $totales['ice'],
        'iva_12'=> $totales['iva_12'],
        'propina'=>$totales['propina'],
        'valor_total'=>$totales['valor_total']];

        $data['cabecera'] =  $cabecera;
        $data['cliente'] = $cliente;
        $data['detalles'] = $detalles;
        $data['totales'] = $totales;
        return $data;
    }

    public function generar_zip($iddocumento) 
    {
        $xml = glob($this->storagePath.'/'.$this->user['id_user'].'/'.$iddocumento.".xml");
        $zip=Zipper::make($this->storagePath.'/'.$this->user['id_user'].'/'.$iddocumento.".zip")->add($xml);
    }

    public function checkFileExists($iddocumento){
        if (File::exists($this->storagePath.'/'.$this->user['id_user'].'/'.$iddocumento.".zip")) {
                return false;
            }else{
                return true;
            }
    }
    public function generar_xml(Request $request) 
    {
        $iddocumento=$request->input('iddocumento');
        $filename=$iddocumento.".zip";

        $nofileexists = true;
        while($nofileexists) { // loop until your file is there
            $zip_result=$this->generar_zip($iddocumento);
          $nofileexists = $this->checkFileExists($iddocumento); //check to see if your file is there
          sleep(2); //sleeps for X seconds, in this case 5 before running the loop again
        }

        if (!$nofileexists) {
        $file_path=$this->storagePath.'/'.$this->user['id_user'].'/'.$iddocumento.".zip";
        // return response()->download($file_path, $filename)->deleteFileAfterSend(true);
            $headers = array(
                        'Content-Type' => 'application/octet-stream',
                        'Content-Disposition' => 'attachment; filename="fac.zip'
                    );
             return response()->download($this->storagePath.'/'.$this->user['id_user'].'/'.$iddocumento.".zip",$iddocumento.".zip",$headers)->deleteFileAfterSend(true);

        }

    }
}
