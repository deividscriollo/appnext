<?php

namespace App\libs;

//----------------------- paginador --------
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

/* --------------------------------------- Funciones --------------------------------*/
class Funciones
{
    
    function __construct()
    {
        
    }
    
    /* -------------------- funcion generar ID -----------------*/
    function generarID()
    {
        date_default_timezone_set('America/Guayaquil');
        $fecha = date("YmdHis");
        return ($fecha . uniqid());
    }
    function generarActivacion($user){
       $activation_code=bcrypt(mt_rand(10000,99999).time().$user);
       return $activation_code;
    }
    
    function generarPass($length = 9, $add_dashes = false, $available_sets = 'lud')
    {
        $sets = array();
        if (strpos($available_sets, 'l') !== false)
            $sets[] = 'abcdefghjkmnpqrstuvwxyz';
        if (strpos($available_sets, 'u') !== false)
            $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
        if (strpos($available_sets, 'd') !== false)
            $sets[] = '23456789';
        if (strpos($available_sets, 's') !== false)
            $sets[] = '!@$%*?';
        $all      = '';
        $password = '';
        foreach ($sets as $set) {
            $password .= $set[array_rand(str_split($set))];
            $all .= $set;
        }
        $all = str_split($all);
        for ($i = 0; $i < $length - count($sets); $i++)
            $password .= $all[array_rand($all)];
        $password = str_shuffle($password);
        if (!$add_dashes)
            return $password;
        $dash_len = floor(sqrt($length));
        $dash_str = '';
        while (strlen($password) > $dash_len) {
            $dash_str .= substr($password, 0, $dash_len) . '-';
            $password = substr($password, $dash_len);
        }
        $dash_str .= $password;
        return $dash_str;
    }

    function paginarDatos($datos,$currentPage,$perPage){
     
    Paginator::currentPageResolver(function () use($currentPage)
      {
      return $currentPage;
      });
    $currentPage = LengthAwarePaginator::resolveCurrentPage();
    $collection = new Collection($datos);
    $currentPageSearchResults = $collection->forPage($currentPage, $perPage)->all();
    $paginatedSearchResults = new LengthAwarePaginator($currentPageSearchResults, count($collection) , $perPage);

    return $paginatedSearchResults;
    }


}
?>