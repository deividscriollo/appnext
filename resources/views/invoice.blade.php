<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Example 2</title>
    {!! Html::style('assets/css/style.css') !!}
  </head>
  <body>
    <header>
    </header>
    <main>
<table>
        <tbody>
        <tr>
        <td style=" width: 380px;">
        <div style=" float: left;">
        <div id="client">
        <img src="../public/img/logo_fac.png" style="width: 290px;">
        <p>{{$data['cabecera']['razonSocial']}}</p>
        <p>Dir Matriz: {{$data['cabecera']['dirMatriz']}}</p>
        <p>Contribuyente Especial Resolución Nro: {{$data['cabecera']['contribuyenteEspecial']}} </p>
        <p>Obligado a llevar Contabilidad: {{$data['cabecera']['obligadoContabilidad']}}</p>
        </div> 
    </div></td>
        <td class="unit">
    <div style="float: left;">
        <div id="client">
        <p>RUC: {{$data['cabecera']['ruc']}}</p>
        <p>FACTURA No. {{$data['cabecera']['nromFactura']}}</p>
        <p>NÚMERO DE AUTORIZACIÓN:</p>
        <p>FECHA Y HORA DE AUTORIZACIÓN:</p>
        <p>AMBIENTE:{{$data['cabecera']['ambiente']}}</p>
        <p>EMISIÓN: {{$data['cabecera']['tipoEmision']}}</p>
        <p>CLAVE DE ACCESO: {{$data['cabecera']['claveAcceso']}}</p>
        <div id="clave_acceso">
        <img src="../public/temp.gif" >
        </div>
        </div> 
    </div>
      </td>
       </tr>
      <tr>
      <td>
      <p>Razón Social / Nombres y Apellidos: {{$data['cliente']['nombres_apellidos']}}</p>
      </td>
      <td>
      <p>RUC / CI: {{$data['cliente']['cedula']}}</p>
      <p>Fecha de Emisión: {{$data['cabecera']['fechaEmision']}}</p>
      <p>Guía de Remisión:</p></td>
      </tr>
          </tbody>
  </table>
<h1>Detalles</h1>
      <table border="0" cellspacing="0" cellpadding="0" id="tabla_detalles">
        <thead>
          <tr>
            <th>Cod. Princial</th>
            <th>Cod.Auxiliar</th>
            <th >Cant.</th>
            <th >Descripción</th>
            <th >Detalle Adicional</th>
            <th >Precio Unitario</th>
            <th >Descuento</th>
            <th >Precio Total</th>
          </tr>
        </thead>
        <tbody>

        @foreach ($data['detalles'] as $item) 
          <tr>
            <td >{{ $item['codigoPrincipal'] }}</td>
            <td >{{ $item['codigoAuxiliar'] }}</td>
            <td >{{ $item['cantidad'] }}</td>
            <td >{{ $item['descripcion'] }}</td>
            <td ></td>
            <td >{{ $item['precioUnitario'] }}</td>
            <td >{{ $item['descuento'] }}</td>
            <td >{{ $item['precioTotalSinImpuesto'] }}</td>
          </tr>
        @endforeach

        </tbody>
        <tfoot>
          <tr>
            <td colspan="4"></td>
            <td colspan="3">SUBTOTAL 12.00%</td>
            <td>{{$data['totales']['subtotal_12']}}</td>
          </tr>
          <tr>
            <td colspan="4"></td>
            <td colspan="3">SUBTOTAL 0.00%</td>
            <td>{{$data['totales']['subtotal_0']}}</td>
          </tr>
          <tr>
            <td colspan="4"></td>
            <td colspan="3">SUBTOTAL No Sujeto de IVA</td>
            <td>{{$data['totales']['subtotal_exento_iva']}}</td>
          </tr>
          <tr>
            <td colspan="4"></td>
            <td colspan="3">SUBTOTAL SIN IMPUESTOS</td>
            <td>{{$data['totales']['subtotal_sin_impuestos']}}</td>
          </tr>
          <tr>
            <td colspan="4"></td>
            <td colspan="3">DESCUENTO</td>
            <td>{{$data['totales']['descuento']}}</td>
          </tr>
          <tr>
            <td colspan="4"></td>
            <td colspan="3">ICE</td>
            <td>{{$data['totales']['ice']}}</td>
          </tr>
          <tr>
            <td colspan="4"></td>
            <td colspan="3">IVA 12%</td>
            <td>{{$data['totales']['iva_12']}}</td>
          </tr>
          <tr>
            <td colspan="4"></td>
            <td colspan="3">PROPINA</td>
            <td>{{$data['totales']['propina']}}</td>
          </tr>
          <tr>
            <td colspan="4"></td>
            <td colspan="3">VALOR TOTAL</td>
            <td>{{$data['totales']['valor_total']}}</td>
          </tr>
        </tfoot>
      </table>
      <div id="thanks">Thank you!</div>
      <div id="notices">
        <div>NOTICE:</div>
        <div class="notice">A finance charge of 1.5% will be made on unpaid balances after 30 days.</div>
      </div>
    </main>
    <footer>
      Invoice was created on a computer and is valid without the signature and seal.
    </footer>
  </body>
</html>