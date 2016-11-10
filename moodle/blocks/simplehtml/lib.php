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


    return array_values($resultados);

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

function sql_get_docentes_curso($curso)
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


    return array_values($resultados);

}


function sql_get_milestones($curso){

    global $DB;
    $sql =
        "SELECT m.id_milestone as hitoId,
            m.course_id,
            m.user_id,
            FROM_UNIXTIME(m.date , '%Y-%m-%d') as hitoFecha,
            m.short_description as hitoNombre,
            m.long_description as hitoDescripcion
        FROM mdl_aplusabc_milestones m
        where m.course_id  = $curso"
    ;



    $params = array();

    $resultados = $DB->get_records_sql($sql, $params, 0, $userlimit=0);
//    print_object($resultados);

    return array_values($resultados);
}

function sql_get_student_average($student_id){
    //grafica de puntos
    global $DB;
    //#Esta consulta da el promedio de las notas de todas las notas totales de todas las pruebas realizadas por el alumno con id=$student_id
    $sql =  "SELECT avg(mdl_quiz_grades.grade) as PromedioDelAlumno, mdl_quiz.name AS Prueba, FROM_UNIXTIME(mdl_quiz.timemodified, '%d-%m-%Y') AS Fecha
    FROM ((mdl_user RIGHT JOIN mdl_quiz_grades ON mdl_user.id=mdl_quiz_grades.userid)
    LEFT JOIN mdl_quiz ON mdl_quiz.id=mdl_quiz_grades.quiz)
    LEFT JOIN mdl_course ON mdl_course.id=mdl_quiz.course
    Where  mdl_quiz_grades.quiz
                 IN (SELECT mdl_quiz_grades.quiz
                     FROM (mdl_user RIGHT JOIN mdl_quiz_grades ON mdl_user.id=mdl_quiz_grades.userid)
                     WHERE  mdl_user.id=5)
    group by mdl_quiz_grades.quiz ";



    $params = array();

    $resultados = $DB->get_records_sql($sql, $params, 0, $userlimit=0);

    return array_values($resultados);

}
 function sql_get_student_tests($student_id = null, $course_id = null){
//grafica de puntos
     global $DB;
//     $sql = "SELECT   u.id as alumnoId, CONCAT(firstname,' ', lastname) AS alumnoNombre, truncate(qg.grade, 1) AS pruebanota ,
//truncate((qg.grade *100/q.grade),1) as porcentaje,
//    truncate(q.grade,1) notatotal, q.name as nombreprueba, from_unixtime(q.timeopen) as pruebafecha, q.id as idquiz
//    FROM ((mdl_user u RIGHT JOIN mdl_quiz_grades qg ON u.id=qg.userid)
//    LEFT JOIN mdl_quiz q ON q.id=qg.quiz)
//    LEFT JOIN mdl_course c ON c.id=q.course";

     $sql = "SELECT u.id as alumnoId, CONCAT(firstname,' ', lastname) AS alumnoNombre,IFNULL(truncate(qg.grade, 1),0) AS pruebanota,
IFNULL(truncate((qg.grade *100/q.grade),1),0) as porcentaje,  
truncate(q.grade,1) notatotal, q.name as nombreprueba, from_unixtime(q.timeopen) as pruebafecha, q.id as idquiz
FROM mdl_user u
INNER JOIN mdl_role_assignments ra ON ra.userid = u.id
INNER JOIN mdl_context ct ON ct.id = ra.contextid
INNER JOIN mdl_course c ON c.id = ct.instanceid
INNER JOIN mdl_role r ON r.id = ra.roleid
INNER JOIN mdl_course_categories cc ON cc.id = c.category
LEFT JOIN mdl_quiz q ON q.course = c.id
LEFT JOIN mdl_quiz_grades qg ON (u.id=qg.userid and q.id = qg.quiz)
WHERE c.id =3 and  roleid=5 and q.id IN (Select quiz FROM mdl_quiz_grades) order by CONCAT(firstname,' ', lastname)";



//    WHERE u.id = $student_id  and c.id = 3";

    $params = array();

//     if (!is_null($course_id ) {
//        $params = array('u.id', $student_id);
//     }
//     if (!is_null($student_id)){
//         $params = array_merge($params, array('c.id', $course_id));
//     }

    $resultados = $DB->get_records_sql($sql, $params, 0, $userlimit=0);


    return array_values($resultados);

}

function sql_get_goals($course_id=null){
    global $DB;
    
    $sql = "SELECT concat(goal.id_goal, goal.date, goal.course_id, goal.user_id) id,
    goal.id_goal objetivoId,
    goal.name objetivoNombre,
    goal.short_description,
    goal.long_description,
    goal.course_id,
    goal.user_id,
    FROM_UNIXTIME(goal.date) objetivoFecha,
    goal.grade objetivoNota
FROM quatros1_moodle.mdl_aplusabc_goals goal";
// $sql = "select * from quatros1_moodle.mdl_aplusabc_goals ";

    $params = array();
    $resultados = $DB->get_records_sql($sql, $params, 0, $userlimit=0);


    return array_values($resultados);
}
