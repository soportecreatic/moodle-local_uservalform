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

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/formslib.php');

/**
 * Class local_uservalform_validation_form
 *
 * @package   local_uservalform
 * @copyright 2018-2019, Creatic SAS <soporte@creatic.co>.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class local_uservalform_validation_form extends moodleform {

    /**
     * Form definition.
     *
     * @throws HTML_QuickForm_Error
     * @throws coding_exception
     */

    public function definition() {

        // Brings info field data and course's local_uservalform data to use the form.
        list($userinfofield, $uservalformdata) = $this->_customdata;
        $mform = $this->_form;

        // Array that matchs custom data field names with Moodle Form elements.

        $inputtypes = array(
            'checkbox'  => 'advcheckbox',
            'text'      => 'text',
            'textarea'  => 'textarea',
            'datetime'  => 'date_selector',
            'menu'      => 'select'
        );

        // Hidden course ID field.

        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        // Adds the validation field, depending on custom field type.
        switch($userinfofield->datatype) {
            case 'checkbox':
                $mform->addElement($inputtypes[$userinfofield->datatype], 'validationinfo',
                        $userinfofield->name, $userinfofield->description);
                break;
            case 'menu':
                $options = explode(PHP_EOL, $userinfofield->param1);
                $optionslist = array();
                foreach($options as $option) {
                    $optionslist[$option] = $option;
                }
                $mform->addElement($inputtypes[$userinfofield->datatype], 'validationinfo', $userinfofield->name, $optionslist);
                break;

            case 'text':
                $mform->addElement($inputtypes[$userinfofield->datatype], 'validationinfo', $userinfofield->name,
                        array('size' => 40));
                $mform->setType('validationinfo', PARAM_TEXT);
                break;

            case 'textarea':
                $mform->addElement($inputtypes[$userinfofield->datatype], 'validationinfo', $userinfofield->name,
                        'wrap="virtual" rows="5" cols="50"');
                break;

            case 'datetime':
                $options = array(
                    'startyear' => $userinfofield->param1,
                    'stopyear'  => $userinfofield->param2
                );
                $mform->addElement($inputtypes[$userinfofield->datatype], 'validationinfo', $userinfofield->name, $options);
                break;
        }

        $this->add_action_buttons(true, get_string('send', 'local_uservalform'));
        $this->set_data($uservalformdata);
    }
}
