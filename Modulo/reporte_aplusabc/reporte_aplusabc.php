<?php
/**
 * Created by PhpStorm.
 * User: pol
 * Date: 15/8/2016
 * Time: 23:26
 */

class block_reporte_aplusabc extends block_base
{
    public function init()
    {
        $this->title = get_string('reporte_aplusabc', 'block_reporte_aplusabc');
    }

    public function get_content()
    {
        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = 'The content of our SimpleHTML block!';
        $this->content->footer = 'Footer here...';

        return $this->content;
    }
}