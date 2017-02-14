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
$today = (new \DateTime())->format('Ymd');
$ending = 'curso_'.$courseid.'_'.$today.$output_file_type;



////inicio del acordion de graficas
//echo html_writer::start_div('', array('id' => 'container_graficos'));
//echo html_writer::end_div();


/**
 *
 *
 *
 *
 *
 *
 *
 *
 */

?>
<a id="showlink" style="visibility: hidden;" href='#' onclick='overlay()'>Mostrar gr&aacute;ficas</a>
    <div id="pepe" style="visibility: visible;
            position: absolute;
            left: 0px;
            top: 0px;
            width:100%;
            height:100%;
            text-align:center;
            z-index: 1000;">

        <div id="layer_container" style="width:1200px;
            margin: 100px auto;
            background-color: #fff;
            border:1px solid #000;
            padding:15px;
            text-align:center;">
            <a href='#' onclick='overlay()'
               style="position:fixed;
               right: 10px;
               background-color: rgba(25, 245, 8, 0.36);">
                   Ocultar gr&aacute;ficas</a>
            <h3>Evolución año 2016 del grupo C</h3>
            <div id="container_student_tests_graph">
            </div>
            <hr>
            <h3>Detalles de prueba 2016-11-01</h3>
            <div>
                <h4>Resultados </h4>
                <div>
                    <div>Estudiantes aprueban: 9</div>
                    <div>Promedio que aprueban: 34.6%</div>
                    <div>Estudiantes no aprueban: 17</div>

                </div>
            </div>
            <div id="container_div_bar_graph" style="width: 800">
            </div>


            <h3>Reporte</h3>
            <h3>Asistencia a prueba 2016-11-01</h3>
            <div id="container_div_pie_graph">
            </div>


        </div>
    </div>
<?php
/**
 *
 *
 *
 *
 *
 *
 *
 *
 */






/*
 *
 * genera el reporte de promedios de pruebas de alumno
 *
 * */

/*
 *
 * primero genera el reporte con hitos
 *
 * */
//primero genero los hitos
//$data_graph=sql_get_milestones_json($courseid);

//$report_type = "reporteHitos";
///*ruta total del reporte*/
//$path_milestone_report = $dir . "/" . $report_type.'_'.$ending;
//
///*guarda el reporte en el archivo*/
////file_put_contents($path_milestone_report, $data_graph);
///*
// *
// * genera el listado de objetivos
// *
// * */
//
//
//
////$data_graph=sql_get_goals_json($courseid);
//
//$report_type = "reporteObjetivos";
///*ruta total del reporte*/
//$path_goals_report = $dir . "/" . $report_type.'_'.$ending;
//
///*guarda el reporte en el archivo*/
////file_put_contents($path_goals_report, $data_graph);
//;
////print_object($data_graph);
//
////echo 'students';
////print_object(sql_course_students_json(7, 9));
//
////$data_graph=sql_get_student_tests_json();
////echo 'student tests';
////print_object($data_graph);
//
////$data_graph=json_encode(sql_get_student_average($student_id));
//
//$report_type = "reportePuntosHitos";
///*ruta total del reporte*/
//$path_student_tests_report = $dir . "/" . $report_type.$ending; //aca le pongo el id del alumno para que no se pise con otra cosa
////$path_student_average_report = $dir . "/" . 'datosFechaNota2.txt'; //aca le pongo el id del alumno para que no se pise con otra cosa

/*guarda el reporte en el archivo*/
//file_put_contents($path_student_tests_report, $data_graph);



//habilitar con el reporte de alumnos
//$graph_functions .= $report_type;

////se puede borrar
//echo "promedio de alumnos";
//print_object($data_graph);
////fin se puede borrar



/*
 *
 * genera el reporte de barras divididas
 *
 * */
//$data_graph=sql_categories_report_json();
//print_object($data_graph);
//$data_graph=sql_categories_report_json();
//print_object($data_graph);


//$report_type = "reporteBarrasDivididas";
/*ruta total del reporte*/
//$path_div_bar_report = $dir . "/" . $report_type.'_'.$ending;

/*guarda el reporte en el archivo*/
//file_put_contents($path_div_bar_report, $data_graph);

echo html_writer::start_div('', array('id' => 'container_div_bar_graph'));
echo html_writer::end_div();
//$graph_functions .= ','.$report_type;




echo html_writer::end_div();


echo html_writer::start_div('', array('id' => 'container_div_bar_graph'));
echo html_writer::end_div();


/*
 *
 * genera el reporte de torta
 *
 * */

$report_type = "reporteCircular";
/*ruta total del reporte*/
$path_pie_report = $dir . "/" . $report_type.'_'.$ending;
//$data_graph=json_encode(sql_grafica_circular());
//$data_graph=json_encode(sql_grafica_circular());

//print_object(sql_grafica_circular());
/*guarda el reporte en el archivo*/
//file_put_contents($path_pie_report, $data_graph);


//$graph_functions .= ','.$report_type;
echo html_writer::start_div('', array('id' => 'container_pie_graph'));
echo html_writer::end_div();




/*
 *
 * genera el reporte de barras
 *
 * */
//$data_graph=json_encode(sql_grafica_barras());


$report_type = "reporteBarras";
/*ruta total del reporte*/
$path_bar_report = $dir . "/" . $report_type.'_'.$ending;

/*guarda el reporte en el archivo*/
//file_put_contents($path_bar_report, $data_graph);

echo html_writer::start_div('', array('id' => 'container_bar_graph'));
echo html_writer::end_div();
//$graph_functions .= ','.$report_type;




/*
 *
 * genera el reporte de docentes
 *
 * */




//$data_graph=sql_get_docentes_curso($courseid);
//
//$report_type = "reporteDocentes";
///*ruta total del reporte*/
//
//$path_teachers_report = $dir . "/" . $report_type.'_'.$ending;
//
///*guarda el reporte en el archivo*/
//
//file_put_contents($path_teachers_report, $data_graph);
//
//echo html_writer::start_div('', array('id' => 'container_div_teachers'));
//echo html_writer::end_div();
////$graph_functions .= ','.$report_type;
//
////habilitar con el reporte de docentes
////$graph_functions .= ','.$report_type;
//
//
//


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
require_once('scripts/app_js.php');
echo $OUTPUT->footer();

?>
