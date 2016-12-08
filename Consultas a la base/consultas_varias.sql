/*
	consulta que saca la lista de alumnos de un curso/grupo

*/


select * from
(SELECT @rn:=@rn+1 AS alumnoposicion, alumno.*
	from (
			select  u1.id alumnoid, concat( u1.firstname, ' ', u1.lastname) alumnonombre, g.id groupoid, g.name gruponombre
			FROM
			mdl_role_assignments ra
			JOIN mdl_user u1 ON u1.id = ra.userid
			JOIN mdl_role r ON r.id = ra.roleid
			JOIN mdl_context cxt ON cxt.id = ra.contextid
			JOIN mdl_course c ON c.id = cxt.instanceid
            join mdl_groups_members gm on gm.userid = u1.id
            join mdl_groups g on g.id = gm.groupid

			WHERE ra.userid = u1.id

			AND ra.contextid = cxt.id
			AND cxt.contextlevel =50
			AND cxt.instanceid = c.id
			AND  roleid = 5
			and c.id = 7 /* este es el curso que tiene que estar dinamico */
			and g.id in (8,9) /*este es el grupo que tiene que estar dinamico*/
			ORDER BY u1.lastname, u1.firstname
		)alumno,(SELECT @rn:=0) t2
    ) u ;



/*
consulta de generacion de reporte de macrohabilidades
*/
SELECT
 qc.id as mhid, qc.name	as mhnombre, q.id as idquiz, qas.state,
  ifnull(truncate((qa.maxmark * qas.fraction*100/qa.maxmark ),1), 0) as notaporcentaje,
 (Select case notaporcentaje=ifnull(truncate((qa.maxmark * qas.fraction*100/qa.maxmark ),1), 0)
 WHEN notaporcentaje<40 THEN 'D'
 WHEN (40.0<=notaporcentaje) and (notaporcentaje<50.0) THEN 'R'
 WHEN (50.0<=notaporcentaje) and (notaporcentaje<70.0) THEN 'B'
 WHEN (70.0<=notaporcentaje)  THEN 'S'
 else 'otro' end)  as notaletra,
 truncate(qa.maxmark * qas.fraction,1) as notapregunta,
 truncate(maxmark,1) as puntosmaximos,
 u.alumnoid as alumnoid, u.alumnoposicion,  alumnonombre,
 gm.groupid

/*
tablas
*/
FROM

    /*alumnos del curso numerados*/

	(SELECT @rn:=@rn+1 AS alumnoposicion, alumno.*
	from (
			select  u1.id alumnoid, concat( u1.firstname, ' ', u1.lastname) alumnonombre, g.id groupoid, g.name gruponombre
			FROM
			mdl_role_assignments ra
			JOIN mdl_user u1 ON u1.id = ra.userid
			JOIN mdl_role r ON r.id = ra.roleid
			JOIN mdl_context cxt ON cxt.id = ra.contextid
			JOIN mdl_course c ON c.id = cxt.instanceid
            join mdl_groups_members gm on gm.userid = u1.id
            join mdl_groups g on g.id = gm.groupid

			WHERE ra.userid = u1.id

			AND ra.contextid = cxt.id
			AND cxt.contextlevel =50
			AND cxt.instanceid = c.id
			AND  roleid = 5
			and c.id = 7 /* este es el curso que tiene que estar dinamico */
			and g.id in (12,8,9) /*este es el grupo que tiene que estar dinamico*/
			ORDER BY u1.lastname, u1.firstname
		)alumno,(SELECT @rn:=0) t2
	) u
/*fin alumnos del curso numerados*/
LEFT JOIN mdl_quiz_attempts quiza ON u.alumnoid = quiza.userid
JOIN mdl_question_usages qu ON quiza.uniqueid= qu.id
JOIN mdl_question_attempts qa ON qa.questionusageid = qu.id
JOIN mdl_question_attempt_steps qas ON qas.questionattemptid = qa.id
-- JOIN mdl_question_attempt_step_data qasd ON qasd.attemptstepid = qas.id
JOIN mdl_quiz q ON q.id  = quiza.quiz
left join mdl_groups_members gm on gm.userid = u.alumnoid
JOIN mdl_question ques ON ques.id = qa.questionid
LEFT JOIN mdl_question_categories qc ON ques.category =qc.id

/*
condiciones
*/
  WHERE -- (qas.state in('gradedpartial', 'gradedright', 'gradedwrong', 'todo') or qas.state is null) and
  -- WHERE   qas.state in('gradedpartial', 'gradedright')  and
  (q.id=23 or q.id is null)
 and   alumnoposicion in (18, 1)
order by  mhid,  u.alumnoposicion,groupid,notaletra

;


/*

context es una instancia de algo en moodle
    contextlevel es el tipo de instancia.
        50 = curso
        40 = categoria
    instanceid es el id de la instancia
    path es la secuencia de id de contextos en la que se encuentra
*/