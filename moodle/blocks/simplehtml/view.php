<?php
/**
 * Created by PhpStorm.
 * User: pol
 * Date: 10/09/2016
 * Time: 9:51
 */

/**
 * view.php is created which:
 * loads base moodle API and any necessary third party modules or non-base API files.
 * loads the necessary course object and globals.
 * performs necessary access control.
 * loads our form and branches execution appropriately based on our form state.
 *
 *
 * */
global $DB, $OUTPUT, $PAGE, $CFG;

/* partes fijas del archivo de salida*/
$dir = "reportes";
$output_file_type = ".txt";

/* funciones a ejecutar en el js de graficas*/

$graph_functions = '';

require_once('view_header.php');
require_once('../../config.php');
require_once('simplehtml_form.php');
//TODO: averiguar por qué acá funciona $CFG->dirroot y si lo pongo en las anteriores líneas no
require_once($CFG->dirroot . '/blocks/simplehtml/lib.php');



// Check for all required variables.
$courseid = required_param('courseid', PARAM_INT);

$blockid = required_param('blockid', PARAM_INT);

// Next look for optional variables.
$id = optional_param('id', 0, PARAM_INT);

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourse', 'block_simplehtml', $courseid);
}

//header
require_login($course);
$PAGE->set_url('/blocks/simplehtml/view.php', array('id' => $courseid));
$PAGE->set_pagelayout('standard');
$PAGE->set_heading(get_string('edithtml', 'block_simplehtml'));
//breadcrumbs
$settingsnode = $PAGE->settingsnav->add(get_string('simplehtmlsettings', 'block_simplehtml'));
$editurl = new moodle_url('/blocks/simplehtml/view.php', array('id' => $id, 'courseid' => $courseid, 'blockid' => $blockid));
$editnode = $settingsnode->add(get_string('editpage', 'block_simplehtml'), $editurl);
$editnode->make_active();


// no se que significa esto, pero parece importante
//Don't forget to include definitions for the new strings that you are creating, into block_simplehtml.php

//$simplehtml_form = new simplehtml_form();
echo $OUTPUT->header();


/*
 * aca empieza la parte de las graficas
 * */


/*genera la fecha que va a componer el archivo de salida y el nombre por el tipo de reporte*/
$report_type = "reporteCircular";
$today = (new \DateTime())->format('Ymd');
$ending = 'curso_'.$courseid.'_'.$today.$output_file_type;

/*ruta total del reporte*/
$path_pie_report = $dir . "/" . $report_type.'_'.$ending;




/*
 *
 * genera el reporte de torta
 *
 * */
$data_graph=json_encode(sql_grafica_circular());

//print_object(sql_grafica_circular());
/*guarda el reporte en el archivo*/
file_put_contents($path_pie_report, $data_graph);


$graph_functions .= $report_type;
echo html_writer::start_div('', array('id' => 'container_pie_graph'));
echo html_writer::end_div();



/*
 *
 * genera el reporte de barras
 *
 * */
$data_graph=json_encode(sql_grafica_barras());


$report_type = "reporteBarras";
/*ruta total del reporte*/
$path_bar_report = $dir . "/" . $report_type.'_'.$ending;

/*guarda el reporte en el archivo*/
file_put_contents($path_bar_report, $data_graph);

echo html_writer::start_div('', array('id' => 'container_bar_graph'));
echo html_writer::end_div();
$graph_functions .= ','.$report_type;




/*
 *
 * genera el reporte de barras divididas
 *
 * */
$data_graph=sql_grafica_barras_divididas();


$report_type = "reporteBarrasDivididas";
/*ruta total del reporte*/
$path_div_bar_report = $dir . "/" . $report_type.'_'.$ending;

/*guarda el reporte en el archivo*/
file_put_contents($path_div_bar_report, $data_graph);

echo html_writer::start_div('', array('id' => 'container_div_bar_graph'));
echo html_writer::end_div();
$graph_functions .= ','.$report_type;


$salida = sql_grafica_circular();

print_object($salida);
$result= json_encode($salida);
echo result;

$expected = '[{"nombre":"Alumno 2","nota":"10.0"},{"nombre":"Alumno 5","nota":"5.0"}]';
echo $expected ;

print_object(json_encode(array_values($salida)));///creo que esto arregla todo

if ($expected== $result){
    echo "si";
}else {
    echo "no";
}
;


/*
 *
 * genera el reporte de docentes
 *
 * */


/*
$data_graph=sql_docentes_curso($courseid);
*/

$report_type = "reporteDocentes";
/*ruta total del reporte*/
/*
$path_teachers_report = $dir . "/" . $report_type.'_'.$ending;*/

/*guarda el reporte en el archivo*/
/*
file_put_contents($path_teachers_report, $data_graph);

echo html_writer::start_div('', array('id' => 'container_div_teachers'));
echo html_writer::end_div();
$graph_functions .= ','.$report_type;
*/



/*
 * aca termina la parte de las graficas
 * */


/*
 * imprime el formulario configurado en simplehtml_form.php
 *
 * */

//$simplehtml_form->display();
//print_object($this->config);



/*
 * los javascript para mostrar las gráficas
 * por ultimo para que esten cargadas todas las variables que precisa el php importado
 * */
require_once('app_js.php');
echo $OUTPUT->footer();

?>
