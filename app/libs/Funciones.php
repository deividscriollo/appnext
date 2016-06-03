<?php 

namespace App\libs;

/* --------------------------------------- Funciones --------------------------------*/
	class Funciones {

		function __construct() {
			
		}

		/* -------------------- funcion generar ID -----------------*/
		function generarID() {
			 date_default_timezone_set('America/Guayaquil');
        $fecha = date("YmdHis");
        return ($fecha . uniqid());
		}
	}
?>