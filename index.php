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

$id = optional_param('id', 0, PARAM_INT);

require_login();
require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

admin_externalpage_setup('local_mmrdebugger', '', null);

echo $OUTPUT->header();

$cache = cache::make('local_mmrdebugger', 'messages');

if ($id) {
    $user = $DB->get_record('user', array('id'=>$id), '*', MUST_EXIST);

    echo '<iframe src="user.php?id=' .$id. '" width="100%" height="400" name="uactions" id="uactions"></iframe>';
    echo '<form id="userf" action="user.php" target="uactions" onsubmit="">
        <input type="text" size="40" name="command" id="command">
        <input type="hidden" name="id" value="'.$id.'">
        <input type="hidden" name="type" value="command">
        ';
    
    echo html_writer::start_tag('img',  array('src' => $OUTPUT->pix_url('camera', 'local_mmrdebugger'),
                                              'alt' => get_string('screenshot'),
                                              'id' => 'screenshotbutton'
                                              ));
    echo html_writer::start_tag('img',  array('src' => $OUTPUT->pix_url('phone', 'local_mmrdebugger'),
                                              'alt' => get_string('streaming', 'local_mmrdebugger'),
                                              'id' => 'streampagebutton'
                                              ));
    echo '</form>';
    
    $jsmodule = array(
                    'name' => 'local_mmrdebugger',
                    'fullpath' => '/local/mmrdebugger/module.js',
                    'requires' => array("io", "gallery-base64"));
    
    $PAGE->requires->js_init_call('M.local_mmrdebugger.init', array(array('userid'=>$id)), false, $jsmodule);    
    
    
} else {
    if ($users = $cache->get(0)) {
        echo $OUTPUT->heading(get_string("users"));
        echo $OUTPUT->container_start('info');
        foreach($users as $userid){
            if($user = $DB->get_record('user', array('id' => $userid, 'deleted' => 0))){
                $link = new moodle_url('/local/mmrdebugger/index.php', array('id'=>$userid));
                echo html_writer::link($link, fullname($user));
            }
        }
        echo $OUTPUT->container_end();
    
    } else {
        echo get_string("noactiveusers", "local_mmrdebugger");
    }
    
    echo $OUTPUT->heading(html_writer::link("", get_string("refresh")));
}

echo $OUTPUT->footer();
