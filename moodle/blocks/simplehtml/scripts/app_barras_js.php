    <script type="text/css">


        /* estilos para grafica de barras*/

        #container_bar_graph .bar {
            fill: steelblue;
        }

        #container_bar_graph text{
            fill: "Blue";
        }

        #container_bar_graph .axis path,
        #container_bar_graph .axis line{
            fill: none;
            stroke: black;
        }

        #container_bar_graph .line{
            fill: none;
            stroke: blue;
            stroke-width: 2px;
        }

        #container_bar_graph .tick text{
            font-size: 12px;
        }

        #container_bar_graph .tick line{
            opacity: 0.1;
        }

    </script>

    <script type="application/javascript">


        /*
         * inicio codigo para grafica de barras
         * */
        function reporteBarras(){


            <?php
            $html_to_append =  html_writer::start_tag('h2').'Gráfica de barras para el curso '.$course->fullname;
            $html_to_append.= html_writer::end_tag('h2');
            $html_to_append .=html_writer::start_div('', array('id' => 'grafica_barras')); //aca escribe con javascript la grafica
            $html_to_append .= html_writer::end_div();

            ?>

            $('#container_bar_graph').append('<?php echo $html_to_append ?>');
            //d3.json('rest_call_grafica_circular.php', function (err, data) {
            d3.json('<?php echo $path_bar_report ?>', function (err, datos) {
//                    datos = data;
                    graficarBarras(datos);
                }
            );
        }

        function graficarBarras(datos){
            var w = 500;
            var h = 300;
            var wl = 20;

            var svg = d3.select("#grafica_barras")
                .append("svg")
                .attr("height", h)
                .attr("width", w);

            svg.selectAll("rect")
                .data(datos)
                .enter()
                .append("rect")
                .attr("style","fill: SteelBlue")
                .attr("x", function(d,i){return i * (wl + 1) + 30;})
                .attr("y", function(d){return h - d - 50;})
                .attr("width", wl)
                .attr("height", function(d){return d;})
                .attr("fill", "SteelBlue")
                .on("mouseover", function(){
                    d3.select(this) // this es el elemento que está activo ahora en la iteración.
                        .attr("fill", "tomato")
                })
                .on("mouseout", function(){
                    d3.select(this) // this es el elemento que está activo ahora en la iteración.
                        .attr("fill", "SteelBlue")
                })
                .on("click", function(){
                    d3.select(this) // this es el elemento que está activo ahora en la iteración.
                        .attr("fill", "Green")
                })
            ;


            svg.selectAll("text")
                .data(datos)
                .enter()
                .append("text")
                .text(function(d){return d;})
                .attr("x", function(d, i){return i * (wl + 1) + 40;})
                .attr("y", function(d, i){return h - d - 53;})
            ;



        }

        /*
         * fin codigo para grafica de barras
         * */


    </script>
