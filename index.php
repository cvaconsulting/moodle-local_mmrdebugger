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
 * Main file
 *
 * @package   local_mmrdebugger
 * @copyright 2012 Juan Leyva <jleyva@cvaconsulting.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->libdir . '/adminlib.php');

require_login();
require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

admin_externalpage_setup('local_mmrdebugger', '', null);

echo $OUTPUT->header();

$cache = cache::make('local_mmrdebugger', 'messages');

if ($users = $cache->get(0)) {
    echo $OUTPUT->heading(get_string("users"));
    echo $OUTPUT->container_start('info');
    foreach($users as $userid){
        if($user = $DB->get_record('user', array('id' => $userid, 'deleted' => 0))){
            $link = new moodle_url('/local/mmrdebugger/shell.php', array('id'=>$userid));
            echo '<p>'.$OUTPUT->action_link($link, fullname($user), new popup_action('click', $link, 'user'.$userid, array('height' => 600, 'width' => 800))).'</p>';
        }
    }
    echo $OUTPUT->container_end();

} else {
    echo get_string("noactiveusers", "local_mmrdebugger");
    
}

echo $OUTPUT->footer();
