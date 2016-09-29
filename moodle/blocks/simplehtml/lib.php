<?php
/**
 * Created by PhpStorm.
 * User: pol
 * Date: 10/09/2016
 * Time: 11:42
 */



function print_grafica_circular()
{

    global $DB;



    $sql = "SELECT  CONCAT(firstname,\" \", lastname) AS Nombre,mdl_quiz_grades.grade AS Nota 
FROM ((mdl_user RIGHT JOIN mdl_quiz_grades ON mdl_user.id=mdl_quiz_grades.userid) 
LEFT JOIN mdl_quiz ON mdl_quiz.id=mdl_quiz_grades.quiz) 
LEFT JOIN mdl_course ON mdl_course.id=mdl_quiz.course";


    $params = array();

    $resultados = $DB->get_records_sql($sql, $params, 0, $userlimit=0);//return $resultados;


    $value = array(array('nombre' => 'Pol', 'nota' => '10'), array('nombre' => 'Juan', 'nota' => '10'));
    return $value;
    //return $resultados;

}

/*function print_keyvalue()
{

    global $DB;



    $sql = "SELECT CONCAT(firstname,\" \", lastname) AS Nombre,mdl_quiz_grades.grade AS Nota 
FROM ((mdl_user RIGHT JOIN mdl_quiz_grades ON mdl_user.id=mdl_quiz_grades.userid) 
LEFT JOIN mdl_quiz ON mdl_quiz.id=mdl_quiz_grades.quiz) 
LEFT JOIN mdl_course ON mdl_course.id=mdl_quiz.course";


    $params = array();

    $resultados = $DB->get_records_sql_menu($sql, array $params=null, $limitfrom=0, $limitnum=0);


    return $resultados;

}*/


function print_coco()
{

    global $DB;



    $sql = "SELECT CONCAT(firstname,\" \", lastname) AS Nombre,mdl_quiz_grades.grade AS dato 
FROM ((mdl_user RIGHT JOIN mdl_quiz_grades ON mdl_user.id=mdl_quiz_grades.userid) 
LEFT JOIN mdl_quiz ON mdl_quiz.id=mdl_quiz_grades.quiz) 
LEFT JOIN mdl_course ON mdl_course.id=mdl_quiz.course";



    $params = array();

    $resultados = $DB->get_records_sql($sql, $params, 0, 0);//return $resultados;

    return json_encode($resultados);

}
