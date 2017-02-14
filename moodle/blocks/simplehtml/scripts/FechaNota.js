
(function (fechanotamain, undefined){

/////////////////////////////////////////
    /// esta es solo una prueba que funciona haciendo fechanotamain.run()
    var lista = {};
    function crear(){
        lista[1]=1;
        lista[2]=2;
        for (var i in lista) {

            console.log('la lista en crear' + lista[i]);
        }

    }
    function mostrar(){
        for (var i in lista) {

            console.log('la lista en mostrar' + lista[i]);
        }

    }
    fechanotamain.run = function (){
        crear();
        mostrar();
    }
    ///// ////////////////////////////////

    var origen_hitos_fecha_nota = 'reportes/hitosFechaNota.txt';
    var origen_objetivos_fecha_nota = 'reportes/objetivosFechaNota.txt';
    var origen_datos_fecha_nota = 'reportes/datosFechaNota.txt';
    var origen_lista_alumnos = 'reportes/listaalumnos.txt';
    var origen_parametros_fecha_nota = 'reportes/parametrosFechaNota.txt';
    var datosFechaNota;
    var hitosFechaNota;
    var objetivosFechaNota;
    var datosAlumnos;

    var svgWFechaNota;
    var svgHFechaNota;
    var circleRadius;
    var ticksX;
    var ticksY;
    var hitoOpacity;
    var paddingX = 100;
    var paddingY = 80;

    var init = 0;

    var valorMinEjeX;
    var valorMaxEjeX;
    var valorMinEjeY;
    var valorMaxEjeY;
    var valorIniEjeX = 30;
    var valorFinEjeX;
    var valorIniEjeY;
    var valorFinEjeY;

    var tamanoEjeX;

    var cantidadClick = 0;
    var listaalumnos = {}
    //fechanotamain.listaalumnos = listaalumnos;
    var cantidadAlumnos = 0;

    var x1Ant;
    var y1Ant;

    var colores = d3.scaleOrdinal()
            .range(["#CEF6EC", "#D358F7", "#8000FF", "#ACFA58", "#FE2E64", "#FACC2E", "#6E6E6E", "#DF01A5", "#A901DB", "#00BFFF", "#00FFFF",
                "#00FF80", "#00FF00", "#F3F781", "#F7BE81", "#F79F81"])
        ;
   /* var colores = d3.scaleOrdinal()
            .range(["#000000", "#000000", "#000000", "#000000", "#000000", "#000000", "#000000", "#000000", "#000000", "#000000", "#000000",
                "#000000", "#000000", "#000000", "#000000", "#000000"])
        ;
*/

    var coloresObjetivo =
            d3.scaleOrdinal()
                .range(["red", "yellow", "green"])
        ;
    fechanotamain.ejecutar = function (){
        cargarParametrosFechaNota();
    }

    function cargarParametrosFechaNota() {
        d3.csv(origen_parametros_fecha_nota, function (err, data) {
            svgWFechaNota = +data.columns[0];
            svgHFechaNota = +data.columns[1];
            circleRadius = +data.columns[2];
            ticksX = +data.columns[3];
            ticksY = +data.columns[4];
            hitoOpacity = +data.columns[5];
            cargarDatosFechaNota();
        });
    }


    function cargarDatosAlumnos(callback) {
        d3.json(origen_lista_alumnos, function (err, data) {
            datosAlumnos = data;
            var count = 0;
            for (var i in datosAlumnos) {
                count += 1;
                listaalumnos[datosAlumnos[i].alumnoid] = count;


            }

            cantidadAlumnos = count;
            callback(listaalumnos);
        });



    }

    function cargarDatosFechaNota() {


        d3.json(origen_datos_fecha_nota, function (err, data) {
            datosFechaNota = data;


            valorMinEjeX = d3.min(datosFechaNota, function (d) {
                return new Date(d.pruebafecha);
            });
            var anio = valorMinEjeX.getFullYear();

            console.log('alumnos ' + cantidadAlumnos);
            //cantidadAlumnos = d3.max(datosFechaNota, function (d) { return d.alumnoid;});
            //cantidadAlumnos = datosAlumnos.length;

            valorMinEjeX = new Date(anio + '-01-01');
            valorMaxEjeX = new Date(anio + '-12-31');

            valorMinEjeY = d3.min(datosFechaNota, function (d) {
                return d.pruebanota;
            });
            valorMinEjeY = valorMinEjeY < 0 ? valorMinEjeY : 0;

            valorMaxEjeY = 100;
            valorMaxEjeY = valorMaxEjeY < 10 ? 10 : valorMaxEjeY;

            valorFinEjeX = svgWFechaNota - paddingX;
            valorIniEjeY = svgHFechaNota - 40;
            valorFinEjeY = paddingY;

            tamanoEjeY = valorIniEjeY - valorFinEjeY;
            tamanoRangoY = valorMaxEjeY - valorMinEjeY;

            tamanoEjeX = valorFinEjeX - valorIniEjeX;

            cargarObjetivosFechaNota();
        });
    }

    function cargarObjetivosFechaNota() {
        d3.json(origen_objetivos_fecha_nota, function (err, data) {
            objetivosFechaNota = data;
            x1Ant = [];
            y1Ant = [];

            valorMinEjeX1 = d3.min(objetivosFechaNota, function (d) {
                return new Date(d.objetivofecha);
            });
            valorMinEjeY1 = d3.min(objetivosFechaNota, function (d) {
                return d.objetivonota;
            });

            /* ***************************** ARREGLAR ***************************** */
            for (i = 0; i < data.length / 2; i++) {
                x1Ant[i] = 0;
                y1Ant[i] = 0;
            }

            cargarHitosFechaNota();
        });
    }

    function cargarHitosFechaNota() {
        d3.json(origen_hitos_fecha_nota, function (err, data) {
            hitosFechaNota = data;

            cargarDatosAlumnos(graficarFechaNota);

        });
    }

    function graficarFechaNota(listaalumnos) {


      /*  for (var key in listaalumnos){
            console.log ('adentro '+ key + ' '+ listaalumnos[key]);
        }*/
        var svg = d3.select("#container_student_tests_graph")
                .append("svg")
                .attr("id", "svgFechaNota")
                .attr("height", svgHFechaNota)
                .attr("width", svgWFechaNota)
            ;
        /*
         >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Axis
         */

        var xScale = d3.scaleTime()
                .domain([valorMinEjeX, valorMaxEjeX])
                .range([valorIniEjeX, valorFinEjeX - paddingX])
            ;

        var yScale = d3.scaleLinear()
                .domain([valorMinEjeY, valorMaxEjeY])
                .range([valorIniEjeY, valorFinEjeY])
            ;

        //Define X axis
        var xAxis = d3.axisBottom()
                .scale(xScale)
                .ticks(12) // cantidad de divisiones
                .tickSizeInner(-tamanoEjeY)
                .tickFormat(d3.timeFormat("%B-%d"))
            ;

        //Define Y axis
        var yAxis = d3.axisLeft()
                .scale(yScale)
                .tickSizeInner(-tamanoEjeX + paddingX)
                //.ticks(valorMaxEjeY) // cantidad de divisiones
                .ticks(10) // cantidad de divisiones
                .tickPadding(13)
            ;

        svg.append("g")
            .attr("class", "axis")
            .attr("transform", "translate(0," + valorIniEjeY + ")")
            .call(xAxis)
        ;

        svg.append("g")
            .attr("class", "axis")
            .attr("transform", "translate(" + valorIniEjeX + ",0)")
            .call(yAxis)
        ;

        /*
         <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Axis
         */


        /*
         >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Circles
         */

        var circlesGroup = svg
                .append("g")
                .attr("id", "circlesGroup")
            ;

        var circles = circlesGroup.selectAll("circle")
                .data(datosFechaNota)
                .enter()
                .append("circle")
                .attr("class", function (d) {
                    return "circuloFechaNota_" + d.alumnoid + " " + "circuloNota_" + (d.pruebanota * 10);
                })
                .classed("view", true)
                .attr("cx", function (d) {
                    return xScale(new Date(d.pruebafecha));
                })
                .attr("cy", function (d) {
                    return yScale(d.pruebanota);
                })
                .attr("r", circleRadius)
                .style("fill", function (d) {
                    return colores(d.alumnoid);
                })
                .style("cursor", "pointer")
                .on("mouseover", function (d) {
                    prueba(d.pruebanota, true, 900);
                    return tooltiptext.text("Fecha: " + d.pruebafecha + " - Nota: " + d.pruebanota)
                        .attr("opacity", 1);
                })
                .on("mouseout", function (d) {
                    prueba(d.pruebanota, false, 100);
                    return tooltiptext.attr("opacity", 0);
                })
            ;
        /*
         <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Circles
         */

        /*
         >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Objetivos
         */

        var objetivosGroup = svg
                .append("g")
                .attr("id", "objetivosGroup")
            ;

        var objetivosCircles =
                objetivosGroup
                    .selectAll("#svgFechaNota")
                    .data(objetivosFechaNota)
                    .enter()
                    .append("circle")
                    .attr("id", "circuloObjetivosFechaNota")
                    .attr("cx", function (d) {
                        return xScale(new Date(d.objetivofecha));
                    })
                    .attr("cy", function (d) {
                        return yScale(d.objetivonota);
                    })
                    .attr("r", circleRadius / 3)
                    .attr("opacity", 0.7)
                    .style("fill", function (d) {
                        return coloresObjetivo(d.objetivoid);
                    })
                    .on("mouseover", function (d) {
                        return tooltiptext.text(d.objetivonombre)
                            .attr("opacity", 1);
                    })
                    .on("mouseout", function () {
                        return tooltiptext.attr("opacity", 0);
                    })
            ;

        var objetivosLines =
                objetivosGroup
                    .selectAll("#svgFechaNota")
                    .data(objetivosFechaNota)
                    .enter()
                    .append("line")
                    .attr("id", "objetivosLineaFechaNota")
                    .attr("x1", function (d) {
                        return xScale(new Date(calcularPosicionX1(d.objetivofecha, d.objetivoid)));
                    })
                    .attr("y1", function (d) {
                        return yScale(calcularPosicionY1(d.objetivonota, d.objetivoid));
                    })
                    .attr("x2", function (d) {
                        return xScale(new Date(d.objetivofecha));
                    })
                    .attr("y2", function (d) {
                        return yScale(d.objetivonota);
                    })
                    .attr("opacity", hitoOpacity)
                    .attr("stroke-width", 5)
                    .attr("stroke", function (d) {
                        return coloresObjetivo(d.objetivoid);
                    })
                    .on("mouseover", function (d) {
                        return tooltiptext.text("Siguiente objetivo: " + d.objetivonota)
                            .attr("opacity", 1);
                    })
                    .on("mouseout", function () {
                        return tooltiptext.attr("opacity", 0);
                    })
            ;
        ;

        /*
         <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Circles
         */


        /*
         >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Hitos
         */

        var hitosGroup = svg
                .append("g")
                .attr("id", "hitosGroup")
            ;

        var hitoLine = hitosGroup
            .selectAll("hitos")
            .data(hitosFechaNota)
            .enter()
            .append("line")
            .attr("id", "hitoLineaFechaNota")
            .attr("x1", function (d) {
                return (xScale(new Date(d.hitofecha)));
            })
            .attr("y1", svgHFechaNota)
            .attr("x2", function (d) {
                return (xScale(new Date(d.hitofecha)));
            })
            .attr("y2", function (d) {
                return paddingY - 10;
            })
            .attr("opacity", hitoOpacity)
            .attr("stroke-width", 3)
            .attr("stroke", "blue")

        var hitoCircle = hitosGroup
                .selectAll("hitos")
                .data(hitosFechaNota)
                .enter()
                .append("circle")
                .attr("id", "hitoCirculoFechaNota")
                .attr("cx", function (d) {
                    return (xScale(new Date(d.hitofecha)));
                })
                .attr("cy", function (d) {
                    return paddingY - 30;
                })
                .attr("r", 20)
                .attr("opacity", 0.2)
                .attr("fill", "blue")
                .on("mouseover", function (d) {
                    return tooltiptext.text(d.hitodescripcion)
                        .attr("opacity", 1);
                })
                .on("mouseout", function () {
                    return tooltiptext.attr("opacity", 0);
                })
            ;

        var hitoText = hitosGroup
                .selectAll("hitosText")
                .data(hitosFechaNota)
                .enter()
                .append("text")
                .attr("id", "hitoTextoFechaNota")
                .attr("x", function (d) {
                    return (xScale(new Date(d.hitofecha)));
                })
                .attr("y", function (d) {
                    return paddingY - 25;
                })
                .text(function (d) {
                    return d.hitonombre
                })
                .attr("font-family", "sans-serif")
                .attr("font-size", "12px")
                .attr("fill", "blue")
                .on("mouseover", function (d) {
                    return tooltiptext.text(d.hitodescripcion)
                        .attr("opacity", 1);
                })
                .on("mouseout", function () {
                    return tooltiptext.attr("opacity", 0);
                })
            ;

        var tooltiptext = svg
                .append("text")
                .attr("id", "tooltiptext")
                .attr("x", svgWFechaNota - paddingX * 3)
                .attr("y", 30)
                .attr("font-family", "sans-serif")
                .attr("font-size", "20px")
                .attr("fill", "rgb(150, 150, 150)")
                .attr("opacity", 0)
            ;

        var grupoTextoAlumnos = svg
                .append("g")
                .attr("id", "grupoTextoAlumnos")
            ;

        var textoAlumnos = grupoTextoAlumnos
                .selectAll("text")
                .data(datosFechaNota)
                .enter()
                .append("text")
                .attr("id", function (d) {
                    return "ttt_" + d.alumnoid;
                })
                .attr("class", function (d) {
                    return "textoNota_" + (d.pruebanota * 10);
                })
                .attr("x", svgWFechaNota - paddingX - 50)
                .attr("y", function (d) {
                    //console.log(' ayayay '+d.alumnoid + ' ' + listaalumnos[d.alumnoid])
                    return 40 + listaalumnos[d.alumnoid] * 20;
                })
                .attr("font-weight", "")
                .attr("opacity", 1)
                .text(function (d) {
                    return d.alumnonombre;
                })
                .style("cursor", "pointer")
                .on("mouseover", function (d) {
                    textoAlumnos_MouseOver(d.alumnoid);
                })
                .on("mouseout", function (d) {
                    textoAlumnos_MouseOut(d.alumnoid);
                })
                .on("click", function (d) {
                    textoAlumnos_Click(d.alumnoid);
                })
            ;

        var textoAlumnoTodos = grupoTextoAlumnos
            .append("text")
            .attr("id", "ttt_0")
            .attr("x", svgWFechaNota - paddingX - 50)
            .attr("y", 60 + cantidadAlumnos * 20)
            .attr("font-weight", "bold")
            .attr("opacity", 1)
            .text("VER TODOS")
            .on("click", function (d) {
                textoAlumnosTodos_Click();
            })
            .style("cursor", "pointer")

        /*
         <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Hitos
         */


        /*
         >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> CheckBoxes
         */


// parece que SVG no soporta checkbox, por eso se dibuja afuera.
        var checkboxObjetivos =
                d3.select("#container_student_tests_graph")
                    .append("label")
                    .attr("id", "labelCheckboxObjetivos")
                    .text("Ver Objetivos?")
                    .style("display", "block")
                    .append("input")
                    .attr("id", "checkboxObjetivos")
                    .attr("type", "checkbox")
                    .property("checked", true)
                    .on("change", function (d) {
                        checkBoxObjetivosChange();
                    })
            ;

        var checkboxHitos =
                d3.select("#container_student_tests_graph")
                    .append("label")
                    .attr("id", "labelCheckboxHitos")
                    .text("Ver Hitos?")
                    .style("display", "block")
                    .append("input")
                    .attr("id", "checkboxHitos")
                    .attr("type", "checkbox")
                    .property("checked", true)
                    .on("change", function (d) {
                        checkBoxHitosChange();
                    })
            ;

        function checkBoxObjetivosChange() {
            var cbObjetivosOpacity = d3.select("#checkboxObjetivos").node().checked ? 1 : 0;
            d3.select("#objetivosGroup")
                .attr("opacity", cbObjetivosOpacity);
        };

        function checkBoxHitosChange() {
            var cbHitosOpacity = d3.select("#checkboxHitos").node().checked ? 1 : 0;
            d3.select("#hitosGroup")
                .attr("opacity", cbHitosOpacity);
        };

        /*
         <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< CheckBoxes
         */


        function calcularPosicionX1(posActual, indice) {
            var devolver = x1Ant[indice - 1];
            x1Ant[indice - 1] = posActual;
            return devolver;
        }

        function calcularPosicionY1(posActual, indice) {
            var devolver = y1Ant[indice - 1];
            y1Ant[indice - 1] = posActual
            return devolver;
        }

        function textoAlumnos_MouseOver(alumnoid) {
            setOffClassView();

            d3.selectAll(".circuloFechaNota_" + alumnoid)
                .each(function (e, i) {
                    d3.select(this)
                        .classed("view", true)
                        .classed("hidden", false)
                    ;
                })
            ;
        }

        function textoAlumnos_MouseOut(alumnoid) {
            var clickclick = d3.selectAll(".circuloFechaNota_" + alumnoid).classed("click");

            d3.selectAll(".circuloFechaNota_" + alumnoid)
                .each(function (e, i) {
                    d3.select(this)
                        .classed("view", clickclick)
                        .classed("hidden", !clickclick)
                    ;
                });
            setOnClassView();
        }

        function textoAlumnos_Click(alumnoid) {
            var clickclick = d3.selectAll(".circuloFechaNota_" + alumnoid).classed("click");
            setOffClassView();

            d3.selectAll(".circuloFechaNota_" + alumnoid)
                .each(function (e, i) {
                    d3.select(this)
                        .classed("click", !clickclick)
                        .classed("hidden", clickclick)
                        .classed("view", !clickclick)
                    ;
                });

            if (!clickclick) {
                cantidadClick++;
                textoAlumnoOnOff(alumnoid, 0.2)
            } else {
                cantidadClick--;
                textoAlumnoOnOff(alumnoid, 1)

            }
        }

        function textoAlumnosTodos_Click() {
            cantidadClick = 0;
            setOnClassView();
            console.log('uso cantidad alumnos '+cantidadAlumnos);
            for (i = 1; i <= cantidadAlumnos; i++) {
                textoAlumnoOnOff(i, 1);
            }
        }

        function textoAlumnoOnOff(alumnoid, opacity) {
            d3.selectAll("#ttt_" + alumnoid)
                .each(function (e, i) {
                    d3.select(this)
                        .attr("opacity", opacity)
                })
            ;
        }

        function setOffClassView() {
            if (cantidadClick == 0) {
                d3.selectAll(".view")
                    .each(function (e, i) {
                        d3.select(this)
                            .classed("view", false)
                            .classed("hidden", true);
                    });
            }
        }

        function setOnClassView() {
            if (cantidadClick == 0) {
                d3.selectAll(".hidden")
                    .each(function (e, i) {
                        d3.select(this)
                            .classed("view", true)
                            .classed("hidden", false)
                            .classed("click", false)
                        ;
                    });
            }
        }

        function prueba(nota, apagar, bold) {
           d3.selectAll("#grupoTextoAlumnos").each(function(e,i){
               var nodes = this.childNodes;
               d3.select(nodes.forEach(function(f,j){
                 d3.select(f)
                   .attr("opacity", apagar? 0.1:1)
                   ;
               }));
             })
             
             d3.selectAll(".textoNota_" + (nota * 10))
                .each(function (e, i) {
                    d3.select(this)
                      .attr("opacity", 1)
                      .attr("font-weight", bold);
                });
        }
    }



})(window.fechanotamain = window.fechanotamain || {});