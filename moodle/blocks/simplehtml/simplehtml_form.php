<?php
/**
 * Created by PhpStorm.
 * User: pol
 * Date: 10/09/2016
 * Time: 9:58
 */

/**
 *
 * simplehtml_form.php is created which defines the class used later to display our form.
 */


require_once("{$CFG->libdir}/formslib.php");

class simplehtml_form extends moodleform {

    function definition() {

        $mform =& $this->_form;
        $mform->addElement('header','displayinfo', get_string('textfields', 'block_simplehtml'));
    }
}