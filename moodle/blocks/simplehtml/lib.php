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



    $sql = "SELECT  CONCAT(firstname,\" \", lastname) AS Nombre, round(mdl_quiz_grades.grade,1) AS Nota 
FROM ((mdl_user RIGHT JOIN mdl_quiz_grades ON mdl_user.id=mdl_quiz_grades.userid) 
LEFT JOIN mdl_quiz ON mdl_quiz.id=mdl_quiz_grades.quiz) 
LEFT JOIN mdl_course ON mdl_course.id=mdl_quiz.course";


    $params = array();

    $resultados = $DB->get_records_sql($sql, $params, 0, $userlimit=0);

    return $resultados;

}



function sql_grafica_circular_json()
{
    return json_encode(array_values(sql_grafica_circular()));
}


function sql_grafica_barras()
{

    global $DB;


    /*
        $sql = "SELECT  CONCAT(firstname,\" \", lastname) AS Nombre, round(mdl_quiz_grades.grade,1) AS Nota
    FROM ((mdl_user RIGHT JOIN mdl_quiz_grades ON mdl_user.id=mdl_quiz_grades.userid)
    LEFT JOIN mdl_quiz ON mdl_quiz.id=mdl_quiz_grades.quiz)
    LEFT JOIN mdl_course ON mdl_course.id=mdl_quiz.course";


        $params = array();

        $resultados = $DB->get_records_sql($sql, $params, 0, $userlimit=0);//return $resultados;
    */
    $resultados = array(144,89,55,34,21,13,8,5,3,2,1,2,3,5,8,13,21,34,55,89,144);
    return $resultados;

}


function sql_categories_report($course_id = 7, $group_id = 9, $quiz_id = 23)
{

    global $DB;

    $conditions1= '';
    if(!is_null($courseid)){
        $conditions1 = " and c.id = $course_id ";
    }
    if (!is_null($groupid)){
        $conditions1 = $conditions." and g.groupid = $group_id ";
    }

    $conditions2 = '';
    if (!is_null($quiz_id)){
        $conditions2 = " and q.id = $quiz_id ";
    }




    $sql = "SELECT 
	@rn2:=@rn2 + 1 AS id, b.* from (
	select 
    mhid,
    mhnombre,
    idquiz,
    max(notaletra) notaletravalor,
	round(SUM(notaporcentaje),2) notaporcentaje,
    round(SUM(notapregunta),2) notapregunta,
    puntosmaximos,
    alumnoid,
    alumnoposicion,
    alumnonombre,
    groupid
FROM
    (SELECT 
        qc.id AS mhid,
            qc.name AS mhnombre,
            q.id AS idquiz,
            qas.state,
            IFNULL((qa.maxmark * qas.fraction * 100 / qa.maxmark), 0) AS notaporcentaje,
            (SELECT 
                    CASE notaporcentaje = IFNULL((qa.maxmark * qas.fraction * 100 / qa.maxmark), 0)
                            WHEN notaporcentaje < 40 THEN 1
                            WHEN
                                (40.0 <= notaporcentaje)
                                    AND (notaporcentaje < 50.0)
                            THEN
                                2
                            WHEN
                                (50.0 <= notaporcentaje)
                                    AND (notaporcentaje < 70.0)
                            THEN
                                3
                            WHEN (70.0 <= notaporcentaje) THEN 4
                            ELSE -1
                        END
                ) AS notaletra,
            IFNULL(qa.maxmark * qas.fraction, 0) AS notapregunta,
            IFNULL(ROUND(maxmark, 1), 0) AS puntosmaximos,
            u.alumnoid AS alumnoid,
            u.alumnoposicion,
            alumnonombre,
            gm.groupid
    FROM
        (SELECT 
        @rn:=@rn + 1 AS alumnoposicion, alumno.*
    FROM
        (SELECT 
        u1.id alumnoid,
            CONCAT(u1.firstname, ' ', u1.lastname) alumnonombre,
            g.id groupoid,
            g.name gruponombre
    FROM
        mdl_role_assignments ra
    JOIN mdl_user u1 ON u1.id = ra.userid
    JOIN mdl_role r ON r.id = ra.roleid
    JOIN mdl_context cxt ON cxt.id = ra.contextid
    JOIN mdl_course c ON c.id = cxt.instanceid
    JOIN mdl_groups_members gm ON gm.userid = u1.id
    JOIN mdl_groups g ON g.id = gm.groupid
    WHERE
        ra.userid = u1.id
            AND ra.contextid = cxt.id
            AND cxt.contextlevel = 50
            AND cxt.instanceid = c.id
            AND roleid = 5 ".$conditions1."
    ORDER BY u1.lastname , u1.firstname) alumno, (SELECT @rn:=0) t2) u
    JOIN mdl_quiz_attempts quiza ON u.alumnoid = quiza.userid
    LEFT JOIN mdl_question_usages qu ON quiza.uniqueid = qu.id
    LEFT JOIN mdl_question_attempts qa ON qa.questionusageid = qu.id
    LEFT JOIN mdl_question_attempt_steps qas ON qas.questionattemptid = qa.id
    LEFT JOIN mdl_quiz q ON q.id = quiza.quiz
    LEFT JOIN mdl_groups_members gm ON u.alumnoid = gm.userid
    LEFT JOIN mdl_question ques ON ques.id = qa.questionid
    LEFT JOIN mdl_question_categories qc ON ques.category = qc.id
    WHERE 1=1 
        ".$conditions2."
    ) a


group by 
mhid, mhnombre, idquiz, alumnoid, alumnoposicion, alumnonombre, groupid 

ORDER BY mhid , alumnoposicion , groupid , notaletra ) b, (SELECT @rn2:=0) t3

";


    $params = array();

    $resultados = $DB->get_records_sql($sql, $params, 0, $userlimit=0);


    return $resultados;

}


