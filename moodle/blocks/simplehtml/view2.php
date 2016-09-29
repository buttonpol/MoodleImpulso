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

require_once('app_js.php');
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


$simplehtml = new simplehtml_form();
echo $OUTPUT->header();


echo html_writer::start_tag('h2');
echo 'Gráfica circular para el curso '.$course->fullname;
echo html_writer::end_tag('h2');
echo html_writer::empty_tag('div', array('id' => 'grafica_circular')); //aca escribe con javascript la grafica


//imprime el formulario configurado en simplehtml_form.php
$simplehtml->display();
//print_object($course);

//print_object() is a useful moodle function which prints out data from mixed data types showing the keys and data for
// arrays and objects. Now visit the "Add Page" link for the block and submit some form data.


// prueba


/*
echo 'lib.php print_grafica_circular';
$datos_grafica_circular=print_grafica_circular();
print_object($datos_grafica_circular);
echo '';


//echo 'lib.php print_keyvalue';
//$datos_keyvalue=print_keyvalue();
//print_object($datos_keyvalue);
//echo '';

$jsonencode_datos_grafica_circular = json_encode($datos_grafica_circular);
echo 'json_encode print_grafica_circular';
print_object($jsonencode_datos_grafica_circular, true);
echo '';

echo 'json_decode print_grafica_circular';
print_object(json_decode($jsonencode_datos_grafica_circular, true));
echo '';


echo 'resultado ';
print_object($resultados);
$r2 = json_decode(print_object(json_encode($resultados)), TRUE);
print_object($r2);
print_object(json_encode($resultados));
print_object(json_encode($r2));

echo 'array ';
$array = array(array('nombre' => 'Pol', 'dato' => '10'), array('nombre' => 'Juan', 'dato' => '10'));
print_object($array);
print_object(json_encode($array));

$json = '[
	{
		"nombre":"Leonell",
		"dato": "12"
	},

	{
		"nombre":"Pol",
		"dato": "14"
	},

	{
		"nombre":"Nacho",
		"dato": "19"
	},

	{
		"nombre":"Valentina",
		"dato": "23"
	},

	{
		"nombre":"Sonia",
		"dato": "28"
	}
]';

echo 'json decode';
print_object(json_decode($json));
echo 'json encode 2';
print_object(json_encode(json_decode($json)));
*/

// fin prueba


echo $OUTPUT->footer();

?>
