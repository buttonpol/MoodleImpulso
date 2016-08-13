use quatros1_moodle;
SELECT CONCAT(firstname,"", lastname) AS Nombre,mdl_quiz_grades.grade AS Nota 
FROM ((mdl_user RIGHT JOIN mdl_quiz_grades ON mdl_user.id=mdl_quiz_grades.userid) LEFT JOIN mdl_quiz ON mdl_quiz.id=mdl_quiz_grades.quiz) LEFT JOIN mdl_course ON mdl_course.id=mdl_quiz.course

#---------- Lo siguiente es para que el resultado vaya para un CSV.
INTO OUTFILE 'C:\\xampp\\htdocs\\MoodleImpulso\\D3_Graficas\\SVG_Circular\\Hola3.csv'
FIELDS TERMINATED BY ','
#ENCLOSED BY '"' 
LINES TERMINATED BY '\n';