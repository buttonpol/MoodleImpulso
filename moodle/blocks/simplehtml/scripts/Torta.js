var datos;
var origen_datos_torta = 'reportes/datosTorta.txt';


function cargarDatosTorta(){
	d3.json(origen_datos_torta, function(err, data){
		datos = data;
		graficarTorta();
	}
	);
}

function graficarTorta(){
	var width = 300;
	var height = 300;
	var radius = Math.min(width, height) / 2;
	var color = d3.scaleOrdinal()
				  .range(["#00FF7F", "#FA5858", "#708090","#4682B4","#D2B48C","#008080","#D8BFD8","#FF6347","#40E0D0","#EE82EE","#F5DEB3"]);

	// para dibujar semicirculos, elipses, arcos..etc
	var arc = d3.arc()
				.outerRadius(radius - 10) // entre el svg y el radio de afuera
				.innerRadius(0); //radio de adentro (queda como una dona)

	// ayuda para entender la información que viene del json
	var pie = d3.pie()
				.value(function(d){
					return d.dato;
				});

	/*
		Selecciona el body html, le agrega una sección svg (el recuadro)
		y luego agrega un grupo g que sirve para agrupar los elementos
		que van a formar el circulo
		Transform y translate se usa para mover el centro de la gráfica, en este
		caso al centro del svg
	*/ 
	var svg = d3.select("#container_div_pie_graph")
				.append("svg")
				.attr("id", "svgTorta")
				.attr("width", width)
				.attr("height", height)
				.append("g")
				.attr("transform", "translate(" + width / 2 + "," + height / 2 + ")");

	/*
	  Grupo de grupos para agregar la grafica
	*/
	var g = svg.selectAll(".arc")
				.data(pie(datos)) //referencia al layout pie.
				.enter()
				.append("g")
				.attr("class", "arc"); //un atributo arc para adm estilos en css para cada elemento.
				
		g.append("path") // para dibujar elementos que pueden no ser 'primitiva'
		.attr("d", arc) //tipo de gráfico, eliptical_arc
		.style("fill", function(d){
			return color(d.data.nombre)
		})


		g.append("text")
			.text(function(d){
				return d.data.nombre + "(" + d.data.dato + ")";
			})
			.attr("transform", function(d){
				return "translate(" + arc.centroid(d) + "), rotate(" + angle(d) +")";
			})
			;

	function angle(d){
		var a = (d.startAngle + d.endAngle) * 90 / Math.PI - 90;
		return a > 90? a - 180: a;
		}
}