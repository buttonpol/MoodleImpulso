var datosMH;
var svgWhabilidades = 800;
var svgHhabilidades = 650;

var paddingX = 100;
var paddingY = 100;

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

var barText;

var primeranotaletra;
var cantidadNotas = -1;

var opacityRectaOff = 0.5;
var opacityRectaOn = 1;
var opacityTextoOff = 0.3;
var opacityTextoOn = 1;

var leyendaX = 0;

var map = new Map();

function cargarDatosMacroHabilidades(){
    d3.json('reportes/datosMH.txt', function(err, data){
        datosMH = data;

        valorIniEjeX = paddingX;
        valorFinEjeX = svgWhabilidades - paddingX;
        valorIniEjeY = svgHhabilidades - paddingY;
        valorFinEjeY = paddingY;
        
        valorMinEjeX = 0;
        valorMaxEjeX = d3.sum(datosMH, function(d){ return 1;}); //cantidad de macrohabilidades
        
        valorMinEjeY = 0;
        valorMaxEjeY = d3.sum(datosMH[0].notas, function(d){ return d.alumnos.length;}); //cantidad total de alumnos

        tamanoEjeX = valorFinEjeX - valorIniEjeX;
        tamanoEjeY = valorIniEjeY - valorFinEjeY;

        primeranotaletra = datosMH[0].notas[0].notaletra;

        graficarMacroHabilidades();
    });
}

function graficarMacroHabilidades(){
     var svg =      d3.select("#container_div_bar_graph")
                      .append("svg")
                      .attr("id", "svgMacroHabilidades")
                      .attr("height", svgHhabilidades)
                      .attr("width", svgWhabilidades+30)
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
                      .tickFormat(function(d) { if (d < valorMaxEjeX) {return datosMH[d].mhnombre}; })
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
                  .attr("x", function(d) { if (d < valorMaxEjeX) {return 50}; })
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
                      .attr("class", function(d) {return "serie_" + d.id;})
                      .attr("id", function(d) {return "serie_" + d.id;})
                      ;

     var bar =  serie.selectAll("g")
                     .data(function(d) { return d.notas; })
                     .enter()
                     .append("g")
                     .attr("id", function (d) {map.set(d.notaletra, d.notaletra); return ("grupoSerie_" + d.notaletra);})
                     ;

     var barRect = bar.append("rect")
                      .attr("id","barRect")
                      .attr("x", function(d) { 
                          cantidadNotas = d.notaletra == primeranotaletra? (cantidadNotas + 1) : cantidadNotas;
                          return (15 + xScale(cantidadNotas)); 
                        })
                      .attr("y",       function(d) { return calcularPosicionY(d.alumnos.length, d.notaletra); })
                      .attr("height",  function(d) { return calcularHeight(d.alumnos.length); })
                      .attr("width",   rectaWidth)
                      .attr("fill",    function(d) { return colores(d.notaletra); })
                      .attr("opacity", opacityRectaOff)
                      .on("mouseover", barRect_MouseOver)
                      .on("mouseout",  barRect_MouseOut)
                      ;

    var grupoTextoNota = bar.append("g")
                            .attr("id", "grupoTextoNota")
                            ;

    barText = grupoTextoNota
                      .selectAll("text")
                      .data(function (d) {return d.alumnos;})
                      .enter()
                      .append("text")
                      .attr("id",function(d) {return "barText_" + d.alumnoposicion;})
                      .attr("x", svgWhabilidades - paddingX + 10)
                      .attr("y", function(d) {return paddingY + d.alumnoposicion * 22 -50;})
                      .attr("font-weight", "")
                      .attr("opacity", opacityTextoOff)
                      .text(function(d) { return d.alumnonombre; })
                      .on("mouseover", function(d) {barText_MouseOver(d.alumnoposicion);})
                      .on("mouseout",  function(d) {barText_MouseOut(d.alumnoposicion);})
                      ;

    var notaText = bar.append("text")
                      //.attr("x", function(d) {console.log(xScale(d.notaletra));})
                      //.attr("y", function(d) {return xScale(d.notaletra);})
                      .attr("text", function (d) {return d.notaletra;})
                      ;

    var leyendaGroup = svg
                      .append("g")
                      .attr("id","leyendaGroup")
                      ;

    var leyendaRect = leyendaGroup
                      .selectAll("#leyendaGroup")
                      .data(Array.from(map))
                      .enter()
                      .append("rect")
                     //.attr("id", function (d) {console.log(d);})
                      .attr("x",      function(d) { return LeyendaCalcularX(d[0]);})
                      .attr("y",      svgHhabilidades - 40)
                      .attr("height", 120)
                      .attr("width",  50)
                      .attr("opacity", opacityRectaOff)
                      .attr("fill",    function(d) { return colores(d[0]); })
                      ;
                     
    var leyendaText = leyendaGroup
                      .selectAll("text")
                      .data(Array.from(map))
                      .enter()
                      .append("text")
                      //.attr("id",function(d) {return "barText_" + d.alumnoId;})
                      .attr("x",      function(d) {return 20 + LeyendaCalcularX(d[0]);})
                      .attr("y",      svgHhabilidades - 15)
                      .attr("font-weight", "")
                      .attr("opacity", 1)
                      .text(function(d) {return d[0]; })
                      ;

    }

