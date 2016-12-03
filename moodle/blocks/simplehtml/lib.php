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



        $sql = "SELECT
             qc.id as mhid, qc.name	as mhnombre, q.id as idquiz,
              truncate((qa.maxmark * qas.fraction*100/maxmark ),1) as notaporcentaje,
             (Select case notaporcentaje=truncate((qa.maxmark * qas.fraction*100/maxmark ),1)
             WHEN notaporcentaje<40 THEN 'D'
             WHEN (40.0<=notaporcentaje) and (notaporcentaje<50.0) THEN 'R'
             WHEN (50.0<=notaporcentaje) and (notaporcentaje<70.0) THEN 'B'
             WHEN (70.0<=notaporcentaje)  THEN 'S'
             else 'otro' end)  as notaletra, 
             truncate(qa.maxmark * qas.fraction,1) as notapregunta,
             truncate(maxmark,1) as puntosmaximos,
             u.id as alumnoid, CONCAT(u.firstname,' ', u.lastname) AS alumnonombre,
             gm.groupid
            
            FROM mdl_quiz_attempts quiza
            JOIN mdl_question_usages qu ON qu.id = quiza.uniqueid
            JOIN mdl_question_attempts qa ON qa.questionusageid = qu.id
            JOIN mdl_question_attempt_steps qas ON qas.questionattemptid = qa.id
            LEFT JOIN mdl_question_attempt_step_data qasd ON qasd.attemptstepid = qas.id
            LEFT JOIN mdl_quiz q ON quiza.quiz=q.id
            LEFT JOIN mdl_user u ON quiza.userid=u.id
            left join mdl_groups_members gm on gm.userid = u.id
            LEFT JOIN mdl_question ques ON ques.id = qa.questionid
            LEFT JOIN mdl_question_categories qc ON ques.category = qc.id
            
             WHERE   qas.state in('gradedpartial', 'gradedright')  and q.id=23
            
            order by groupid, mhid,notaletra
            ";
/*
    $sql = "SELECT count(*)
             
            
            FROM mdl_quiz_attempts quiza
            JOIN mdl_question_usages qu ON qu.id = quiza.uniqueid
            JOIN mdl_question_attempts qa ON qa.questionusageid = qu.id
            JOIN mdl_question_attempt_steps qas ON qas.questionattemptid = qa.id
            LEFT JOIN mdl_question_attempt_step_data qasd ON qasd.attemptstepid = qas.id
            LEFT JOIN mdl_quiz q ON quiza.quiz=q.id
            LEFT JOIN mdl_user u ON quiza.userid=u.id
            left join mdl_groups_members gm on gm.userid = u.id
            LEFT JOIN mdl_question ques ON ques.id = qa.questionid
            LEFT JOIN mdl_question_categories qc ON ques.category = qc.id
            
             WHERE   qas.state in('gradedpartial', 'gradedright')  and q.id=23
            
         */

        $params = array();

        $resultados = $DB->get_records_sql($sql, $params, 0, $userlimit=0);


    return array_values($resultados);

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

     $sql = "SELECT   u.id as idalumno, 
        CONCAT(firstname,' ', lastname, ' (', IFNULL(truncate((qg.grade *100/q.grade),1),0),')') AS alumnoNombre,IFNULL(truncate(qg.grade, 1),0) AS pruebanotaoriginal,
        IFNULL(truncate((qg.grade *100/q.grade),1),0) as pruebanota,  
        truncate(q.grade,1) notatotal, q.name as nombreprueba, date(from_unixtime(q.timeopen)) as pruebafecha, q.id as idquiz, 
        truncate(q.grade,1) as puntajemax, gm.groupid
        FROM mdl_user u
        left join mdl_groups_members gm on gm.userid = u.id
        INNER JOIN mdl_role_assignments ra ON ra.userid = u.id
        INNER JOIN mdl_context ct ON ct.id = ra.contextid
        INNER JOIN mdl_course c ON c.id = ct.instanceid
        INNER JOIN mdl_role r ON r.id = ra.roleid
        INNER JOIN mdl_course_categories cc ON cc.id = c.category
        LEFT JOIN mdl_quiz q ON q.course = c.id
        LEFT JOIN mdl_quiz_grades qg ON (u.id=qg.userid and q.id = qg.quiz)
         WHERE c.id = 7 and groupid = 9 /*esto hay que dejarlo dinamico*/ 
         and roleid=5 and q.id IN (Select quiz FROM mdl_quiz_grades) 
         order by IFNULL(truncate((qg.grade *100/q.grade),1),0) desc, CONCAT(firstname,' ', lastname)";

//    WHERE u.id = $student_id  and c.id = 3"; //agregar el grupo
//     WHERE c.id =3 and roleid=5 and q.id IN (Select quiz FROM mdl_quiz_grades) order by CONCAT(firstname,' ', lastname)";





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
FROM mdl_aplusabc_goals goal";
// $sql = "select * from quatros1_moodle.mdl_aplusabc_goals ";

    $params = array();
    $resultados = $DB->get_records_sql($sql, $params, 0, $userlimit=0);


    return array_values($resultados);
}
