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
 * @package   local_uservalform
 * @copyright 2018-2019, Creatic SAS <soporte@creatic.co>.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot . '/local/uservalform/modalsettings_form.php');

// Validation form settings requires a course ID parameter.
$courseid = required_param('courseid', PARAM_INT);
$return = new moodle_url('/course/view.php', array('id'=>$courseid));

// Gets database data from course and local_uservalform settings table.
$course = $DB->get_record('course', array('id'=>$courseid), '*', MUST_EXIST);
$uservalformdata = $DB->get_record('local_uservalform', array('courseid' => $courseid));

// If there is not records, creates an object.
if (!$uservalformdata) {
    $uservalformdata = new stdClass();
}

// Uses course ID data to define a course context variable. And check update
// course capability.
$uservalformdata->courseid = $courseid;
$coursecontext = context_course::instance($course->id);
require_login($course);
require_capability('moodle/course:update', $coursecontext);


$PAGE->set_title(get_string('modalsettingstitle', 'local_uservalform'));
$PAGE->set_heading($course->fullname);
$PAGE->set_pagelayout('course');
$PAGE->set_url('/local/uservalform/modalsettings.php', array('courseid'=>$courseid));

// Gets database information about custom field types and categories.
$userinfocategory = $DB->get_records('user_info_category', null, 'sortorder');
$userinfofield = $DB->get_records('user_info_field', null,
        'categoryid,sortorder', 'id,shortname,name,description,categoryid,sortorder');

$settingsform = new local_uservalform_form_settings(null, array($userinfocategory, $userinfofield, $uservalformdata));

if ($settingsform->is_cancelled()) {
    redirect($return);

// Processes settings data.
} else if ($data = $settingsform->get_data()) {

    // Checks if custom field record exists to set the ID in submitted data.
    foreach($userinfofield as $field) {
        if($field->name == $data->fieldname) {
            $data->userinfofieldid = $field->id;
        }
    }

    // Checks if a user validation form data ID exist to insert or update data.
    if (isset($uservalformdata->id)) {
        $data->id = $uservalformdata->id;
        $DB->update_record('local_uservalform', $data);
    } else {
        $DB->insert_record('local_uservalform', $data);
    }

    // Redirects with a notification.
    redirect($PAGE->url, get_string('changessaved'), null, \core\output\notification::NOTIFY_SUCCESS);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('modalsettingstitle', 'local_uservalform'));

$settingsform->display();

// Copyright data.
echo html_writer::link('https://creatic.co', get_string('developedby', 'local_uservalform'),
        array('style' => 'float: right'));

echo $OUTPUT->footer();