function LeyendaCalcularX(nota){
  if (primeranotaletra == nota)
    leyendaX = 0;
  leyendaX += 60; 
  return svgWhabilidades / 2 - 200 + leyendaX;
}

function calcularPosicionY(cantidad, nota){
    porcentajeAcumulado = nota != primeranotaletra? porcentajeAcumulado : 0;
    porcentajeAcumulado += (cantidad / valorMaxEjeY);
    return valorIniEjeY - (tamanoEjeY * porcentajeAcumulado);
}

function calcularHeight(cantidad){
    return tamanoEjeY * (cantidad / valorMaxEjeY);
}

function apagarPrenderSeries(nodo, opacity){
  // se queda con el ID de la serie para apagar todas las demás.
  clase = nodo.parentNode.parentNode.getAttribute("class");
  claseInt = clase.indexOf("_") + 1;
  claseId = clase.substring(claseInt, clase.length)
  
  // apaga las demas series
  nodo.parentNode.parentNode.parentNode.childNodes.forEach(function(e, i) {
    if (e.id.substring(0,5) == "serie" && e.id != ("serie_" + claseId)) {
      e.childNodes.forEach(function(f,j) {
        d3.select(f)
          .attr("opacity", opacity)
          ;
      });
    };
  })
}

function barRect_MouseOver(){
  apagarPrenderSeries(this, 0);

  this.parentNode.childNodes.forEach(function(e, i) {
    if (e.id == "grupoTextoNota") {
      e.childNodes.forEach(function(f,j) {
                d3.select(f)
                  .attr("opacity", opacityRectaOn)
                  .attr("font-weight","bold")
                  ;
              });
    };
  })
}

function barRect_MouseOut(){
  apagarPrenderSeries(this, 1);

  barText.attr("opacity", 0.3)
         .attr("font-weight","")
         ;
}

function barText_MouseOver(alumnoid){
  barRectOff();
  d3.selectAll("#barText_" + alumnoid)
    .each(function(e,i){
      var nodes = this.parentNode.parentNode.childNodes;
      d3.select(nodes.forEach(function(f,j){
        if (f.id == "barRect"){
          d3.select(f)
            .attr("opacity", opacityTextoOn)
            ;
        }
      }));
    })
    .attr("opacity", opacityTextoOn)
  };

function barText_MouseOut(alumnoid){
  barRectOn();
  d3.selectAll("#barText_" + alumnoid)
    .each(function(e,i){
      var nodes = this.parentNode.parentNode.childNodes;
      d3.select(nodes.forEach(function(f,j){
        if (f.id == "barRect"){
          d3.select(f)
            .attr("opacity", opacityRectaOff)
            ;
        }
      }));
    })
    .attr("opacity", opacityTextoOff)  
  };

function barRectOff(){
  d3.selectAll("#barRect")
    .attr("opacity", 0)
}

function barRectOn(){
  d3.selectAll("#barRect")
    .attr("opacity", opacityRectaOff)
}
