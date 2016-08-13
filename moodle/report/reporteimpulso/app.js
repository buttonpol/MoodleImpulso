var datos = [];

function cargarDatos(){
	d3.json('datos.txt', function(err, data){
		datos = data;
		graficar();
	}
	);
}

function graficar(){
	var w = 500;
	var h = 300;
	var wl = 20;
	
	var svg = d3.select("body")
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
	   .attr("height", function(d){return d;});

	svg.selectAll("text")
	   .data(datos)
	   .enter()
	   .append("text")
	   .text(function(d){return d;})
	   .attr("x", function(d, i){return i * (wl + 1) + 40;})
	   .attr("y", function(d, i){return h - d - 53;})
}