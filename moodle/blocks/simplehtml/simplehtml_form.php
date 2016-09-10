<?php
/**
 * Created by PhpStorm.
 * User: pol
 * Date: 10/09/2016
 * Time: 9:58
 */


require_once("{$CFG->libdir}/formslib.php");

class simplehtml_form extends moodleform {

    function definition() {

        $mform =& $this->_form;
        $mform->addElement('header','displayinfo', get_string('textfields', 'block_simplehtml'));
    }
}