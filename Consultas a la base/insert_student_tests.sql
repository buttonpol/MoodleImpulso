INSERT INTO `quatros1_moodle_20161125`.`mdl_impulsoweb_student_tests`
(
`idalumno`,
`alumnoNombre`,
`pruebanotaoriginal`,
`pruebanota`,
`notatotal`,
`nombreprueba`,
`pruebafecha`,
`idquiz`,
`puntajemax`,
`groupid`)




SELECT   u.id as idalumno,
        CONCAT(firstname,' ', lastname) AS alumnoNombre,IFNULL(round(qg.grade, 1),0) AS pruebanotaoriginal,
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
         -- WHERE c.id = 7 and groupid = 8 and /*esto era para filtrar grupo y curso*/
         where roleid=5 and q.id IN (Select distinct quiz FROM mdl_quiz_grades)
         order by q.id, u.id
         ;





select count(*) from mdl_impulsoweb_student_tests where groupid = 9 and idquiz =23 and pruebanota <50 ;

select count(*) from mdl_impulsoweb_student_tests where groupid = 9 and idquiz =23 and pruebanota <50 ;
