<?php
/**
 * Created by PhpStorm.
 * User: pol
 * Date: 23/08/2016
 * Time: 22:37
 */

class block_simplehtml extends block_base
{
    public function init()
    {
        $this->title = get_string('simplehtml', 'block_simplehtml');
    }

    //inmediatamente despues de init()
    public function specialization() {
        if (isset($this->config)) {
            if (empty($this->config->title)) {
                $this->title = get_string('defaulttitle', 'block_simplehtml');
            } else {
                $this->title = $this->config->title;
            }

            if (empty($this->config->text)) {
                $this->config->text ='<script type=\"text/javascript\" src=\"file.js\"></script>';
                $this->config->text .= get_string('defaulttext', 'block_simplehtml');
            }
        }
    }

    //el contenido del bloque
    public function get_content()
    {
        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;
        if (! empty($this->config->text)) {
            $this->content->text = $this->config->text;
        }else{
            $this->content->text = 'The content of our SimpleHTML block!';
        }


        global $COURSE;

// The other code.

        $url = new moodle_url('/blocks/simplehtml/view.php', array('blockid' => $this->instance->id, 'courseid' => $COURSE->id));
        $this->content->footer = html_writer::link($url, get_string('addpage', 'block_simplehtml'));

        return $this->content;
    }

    public function instance_allow_multiple() {
        return true;
    }
}