function sql_categories_report_json($course_id = 7, $group_id = 9, $quiz_id = 23)
{
    $resultados = sql_categories_report( $course_id, $group_id , $quiz_id  );

    $alumno_lista_posicion = array();
    $categoria_lista = array();
    $categoria_item_nota_lista = array();
    $alumno_posicion = 0;
    $salida = array();

    //print_object($resultados);
    foreach ($resultados as $id=>$resultado){
        $letra = '';
        switch ($resultado->notaletravalor) {
            case 1:
                $letra = 'D';
                break;
            case 2:
                $letra = 'R';
                break;
            case 3:
                $letra = 'B';
                break;

            case 4:
                $letra = 'S';
        }
        // asigno a cada alumno una posicion en la lista final
        if(!array_key_exists($resultado->alumnoid,$alumno_lista_posicion)){
            $alumno_posicion +=1;
            $alumno_lista_posicion[$resultado->alumnoid] = array(
                'alumnoposicion'=>$alumno_posicion,
                'alumnoid'=>$resultado->alumnoid,
                'alumnonombre'=>$resultado->alumnonombre
                );
        }
        //mantengo la lista madre de categorias
        if (!array_key_exists($resultado->mhid, $categoria_lista)){

            $categoria_item = array();
            $categoria_item['mhid']=$resultado->mhid;
            $categoria_item['id']=$resultado->id;
            $categoria_item['mhnombre']=$resultado->mhnombre;
            $categoria_item['idquiz']=$resultado->idquiz;
            $categoria_lista[$resultado->mhid]=$categoria_item;
        }
        //mantengo la lista de notas dentro de una categoria
        if (!array_key_exists($resultado->mhid, $categoria_item_nota_lista)){
            $categoria_item_nota_lista[$resultado->mhid]=array();

        }
        if (!array_key_exists($resultado->notaletravalor, $categoria_item_nota_lista[$resultado->mhid])){

            //lo inicio con las categorias de notas hardcoded, corregir a dinamico luego
            //D
            $categoria_item_nota_item = array();
            $categoria_item_nota_item['notaletra']="D";
            $categoria_item_nota_item['notaletravalor']=1;
            $categoria_item_nota_item['cantidadalumnos']=0;
            $categoria_item_nota_item['categorianombre']=$resultado->mhnombre;
            $categoria_item_nota_item['mhid']=$resultado->mhid;
            $categoria_item_nota_item['alumnos']=array();

            $categoria_item_nota_lista[$resultado->mhid][1]=$categoria_item_nota_item;
            //B
            $categoria_item_nota_item = array();
            $categoria_item_nota_item['notaletra']="R";
            $categoria_item_nota_item['notaletravalor']=2;
            $categoria_item_nota_item['cantidadalumnos']=0;
            $categoria_item_nota_item['categorianombre']=$resultado->mhnombre;
            $categoria_item_nota_item['mhid']=$resultado->mhid;
            $categoria_item_nota_item['alumnos']=array();

            $categoria_item_nota_lista[$resultado->mhid][2]=$categoria_item_nota_item;

            //B
            $categoria_item_nota_item = array();
            $categoria_item_nota_item['notaletra']="B";
            $categoria_item_nota_item['notaletravalor']=3;
            $categoria_item_nota_item['cantidadalumnos']=0;
            $categoria_item_nota_item['categorianombre']=$resultado->mhnombre;
            $categoria_item_nota_item['mhid']=$resultado->mhid;
            $categoria_item_nota_item['alumnos']=array();

            $categoria_item_nota_lista[$resultado->mhid][3]=$categoria_item_nota_item;

            //S
            $categoria_item_nota_item = array();
            $categoria_item_nota_item['notaletra']="S";
            $categoria_item_nota_item['notaletravalor']=4;
            $categoria_item_nota_item['cantidadalumnos']=0;
            $categoria_item_nota_item['categorianombre']=$resultado->mhnombre;
            $categoria_item_nota_item['mhid']=$resultado->mhid;
            $categoria_item_nota_item['alumnos']=array();

            $categoria_item_nota_lista[$resultado->mhid][4]=$categoria_item_nota_item;

        }
        array_push($categoria_item_nota_lista[$resultado->mhid][$resultado->notaletravalor]['alumnos'], $alumno_lista_posicion[$resultado->alumnoid]);
        $categoria_item_nota_lista[$resultado->mhid][$resultado->notaletravalor]['cantidadalumnos'] +=1;

    }

    //echo 'categoria_item_nota_lista';
    //print_object($categoria_item_nota_lista);

    foreach ($categoria_item_nota_lista as $item){

        $mhidactual='0';
        //echo 'lista item';
       // print_object($item);
        ksort($item);
        $salidanotas = array();
        foreach ($item as $nota){

            //print_object($nota);
           $mhidactual = $nota['mhid'];
            array_push( $salidanotas, $nota);
        }
        //echo 'categoria columna';
        $categoria_lista[$mhidactual]['notas'] = $salidanotas;
        //print_object($categoria_lista[$mhidactual]);
        //print_object(json_encode($salidanotas));
    }

    //print_object($categoria_lista);
    //print_object(json_encode($categoria_item_nota_lista));



    //genero la salida a partir de la lista de categorias
    foreach ($categoria_lista as $id=>$categoria){

        array_push($salida, $categoria);
    }

    //print_object($salida);
    //print_object(json_encode($salida));

    return json_encode(array_values($salida));

}


