
         SET @idQuiz = 23;
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
;






SELECT tabla1.grupo,alumnosGrupo,
alumnosAsistieron,
(alumnosGrupo - alumnosAsistieron) as Faltaron   FROM (SELECT  gm.groupid as grupo,count(*) as alumnosGrupo
FROM mdl_user u
inner join mdl_groups_members gm on gm.userid = u.id
INNER JOIN mdl_role_assignments ra ON ra.userid = u.id
INNER JOIN mdl_context ct ON ct.id = ra.contextid
INNER JOIN mdl_course c ON c.id = ct.instanceid
INNER JOIN mdl_role r ON r.id = ra.roleid
INNER JOIN mdl_course_categories cc ON cc.id = c.category
WHERE roleid=5 and c.id=7  group by grupo order by gm.groupid, u.lastname)  tabla1 INNER JOIN

(SELECT groupid as grupo, count(*) as alumnosAsistieron
FROM quatros1_moodle.mdl_impulsoweb_student_tests
WHERE idquiz=-1
group by grupo
order by grupo) tabla2 ON  tabla1.grupo = tabla2.grupo;