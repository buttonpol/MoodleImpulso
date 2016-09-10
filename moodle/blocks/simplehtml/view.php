<!DOCTYPE html>
<html ng-app="AppPrueba">

<head>
    <meta charset="UTF-8">
    <title>Gráfica Circular</title>
    <link rel="stylesheet" type="text/css" href="estilo.css">
    <script src="https://d3js.org/d3.v4.min.js"></script>
    <script src="app.js"></script>
</head>



<?php
/**
 * Created by PhpStorm.
 * User: pol
 * Date: 10/09/2016
 * Time: 9:51
 */


require_once('../../config.php');
require_once('simplehtml_form.php');

global $DB;

// Check for all required variables.
$courseid = required_param('courseid', PARAM_INT);


if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourse', 'block_simplehtml', $courseid);
}

//require_login($course);

$simplehtml = new simplehtml_form();

$simplehtml->display();
?>


<body onload="cargarDatos()">

</body>
</html>