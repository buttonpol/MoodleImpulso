<?php
/**
 * Created by PhpStorm.
 * User: pol
 * Date: 24/09/2016
 * Time: 16:20
 */
/*
$services = array(
    'MY SERVICE' => array(
        'functions' => array ('local_PLUGINNAME_FUNCTIONNAME'),
        'restrictedusers' => 0, // if 1, the administrator must manually select which user can use this service.
        // (Administration > Plugins > Web services > Manage services > Authorised users)
        'enabled'=>1, // if 0, then token linked to this service won't work
    )
);
*/


// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Web service local plugin template external functions and service definitions.
 *
 * @package    localwstemplate
 * @copyright  2011 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// We defined the web service functions to install.
$functions = array(
    'blocks_simplehtml_hello_world' => array(
        'classname'   => 'blocks_simplehtml_external',
        'methodname'  => 'hello_world',
        'classpath'   => 'blocks/simplehtml/externallib.php',
        'description' => 'Return Hello World FIRSTNAME. Can change the text (Hello World) sending a new text as parameter',
        'type'        => 'read',
    )
);

// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = array(
    'My service' => array(
        'functions' => array ('blocks_simplehtml_hello_world'),
        'restrictedusers' => 0,
        'enabled'=>1,
    )
);
