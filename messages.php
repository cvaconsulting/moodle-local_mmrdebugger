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
 * Publig messages hub
 * A basic messages processor that stores the messages in the application cache
 *
 * @package   local_mmrdebugger
 * @copyright 2012 Juan Leyva <jleyva@cvaconsulting.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->dirroot . '/webservice/lib.php');

$token = required_param('token', PARAM_ALPHANUM);
$messageid = optional_param('messageid', 0, PARAM_INT);
$action = required_param('action', PARAM_STRINGID);
$response = optional_param('response', '', PARAM_RAW);
$type = optional_param('type', '', PARAM_STRINGID);

// Auth the user.
$webservicelib = new webservice();
$authenticationinfo = $webservicelib->authenticate_user($token);

$userid = $authenticationinfo['user']->id;

$cache = cache::make('local_mmrdebugger', 'messages');

// Security, active users allways has an element in cache
if (!$messages = $cache->get($userid)) {
    // We add this user as active
    if(!$activeusers = $cache->get(0)) {
        $activeusers = array();
    }
    $activeusers[] = $userid;
    $cache->set(0, array_unique($activeusers));
    die;
}

if ($action == 'get_messages') {
    $pendingmessages = array();
    foreach ($messages as $key=>$message) {
        if (empty($message['response'])) {
            $pendingmessages[$key] = $message;
        }
    }
    echo json_encode($pendingmessages);
} else if ($action == 'reply_message') {
    $messages[$messageid]['response'] = $response;
    $messages[$messageid]['type'] = $type;
    $cache->set($userid, $messages);
}
