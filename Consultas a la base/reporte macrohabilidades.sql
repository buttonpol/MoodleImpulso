
SELECT
 qc.id as idCategoria, qc.name	as categoria,
 truncate(qa.maxmark * qas.fraction,1) as notaPregunta,
 truncate(maxmark,1) as puntosMaximos,
 truncate((qa.maxmark * qas.fraction*100/maxmark ),1) as notaPorcentaje,
 (Select case notaPorcentaje=truncate((qa.maxmark * qas.fraction*100/maxmark ),1)
 WHEN notaPorcentaje<40 THEN 'D'
 WHEN (40.0<=notaPorcentaje) and (notaPorcentaje<60.0) THEN 'R'
 WHEN (60.0<=notaPorcentaje) and (notaPorcentaje<90.0) THEN 'B'
 WHEN (90.0<=notaPorcentaje) and (notaPorcentaje<100.0) THEN 'S'
 else 'otro' end)  as GrupoNota,
 u.id as idAlumno,CONCAT(u.firstname,' ', u.lastname) AS alumnoNombre,ques.name AS pregunta

FROM mdl_quiz_attempts quiza
JOIN mdl_question_usages qu ON qu.id = quiza.uniqueid
JOIN mdl_question_attempts qa ON qa.questionusageid = qu.id
JOIN mdl_question_attempt_steps qas ON qas.questionattemptid = qa.id
LEFT JOIN mdl_question_attempt_step_data qasd ON qasd.attemptstepid = qas.id
LEFT JOIN mdl_quiz q ON quiza.quiz=q.id
LEFT JOIN mdl_user u ON quiza.userid=u.id
LEFT JOIN mdl_question ques ON ques.id = qa.questionid
LEFT JOIN mdl_question_categories qc ON ques.category = qc.id

WHERE  qas.state='gradedpartial' and q.id=17
#group by categoria,alumnoNombre
order by categoria,GrupoNota