function sql_course_students($courseid = null, $groupid = null)
{

    global $DB;
    $conditions= '';
    if(!is_null($courseid)){
        $conditions = "and c.id = $courseid ";
    }
    if (!is_null($groupid)){
        $conditions = $conditions." and gm.groupid = $groupid ";
    }

    $sql = "SELECT  u.id alumnoid, c.id cursoid, gm.groupid grupoid, CONCAT(firstname,' ', lastname) alumnonombre, u.firstname nombre,u.lastname alumnoapellido, 
c.shortname 'codigocurso',c.fullname 'nombrecurso'
FROM mdl_user u
inner join mdl_groups_members gm on gm.userid = u.id
INNER JOIN mdl_role_assignments ra ON ra.userid = u.id
INNER JOIN mdl_context ct ON ct.id = ra.contextid
INNER JOIN mdl_course c ON c.id = ct.instanceid
INNER JOIN mdl_role r ON r.id = ra.roleid
INNER JOIN mdl_course_categories cc ON cc.id = c.category
WHERE roleid=5 ".$conditions ." order by gm.groupid, u.lastname"; //aca poner condicional el groupid y el courseid

//    echo $sql;

    $params = array();

    $resultados = $DB->get_records_sql($sql, $params, 0, $userlimit=0);


    return $resultados;

}


function sql_course_students_json($courseid = null, $groupid = null){
    return json_encode(array_values(sql_course_students($courseid, $groupid)));
}

