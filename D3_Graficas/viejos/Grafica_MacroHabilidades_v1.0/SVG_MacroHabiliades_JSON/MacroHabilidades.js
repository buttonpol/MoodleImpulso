var datosMH;
var svgW = 700;
var svgH = 500;

var paddingX = 100;
var paddingY = 30;

var valorIniEjeX;
var valorFinEjeX;
var valorIniEjeY;
var valorFinEjeY;

var valorMinEjeX;
var valorMaxEjeX;
var valorMinEjeY;
var valorMaxEjeY;

var tamanoEjeX;
var tamanoEjeY;

var porcentajeAcumulado = 0;

var rectaWidth = 70;

var primeraNotaLetra;
var cantidadNotas = -1;

function cargarDatosMacroHabilidades(){
    d3.json('datosMH.txt', function(err, data){
        datosMH = data;

        valorIniEjeX = paddingX;
        valorFinEjeX = svgW - paddingX;
        valorIniEjeY = svgH - paddingY;
        valorFinEjeY = paddingY;
        
        valorMinEjeX = 0;
        valorMaxEjeX = d3.sum(datosMH, function(d){ return 1;}); //cantidad de macrohabilidades
        
        valorMinEjeY = 0;
        valorMaxEjeY = d3.sum(datosMH[0].notas, function(d){ return d.alumnos.length;}); //cantidad total de alumnos

        tamanoEjeX = valorFinEjeX - valorIniEjeX;
        tamanoEjeY = valorIniEjeY - valorFinEjeY;

        primeraNotaLetra = datosMH[0].notas[0].notaLetra;

        graficarMacroHabilidades();
    });
}


function graficarMacroHabilidades(){
     var svg =      d3.select("body")
                      .append("div")
                      .attr("id", "divMacroHabilidades")
                      .append("svg")
                      .attr("id", "svgMacroHabilidades")
                      .attr("height", svgH)
                      .attr("width", svgW)
                      ;

     var xScale =   d3.scaleLinear()
                      .domain([valorMinEjeX, valorMaxEjeX])
                      .range ([valorIniEjeX, valorFinEjeX])
                      ;

     var yScale =   d3.scaleLinear()
                      .domain([valorMinEjeY, valorMaxEjeY])
                      .range ([valorIniEjeY, valorFinEjeY])
                      ;

     var xAxis =    d3.axisBottom()
                      .scale(xScale)
                      .ticks(valorMaxEjeX)
                      .tickSizeInner(-tamanoEjeY)
                      .tickPadding(10)
                      .tickFormat(function(d) { if (d < valorMaxEjeX) {return datosMH[d].mHNombre}; })

                      ;

     var yAxis =    d3.axisLeft() 
                      .scale(yScale)
                      .tickSizeInner(-tamanoEjeX)
                      .ticks(valorMaxEjeY)
                      ;

                   svg.append("g")
                      .attr("class", "axis")
                      .attr("id", "x_axis")
                      .attr("transform", "translate(0," + valorIniEjeY + ")")
                      .call(xAxis)
                      .selectAll("text")
                      .attr("x", function(d) { if (d < valorMaxEjeX) {return 85}; })
                      .style("text-anchor", "middle");
                      ;
                   
                   svg.append("g")
                      .attr("class", "axis")
                      .attr("id", "y_axis")
                      .attr("transform", "translate(" + paddingX + ",0)")
                      .call(yAxis)
                      ;

     var colores =  d3.scaleOrdinal()
                      .range(["red", "orange", "skyblue", "green", "blue", "pink", "black"])
                      ;

     var serie =   svg.selectAll("serie")
                      .data(datosMH)
                      .enter()
                      .append("g")
                      .attr("class", "serie")
                      ;

     var bar =  serie.selectAll("g")
                     .data(function(d) { return d.notas; })
                     .enter()
                     .append("g")
                     .on("mouseover", function(d) {return (this.childNodes.forEach(function(e, i) {if (e.id == "barText") {d3.select(e)
                                                                                                                             .attr("opacity",1)
                                                                                                                             .attr("font-weight","bold")
                                                                                                                             ;}}));})
                     .on("mouseout", function(d) {barText.attr("opacity",0.3)
                                                         .attr("font-weight","")
                                                         ;})
                     ;
          
     var barRect = bar.append("rect")
                      .attr("id","barRect")
                      .attr("x", function(d) { 
                          cantidadNotas = d.notaLetra ==  primeraNotaLetra? (cantidadNotas + 1) : cantidadNotas;
                          return (50 + xScale(cantidadNotas)); 
                        })
                      .attr("y",       function(d) { return calcularPosicionY(d.alumnos.length, d.notaLetra); })
                      .attr("height",  function(d) { return calcularHeight(d.alumnos.length); })
                      .attr("width",   rectaWidth)
                      .attr("fill",    function(d) { return colores(d.notaLetra); })
                      .attr("opacity", 0.7)
                      ;

     var barText = bar.selectAll("text")
                      .data(function (d) {return d.alumnos;})
                      .enter()
                      .append("text")
                      .attr("id","barText")
                      .attr("x", 600)
                      .attr("y", function(d) { return d.alumnoId * 20;})
                      .attr("font-weight", "")
                      .attr("opacity", 0.3)
                      .text(function(d) { return d.alumnoNombre; })
                      ;

    var notaText = bar.append("text")
                      //.attr("x", function(d) {console.log(xScale(d.notaLetra));})
                      //.attr("y", function(d) {return xScale(d.notaLetra);})
                      .attr("text", function (d) {return d.notaLetra;})
                      ;

    }

function calcularPosicionY(cantidad, nota){
    porcentajeAcumulado = nota != primeraNotaLetra? porcentajeAcumulado : 0;
    porcentajeAcumulado += (cantidad / valorMaxEjeY);
    return valorIniEjeY - (tamanoEjeY * porcentajeAcumulado);
}

function calcularHeight(cantidad){
    return tamanoEjeY * (cantidad / valorMaxEjeY);
}