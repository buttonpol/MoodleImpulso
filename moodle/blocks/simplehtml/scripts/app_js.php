
    <script src="https://d3js.org/d3.v4.min.js"></script>


    <script type="application/javascript">

        /*
        * ejecuta todas las funciones de graficas que esten cargadas en $graph_functions
        * */

        function cargarDatos() {


            var array_of_functions = [
                <?php echo $graph_functions?>
            ]
//            console.log(array_of_functions);
            for (i = 0; i < array_of_functions.length; i++) {
                array_of_functions[i]();
            }
        }


    </script>
        <?php
        /* para cargar los javascript de cada grafica*/
        require_once('app_circular_js.php');
        require_once('app_barras_js.php');
        require_once('app_pruebas_fecha_js.php');
        require_once('app_barras_divididas_js.php');
        ?>




</head>

<body onload="cargarDatos()">