function sql_get_docentes_curso($curso)
{

    global $DB;

    $sql = "SELECT u.firstname Nombre,u.lastname Apellido, c.shortname 'Codigo Curso',c.fullname 'Nombre curso', c.id cursoid
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


function sql_get_docentes_curso_json($curso)
{

    return json_encode(array_values(sql_get_docentes_curso($curso)));

}

function sql_get_quiz_stats(){
/*    SET @idQuiz = 23;
SET @porcentajeDeAprobacion = 50;

SELECT tabla1.grupo,alumnosQueAsistieron,
cantidadAprobados,
(alumnosQueAsistieron - cantidadAprobados) as CantidadNoAprobados,
truncate((cantidadAprobados*100 / alumnosQueAsistieron),1) as PorcentajeAprobados
FROM
(SELECT groupid as grupo, count(*) as alumnosQueAsistieron
FROM quatros1_moodle_20161125.mdl_impulsoweb_student_tests
WHERE  idquiz=@idQuiz
Group by grupo
order by grupo) tabla1
INNER JOIN
    (SELECT groupid as grupo, count(*) as cantidadAprobados
FROM quatros1_moodle_20161125.mdl_impulsoweb_student_tests
WHERE  idquiz=@idQuiz and pruebanota > @porcentajeDeAprobacion
Group by grupo
order by grupo) tabla2
ON  tabla1.grupo = tabla2.grupo
*/
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
        FROM mdl_impulsoweb_milestones m
        where m.course_id  = $curso"
    ;



    $params = array();

    $resultados = $DB->get_records_sql($sql, $params, 0, $userlimit=0);
//    print_object($resultados);

    return $resultados;

}


function sql_get_milestones_json($curso){
    return json_encode(array_values(sql_get_milestones($curso)));
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
//     $sql = "SELECT   u.id as alumnoId, CONCAT(firstname,' ', lastname) AS alumnoNombre, round(qg.grade, 1) AS pruebanota ,
//round((qg.grade *100/q.grade),1) as porcentaje,
//    round(q.grade,1) notatotal, q.name as nombreprueba, from_unixtime(q.timeopen) as pruebafecha, q.id as idquiz
//    FROM ((mdl_user u RIGHT JOIN mdl_quiz_grades qg ON u.id=qg.userid)
//    LEFT JOIN mdl_quiz q ON q.id=qg.quiz)
//    LEFT JOIN mdl_course c ON c.id=q.course";

     $sql = "SELECT   u.id as alumnoid, 
        CONCAT(firstname,' ', lastname) AS alumnonombre,IFNULL(round(qg.grade, 1),0) AS pruebanotaoriginal,
        IFNULL(round((qg.grade *100/q.grade),1),0) as pruebanota,  
        round(q.grade,1) notatotal, q.name as nombreprueba, date(from_unixtime(q.timeopen)) as pruebafecha, q.id as idquiz, 
        round(q.grade,1) as puntajemax, gm.groupid
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
         order by IFNULL(round((qg.grade *100/q.grade),1),0) desc, CONCAT(firstname,' ', lastname)";

//    WHERE u.id = $student_id  and c.id = 3"; //agregar el grupo
//     WHERE c.id =3 and roleid=5 and q.id IN (Select quiz FROM mdl_quiz_grades) order by CONCAT(firstname,' ', lastname)";

     $sql2 = "select * from mdl_impulsoweb_student_tests  where groupid = 9";



    $params = array();

//     if (!is_null($course_id ) {
//        $params = array('u.id', $student_id);
//     }
//     if (!is_null($student_id)){
//         $params = array_merge($params, array('c.id', $course_id));
//     }

    $resultados = $DB->get_records_sql($sql2, $params, 0, $userlimit=0);


    return $resultados;

}


function sql_get_student_tests_json($student_id = null, $course_id = null){
    return json_encode(array_values(sql_get_student_tests()));
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
FROM mdl_impulsoweb_goals goal";
// $sql = "select * from quatros1_moodle.mdl_impulsoweb_goals ";

    $params = array();
    $resultados = $DB->get_records_sql($sql, $params, 0, $userlimit=0);


    return array_values($resultados);
}

function sql_get_goals_json($course_id=null){
    return json_encode(array_values(sql_get_goals($course_id)));
}