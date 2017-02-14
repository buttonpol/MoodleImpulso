<?php
/**
 * Created by PhpStorm.
 * User: pol
 * Date: 24/09/2016
 * Time: 14:26
 */

// ver cómo se debe hacer prolijo
?>

<!DOCTYPE html>
<html ng-app="AppPrueba">
<head>
    <meta charset="UTF-8">
    <title>Reportes Liceo Impulso</title>
    <link rel="stylesheet" type="text/css" href="styles/estilo.css">
    <script src="scripts/d3_4.2.6.js"></script>
    <script src="scripts/jquery-3.1.1.js"></script>
    <script src="scripts/graficas.js"></script>
    <script src="scripts/MacroHabilidades.js"></script>
    <script src="scripts/FechaNota.js"></script>
    <script src="scripts/Torta.js"></script>

    <script type="text/css">
        #pepe {
            visibility: visible;
            position: absolute;
            left: 0px;
            top: 0px;
            width:100%;
            height:100%;
            text-align:center;
            z-index: 1000;
        }
        #container {
            width:300px;
            margin: 100px auto;
            background-color: #fff;
            border:1px solid #000;
            padding:15px;
            text-align:center;
        }
        body {
            height:100%;
            margin:0;
            padding:0;
        }
    </script>
    <script type="text/javascript">
        $( document ).ready(function() {
           graficas();
        });
        function graficas(){
            cargarDatosMacroHabilidades();
            //fechanotamain.run();
            fechanotamain.ejecutar();
            //cargarParametrosFechaNota();
            cargarDatosTorta();
        }
        function overlay() {
            el = document.getElementById("pepe");
            el.style.visibility = (el.style.visibility == "visible") ? "hidden" : "visible";
            el = document.getElementById("showlink");
            el.style.visibility = (el.style.visibility == "visible") ? "hidden" : "visible";
        }
    </script>