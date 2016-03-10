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
 * The main newmodule configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package   mod_newmodule
 * @copyright 2010 Your Name
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');

class mod_ontology_mod_form extends moodleform_mod {

    function definition() {

        global $COURSE;
        global $DB;
        $mform = & $this->_form;
        $ontologii = array();
        $id_a_na_ont = array();
        $redici = $DB->get_records('ontology', array('course' => $COURSE->id));
        foreach ($redici as $key => $tmp) {
            //array_push($ontologii,$tmp->name.": ".substr($tmp->intro,0,58)."...");
            $ontologii[$key] = $tmp->name;
        }
//-------------------------------------------------------------------------------
        /// Adding the "general" fieldset, where all the common settings are showed
        ?>
        <script type="text/javascript">
            function show(){
                var list=document.getElementById("id_select");
                var index=list.selectedIndex;
                document.getElementById("id_ime").value=list.options[index].text;
            }
            function first() {
                var list=document.getElementById("id_izbor");
                var index=list.selectedIndex;
                if (index==1) {
                    if (document.getElementById("id_ime")!=null){
                        document.getElementById("id_ime").value=document.getElementById("id_select").options[0].text;
                        document.getElementById("id_ime").readOnly=true;
                        document.getElementById("id_ime").style.background="#E2E1E1";
                    }
                    if (document.getElementById("ime")!=null){
                        document.getElementById("ime").value=document.getElementById("id_select").options[0].text;
                        document.getElementById("ime").readOnly=true;
                        document.getElementById("ime").style.background="#E2E1E1";
                    }
                }
                else {
                    if (document.getElementById("id_ime")!=null) {
                        document.getElementById("id_ime").removeAttribute("readonly",0);
                        document.getElementById("id_ime").value="";
                        document.getElementById("id_ime").style.background="white";
                    }
                    if (document.getElementById("ime")!=null) {
                        document.getElementById("ime").removeAttribute("readonly",0);
                        document.getElementById("ime").value="";
                        document.getElementById("ime").style.background="white";
                    }
                }
            }
        </script>
        <?php

        /// Adding the standard "name" field
        $moduleid = $_GET['update'];
        if ($moduleid == null) {
            $mform->addElement('header', 'general', get_string('Choose', 'ontology'));
            $mform->addElement('select', 'izbor', get_string('Choose', 'ontology') . ':', array('KREIRANJE_NOVA' => get_string('Create_new_ontology', 'ontology'),
                'PRODOPZUVANJE_STARA' => get_string('Fill_old_ontology', 'ontology')), array('onchange' => 'first()', 'id' => 'izbor'));
        }
        $mform->setDefault('izbor', 'PRODOPZUVANJE_STARA');

        $mform->addElement('header', 'izbor_lbl', get_string('For_ontology', 'ontology'));
        $mform->addElement('select', 'izbor_ontologija', get_string('Add_to_ontology', 'ontology'), $ontologii, array('onchange' => "show()", 'id' => 'select'));
        $mform->disabledIf('izbor_ontologija', 'izbor', 'eq', 'KREIRANJE_NOVA');
        //$mform->disabledIf('name','izbor','eq','PRODOPZUVANJE_STARA');
        $mform->addElement('text', 'name', get_string('Ontology_name', 'ontology') . ':', array('size' => '64', 'value' => reset($ontologii), 'id' => 'ime', 'name' => 'ime', 'readOnly' => 'true', 'style' => 'background:#E2E1E1'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEAN);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        //   $mform->addRule('name', null, 'required', null, 'server');
        $mform->addHelpButton('name', 'newmodulename', 'ontology');
        /// Adding the standard "intro" and "introformat" fields
        $this->add_intro_editor();

//-------------------------------------------------------------------------------
        /// Adding the rest of newmodule settings, spreeading all them into this fieldset
        /// or adding more fieldsets ('header' elements) if needed for better logic
        //  $mform->addElement('static', 'label1', 'ontologysetting1', 'Your ontology fields go here. Replace me!');

        $mform->addElement('header', 'ontologyfieldset', get_string('Activity_data', 'ontology'));
        //  $mform->addElement('static', 'label2', 'ontologysetting2', 'Your ontology fields go here. Replace me!');
        /*
          $mform->addElement('date_time_selector','assigmentstart', 'Отворање на активноста на',array(
          'startyear' => 2010,
          'stopyear'  => 2020,
          'applydst'  => true,
          'optional'  => false
          ));
          $mform->addElement('date_time_selector','assigmentfinish', 'Затворање на активноста на',array(
          'startyear' => 2010,
          'stopyear'  => 2020,
          'applydst'  => true,
          'optional'  => false
          ));
         */
        $mform->addElement('date_time_selector', 'assigmentstart', get_string('Activity_start', 'ontology') . ':', array('optional' => false));
        $mform->addElement('date_time_selector', 'assigmentfinish', get_string('Activity_end', 'ontology') . ':', array('optional' => false));
        $moduleid = $_GET['update'];
        if ($moduleid != null) {
            $module = $DB->get_record('course_modules', array('id' => $moduleid));
            $mform->setDefault('assigmentstart', $module->availablefrom);
            $mform->setDefault('assigmentfinish', $module->availableuntil);
            $mform->setDefault('izbor_ontologija', $module->instance);
        } else {
            $mform->setDefault('assigmentstart', time());
            $mform->setDefault('assigmentfinish', time() + 7 * 24 * 3600);
        }

        //   $mform->addElement('text', 'bingo', 'Vnesi nesto', array('size'=>'64'));
//-------------------------------------------------------------------------------
        // add standard elements, common to all modules
        $this->standard_coursemodule_elements();
//-------------------------------------------------------------------------------
        // add standard buttons, common to all modules
        $this->add_action_buttons();
    }

}
