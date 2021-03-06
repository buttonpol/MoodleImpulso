/*
var datos = [];

function cargarDatos(){
	d3.cvs('datos.txt', function(err, data){
		datos = data;
		graficar();
	});
}
*/
function graficar(){
	var width = 300;
	var height = 300;
	var radius = Math.min(width, height) / 2;
	var color = d3.scaleOrdinal()
		   		  .range(["#708090","#00FF7F","#4682B4","#D2B48C","#008080",
				   	      "#D8BFD8","#FF6347","#40E0D0","#EE82EE","#F5DEB3"]);

	// para dibujar semicirculos, elipses, arcos..etc
	var arc = d3.arc()
				.outerRadius(radius - 10) // entre el svg y el radio de afuera
				.innerRadius(0); //radio de adentro (queda como una dona)

	

	/*
		Selecciona el body html, le agrega una sección svg (el recuadro)
		y luego agrega un grupo g que sirve para agrupar los elementos
		que van a formar el circulo
		Transform y translate se usa para mover el centro de la gráfica, en este
		caso al centro del svg
	*/ 
	var svg = d3.select("body").append("svg")
		.attr("width", width)
		.attr("height", height)
		.append("g")
		.attr("transform", "translate(" + width / 2 + "," + height / 2 + ")")
		
	d3.csv("datos.txt", type, function(error, data) {
		  if (error) throw error;
	
		// ayuda para entender la información que viene del json
		var pie = d3.pie()
					.value(function(d){
					return d.Nota
				});

		var stack = d3.stack()
		    .offset(d3.stackOffsetExpand);

		/*
		  Grupo de grupos para agregar la grafica
		*/
		var g = svg.selectAll(".arc")
					.data(pie(data)) //referencia al layout pie.
					.enter().append("g")
					.attr("class", "arc"); //un atributo arc para adm estilos en css para cada elemento.

		g.append("path") // para dibujar elementos que pueden no ser 'primitiva'
			.attr("d", arc) //tipo de gráfico, eliptical_arc
			.style("fill", function(d){
				return color(d.data.Nota)
			});

		g.append("text")
			.text(function(d){
				return d.data.Nombre + " (" + d.data.Nota + ")";
			})
			.attr("transform", function(d){
				return "translate(" + arc.centroid(d) + "), rotate(" + angle(d) +")";
			


			});

		g.selectAll("arc")
	 		.on("mouseover", function(){
	   		d3.select(this) // this es el elemento que está activo ahora en la iteración.
	   		//.style("fill", "tomato")
	   		.text("tritri")
	   		})
	    .on("mouseout", function(){
	   		d3.select(this) // this es el elemento que está activo ahora en la iteración.
	   		.style("fill", "SteelBlue")
	   		})
	    .on("click", function(){
	   		d3.select(this) // this es el elemento que está activo ahora en la iteración.
	   		.style("fill", "Green")



	   		});
	});

	function angle(d){
		var a = (d.startAngle + d.endAngle) * 90 / Math.PI - 90;
		return a > 90? a - 180: a;
		}


	function type(d, i, columns) {
	  for (i = 1, t = 0; i < columns.length; ++i) t += d[columns[i]] = +d[columns[i]];
	  d.total = t;
	  return d;
	}

}