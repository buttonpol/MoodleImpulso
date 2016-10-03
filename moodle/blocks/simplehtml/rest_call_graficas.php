<?php
/**
 * Created by PhpStorm.
 * User: pol
 * Date: 10/09/2016
 * Time: 12:32
 */

/**
 * esto tiene que ser un servicio de moodle o ver de usar alguna otra técnica
 * ver https://docs.moodle.org/dev/Web_services
 * por las dudas también https://docs.oracle.com/cd/E24329_01/web.1211/e24983/secure.htm#RESTF113
 *
 */
// aca está la lógica
require_once('lib.php');


echo json_encode(sql_grafica_circular());


?>
