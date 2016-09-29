<?php
// This file is part of Moodle - http://moodle.org/
//
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
 *
 */

namespace block_simplehtml;

defined('MOODLE_INTERNAL') || die();

/**
 * Class used to list and count online users
 *
 * @package    block_online_users
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class fetcher
{

    /** @var string The SQL query for retrieving a list of online users */
    public $sql;
    /** @var string The SQL query for counting the number of online users */
    public $csql;
    /** @var string The params for the SQL queries */
    public $params;

    /**
     * Class constructor
     *
     * @param int $currentgroup The group (if any) to filter on
     * @param int $now Time now
     * @param int $timetoshowusers Number of seconds to show online users
     * @param context $context Context object used to generate the sql for users enrolled in a specific course
     * @param bool $sitelevel Whether to check online users at site level.
     * @param int $courseid The course id to check
     */
    public function __construct()
    {
        $this->set_sql();
    }

    /**
     * Store the SQL queries & params for listing online users
     *
     * @param int $currentgroup The group (if any) to filter on
     * @param int $now Time now
     * @param int $timetoshowusers Number of seconds to show online users
     * @param context $context Context object used to generate the sql for users enrolled in a specific course
     * @param bool $sitelevel Whether to check online users at site level.
     * @param int $courseid The course id to check
     */
    protected function set_sql()
    {

        $params = array();


        $sql = "SELECT CONCAT(firstname,\";\", lastname) AS Nombre,mdl_quiz_grades.grade AS Nota 
FROM ((mdl_user RIGHT JOIN mdl_quiz_grades ON mdl_user.id=mdl_quiz_grades.userid) 
LEFT JOIN mdl_quiz ON mdl_quiz.id=mdl_quiz_grades.quiz) 
LEFT JOIN mdl_course ON mdl_course.id=mdl_quiz.course";


        $this->sql = $sql;
        $this->params = $params;
    }

    /**
     * Get a list of the most recent online users
     *
     * @param int $userlimit The maximum number of users that will be returned (optional, unlimited if not set)
     * @return array
     */
    public function get_resultados($userlimit = 0)
    {
        global $DB;
        $resultados = $DB->get_records_sql($sql, $params, 0, $userlimit);
        return $resultados;
    }

    /**
     * Count the number of online users
     *
     * @return int
     */
    public function count_users()
    {
        global $DB;
        return $DB->count_records_sql($this->csql, $this->params);
    }

}
