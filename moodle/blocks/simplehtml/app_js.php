
    <script src="https://d3js.org/d3.v4.min.js"></script>
    <script type="application/javascript">
        var datos = '[{"nombre":"Alumno 2","nota":"10.00000"},{"nombre":"Alumno 5","nota":"5.00000"}]';

        function cargarDatos() {

            //d3.json('rest_call_grafica_circular.php', function (err, data) {
            d3.json('datos2.txt', function (err, data) {
                console.log('hola');
                console.log(datos);
                    datos = data;
                    graficarCircular();
                }
            );

            //d3.json('rest_call_grafica_circular.php', function (err, data) {
            d3.json('datosBarras.txt', function (err, data) {
                    datos = data;
                    graficarBarras();
                }
            );


            graficarBarrasDivididas();


        }

        function graficarCircular() {
            var width = 300;
            var height = 300;
            var radius = Math.min(width, height) / 2;
            var color = d3.scaleOrdinal()
                .range(["#708090", "#00FF7F", "#4682B4", "#D2B48C", "#008080",
                    "#D8BFD8", "#FF6347", "#40E0D0", "#EE82EE", "#F5DEB3"]);

            // para dibujar semicirculos, elipses, arcos..etc
            var arc = d3.arc()
                .outerRadius(radius - 10) // entre el svg y el radio de afuera
                .innerRadius(0); //radio de adentro (queda como una dona)

            // ayuda para entender la información que viene del json
            var pie = d3.pie()
                .value(function (d) {
                    console.log(d);
                    console.log('pepe');
                    return d.nota;
                });


            /*
             Selecciona el body html, le agrega una sección svg (el recuadro)
             y luego agrega un grupo g que sirve para agrupar los elementos
             que van a formar el circulo
             Transform y translate se usa para mover el centro de la gráfica, en este
             caso al centro del svg
             */
            var svg = d3.select("#grafica_circular").append("svg")
                .attr("width", width)
                .attr("height", height)
                .append("g")
                .attr("transform", "translate(" + width / 2 + "," + height / 2 + ")");


            /*
             Grupo de grupos para agregar la grafica
             */
            var g = svg.selectAll(".arc")
                .data(pie(datos)) //referencia al layout pie.
                .enter().append("g")
                .attr("class", "arc"); //un atributo arc para adm estilos en css para cada elemento.

            g.append("path") // para dibujar elementos que pueden no ser 'primitiva'
                .attr("d", arc) //tipo de gráfico, eliptical_arc
                .style("fill", function (d) {
                    return color(d.data.nombre)
                })


            g.append("text")
                .text(function (d) {
                    return d.data.nombre + "(" + d.data.dato + ")";
                })
                .attr("transform", function (d) {
                    return "translate(" + arc.centroid(d) + "), rotate(" + angle(d) + ")";
                })


            function angle(d) {
                var a = (d.startAngle + d.endAngle) * 90 / Math.PI - 90;
                return a > 90 ? a - 180 : a;
            }
        }

        function graficarBarras(){
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


        function graficarBarrasDivididas(){
            var w = 500;
            var h = 300;

            var svg = d3.select("#grafica_barras_divididas")
                .append("svg")
                .attr("width", w)
                .attr("height", h);

            var margin = {top: 20, right: 60, bottom: 30, left: 40}
            //width = +svg.attr("width") - margin.left - margin.right,
            //height = +svg.attr("height") - margin.top - margin.bottom,
            //width = +svg.attr("width") - margin.left - margin.right,
            //height = +svg.attr("height") - margin.top - margin.bottom,
            var g = svg.append("g").attr("transform", "translate(" + margin.left + "," + margin.top + ")");

            var x = d3.scaleBand()
                .rangeRound([0, w])
                .padding(0.1)
                .align(0.1);

            var y = d3.scaleLinear()
                .rangeRound([h, 0]);

            var z = d3.scaleOrdinal()
                .range(["#98abc5", "#8a89a6", "#7b6888", "#6b486b", "#a05d56", "#d0743c", "#ff8c00"]);

            var stack = d3.stack()
                .offset(d3.stackOffsetExpand);

            d3.csv("datosBarrasDivididas.txt", type, function(error, data) {
                    if (error) throw error;

                    data.sort(function(a, b) { return b[data.columns[1]] / b.total - a[data.columns[1]] / a.total; });

                    x.domain(data.map(function(d) { return d.State; }));
                    z.domain(data.columns.slice(1));

                    /*
                     Para cada columna (a partid de la 1 (que en realidad es la segunda)) del archivo, genera una clase serie con un color
                     */
                    var serie = g.selectAll(".serie")
                        .data(stack.keys(data.columns.slice(1))(data))
                        .enter()
                        .append("g")
                        .attr("class", "serie")
                        .attr("fill", function(d) { return z(d.key); });


                    /*

                     */
                    serie.selectAll("rect")
                        .data(function(d) { return d; })
                        .enter().append("rect")
                        .attr("x", function(d) { return x(d.data.State); })
                        .attr("y", function(d) { return y(d[1]); })
                        .attr("height", function(d) { return y(d[0]) - y(d[1]); })
                        .attr("width", x.bandwidth());

                    g.append("g")
                        .attr("class", "axis axis--x")
                        .attr("transform", "translate(0," + h + ")")
                        .call(d3.axisBottom(x));

                    g.append("g")
                        .attr("class", "axis axis--y")
                        .call(d3.axisLeft(y).ticks(10, "%"));

                    var legend = serie.append("g")
                        .attr("class", "legend")
                        .attr("transform", function(d) { var d = d[d.length - 1]; return "translate(" + (x(d.data.State) + x.bandwidth()) + "," + ((y(d[0]) + y(d[1])) / 2) + ")"; });

                    legend.append("line")
                        .attr("x1", -6)
                        .attr("x2", 6)
                        .attr("stroke", "#000");

                    legend.append("text")
                        .attr("x", 9)
                        .attr("dy", "0.35em")
                        .attr("fill", "#000")
                        .style("font", "10px sans-serif")
                        .text(function(d) { return d.key; });
                }
            );
        }

        function type(d, i, columns) { //lo usa la funcion graficarBarrasDivididas
            for (i = 1, t = 0; i < columns.length; ++i) t += d[columns[i]] = +d[columns[i]];
            d.total = t;
            return d;
        }

    </script>


</head>

<body onload="cargarDatos()">
