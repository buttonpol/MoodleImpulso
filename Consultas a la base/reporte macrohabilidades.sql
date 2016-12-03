SELECT
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

 WHERE   qas.state='gradedpartial'  and q.id=23
#group by categoria,alumnoNombre
order by groupid, mhid,notaletra

;
