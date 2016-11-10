var datosFechaNota;
var hitosFechaNota;

var svgW;
var svgH;
var circleRadius;
var ticksX;
var ticksY;
var hitoOpacity;
var paddingX = 20;
var paddingY = 60;

var valorMinEjeX;
var valorMaxEjeX;
var valorMinEjeY;
var valorMaxEjeY;	
var valorIniEjeX;
var valorFinEjeX;
var valorIniEjeY;
var valorFinEjeY;

var tamanoEjeX;

var colores =  d3.scaleOrdinal()
              .range(["blue", "red", "orange", "green", "pink", "skyblue", "black"])
              ;


function cargarParametrosFechaNota(){
	d3.csv('parametrosFechaNota.txt', function(err, data){
		svgW = +data.columns[0];
		svgH = +data.columns[1];
		circleRadius = +data.columns[2];
		ticksX = +data.columns[3];
		ticksY = +data.columns[4];
		hitoOpacity = +data.columns[5];
		cargarDatosFechaNota();
	});
}

function cargarDatosFechaNota(){
	d3.json('datosFechaNota.txt', function(err, data){
		datosFechaNota = data;
		valorMinEjeX = d3.min(datosFechaNota, function (d) {return new Date(d.pruebaFecha);});
		valorMinEjeX.setDate(valorMinEjeX.getDate() - 10);

		valorMaxEjeX = d3.max(datosFechaNota, function (d) {return new Date(d.pruebaFecha);});
		valorMaxEjeX.setDate(valorMaxEjeX.getDate() + 10);

		valorMinEjeY = d3.min(datosFechaNota, function (d) {return d.pruebaNota;});
		valorMinEjeY = valorMinEjeY < 0? valorMinEjeY:0;

		valorMaxEjeY = d3.max(datosFechaNota, function (d) {return d.pruebaNota;});	
		valorMaxEjeY = valorMaxEjeY < 10? 10: valorMaxEjeY;

		valorIniEjeX = paddingX;
		valorFinEjeX = svgW - paddingX;
		valorIniEjeY = svgH - paddingY;
		valorFinEjeY = paddingY;

		tamanoEjeY = valorIniEjeY - valorFinEjeY;
		tamanoRangoY = valorMaxEjeY - valorMinEjeY;

		tamanoEjeX = valorFinEjeX - valorIniEjeX;

		cargarHitosFechaNota();
	});
}

function cargarHitosFechaNota(){
	d3.json('hitosFechaNota.txt', function(err, data){
		hitosFechaNota = data;
		graficarFechaNota();
	});
}

function graficarFechaNota(){
	var svg =     d3.select("body")
					.append("div")
					.attr("id", "divFechaNota")
					.append("svg")
					.attr("id", "svgFechaNota")
					.attr("height", svgH)
					.attr("width", svgW)
					;
	/*
	>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Axis
	*/

	var xScale =  d3.scaleTime()
				 	.domain([valorMinEjeX, valorMaxEjeX])
				 	.range ([valorIniEjeX, valorFinEjeX])
				 	;

	var yScale =  d3.scaleLinear()
				 	.domain([valorMinEjeY, valorMaxEjeY])
				 	.range ([valorIniEjeY, valorFinEjeY])
				 	;

	//Define X axis
	var xAxis =   d3.axisBottom()
				  	.scale(xScale)
				  	.ticks(8) // cantidad de divisiones
				  	.tickSizeInner(-tamanoEjeY)
				  	.tickFormat(d3.timeFormat("%B-%d"))
				  	;

	//Define Y axis
	var yAxis =   d3.axisLeft() 
			  		.scale(yScale)
			  		.tickSizeInner(-tamanoEjeX)
					.ticks(valorMaxEjeY) // cantidad de divisiones
					.tickPadding(13)
				  	;

	svg.append("g")
	   .attr("class", "axis")
	   .attr("transform", "translate(0," + valorIniEjeY + ")")
	   .call(xAxis)
	   ;
			
	svg.append("g")
	   .attr("class", "axis")
	   .attr("transform", "translate(" + paddingX + ",0)")
	   .call(yAxis)
	   ;

	/*
	<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Axis
	*/


	/*
	>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Circles
	*/

	var circles = svg 	.selectAll("circle")
	   				 	.data(datosFechaNota)
	   				 	.enter()
	   				 	.append("circle")
	   				 	.attr("id", "circuloFechaNota")
	   				 	.attr("cx",    function (d) { return xScale(new Date(d.pruebaFecha)); })
			        	.attr("cy",    function (d) { return yScale(d.pruebaNota); })
			        	.attr("r",     circleRadius)
			        	.attr("opacity", 0.7)
			        	.style("fill", function (d) { return colores(d.alumnoId);  })
						.on("mouseover", function(d) 
					    	{return tooltiptext.text(d.alumnoNombre + " - Nota: " + d.pruebaNota)
					    					   .attr("opacity",1);})
					    .on("mouseout", function() 
					    	{return tooltiptext.attr("opacity",0);})
						;


	/*
	<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Circles
	*/

	/*
	>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Hitos
	*/

    var hitoLine = svg.selectAll("hitos")
    			  	  .data(hitosFechaNota)
    			  	  .enter()
    			  	  .append("line")
    			  	  .attr("id", "hitoLineaFechaNota")
				  	  .attr("x1", function (d) { return d.x1; })
				  	  .attr("y1", svgH)
				  	  .attr("x2", function (d) { return d.x2; })
				  	  .attr("y2", function (d) { return d.y2; })
				  	  .attr("opacity",hitoOpacity)
				  	  .attr("stroke-width", 3)
				  	  .attr("stroke", "blue")
				  
	var hitoCircle = svg.selectAll("hitos")
	      			    .data(hitosFechaNota)
	    			  	.enter()
						.append("circle")
						.attr("id", "hitoCirculoFechaNota")
					    .attr("cx", function (d) { return d.x2; })
					    .attr("cy", function (d) { return d.y2 - 20; })
					    .attr("r", 20)
					    .attr("opacity", 0.2)	
					    .attr("fill", "blue")
					    .on("mouseover", function(d) 
					    	{return tooltiptext.text(d.hitoDescripcion)
					    					   .attr("opacity",1);})
					    .on("mouseout", function() 
					    	{return tooltiptext.attr("opacity",0);})
						;

	var hitoText = svg 	.selectAll("hitosText")
      			      	.data(hitosFechaNota)
    			      	.enter()
					  	.append("text")
					  	.attr("id", "hitoTextoFechaNota")
					    .attr("x", function(d) { return d.x2; })
		                .attr("y", function(d) { return d.y2 - 15; })
		                .text(function(d) {return d.hitoNombre})
		                .attr("font-family", "sans-serif")
		                .attr("font-size", "12px")
		                .attr("fill", "blue")
		                .on("mouseover", function(d) 
					    	{return tooltiptext.text(d.hitoDescripcion)
					    					   .attr("opacity",1);})
					    .on("mouseout", function() 
					    	{return tooltiptext.attr("opacity",0);})
		            	;

	var tooltiptext = svg 	.append("text")
							.attr("id","tooltiptext")
							.attr("x", 400)
			                .attr("y", 30)
							.attr("font-family", "sans-serif")
			                .attr("font-size", "20px")
		    	            .attr("fill", "rgb(221, 221, 221)")
		    	            .attr("opacity",0)
		    	            ;

	/*
	<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Hitos
	*/
}