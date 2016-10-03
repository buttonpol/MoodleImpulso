<?php
/**
 * Created by PhpStorm.
 * User: pol
 * Date: 10/09/2016
 * Time: 11:42
 */



function sql_grafica_circular()
{

    global $DB;



    $sql = "SELECT  CONCAT(firstname,\" \", lastname) AS Nombre, truncate(mdl_quiz_grades.grade,1) AS Nota 
FROM ((mdl_user RIGHT JOIN mdl_quiz_grades ON mdl_user.id=mdl_quiz_grades.userid) 
LEFT JOIN mdl_quiz ON mdl_quiz.id=mdl_quiz_grades.quiz) 
LEFT JOIN mdl_course ON mdl_course.id=mdl_quiz.course";


    $params = array();

    $resultados = $DB->get_records_sql($sql, $params, 0, $userlimit=0);


    return $resultados;

}



function sql_grafica_barras()
{

    global $DB;


    /*
        $sql = "SELECT  CONCAT(firstname,\" \", lastname) AS Nombre, truncate(mdl_quiz_grades.grade,1) AS Nota
    FROM ((mdl_user RIGHT JOIN mdl_quiz_grades ON mdl_user.id=mdl_quiz_grades.userid)
    LEFT JOIN mdl_quiz ON mdl_quiz.id=mdl_quiz_grades.quiz)
    LEFT JOIN mdl_course ON mdl_course.id=mdl_quiz.course";


        $params = array();

        $resultados = $DB->get_records_sql($sql, $params, 0, $userlimit=0);//return $resultados;
    */
    $resultados = array(144,89,55,34,21,13,8,5,3,2,1,2,3,5,8,13,21,34,55,89,144);
    return $resultados;

}


function sql_grafica_barras_divididas()
{

    global $DB;


    /*
        $sql = "SELECT  CONCAT(firstname,\" \", lastname) AS Nombre, truncate(mdl_quiz_grades.grade,1) AS Nota
    FROM ((mdl_user RIGHT JOIN mdl_quiz_grades ON mdl_user.id=mdl_quiz_grades.userid)
    LEFT JOIN mdl_quiz ON mdl_quiz.id=mdl_quiz_grades.quiz)
    LEFT JOIN mdl_course ON mdl_course.id=mdl_quiz.course";


        $params = array();

        $resultados = $DB->get_records_sql($sql, $params, 0, $userlimit=0);//return $resultados;
    */

    $resultados = "State,Under 5 Years,5 to 13 Years,14 to 17 Years,18 to 24 Years,25 to 44 Years,45 to 64 Years,65 Years and Ove
AL,310504,552339,259034,450818,1231572,1215966,641667
AK,52083,85640,42153,74257,198724,183159,50277
AZ,515910,828669,362642,601943,1804762,1523681,862573
AR,202070,343207,157204,264160,754420,727124,407205
CA,2704659,4499890,2159981,3853788,10604510,8819342,4114496";

    return $resultados;

}


function sql_alumnos_curso($curso)
{

    global $DB;

    $sql = "SELECT u.firstname Nombre,u.lastname Apellido, c.shortname 'Codigo Curso',c.fullname 'Nombre curso'
FROM mdl_user u
INNER JOIN mdl_role_assignments ra ON ra.userid = u.id
INNER JOIN mdl_context ct ON ct.id = ra.contextid
INNER JOIN mdl_course c ON c.id = ct.instanceid
INNER JOIN mdl_role r ON r.id = ra.roleid
INNER JOIN mdl_course_categories cc ON cc.id = c.category
WHERE c.id =$curso and  roleid=5";


    $params = array();

    $resultados = $DB->get_records_sql($sql, $params, 0, $userlimit=0);


    return $resultados;

}

function sql_docentes_curso($curso)
{

    global $DB;

    $sql = "SELECT u.firstname Nombre,u.lastname Apellido, c.shortname 'Codigo Curso',c.fullname 'Nombre curso'
FROM mdl_user u
INNER JOIN mdl_role_assignments ra ON ra.userid = u.id
INNER JOIN mdl_context ct ON ct.id = ra.contextid
INNER JOIN mdl_course c ON c.id = ct.instanceid
INNER JOIN mdl_role r ON r.id = ra.roleid
INNER JOIN mdl_course_categories cc ON cc.id = c.category
WHERE c.id =$curso and  roleid=3";


    $params = array();

    $resultados = $DB->get_records_sql($sql, $params, 0, $userlimit=0);


    return $resultados;

}

