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

class local_uservalform_form_settings extends moodleform {

    /**
     * Form definition.
     *
     * @throws HTML_QuickForm_Error
     * @throws coding_exception
     */

	public function definition() {
        // Brings custom field information, custom field categories
        // and local_uservalform dato to use in settings form.
        list($userinfocategory, $userinfofield, $uservalformdata) = $this->_customdata;
        $mform = $this->_form;

        // Enable user validation.
        $mform->addElement('advcheckbox', 'enabled',
                get_string('enablevalidation', 'local_uservalform'),
                get_string('enablevalidationdescription', 'local_uservalform'));

        // Show validation form only once.
        $mform->addElement('advcheckbox', 'showonlyonce',
                get_string('showpopuponlyonce', 'local_uservalform'),
                get_string('showpopuponlyoncedescription', 'local_uservalform'));

        // Hide 'show only once' checkbox if user validation is disabled.
        $mform->hideif('showonlyonce', 'enabled', 'notchecked');

        // Organizes custom fields in categories.
        $userinfotree = $userinfocategory;
        foreach($userinfofield as $field) {
            if(!isset($userinfotree[$field->categoryid]->items)) {
                $userinfotree[$field->categoryid]->items = array();
            }
            $userinfotree[$field->categoryid]->items[] = $field;
        }

        $fieldlist = array();
        foreach($userinfocategory as $category) {
            $fieldlist[$category->name] = array();
        }

        // Builds an array that allow to organize fields in categories when
        // a group select list element is added.
        foreach($userinfofield as $field) {
            $categoryname = $userinfocategory[$field->categoryid]->name;
            $fieldlist[$categoryname][$field->name] = $field->name;
        }

        $mform->addElement('selectgroups', 'fieldname', get_string('profilefield', 'admin'), $fieldlist);
        $mform->hideif('fieldname', 'enabled', 'notchecked');

        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        $this->add_action_buttons();

        $this->set_data($uservalformdata);
    }
}
