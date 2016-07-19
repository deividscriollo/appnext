<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\libs\Funciones;
use Illuminate\Pagination\Paginator;
use App\Nomina;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class NominaController extends Controller

  {
  public function add_nomina(Request $request)

    {
    $nomina = new Nomina();
    $funciones = new Funciones();
    $nomina->id = $funciones->generarID();
    $nomina->periodicidad = $request->input('periodicidad');
    $nomina->registro_patronal = $request->input('registro_patronal');
    $nomina->dias = $request->input('dias');
    $nomina->fecha_inicio = $request->input('fecha_inicio');
    $nomina->id_sucursal = $request->input('id_sucursal');
    $nomina->estado = 1;
    $save = $nomina->save();
    if ($save)
      {
      return response()->json(['respuesta' => true], 200);
      }
      else
      {
      return response()->json(['respuesta' => false], 200);
      }
    }
  public function update_nomina(Request $request)

    {
    $nomina = new Nomina();
    $update = $nomina->where('id', '=', $request->input('id'))->update(['periodicidad' => $request->input('periodicidad') , 'registro_patronal' => $request->input('registro_patronal') , 'dias' => $request->input('dias') , 'fecha_inicio' => $request->input('fecha_inicio') ]);
    if ($update)
      {
      return response()->json(['respuesta' => true], 200);
      }
      else
      {
      return response()->json(['respuesta' => false], 200);
      }
    }
  public function delete_nomina(Request $request)

    {
    $nomina = new Nomina();
    $delete = $nomina->where('id', '=', $request->input('id'))->update(['estado' => 0]);
    if ($delete)
      {
      return response()->json(['respuesta' => true], 200);
      }
      else
      {
      return response()->json(['respuesta' => false], 200);
      }
    }

  public function get_nomina(Request $request)

    {
    $currentPage = $request->input('pagina_actual');
    Paginator::currentPageResolver(function () use($currentPage)
      {
      return $currentPage;
      });
    $nomina = new Nomina();
    $datos = $nomina->search($request->input('filter'))->get();
    $currentPage = LengthAwarePaginator::resolveCurrentPage();
    // Create a new Laravel collection from the array data
    $collection = new Collection($datos);
    // Define how many items we want to be visible in each page
    $perPage = $request->input('limit');
    // Slice the collection to get the items to display in current page
    $currentPageSearchResults = $collection->slice($currentPage * $perPage, $perPage)->all();
    // Create our paginator and pass it to the view
    $paginatedSearchResults = new LengthAwarePaginator($currentPageSearchResults, count($collection) , $perPage);
    return response()->json(['respuesta' => $paginatedSearchResults], 200);
    }
  }
