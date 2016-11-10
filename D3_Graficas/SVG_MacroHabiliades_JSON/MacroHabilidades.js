    var datosMH;
var svgW = 700;
var svgH = 600;

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

        primeranotaletra = datosMH[0].notas[0].notaletra;

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
                      .attr("id", "serie")
                      ;

     var bar =  serie.selectAll("g")
                     .data(function(d) { return d.notas; })
                     .enter()
                     .append("g")
                     .attr("id", function (d) {return ("grupoSerie_" + d.notaletra);})
                     ;

     var barRect = bar.append("rect")
                      .attr("id","barRect")
                      .attr("x", function(d) { 
                          cantidadNotas = d.notaletra ==  primeranotaletra? (cantidadNotas + 1) : cantidadNotas;
                          return (50 + xScale(cantidadNotas)); 
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
                      .attr("id",function(d) {return "barText_" + d.alumnoid;})
                      .attr("x", svgW - paddingX + 10)
                      .attr("y", function(d) {return paddingY + d.alumnoid * 22;})
                      .attr("font-weight", "")
                      .attr("opacity", opacityTextoOff)
                      .text(function(d) { return d.alumnonombre; })
                      .on("mouseover", function(d) {barText_MouseOver(d.alumnoid);})
                      .on("mouseout",  function(d) {barText_MouseOut(d.alumnoid);})
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
                      .data(datosMH[0].notas)
                      .enter()
                      .append("rect")
                     //.attr("id", function (d) {console.log(d);})
                      .attr("x",      function(d) { return LeyendaCalcularX(d.notaletra);})
                      .attr("y",      svgH - 40)
                      .attr("height", 120)
                      .attr("width",  50)
                      .attr("opacity", opacityRectaOff)
                      .attr("fill",    function(d) { console.log(d); return colores(d.notaletra); })
                      ;
                     
    var leyendaText = leyendaGroup
                      .selectAll("text")
                      .data(datosMH[0].notas)
                      .enter()
                      .append("text")
                      //.attr("id",function(d) {return "barText_" + d.alumnoId;})
                      .attr("x",      function(d) { return 20 + LeyendaCalcularX(d.notaletra);})
                      .attr("y",      svgH - 15)
                      .attr("font-weight", "")
                      .attr("opacity", 1)
                      .text(function(d) { return d.notaletra; })
                      ;

    }

function LeyendaCalcularX(nota){
  if (primeranotaletra == nota)
    leyendaX = 0;
  leyendaX += 60; 
  return svgW / 2 - 200 + leyendaX;
}

function calcularPosicionY(cantidad, nota){
    porcentajeAcumulado = nota != primeranotaletra? porcentajeAcumulado : 0;
    porcentajeAcumulado += (cantidad / valorMaxEjeY);
    return valorIniEjeY - (tamanoEjeY * porcentajeAcumulado);
}

function calcularHeight(cantidad){
    return tamanoEjeY * (cantidad / valorMaxEjeY);
}

function barRect_MouseOver(){
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
