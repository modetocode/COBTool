<?php
//<head>
//  <link type="text/css" href="css/redmond/jquery-ui-1.8.14.custom.css" rel="stylesheet" /> 
//<script type="text/javascript" src="js/jquery-1.5.1.min.js"></script>
// <script type="text/javascript" src="js/jquery-ui-1.8.14.custom.min.js"></script>  
//</head>
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
 * Library of interface functions and constants for module newmodule
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 * All the newmodule specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package   mod_newmodule
 * @copyright 2010 Your Name
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
/** example constant */
//define('NEWMODULE_ULTIMATE_ANSWER', 42);

/**
 * If you for some reason need to use global variables instead of constants, do not forget to make them
 * global as this file can be included inside a function scope. However, using the global variables
 * at the module level is not a recommended.
 */
//global $NEWMODULE_GLOBAL_VARIABLE;
//$NEWMODULE_QUESTION_OF = array('Life', 'Universe', 'Everything');

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $newmodule An object from the form in mod_form.php
 * @return int The id of the newly inserted newmodule record
 */
function ontology_add_instance($ontology) {
    global $DB;
    global $USER;
    global $COURSE;

    $class->superclass = '0';

    if ($ontology->izbor == "KREIRANJE_NOVA") { //kreiranje na nova ontologija
        $ontology->timecreated = time();
        $ontologyid = $DB->insert_record('ontology', $ontology); //id na ontologija
        $moduleid = $DB->get_record_sql("SELECT * FROM mdl_course_modules WHERE id IN (SELECT max(id) FROM mdl_course_modules);");

        //dodavanje na klasa Osnovna
        $class->name = 'Основна';
        $class->superclass = '0';
        $class->status = '2';
        $class->points = '0';
        $class->userid = $USER->id;
        $class->course_modulesid = $moduleid->id;
        $DB->insert_record('ontology_class', $class);

        //dodavanje na novo objektno svojstvo Osnovno
        $oproperty->name = 'Основно';
        $oproperty->superproperty = '0';
        $oproperty->inverse = '0';
        $oproperty->attributes = '0';
        $oproperty->status = '2';
        $oproperty->points = '0';
        $oproperty->userid = $USER->id;
        $oproperty->course_modulesid = $moduleid->id;
        $DB->insert_record('ontology_property_individual', $oproperty);

        //dodavanje na novo podatocno svojstvo Osnovno
        $dproperty->name = 'Основно';
        $dproperty->superproperty = '0';
        $dproperty->rang = '';
        $dproperty->attributes = '0';
        $dproperty->status = '2';
        $dproperty->points = '0';
        $dproperty->userid = $USER->id;
        $dproperty->course_modulesid = $moduleid->id;
        $DB->insert_record('ontology_property_data', $dproperty);

        //dodavanje na rankovi na studenti
        $k = $DB->get_record('course_modules', array('id' => $moduleid->id));
        $r = $DB->get_records_sql('SELECT distinct userid FROM mdl_role_assignments WHERE (roleid=5 AND contextid IN (SELECT id FROM mdl_context WHERE (instanceid=? AND contextlevel=50)));', array($k->course));
        foreach ($r as $key => $value) {
            $rank->userid = $value->userid;
            $rank->ontologyid = $ontologyid;
            $rank->correct = '0';
            $rank->incorrect = '0';
            $rank->penalty = '0';
            $rank->rating = '1';
            $DB->insert_record('ontology_student_rank', $rank);
        }
    } else { //dopolnuvanje vo stara ontologija
        $ontologyid = $ontology->izbor_ontologija;
    }

    $moduleid = $DB->get_record_sql("SELECT * FROM mdl_course_modules WHERE id IN (SELECT max(id) FROM mdl_course_modules);");
    //stavanje na poceten i kraen datum na aktivnosta
    $moduleid->availablefrom = $ontology->assigmentstart;
    $moduleid->availableuntil = $ontology->assigmentfinish;
    $DB->update_record('course_modules', $moduleid);

    //otvoranje na grejding za studentite
    $grade->courseid = $COURSE->id;
    $grdcat = $DB->get_record('grade_categories', array('courseid' => $COURSE->id));
    $grade->categoryid = $grdcat->id;
    $ontology = $DB->get_record('ontology', array('id' => $ontologyid));
    $ontologymodule = $DB->get_record('modules', array('name' => 'ontology'));
    $cmcount = $DB->get_records('course_modules', array('module' => $ontologymodule->id, 'instance' => $ontologyid));
    $grade->itemname = $ontology->name . '-' . (count($cmcount) + 1);
    $grade->itemtype = 'mod';
    $grade->itemmodule = 'ontology';
    $grade->iteminstance = $moduleid->id;
    $grade->gradetype = '1';
    $grade->grademax = '100.00000';
    $grade->grademin = '0.00000';
    $grade->gradepass = '0.00000';
    $grade->multifactor = '1.00000';
    $grade->plusfactor = '0.00000';
    $grade->aggregationcoef = '0.00000';
    $grade->sortorder = '1';
    $grade->display = '0';
    $grade->hidden = '0';
    $grade->locked = '0';
    $grade->locktime = '0';
    $grade->needsupdate = '0';
    $grade->timecreated = time();
    $grade->timemodified = time();
    $DB->insert_record('grade_items', $grade);


    return $ontologyid;
}
/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $newmodule An object from the form in mod_form.php
 * @return boolean Success/Fail
 */
function ontology_update_instance($ontology) {
    global $DB;
    global $USER;
    global $COURSE;

    //$ontology->timemodified = time();
    //$ontology->id = $ontology->instance;
    $moduleid = $DB->get_record('course_modules', array('id' => $ontology->update));
    $moduleid->availablefrom = $ontology->assigmentstart;
    $moduleid->availableuntil = $ontology->assigmentfinish;
    
    /*
      if ($ontology->izbor=="KREIRANJE_NOVA")
      { //kreiranje na nova ontologija
      //trganje na stara
      //kreiranje na nova
      $ontology->timecreated = time();
      $ontologyid=$DB->insert_record('ontology', $ontology);//id na ontologija
      $moduleid->instance=$ontologyid;
      //dodavanje na klasa Osnovna
      $class->name='Основна';
      $class->superclass='0';
      $class->status='2';
      $class->points='0';
      $class->userid=$USER->id;
      $class->course_modulesid=$moduleid->id;
      $DB->insert_record('ontology_class',$class);

      //dodavanje na novo objektno svojstvo Osnovno
      $oproperty->name='Основно';
      $oproperty->superproperty='0';
      $oproperty->inverse='0';
      $oproperty->attributes='0';
      $oproperty->status='2';
      $oproperty->points='0';
      $oproperty->userid=$USER->id;
      $oproperty->course_modulesid=$moduleid->id;
      $DB->insert_record('ontology_property_individual',$oproperty);

      //dodavanje na novo podatocno svojstvo Osnovno
      $dproperty->name='Основно';
      $dproperty->superproperty='0';
      $dproperty->rang='';
      $dproperty->attributes='0';
      $dproperty->status='2';
      $dproperty->points='0';
      $dproperty->userid=$USER->id;
      $dproperty->course_modulesid=$moduleid->id;
      $DB->insert_record('ontology_property_data',$dproperty);

      //dodavanje na rankovi na studenti
      $k=$DB->get_record('course_modules',array('id'=>$moduleid->id));
      $r=$DB->get_records_sql('SELECT distinct userid FROM mdl_role_assignments WHERE (roleid=5 AND contextid IN (SELECT id FROM mdl_context WHERE (instanceid=? AND contextlevel=50)));',array($k->course));
      foreach ($r as $key=>$value)
      {
      $rank->userid=$value->userid;
      $rank->ontologyid=$ontologyid;
      $rank->correct='0';
      $rank->incorrect='0';
      $rank->penalty='0';
      $rank->rating='1';
      $DB->insert_record('ontology_student_rank',$rank);
      }
      //otvoranje na grejding za studentite
      $grade->courseid=$COURSE->id;
      $grade->categoryid='1';
      $ontology=$DB->get_record('ontology',array('id'=>$ontologyid));
      $ontologymodule=$DB->get_record('modules', array('name' => 'ontology'));
      $cmcount=$DB->get_records('course_modules',array('module'=>$ontologymodule->id,'instance'=>$ontologyid));
      $grade->itemname=$ontology->name.'-'.(count($cmcount)+1);
      $grade->itemtype='mod';
      $grade->itemmodule='ontology';
      $grade->iteminstance=$moduleid->id;
      $grade->gradetype='1';
      $grade->grademax='100.00000';
      $grade->grademin='0.00000';
      $grade->gradepass='0.00000';
      $grade->multifactor='1.00000';
      $grade->plusfactor='0.00000';
      $grade->aggregationcoef='0.00000';
      $grade->sortorder='1';
      $grade->display='0';
      $grade->hidden='0';
      $grade->locked='0';
      $grade->locktime='0';
      $grade->needsupdate='0';
      $grade->timecreated=time();
      $grade->timemodified=time();
      $DB->insert_record('grade_items',$grade);
      }
      else
      {//dopolnuvanje vo stara
     */
    if ($ontology->izbor_ontologija != $moduleid->instance) {
        //razlicna stara ontologija
        $moduleid->instance = $ontology->izbor_ontologija;
    } else {
        //ista stara ontologija
    }
    //  }
    $DB->update_record('course_modules', $moduleid);
    $ontologija=$DB->get_record('ontology',array('id'=>$moduleid->instance,'course'=>$moduleid->course));
    $ontologija->intro=$ontology->intro;
    $DB->update_record('ontology',$ontologija);
    //return $DB->update_record('ontology', $ontology);
    return $moduleid->instance;
}
/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function ontology_delete_instance($id) {

    //  if (count($DB->get_records('ontology', array('id' => $id)))==0) {
    //      return false;
    //  }
    global $DB;
    $modules = $DB->get_records('course_modules', array('instance' => $id));
    if (count($modules) == 1) {
        $ontology = $DB->get_record('ontology', array('id' => $id));
        $DB->delete_records('ontology', array('id' => $id));
        $items = $DB->get_records('grade_items', array('itemmodule' => 'ontology'));
        foreach ($items as $key => $value) {
            if (substr($value->itemname, 0, strlen($ontology->name)) == $ontology->name) {
                $DB->delete_records('grade_items', array('id' => $key));
            }
        }
    }
    $class->name = count($modules);
    $class->superclass = '0';
    $class->status = '2';
    $class->points = '0';
    $class->userid = '2';
    $class->course_modulesid = '2';
    $DB->insert_record('ontology_class', $class);
    # Delete any dependent records here #
    //  

    return true;
}
function grade_students($module_id) {
    global $DB;
    $k = $DB->get_record('course_modules', array('id' => $module_id));
    $r = $DB->get_records_sql('SELECT distinct userid FROM mdl_role_assignments WHERE (roleid=5 AND contextid IN (SELECT id FROM mdl_context WHERE (instanceid=? AND contextlevel=50)));', array($k->course));
    $c = $DB->get_records_sql('(SELECT userid from mdl_ontology_class where course_modulesid=?) UNION (SELECT userid from mdl_ontology_individual where course_modulesid=?) UNION (SELECT userid from mdl_ontology_class_expression where course_modulesid=?) UNION (SELECT userid from mdl_ontology_individual_expression where course_modulesid=?) UNION (SELECT userid from mdl_ontology_individual_property_data where course_modulesid=?) UNION (SELECT userid from mdl_ontology_individual_property_individual where course_modulesid=?) UNION (SELECT userid from mdl_ontology_property_data where course_modulesid=?) UNION (SELECT userid from mdl_ontology_property_disjoint where course_modulesid=?) UNION (SELECT userid from mdl_ontology_property_equivalent where course_modulesid=?) UNION (SELECT userid from mdl_ontology_property_expression where course_modulesid=?) UNION (SELECT userid from mdl_ontology_property_individual where course_modulesid=?);', array($module_id, $module_id, $module_id, $module_id, $module_id, $module_id, $module_id, $module_id, $module_id, $module_id, $module_id));

    $poeni_na_studenti = array();
    $kvadratno_otstapuvanje = array();
    $novi_poeni = array();
    $studenti_id = array();
    foreach ($c as $r1) {
        $ima = false;
        foreach ($r as $r2) {
            if ($r1->userid == $r2->userid) {
                $ima = true;
                break;
            }
        }
        if ($ima) {
            $ontology = $DB->get_record('course_modules', array('id' => $module_id));
            $rank = $DB->get_record_sql('SELECT * from mdl_ontology_student_rank WHERE userid=? AND ontologyid=?;', array($r1->userid, $ontology->instance));

            $class_points = 0;
            $vneseni_klasi = $DB->get_records_sql('SELECT * from mdl_ontology_class where ((status=2 or status=3 or status=4 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid));
            foreach ($vneseni_klasi as $k) {
                $class_points = $class_points + $k->points;
            }

            $class_expression_points = 0;
            $vneseni_class_expression = $DB->get_records_sql('SELECT points from mdl_ontology_class_expression where ((status=2 or status=3 or status=4 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid));
            foreach ($vneseni_class_expression as $ke) {
                $class_expression_points = $class_expression_points + $ke->points;
            }

            $individual_points = 0;
            $vneseni_individui = $DB->get_records_sql('SELECT * from mdl_ontology_individual where ((status=2 or status=3 or status=4 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid));
            foreach ($vneseni_individui as $i) {
                $individual_points = $individual_points + $i->points;
            }

            $individual_e_points = 0;
            $vneseni_ie = $DB->get_records_sql('SELECT * from mdl_ontology_individual_expression where ((status=2 or status=3 or status=4 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid));
            foreach ($vneseni_ie as $ie) {
                $individual_e_points = $individual_e_points + $ie->points;
            }

            $individual_pd_points = 0;
            $vneseni_individual_pd = $DB->get_records_sql('SELECT * from mdl_ontology_individual_property_data where ((status=2 or status=3 or status=4 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid));
            foreach ($vneseni_individual_pd as $ipd) {
                $individual_pd_points = $individual_pd_points + $ipd->points;
            }

            $individual_pi_points = 0;
            $vneseni_individual_pi = $DB->get_records_sql('SELECT * from mdl_ontology_individual_property_individual where ((status=2 or status=3 or status=4 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid));
            foreach ($vneseni_individual_pi as $ipi) {
                $individual_pi_points = $individual_pi_points + $ipi->points;
            }

            $property_data_points = 0;
            $vneseni_pd = $DB->get_records_sql('SELECT * from mdl_ontology_property_data where ((status=2 or status=3 or status=4 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid));
            foreach ($vneseni_pd as $pd) {
                $property_data_points = $property_data_points + $pd->points;
            }

            $property_disjoint_points = 0;
            $vneseni_p_disjoint = $DB->get_records_sql('SELECT * from mdl_ontology_property_disjoint where ((status=2 or status=3 or status=4 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid));
            foreach ($vneseni_p_disjoint as $pdisjoint) {
                $property_disjoint_points = $property_disjoint_points + $pdisjoint->points;
            }

            $property_eq_points = 0;
            $vneseni_p_eq = $DB->get_records_sql('SELECT * from mdl_ontology_property_equivalent where ((status=2 or status=3 or status=4 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid));
            foreach ($vneseni_p_eq as $peq) {
                $property_eq_points = $property_eq_points + $peq->points;
            }

            $property_ex_points = 0;
            $vneseni_p_ex = $DB->get_records_sql('SELECT * from mdl_ontology_property_expression where ((status=2 or status=3 or status=4 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid));
            foreach ($vneseni_p_ex as $pex) {
                $property_ex_points = $property_ex_points + $pex->points;
            }

            $property_i_points = 0;
            $vneseni_p_i = $DB->get_records_sql('SELECT * from mdl_ontology_property_individual where ((status=2 or status=3 or status=4 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid));
            foreach ($vneseni_p_i as $pi) {
                $property_i_points = $property_i_points + $pi->points;
            }

            $student_points = $class_points + $class_expression_points + $individual_points + $individual_e_points + $individual_pd_points + $individual_pi_points + $property_data_points + $property_disjoint_points + $property_eq_points + $property_ex_points + $property_i_points;
            //echo $student_points." ";
            $poeni_na_studenti[] = $student_points;
            $studenti_id[] = $r1->userid;

            // broenje na prifateni poimi za preglduvanje po student
            $broi1_prifateni = count($DB->get_records_sql('SELECT * from mdl_ontology_class where ((status=2 or status=3 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi2_prifateni = count($DB->get_records_sql('SELECT * from mdl_ontology_individual where ((status=2 or status=3 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi3_prifateni = count($DB->get_records_sql('SELECT * from mdl_ontology_class_expression where ((status=2 or status=3 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi4_prifateni = count($DB->get_records_sql('SELECT * from mdl_ontology_individual_expression where ((status=2 or status=3 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi5_prifateni = count($DB->get_records_sql('SELECT * from mdl_ontology_individual_property_data where ((status=2 or status=3 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi6_prifateni = count($DB->get_records_sql('SELECT * from mdl_ontology_individual_property_individual where ((status=2 or status=3 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi7_prifateni = count($DB->get_records_sql('SELECT * from mdl_ontology_property_data where ((status=2 or status=3 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi8_prifateni = count($DB->get_records_sql('SELECT * from mdl_ontology_property_disjoint where ((status=2 or status=3 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi9_prifateni = count($DB->get_records_sql('SELECT * from mdl_ontology_property_equivalent where ((status=2 or status=3 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi10_prifateni = count($DB->get_records_sql('SELECT * from mdl_ontology_property_expression where ((status=2 or status=3 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi11_prifateni = count($DB->get_records_sql('SELECT * from mdl_ontology_property_individual where ((status=2 or status=3 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi_prifateni = $broi1_prifateni + $broi2_prifateni + $broi3_prifateni + $broi4_prifateni + $broi5_prifateni + $broi6_prifateni + $broi7_prifateni + $broi8_prifateni + $broi9_prifateni + $broi10_prifateni + $broi11_prifateni;

            // broenje na odbieni poimi za preglduvanje po student
            $broi1_odbieni = count($DB->get_records_sql('SELECT * from mdl_ontology_class where (status=4 and points=0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi2_odbieni = count($DB->get_records_sql('SELECT * from mdl_ontology_individual where (status=4 and points=0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi3_odbieni = count($DB->get_records_sql('SELECT * from mdl_ontology_class_expression where (status=4 and points=0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi4_odbieni = count($DB->get_records_sql('SELECT * from mdl_ontology_individual_expression where (status=4 and points=0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi5_odbieni = count($DB->get_records_sql('SELECT * from mdl_ontology_individual_property_data where (status=4 and points=0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi6_odbieni = count($DB->get_records_sql('SELECT * from mdl_ontology_individual_property_individual where (status=4 and points=0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi7_odbieni = count($DB->get_records_sql('SELECT * from mdl_ontology_property_data where (status=4 and points=0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi8_odbieni = count($DB->get_records_sql('SELECT * from mdl_ontology_property_disjoint where (status=4 and points=0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi9_odbieni = count($DB->get_records_sql('SELECT * from mdl_ontology_property_equivalent where (status=4 and points=0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi10_odbieni = count($DB->get_records_sql('SELECT * from mdl_ontology_property_expression where (status=4 and points=0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi11_odbieni = count($DB->get_records_sql('SELECT * from mdl_ontology_property_individual where (status=4 and points=0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi_odbieni = $broi1_odbieni + $broi2_odbieni + $broi3_odbieni + $broi4_odbieni + $broi5_odbieni + $broi6_odbieni + $broi7_odbieni + $broi8_odbieni + $broi9_odbieni + $broi10_odbieni + $broi11_odbieni;

            // broenje na kazneti poimi za preglduvanje po student
            $broi1_kazneti = count($DB->get_records_sql('SELECT * from mdl_ontology_class where (status=4 and points<0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi2_kazneti = count($DB->get_records_sql('SELECT * from mdl_ontology_individual where (status=4 and points<0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi3_kazneti = count($DB->get_records_sql('SELECT * from mdl_ontology_class_expression where (status=4 and points<0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi4_kazneti = count($DB->get_records_sql('SELECT * from mdl_ontology_individual_expression where (status=4 and points<0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi5_kazneti = count($DB->get_records_sql('SELECT * from mdl_ontology_individual_property_data where (status=4 and points<0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi6_kazneti = count($DB->get_records_sql('SELECT * from mdl_ontology_individual_property_individual where (status=4 and points<0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi7_kazneti = count($DB->get_records_sql('SELECT * from mdl_ontology_property_data where (status=4 and points<0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi8_kazneti = count($DB->get_records_sql('SELECT * from mdl_ontology_property_disjoint where (status=4 and points<0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi9_kazneti = count($DB->get_records_sql('SELECT * from mdl_ontology_property_equivalent where (status=4 and points<0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi10_kazneti = count($DB->get_records_sql('SELECT * from mdl_ontology_property_expression where (status=4 and points<0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi11_kazneti = count($DB->get_records_sql('SELECT * from mdl_ontology_property_individual where (status=4 and points<0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi_kazneti = $broi1_kazneti + $broi2_kazneti + $broi3_kazneti + $broi4_kazneti + $broi5_kazneti + $broi6_kazneti + $broi7_kazneti + $broi8_kazneti + $broi9_kazneti + $broi10_kazneti + $broi11_kazneti;

            // broenje poimi vo ontologija po student
            $broi1_vo_ontologija = count($DB->get_records_sql('SELECT * from mdl_ontology_class where ((status=2) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi2_vo_ontologija = count($DB->get_records_sql('SELECT * from mdl_ontology_individual where ((status=2) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi3_vo_ontologija = count($DB->get_records_sql('SELECT * from mdl_ontology_class_expression where ((status=2) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi4_vo_ontologija = count($DB->get_records_sql('SELECT * from mdl_ontology_individual_expression where ((status=2) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi5_vo_ontologija = count($DB->get_records_sql('SELECT * from mdl_ontology_individual_property_data where ((status=2) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi6_vo_ontologija = count($DB->get_records_sql('SELECT * from mdl_ontology_individual_property_individual where ((status=2) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi7_vo_ontologija = count($DB->get_records_sql('SELECT * from mdl_ontology_property_data where ((status=2) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi8_vo_ontologija = count($DB->get_records_sql('SELECT * from mdl_ontology_property_disjoint where ((status=2) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi9_vo_ontologija = count($DB->get_records_sql('SELECT * from mdl_ontology_property_equivalent where ((status=2) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi10_vo_ontologija = count($DB->get_records_sql('SELECT * from mdl_ontology_property_expression where ((status=2) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi11_vo_ontologija = count($DB->get_records_sql('SELECT * from mdl_ontology_property_individual where ((status=2) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi_vo_ontologija = $broi1_vo_ontologija + $broi2_vo_ontologija + $broi3_vo_ontologija + $broi4_vo_ontologija + $broi5_vo_ontologija + $broi6_vo_ontologija + $broi7_vo_ontologija + $broi8_vo_ontologija + $broi9_vo_ontologija + $broi10_vo_ontologija + $broi11_vo_ontologija;


            $rank->correct = $rank->correct + $broi_prifateni;
            $rank->incorrect = $rank->incorrect + $broi_odbieni;
            $rank->penalty = $rank->penalty + $broi_kazneti;
            $DB->update_record('ontology_student_rank', $rank);
            $rank = $DB->get_record_sql('SELECT * from mdl_ontology_student_rank WHERE userid=? AND ontologyid=?;', array($r1->userid, $ontology->instance));
            $rank->rating = ((1 + ($rank->correct - $broi_vo_ontologija) + 2 * $broi_vo_ontologija) / (1 + $rank->incorrect + 2 * $rank->penalty));
            $DB->update_record('ontology_student_rank', $rank);
        }
    }
    $br_student = count($poeni_na_studenti);
    if ($br_student > 0) {
        $vkupno = 0;
        $maksimum = max($poeni_na_studenti);
        foreach ($poeni_na_studenti as $ps) {
            $vkupno = $vkupno + $ps;
        }
        $prosek = $vkupno / $br_student;
        foreach ($poeni_na_studenti as $ps) {
            if ($ps != 0 and $ps > $prosek)
                $kvadratno_otstapuvanje[] = ($ps - $prosek) * ($ps - $prosek);
            else
                $kvadratno_otstapuvanje[] = 0;
        }
        $suma_kvadratni = 0;
        $br_kvadratni = count($kvadratno_otstapuvanje);
        foreach ($kvadratno_otstapuvanje as $ko) {
            $suma_kvadratni = $suma_kvadratni + $ko;
        }
        $devijacija = sqrt($suma_kvadratni / $br_kvadratni);
        foreach ($poeni_na_studenti as $ps) {
            $pp = $ps / ($prosek + $devijacija);
            if ($ps / ($prosek + $devijacija) > 1)
                $novi_poeni[] = 1 * 100;
            else
                $novi_poeni[] = $pp * 100;
        }

        $gradeitem = $DB->get_record('grade_items', array('itemmodule' => 'ontology', 'iteminstance' => $module_id));
        for ($i = 0; $i < count($novi_poeni); $i++) {
            $grdgrade = $DB->get_records('grade_grades', array('itemid' => $gradeitem->id, 'userid' => $studenti_id[$i]));
            if (count($grdgrade) == 0) {
                $grade->itemid = $gradeitem->id;
                $grade->userid = $studenti_id[$i];
                $grade->rawgrade = $novi_poeni[$i];
                $grade->rawgrademax = '100.00000';
                $grade->rawgrademin = '0.00000';
                $grade->finalgrade = $novi_poeni[$i];
                $grade->hidden = '0';
                $grade->locked = '0';
                $grade->locktime = '0';
                $grade->exported = '0';
                $grade->overridden = '0';
                $grade->excluded = '0';
                $grade->feedbackformat = '0';
                $grade->informationformat = '0';
                $grade->timecreated = time();
                $grade->timemodified = time();
                $DB->insert_record('grade_grades', $grade);
            } else {
                foreach ($grdgrade as $key => $value) {
                    $value->rawgrade = $novi_poeni[$i];
                    $value->finalgrade = $novi_poeni[$i];
                    $value->timecreated = time();
                    $value->timemodified = time();
                    $DB->update_record('grade_grades', $value);
                    break;
                }
            }
        }
    }
}
//funkcija za listanje na studenti za pregleduvanje
function smeni_klasa($klasa, $so_klasa) {
    global $DB;

    $raws = $DB->get_records('ontology_class_expression');
    foreach ($raws as $raw) {

        //za sekoja redica
        $izraz = explode(' ', $raw->expression);
        $text = explode(' ', $raw->expression_text);
        $ime_klasa = $DB->get_record('ontology_class', array('id' => $so_klasa));
        $ime_klasa = $ime_klasa->name;
        $len = count($izraz);
        $vleze = false;
        for ($i = 0; $i < $len; $i++) {
            if ($izraz[$i] == '^k' . $klasa) {
                $izraz[$i] = '^k' . $so_klasa;
                $text[$i] = $ime_klasa;
                echo $izraz[$i];
                $vleze = true;
            }
        }
        if ($vleze == true) {
            $str_izraz = '';
            foreach ($izraz as $zbor) {
                if ($str_izraz == '')
                    $str_izraz = $zbor;
                else
                    $str_izraz = $str_izraz . ' ' . $zbor;
            }
            $str_text = '';
            foreach ($text as $zbor) {
                if ($str_text == '')
                    $str_text = $zbor;
                else
                    $str_text = $str_text . ' ' . $zbor;
            }
            $raw->expression = $str_izraz;
            $raw->expression_text = $str_text;
            $DB->update_record('ontology_class_expression', $raw);
        }
    }

    $raws = $DB->get_records('ontology_property_expression');
    foreach ($raws as $raw) {

        //za sekoja redica
        $izraz = explode(' ', $raw->expression);
        $text = explode(' ', $raw->expression_text);
        $ime_klasa = $DB->get_record('ontology_class', array('id' => $so_klasa));
        $ime_klasa = $ime_klasa->name;
        $len = count($izraz);
        $vleze = false;
        for ($i = 0; $i < $len; $i++) {
            if ($izraz[$i] == '^k' . $klasa) {
                $izraz[$i] = '^k' . $so_klasa;
                $text[$i] = $ime_klasa;
                echo $izraz[$i];
                $vleze = true;
            }
        }
        if ($vleze == true) {
            $str_izraz = '';
            foreach ($izraz as $zbor) {
                if ($str_izraz == '')
                    $str_izraz = $zbor;
                else
                    $str_izraz = $str_izraz . ' ' . $zbor;
            }
            $str_text = '';
            foreach ($text as $zbor) {
                if ($str_text == '')
                    $str_text = $zbor;
                else
                    $str_text = $str_text . ' ' . $zbor;
            }
            $raw->expression = $str_izraz;
            $raw->expression_text = $str_text;
            $DB->update_record('ontology_property_expression', $raw);
        }
    }


    $raws = $DB->get_records('ontology_individual_expression');
    foreach ($raws as $raw) {

        //za sekoja redica
        $izraz = explode(' ', $raw->expression);
        $text = explode(' ', $raw->expression_text);
        $ime_klasa = $DB->get_record('ontology_class', array('id' => $so_klasa));
        $ime_klasa = $ime_klasa->name;
        $len = count($izraz);
        $vleze = false;
        for ($i = 0; $i < $len; $i++) {
            if ($izraz[$i] == '^k' . $klasa) {
                $izraz[$i] = '^k' . $so_klasa;
                $text[$i] = $ime_klasa;
                echo $izraz[$i];
                $vleze = true;
            }
        }
        if ($vleze == true) {
            $str_izraz = '';
            foreach ($izraz as $zbor) {
                if ($str_izraz == '')
                    $str_izraz = $zbor;
                else
                    $str_izraz = $str_izraz . ' ' . $zbor;
            }
            $str_text = '';
            foreach ($text as $zbor) {
                if ($str_text == '')
                    $str_text = $zbor;
                else
                    $str_text = $str_text . ' ' . $zbor;
            }
            $raw->expression = $str_izraz;
            $raw->expression_text = $str_text;
            $DB->update_record('ontology_individual_expression', $raw);
        }
    }
}
function smeni_instanca($instanca, $so_instanca) {
    global $DB;

    $raws = $DB->get_records('ontology_class_expression');
    foreach ($raws as $raw) {

        //za sekoja redica
        $izraz = explode(' ', $raw->expression);
        $text = explode(' ', $raw->expression_text);
        $ime_instanca = $DB->get_record('ontology_individual', array('id' => $so_instanca));
        $ime_instanca = $ime_instanca->name;
        $len = count($izraz);
        $vleze = false;
        for ($i = 0; $i < $len; $i++) {
            if ($izraz[$i] == '^i' . $instanca) {
                $izraz[$i] = '^i' . $so_instanca;
                $text[$i] = $ime_instanca;
                echo $izraz[$i];
                $vleze = true;
            }
        }
        if ($vleze == true) {
            $str_izraz = '';
            foreach ($izraz as $zbor) {
                if ($str_izraz == '')
                    $str_izraz = $zbor;
                else
                    $str_izraz = $str_izraz . ' ' . $zbor;
            }
            $str_text = '';
            foreach ($text as $zbor) {
                if ($str_text == '')
                    $str_text = $zbor;
                else
                    $str_text = $str_text . ' ' . $zbor;
            }
            $raw->expression = $str_izraz;
            $raw->expression_text = $str_text;
            $DB->update_record('ontology_class_expression', $raw);
        }
    }

    $raws = $DB->get_records('ontology_property_expression');
    foreach ($raws as $raw) {

        //za sekoja redica
        $izraz = explode(' ', $raw->expression);
        $text = explode(' ', $raw->expression_text);
        $ime_instanca = $DB->get_record('ontology_individual', array('id' => $so_instanca));
        $ime_instanca = $ime_instanca->name;
        $len = count($izraz);
        $vleze = false;
        for ($i = 0; $i < $len; $i++) {
            if ($izraz[$i] == '^i' . $instanca) {
                $izraz[$i] = '^i' . $so_instanca;
                $text[$i] = $ime_instanca;
                echo $izraz[$i];
                $vleze = true;
            }
        }
        if ($vleze == true) {
            $str_izraz = '';
            foreach ($izraz as $zbor) {
                if ($str_izraz == '')
                    $str_izraz = $zbor;
                else
                    $str_izraz = $str_izraz . ' ' . $zbor;
            }
            $str_text = '';
            foreach ($text as $zbor) {
                if ($str_text == '')
                    $str_text = $zbor;
                else
                    $str_text = $str_text . ' ' . $zbor;
            }
            $raw->expression = $str_izraz;
            $raw->expression_text = $str_text;
            $DB->update_record('ontology_property_expression', $raw);
        }
    }


    $raws = $DB->get_records('ontology_individual_expression');
    foreach ($raws as $raw) {

        //za sekoja redica
        $izraz = explode(' ', $raw->expression);
        $text = explode(' ', $raw->expression_text);
        $ime_instanca = $DB->get_record('ontology_individual', array('id' => $so_instanca));
        $ime_instanca = $ime_instanca->name;
        $len = count($izraz);
        $vleze = false;
        for ($i = 0; $i < $len; $i++) {
            if ($izraz[$i] == '^i' . $instanca) {
                $izraz[$i] = '^i' . $so_instanca;
                $text[$i] = $ime_instanca;
                echo $izraz[$i];
                $vleze = true;
            }
        }
        if ($vleze == true) {
            $str_izraz = '';
            foreach ($izraz as $zbor) {
                if ($str_izraz == '')
                    $str_izraz = $zbor;
                else
                    $str_izraz = $str_izraz . ' ' . $zbor;
            }
            $str_text = '';
            foreach ($text as $zbor) {
                if ($str_text == '')
                    $str_text = $zbor;
                else
                    $str_text = $str_text . ' ' . $zbor;
            }
            $raw->expression = $str_izraz;
            $raw->expression_text = $str_text;
            $DB->update_record('ontology_individual_expression', $raw);
        }
    }
}
function smeni_pod_svojstvo($pod_svoj, $so_pod_svoj) {
    global $DB;
    $raws = $DB->get_records('ontology_class_expression');
    foreach ($raws as $raw) {

        //za sekoja redica
        $izraz = explode(' ', $raw->expression);
        $text = explode(' ', $raw->expression_text);
        $ime_pod_svojstvo = $DB->get_record('ontology_property_data', array('id' => $so_pod_svoj));
        $ime_pod_svojstvo = $ime_pod_svojstvo->name;
        $len = count($izraz);
        $vleze = false;
        for ($i = 0; $i < $len; $i++) {
            if ($izraz[$i] == '^p' . $pod_svoj) {
                $izraz[$i] = '^p' . $so_pod_svoj;
                $text[$i] = $ime_pod_svojstvo;
                echo $izraz[$i];
                $vleze = true;
            }
        }
        if ($vleze == true) {
            $str_izraz = '';
            foreach ($izraz as $zbor) {
                if ($str_izraz == '')
                    $str_izraz = $zbor;
                else
                    $str_izraz = $str_izraz . ' ' . $zbor;
            }
            $str_text = '';
            foreach ($text as $zbor) {
                if ($str_text == '')
                    $str_text = $zbor;
                else
                    $str_text = $str_text . ' ' . $zbor;
            }
            $raw->expression = $str_izraz;
            $raw->expression_text = $str_text;
            $DB->update_record('ontology_class_expression', $raw);
        }
    }

    $raws = $DB->get_records('ontology_property_expression');
    foreach ($raws as $raw) {

        //za sekoja redica
        $izraz = explode(' ', $raw->expression);
        $text = explode(' ', $raw->expression_text);
        $ime_pod_svojstvo = $DB->get_record('ontology_property_data', array('id' => $so_pod_svoj));
        $ime_pod_svojstvo = $ime_pod_svojstvo->name;
        $len = count($izraz);
        $vleze = false;
        for ($i = 0; $i < $len; $i++) {
            if ($izraz[$i] == '^p' . $pod_svoj) {
                $izraz[$i] = '^p' . $so_pod_svoj;
                $text[$i] = $ime_pod_svojstvo;
                echo $izraz[$i];
                $vleze = true;
            }
        }
        if ($vleze == true) {
            $str_izraz = '';
            foreach ($izraz as $zbor) {
                if ($str_izraz == '')
                    $str_izraz = $zbor;
                else
                    $str_izraz = $str_izraz . ' ' . $zbor;
            }
            $str_text = '';
            foreach ($text as $zbor) {
                if ($str_text == '')
                    $str_text = $zbor;
                else
                    $str_text = $str_text . ' ' . $zbor;
            }
            $raw->expression = $str_izraz;
            $raw->expression_text = $str_text;
            $DB->update_record('ontology_property_expression', $raw);
        }
    }


    $raws = $DB->get_records('ontology_individual_expression');
    foreach ($raws as $raw) {

        //za sekoja redica
        $izraz = explode(' ', $raw->expression);
        $text = explode(' ', $raw->expression_text);
        $ime_pod_svojstvo = $DB->get_record('ontology_property_data', array('id' => $so_pod_svoj));
        $ime_pod_svojstvo = $ime_pod_svojstvo->name;
        $len = count($izraz);
        $vleze = false;
        for ($i = 0; $i < $len; $i++) {
            if ($izraz[$i] == '^p' . $pod_svoj) {
                $izraz[$i] = '^p' . $so_pod_svoj;
                $text[$i] = $ime_pod_svojstvo;
                echo $izraz[$i];
                $vleze = true;
            }
        }
        if ($vleze == true) {
            $str_izraz = '';
            foreach ($izraz as $zbor) {
                if ($str_izraz == '')
                    $str_izraz = $zbor;
                else
                    $str_izraz = $str_izraz . ' ' . $zbor;
            }
            $str_text = '';
            foreach ($text as $zbor) {
                if ($str_text == '')
                    $str_text = $zbor;
                else
                    $str_text = $str_text . ' ' . $zbor;
            }
            $raw->expression = $str_izraz;
            $raw->expression_text = $str_text;
            $DB->update_record('ontology_individual_expression', $raw);
        }
    }
}
function smeni_obj_svojstvo($obj_svoj, $so_obj_svoj) {
    global $DB;
    $raws = $DB->get_records('ontology_class_expression');
    foreach ($raws as $raw) {

        //za sekoja redica
        $izraz = explode(' ', $raw->expression);
        $text = explode(' ', $raw->expression_text);
        $ime_obj_svojstvo = $DB->get_record('ontology_property_individual', array('id' => $so_obj_svoj));
        $ime_obj_svojstvo = $ime_obj_svojstvo->name;
        $len = count($izraz);
        $vleze = false;
        for ($i = 0; $i < $len; $i++) {
            if ($izraz[$i] == '^o' . $obj_svoj) {
                $izraz[$i] = '^o' . $so_obj_svoj;
                $text[$i] = $ime_obj_svojstvo;
                echo $izraz[$i];
                $vleze = true;
            }
        }
        if ($vleze == true) {
            $str_izraz = '';
            foreach ($izraz as $zbor) {
                if ($str_izraz == '')
                    $str_izraz = $zbor;
                else
                    $str_izraz = $str_izraz . ' ' . $zbor;
            }
            $str_text = '';
            foreach ($text as $zbor) {
                if ($str_text == '')
                    $str_text = $zbor;
                else
                    $str_text = $str_text . ' ' . $zbor;
            }
            $raw->expression = $str_izraz;
            $raw->expression_text = $str_text;
            $DB->update_record('ontology_class_expression', $raw);
        }
    }

    $raws = $DB->get_records('ontology_property_expression');
    foreach ($raws as $raw) {

        //za sekoja redica
        $izraz = explode(' ', $raw->expression);
        $text = explode(' ', $raw->expression_text);
        $ime_obj_svojstvo = $DB->get_record('ontology_property_individual', array('id' => $so_obj_svoj));
        $ime_obj_svojstvo = $ime_obj_svojstvo->name;
        $len = count($izraz);
        $vleze = false;
        for ($i = 0; $i < $len; $i++) {
            if ($izraz[$i] == '^o' . $obj_svoj) {
                $izraz[$i] = '^o' . $so_obj_svoj;
                $text[$i] = $ime_obj_svojstvo;
                echo $izraz[$i];
                $vleze = true;
            }
        }
        if ($vleze == true) {
            $str_izraz = '';
            foreach ($izraz as $zbor) {
                if ($str_izraz == '')
                    $str_izraz = $zbor;
                else
                    $str_izraz = $str_izraz . ' ' . $zbor;
            }
            $str_text = '';
            foreach ($text as $zbor) {
                if ($str_text == '')
                    $str_text = $zbor;
                else
                    $str_text = $str_text . ' ' . $zbor;;
            }
            $raw->expression = $str_izraz;
            $raw->expression_text = $str_text;
            $DB->update_record('ontology_property_expression', $raw);
        }
    }


    $raws = $DB->get_records('ontology_individual_expression');
    foreach ($raws as $raw) {

        //za sekoja redica
        $izraz = explode(' ', $raw->expression);
        $text = explode(' ', $raw->expression_text);
        $ime_obj_svojstvo = $DB->get_record('ontology_property_individual', array('id' => $so_obj_svoj));
        $ime_obj_svojstvo = $ime_obj_svojstvo->name;
        $len = count($izraz);
        $vleze = false;
        for ($i = 0; $i < $len; $i++) {
            if ($izraz[$i] == '^o' . $obj_svoj) {
                $izraz[$i] = '^o' . $so_obj_svoj;
                $text[$i] = $ime_obj_svojstvo;
                echo $izraz[$i];
                $vleze = true;
            }
        }
        if ($vleze == true) {
            $str_izraz = '';
            foreach ($izraz as $zbor) {
                if ($str_izraz == '')
                    $str_izraz = $zbor;
                else
                    $str_izraz = $str_izraz . ' ' . $zbor;
            }
            $str_text = '';
            foreach ($text as $zbor) {
                if ($str_text == '')
                    $str_text = $zbor;
                else
                    $str_text = $str_text . ' ' . $zbor;
            }
            $raw->expression = $str_izraz;
            $raw->expression_text = $str_text;
            $DB->update_record('ontology_individual_expression', $raw);
        }
    }
}
function read_students($module_id, $OUT) {

    global $DB;
    $k = $DB->get_record('course_modules', array('id' => $module_id));
    $r = $DB->get_records_sql('SELECT distinct userid FROM mdl_role_assignments WHERE (roleid=5 AND contextid IN (SELECT id FROM mdl_context WHERE (instanceid=? AND contextlevel=50)));', array($k->course));
    $c = $DB->get_records_sql('(SELECT userid from mdl_ontology_class where course_modulesid=?) UNION (SELECT userid from mdl_ontology_individual where course_modulesid=?) UNION (SELECT userid from mdl_ontology_class_expression where course_modulesid=?) UNION (SELECT userid from mdl_ontology_individual_expression where course_modulesid=?) UNION (SELECT userid from mdl_ontology_individual_property_data where course_modulesid=?) UNION (SELECT userid from mdl_ontology_individual_property_individual where course_modulesid=?) UNION (SELECT userid from mdl_ontology_property_data where course_modulesid=?) UNION (SELECT userid from mdl_ontology_property_disjoint where course_modulesid=?) UNION (SELECT userid from mdl_ontology_property_equivalent where course_modulesid=?) UNION (SELECT userid from mdl_ontology_property_expression where course_modulesid=?) UNION (SELECT userid from mdl_ontology_property_individual where course_modulesid=?);', array($module_id, $module_id, $module_id, $module_id, $module_id, $module_id, $module_id, $module_id, $module_id, $module_id, $module_id));
    $rankovi = array();
    $studenti = array();
    $sortirani = array();
    foreach ($c as $r1) {
        $ima = false;
        foreach ($r as $r2) {
            if ($r1->userid == $r2->userid) {
                $ima = true;
                break;
            }
        }
        if ($ima) {
            $s = $DB->get_record_sql('SELECT * FROM mdl_user WHERE id=?;', array($r1->userid));
            $user = $DB->get_record('user', array('id' => $s->id), '*', MUST_EXIST);
            $ontology = $DB->get_record('course_modules', array('id' => $module_id));
            $rank = $DB->get_record_sql('SELECT * from mdl_ontology_student_rank WHERE userid=? AND ontologyid=?;', array($r1->userid, $ontology->instance));
            if ($rank==null||$rank->rating==null||$rank->rating==''){
                $rank->rating=1;
                $rank1->userid = $r1->userid;
                $rank1->ontologyid = $ontology->instance;
                $rank1->correct = '0';
                $rank1->incorrect = '0';
                $rank1->penalty = '0';
                $rank1->rating = '1';
                $DB->insert_record('ontology_student_rank', $rank1);
            }
            $rankovi[$r1->userid] = $rank->rating;
            $studenti[$r1->userid] = $r1->userid;

            // broenje na ostanati poimi za preglduvanje po student
            $broi1_ostanati = count($DB->get_records_sql('SELECT * from mdl_ontology_class where (status=1 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi2_ostanati = count($DB->get_records_sql('SELECT * from mdl_ontology_individual where (status=1 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi3_ostanati = count($DB->get_records_sql('SELECT * from mdl_ontology_class_expression where (status=1 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi4_ostanati = count($DB->get_records_sql('SELECT * from mdl_ontology_individual_expression where (status=1 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi5_ostanati = count($DB->get_records_sql('SELECT * from mdl_ontology_individual_property_data where (status=1 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi6_ostanati = count($DB->get_records_sql('SELECT * from mdl_ontology_individual_property_individual where (status=1 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi7_ostanati = count($DB->get_records_sql('SELECT * from mdl_ontology_property_data where (status=1 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi8_ostanati = count($DB->get_records_sql('SELECT * from mdl_ontology_property_disjoint where (status=1 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi9_ostanati = count($DB->get_records_sql('SELECT * from mdl_ontology_property_equivalent where (status=1 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi10_ostanati = count($DB->get_records_sql('SELECT * from mdl_ontology_property_expression where (status=1 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi11_ostanati = count($DB->get_records_sql('SELECT * from mdl_ontology_property_individual where (status=1 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi_ostanati = $broi1_ostanati + $broi2_ostanati + $broi3_ostanati + $broi4_ostanati + $broi5_ostanati + $broi6_ostanati + $broi7_ostanati + $broi8_ostanati + $broi9_ostanati + $broi10_ostanati + $broi11_ostanati;

            // broenje na prifateni poimi za preglduvanje po student
            $broi1_prifateni = count($DB->get_records_sql('SELECT * from mdl_ontology_class where ((status=2 or status=3 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi2_prifateni = count($DB->get_records_sql('SELECT * from mdl_ontology_individual where ((status=2 or status=3 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi3_prifateni = count($DB->get_records_sql('SELECT * from mdl_ontology_class_expression where ((status=2 or status=3 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi4_prifateni = count($DB->get_records_sql('SELECT * from mdl_ontology_individual_expression where ((status=2 or status=3 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi5_prifateni = count($DB->get_records_sql('SELECT * from mdl_ontology_individual_property_data where ((status=2 or status=3 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi6_prifateni = count($DB->get_records_sql('SELECT * from mdl_ontology_individual_property_individual where ((status=2 or status=3 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi7_prifateni = count($DB->get_records_sql('SELECT * from mdl_ontology_property_data where ((status=2 or status=3 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi8_prifateni = count($DB->get_records_sql('SELECT * from mdl_ontology_property_disjoint where ((status=2 or status=3 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi9_prifateni = count($DB->get_records_sql('SELECT * from mdl_ontology_property_equivalent where ((status=2 or status=3 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi10_prifateni = count($DB->get_records_sql('SELECT * from mdl_ontology_property_expression where ((status=2 or status=3 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi11_prifateni = count($DB->get_records_sql('SELECT * from mdl_ontology_property_individual where ((status=2 or status=3 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi_prifateni = $broi1_prifateni + $broi2_prifateni + $broi3_prifateni + $broi4_prifateni + $broi5_prifateni + $broi6_prifateni + $broi7_prifateni + $broi8_prifateni + $broi9_prifateni + $broi10_prifateni + $broi11_prifateni;

            // broenje na odbieni poimi za preglduvanje po student
            $broi1_odbieni = count($DB->get_records_sql('SELECT * from mdl_ontology_class where (status=4 and points=0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi2_odbieni = count($DB->get_records_sql('SELECT * from mdl_ontology_individual where (status=4 and points=0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi3_odbieni = count($DB->get_records_sql('SELECT * from mdl_ontology_class_expression where (status=4 and points=0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi4_odbieni = count($DB->get_records_sql('SELECT * from mdl_ontology_individual_expression where (status=4 and points=0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi5_odbieni = count($DB->get_records_sql('SELECT * from mdl_ontology_individual_property_data where (status=4 and points=0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi6_odbieni = count($DB->get_records_sql('SELECT * from mdl_ontology_individual_property_individual where (status=4 and points=0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi7_odbieni = count($DB->get_records_sql('SELECT * from mdl_ontology_property_data where (status=4 and points=0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi8_odbieni = count($DB->get_records_sql('SELECT * from mdl_ontology_property_disjoint where (status=4 and points=0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi9_odbieni = count($DB->get_records_sql('SELECT * from mdl_ontology_property_equivalent where (status=4 and points=0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi10_odbieni = count($DB->get_records_sql('SELECT * from mdl_ontology_property_expression where (status=4 and points=0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi11_odbieni = count($DB->get_records_sql('SELECT * from mdl_ontology_property_individual where (status=4 and points=0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi_odbieni = $broi1_odbieni + $broi2_odbieni + $broi3_odbieni + $broi4_odbieni + $broi5_odbieni + $broi6_odbieni + $broi7_odbieni + $broi8_odbieni + $broi9_odbieni + $broi10_odbieni + $broi11_odbieni;

            // broenje na kazneti poimi za preglduvanje po student
            $broi1_kazneti = count($DB->get_records_sql('SELECT * from mdl_ontology_class where (status=4 and points<0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi2_kazneti = count($DB->get_records_sql('SELECT * from mdl_ontology_individual where (status=4 and points<0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi3_kazneti = count($DB->get_records_sql('SELECT * from mdl_ontology_class_expression where (status=4 and points<0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi4_kazneti = count($DB->get_records_sql('SELECT * from mdl_ontology_individual_expression where (status=4 and points<0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi5_kazneti = count($DB->get_records_sql('SELECT * from mdl_ontology_individual_property_data where (status=4 and points<0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi6_kazneti = count($DB->get_records_sql('SELECT * from mdl_ontology_individual_property_individual where (status=4 and points<0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi7_kazneti = count($DB->get_records_sql('SELECT * from mdl_ontology_property_data where (status=4 and points<0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi8_kazneti = count($DB->get_records_sql('SELECT * from mdl_ontology_property_disjoint where (status=4 and points<0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi9_kazneti = count($DB->get_records_sql('SELECT * from mdl_ontology_property_equivalent where (status=4 and points<0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi10_kazneti = count($DB->get_records_sql('SELECT * from mdl_ontology_property_expression where (status=4 and points<0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi11_kazneti = count($DB->get_records_sql('SELECT * from mdl_ontology_property_individual where (status=4 and points<0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi_kazneti = $broi1_kazneti + $broi2_kazneti + $broi3_kazneti + $broi4_kazneti + $broi5_kazneti + $broi6_kazneti + $broi7_kazneti + $broi8_kazneti + $broi9_kazneti + $broi10_kazneti + $broi11_kazneti;
            if ($broi_ostanati > 0) {
                if (count($sortirani) == 0) { //nizata e prazna pa dolepuvame element so slednite polinja:
                    $sortirani[0]->firstname = $s->firstname;
                    $sortirani[0]->lastname = $s->lastname;
                    $sortirani[0]->ostanati = $broi_ostanati;
                    $sortirani[0]->rating = $rank->rating;
                    $sortirani[0]->prifateni = $broi_prifateni;
                    $sortirani[0]->odbieni = $broi_odbieni;
                    $sortirani[0]->kazneti = $broi_kazneti;
                    $sortirani[0]->user = $user;
                    $sortirani[0]->userid = $r1->userid;
                } else {
                    $goima = false; //pretpostavuvame deka ima najmal rejting
                    for ($i = 0; $i < count($sortirani); $i++)
                        if ($sortirani[$i]->rating < $rank->rating) {
                            //noviot student treba da se stavi na i-ta pozicija
                            $temp = null;
                            $temp->firstname = $s->firstname;
                            $temp->lastname = $s->lastname;
                            $temp->ostanati = $broi_ostanati;
                            $temp->rating = $rank->rating;
                            $temp->prifateni = $broi_prifateni;
                            $temp->odbieni = $broi_odbieni;
                            $temp->kazneti = $broi_kazneti;
                            $temp->user = $user;
                            $temp->userid = $r1->userid;
                            $sortirani = insertArrayIndex($sortirani, $temp, $i); //go stavame elementot na i-ta pozicija
                            $goima = true; //nema najmal rejting
                            break;
                        }
                    if (!$goima) {//ima najmal rejting od site pa go dolepuvame na kraj na nizata
                        $n = count($sortirani);
                        $sortirani[$n]->firstname = $s->firstname;
                        $sortirani[$n]->lastname = $s->lastname;
                        $sortirani[$n]->ostanati = $broi_ostanati;
                        $sortirani[$n]->rating = $rank->rating;
                        $sortirani[$n]->prifateni = $broi_prifateni;
                        $sortirani[$n]->odbieni = $broi_odbieni;
                        $sortirani[$n]->kazneti = $broi_kazneti;
                        $sortirani[$n]->user = $user;
                        $sortirani[$n]->userid = $r1->userid;
                    }
                }
            }
        }
    }

    for ($i = 0; $i < count($sortirani); $i++) { //'<a style="padding: .5em 1em; " class="ui-state-default ui-corner-all" href="pregled.php?userid='.$sortirani[$i]->userid.'&id='.$module_id.'"> Прегледај </a> '
        //'<a style="padding: .5em 1em; " class="ui-state-default ui-corner-all" href="teachernote.php?userid='.$sortirani[$i]->userid.'&id='.$module_id.'"> Коментар </a> '
        ?>    
        <tr align="center">
            <td  style="border: 1px solid #7CAFFC"> <?php echo $OUT->user_picture($sortirani[$i]->user, array("size" => 30)); ?> </td>
            <td  style="border: 1px solid #7CAFFC" > <?php echo $sortirani[$i]->firstname . " " . $sortirani[$i]->lastname; ?> </td>
            <td  style="border: 1px solid #7CAFFC" > <?php echo '<div class="links"> <a href="pregled_classes.php?userid=' . $sortirani[$i]->userid . '&id=' . $module_id . '">' . get_string('Review', 'ontology') . '</a> </div>'; ?> </td>
            <td  style="border: 1px solid #7CAFFC"> <?php echo '<div class="links"> <a href="teachernote.php?userid=' . $sortirani[$i]->userid . '&id=' . $module_id . '">' . get_string('Enter_comment', 'ontology') . '</a> </div>'; ?> </td>
            <td  style="border: 1px solid #7CAFFC"> <?php echo $sortirani[$i]->ostanati; ?> </td>
            <td  style="border: 1px solid #7CAFFC"> <?php echo $sortirani[$i]->rating; ?> </td>
            <td  style="border: 1px solid #7CAFFC"> <?php echo $sortirani[$i]->prifateni; ?> </td>
            <td  style="border: 1px solid #7CAFFC"> <?php echo $sortirani[$i]->odbieni; ?> </td>
            <td  style="border: 1px solid #7CAFFC"> <?php echo $sortirani[$i]->kazneti; ?> </td>
        </tr>
        <?php
    }
}
function read_non_active_students($module_id, $OUT) {
    global $DB;
    $k = $DB->get_record('course_modules', array('id' => $module_id));
    $r = $DB->get_records_sql('SELECT distinct userid FROM mdl_role_assignments WHERE (roleid=5 AND contextid IN (SELECT id FROM mdl_context WHERE (instanceid=? AND contextlevel=50)));', array($k->course));
    $c = $DB->get_records_sql('(SELECT userid from mdl_ontology_class where course_modulesid=?) UNION (SELECT userid from mdl_ontology_individual where course_modulesid=?) UNION (SELECT userid from mdl_ontology_class_expression where course_modulesid=?) UNION (SELECT userid from mdl_ontology_individual_expression where course_modulesid=?) UNION (SELECT userid from mdl_ontology_individual_property_data where course_modulesid=?) UNION (SELECT userid from mdl_ontology_individual_property_individual where course_modulesid=?) UNION (SELECT userid from mdl_ontology_property_data where course_modulesid=?) UNION (SELECT userid from mdl_ontology_property_disjoint where course_modulesid=?) UNION (SELECT userid from mdl_ontology_property_equivalent where course_modulesid=?) UNION (SELECT userid from mdl_ontology_property_expression where course_modulesid=?) UNION (SELECT userid from mdl_ontology_property_individual where course_modulesid=?);', array($module_id, $module_id, $module_id, $module_id, $module_id, $module_id, $module_id, $module_id, $module_id, $module_id, $module_id));
    $rankovi = array();
    $studenti = array();
    $sortirani = array();
    foreach ($r as $r1) {
        $ima = false;
        foreach ($c as $c1) {
            if ($r1->userid == $c1->userid)
                $ima = true;
        }
        if ($ima == false) {
            $s = $DB->get_record_sql('SELECT * FROM mdl_user WHERE id=?;', array($r1->userid));
            $user = $DB->get_record('user', array('id' => $s->id), '*', MUST_EXIST);
            $ontology = $DB->get_record('course_modules', array('id' => $module_id));
            $rank = $DB->get_record_sql('SELECT * from mdl_ontology_student_rank WHERE userid=? AND ontologyid=?;', array($r1->userid, $ontology->instance));
            if ($rank==null||$rank->rating==null||$rank->rating==''){
                $rank->rating=1;
                $rank1->userid = $r1->userid;
                $rank1->ontologyid = $ontology->instance;
                $rank1->correct = '0';
                $rank1->incorrect = '0';
                $rank1->penalty = '0';
                $rank1->rating = '1';
                $DB->insert_record('ontology_student_rank', $rank1);
            }
            $rankovi[$r1->userid] = $rank->rating;
            $studenti[$r1->userid] = $r1->userid;
            if (count($sortirani) == 0) { //nizata e prazna pa dolepuvame element so slednite polinja:
                $sortirani[0]->firstname = $s->firstname;
                $sortirani[0]->lastname = $s->lastname;
                $sortirani[0]->rating = $rank->rating;
                $sortirani[0]->user = $user;
                $sortirani[0]->userid = $r1->userid;
            } else {
                $goima = false; //pretpostavuvame deka ima najmal rejting
                 if ($rank->rating==null||$rank->rating=='')
                    $rank->Rating=1;
                for ($i = 0; $i < count($sortirani); $i++)
                    if ($sortirani[$i]->rating < $rank->rating) {
                        //noviot student treba da se stavi na i-ta pozicija
                        $temp = null;
                        $temp->firstname = $s->firstname;
                        $temp->lastname = $s->lastname;
                        $temp->rating = $rank->rating;
                        $temp->user = $user;
                        $temp->userid = $r1->userid;
                        $sortirani = insertArrayIndex($sortirani, $temp, $i); //go stavame elementot na i-ta pozicija
                        $goima = true; //nema najmal rejting
                        break;
                    }
                if (!$goima) {//ima najmal rejting od site pa go dolepuvame na kraj na nizata
                    $n = count($sortirani);
                    $sortirani[$n]->firstname = $s->firstname;
                    $sortirani[$n]->lastname = $s->lastname;
                    $sortirani[$n]->rating = $rank->rating;
                    $sortirani[$n]->user = $user;
                    $sortirani[$n]->userid = $r1->userid;
                }
            }
        }
    }
    for ($i = 0; $i < count($sortirani); $i++) {
        ?>    
        <tr align="center">
            <td style="border:1px solid #7CAFFC" > <?php echo $OUT->user_picture($sortirani[$i]->user, array("size" => 30)); ?> </td>
            <td style="border:1px solid #7CAFFC" > <?php echo $sortirani[$i]->firstname . " " . $sortirani[$i]->lastname; ?> </td>
            <td style="border:1px solid #7CAFFC" > <?php echo $sortirani[$i]->rating; ?> </td>
        </tr>
        <?php
    }
}
function read_finished_students($module_id, $OUT) {
    global $DB;
    $k = $DB->get_record('course_modules', array('id' => $module_id));
    $r = $DB->get_records_sql('SELECT distinct userid FROM mdl_role_assignments WHERE (roleid=5 AND contextid IN (SELECT id FROM mdl_context WHERE (instanceid=? AND contextlevel=50)));', array($k->course));
    $c = $DB->get_records_sql('(SELECT userid from mdl_ontology_class where course_modulesid=?) UNION (SELECT userid from mdl_ontology_individual where course_modulesid=?) UNION (SELECT userid from mdl_ontology_class_expression where course_modulesid=?) UNION (SELECT userid from mdl_ontology_individual_expression where course_modulesid=?) UNION (SELECT userid from mdl_ontology_individual_property_data where course_modulesid=?) UNION (SELECT userid from mdl_ontology_individual_property_individual where course_modulesid=?) UNION (SELECT userid from mdl_ontology_property_data where course_modulesid=?) UNION (SELECT userid from mdl_ontology_property_disjoint where course_modulesid=?) UNION (SELECT userid from mdl_ontology_property_equivalent where course_modulesid=?) UNION (SELECT userid from mdl_ontology_property_expression where course_modulesid=?) UNION (SELECT userid from mdl_ontology_property_individual where course_modulesid=?);', array($module_id, $module_id, $module_id, $module_id, $module_id, $module_id, $module_id, $module_id, $module_id, $module_id, $module_id));
    $rankovi = array();
    $studenti = array();
    $sortirani = array();
    foreach ($c as $r1) {
        $ima = false;
        foreach ($r as $r2) {
            if ($r1->userid == $r2->userid) {
                $ima = true;
                break;
            }
        }
        if ($ima) {
            $s = $DB->get_record_sql('SELECT * FROM mdl_user WHERE id=?;', array($r1->userid));
            $user = $DB->get_record('user', array('id' => $s->id), '*', MUST_EXIST);
            $ontology = $DB->get_record('course_modules', array('id' => $module_id));
            $rank = $DB->get_record_sql('SELECT * from mdl_ontology_student_rank WHERE userid=? AND ontologyid=?;', array($r1->userid, $ontology->instance));
            if ($rank==null||$rank->rating==null||$rank->rating==''){
                $rank->rating=1;
                $rank1->userid = $r1->userid;
                $rank1->ontologyid = $ontology->instance;
                $rank1->correct = '0';
                $rank1->incorrect = '0';
                $rank1->penalty = '0';
                $rank1->rating = '1';
                $DB->insert_record('ontology_student_rank', $rank1);
            }
            $rankovi[$r1->userid] = $rank->rating;
            $studenti[$r1->userid] = $r1->userid;

            // broenje na ostanati poimi za preglduvanje po student
            $broi1_ostanati = count($DB->get_records_sql('SELECT * from mdl_ontology_class where (status=1 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi2_ostanati = count($DB->get_records_sql('SELECT * from mdl_ontology_individual where (status=1 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi3_ostanati = count($DB->get_records_sql('SELECT * from mdl_ontology_class_expression where (status=1 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi4_ostanati = count($DB->get_records_sql('SELECT * from mdl_ontology_individual_expression where (status=1 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi5_ostanati = count($DB->get_records_sql('SELECT * from mdl_ontology_individual_property_data where (status=1 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi6_ostanati = count($DB->get_records_sql('SELECT * from mdl_ontology_individual_property_individual where (status=1 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi7_ostanati = count($DB->get_records_sql('SELECT * from mdl_ontology_property_data where (status=1 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi8_ostanati = count($DB->get_records_sql('SELECT * from mdl_ontology_property_disjoint where (status=1 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi9_ostanati = count($DB->get_records_sql('SELECT * from mdl_ontology_property_equivalent where (status=1 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi10_ostanati = count($DB->get_records_sql('SELECT * from mdl_ontology_property_expression where (status=1 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi11_ostanati = count($DB->get_records_sql('SELECT * from mdl_ontology_property_individual where (status=1 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi_ostanati = $broi1_ostanati + $broi2_ostanati + $broi3_ostanati + $broi4_ostanati + $broi5_ostanati + $broi6_ostanati + $broi7_ostanati + $broi8_ostanati + $broi9_ostanati + $broi10_ostanati + $broi11_ostanati;

            // broenje na prifateni poimi za preglduvanje po student
            $broi1_prifateni = count($DB->get_records_sql('SELECT * from mdl_ontology_class where ((status=2 or status=3 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi2_prifateni = count($DB->get_records_sql('SELECT * from mdl_ontology_individual where ((status=2 or status=3 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi3_prifateni = count($DB->get_records_sql('SELECT * from mdl_ontology_class_expression where ((status=2 or status=3 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi4_prifateni = count($DB->get_records_sql('SELECT * from mdl_ontology_individual_expression where ((status=2 or status=3 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi5_prifateni = count($DB->get_records_sql('SELECT * from mdl_ontology_individual_property_data where ((status=2 or status=3 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi6_prifateni = count($DB->get_records_sql('SELECT * from mdl_ontology_individual_property_individual where ((status=2 or status=3 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi7_prifateni = count($DB->get_records_sql('SELECT * from mdl_ontology_property_data where ((status=2 or status=3 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi8_prifateni = count($DB->get_records_sql('SELECT * from mdl_ontology_property_disjoint where ((status=2 or status=3 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi9_prifateni = count($DB->get_records_sql('SELECT * from mdl_ontology_property_equivalent where ((status=2 or status=3 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi10_prifateni = count($DB->get_records_sql('SELECT * from mdl_ontology_property_expression where ((status=2 or status=3 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi11_prifateni = count($DB->get_records_sql('SELECT * from mdl_ontology_property_individual where ((status=2 or status=3 or status=5) and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi_prifateni = $broi1_prifateni + $broi2_prifateni + $broi3_prifateni + $broi4_prifateni + $broi5_prifateni + $broi6_prifateni + $broi7_prifateni + $broi8_prifateni + $broi9_prifateni + $broi10_prifateni + $broi11_prifateni;

            // broenje na odbieni poimi za preglduvanje po student
            $broi1_odbieni = count($DB->get_records_sql('SELECT * from mdl_ontology_class where (status=4 and points=0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi2_odbieni = count($DB->get_records_sql('SELECT * from mdl_ontology_individual where (status=4 and points=0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi3_odbieni = count($DB->get_records_sql('SELECT * from mdl_ontology_class_expression where (status=4 and points=0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi4_odbieni = count($DB->get_records_sql('SELECT * from mdl_ontology_individual_expression where (status=4 and points=0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi5_odbieni = count($DB->get_records_sql('SELECT * from mdl_ontology_individual_property_data where (status=4 and points=0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi6_odbieni = count($DB->get_records_sql('SELECT * from mdl_ontology_individual_property_individual where (status=4 and points=0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi7_odbieni = count($DB->get_records_sql('SELECT * from mdl_ontology_property_data where (status=4 and points=0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi8_odbieni = count($DB->get_records_sql('SELECT * from mdl_ontology_property_disjoint where (status=4 and points=0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi9_odbieni = count($DB->get_records_sql('SELECT * from mdl_ontology_property_equivalent where (status=4 and points=0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi10_odbieni = count($DB->get_records_sql('SELECT * from mdl_ontology_property_expression where (status=4 and points=0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi11_odbieni = count($DB->get_records_sql('SELECT * from mdl_ontology_property_individual where (status=4 and points=0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi_odbieni = $broi1_odbieni + $broi2_odbieni + $broi3_odbieni + $broi4_odbieni + $broi5_odbieni + $broi6_odbieni + $broi7_odbieni + $broi8_odbieni + $broi9_odbieni + $broi10_odbieni + $broi11_odbieni;

            // broenje na kazneti poimi za preglduvanje po student
            $broi1_kazneti = count($DB->get_records_sql('SELECT * from mdl_ontology_class where (status=4 and points<0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi2_kazneti = count($DB->get_records_sql('SELECT * from mdl_ontology_individual where (status=4 and points<0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi3_kazneti = count($DB->get_records_sql('SELECT * from mdl_ontology_class_expression where (status=4 and points<0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi4_kazneti = count($DB->get_records_sql('SELECT * from mdl_ontology_individual_expression where (status=4 and points<0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi5_kazneti = count($DB->get_records_sql('SELECT * from mdl_ontology_individual_property_data where (status=4 and points<0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi6_kazneti = count($DB->get_records_sql('SELECT * from mdl_ontology_individual_property_individual where (status=4 and points<0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi7_kazneti = count($DB->get_records_sql('SELECT * from mdl_ontology_property_data where (status=4 and points<0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi8_kazneti = count($DB->get_records_sql('SELECT * from mdl_ontology_property_disjoint where (status=4 and points<0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi9_kazneti = count($DB->get_records_sql('SELECT * from mdl_ontology_property_equivalent where (status=4 and points<0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi10_kazneti = count($DB->get_records_sql('SELECT * from mdl_ontology_property_expression where (status=4 and points<0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi11_kazneti = count($DB->get_records_sql('SELECT * from mdl_ontology_property_individual where (status=4 and points<0 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi_kazneti = $broi1_kazneti + $broi2_kazneti + $broi3_kazneti + $broi4_kazneti + $broi5_kazneti + $broi6_kazneti + $broi7_kazneti + $broi8_kazneti + $broi9_kazneti + $broi10_kazneti + $broi11_kazneti;

            if ($broi_ostanati == 0) {

                if (count($sortirani) == 0) { //nizata e prazna pa dolepuvame element so slednite polinja:
                    $sortirani[0]->firstname = $s->firstname;
                    $sortirani[0]->lastname = $s->lastname;
                    $sortirani[0]->ostanati = $broi_ostanati;
                    $sortirani[0]->rating = $rank->rating;
                    $sortirani[0]->prifateni = $broi_prifateni;
                    $sortirani[0]->odbieni = $broi_odbieni;
                    $sortirani[0]->kazneti = $broi_kazneti;
                    $sortirani[0]->user = $user;
                    $sortirani[0]->userid = $r1->userid;
                } else {

                    $goima = false; //pretpostavuvame deka ima najmal rejting
                    for ($i = 0; $i < count($sortirani); $i++)
                        if ($sortirani[$i]->rating < $rank->rating) {
                            //noviot student treba da se stavi na i-ta pozicija
                            $temp = null;
                            $temp->firstname = $s->firstname;
                            $temp->lastname = $s->lastname;
                            $temp->ostanati = $broi_ostanati;
                            $temp->rating = $rank->rating;
                            $temp->prifateni = $broi_prifateni;
                            $temp->odbieni = $broi_odbieni;
                            $temp->kazneti = $broi_kazneti;
                            $temp->user = $user;
                            $temp->userid = $r1->userid;
                            $sortirani = insertArrayIndex($sortirani, $temp, $i); //go stavame elementot na i-ta pozicija
                            $goima = true; //nema najmal rejting
                            break;
                        }
                    if (!$goima) {//ima najmal rejting od site pa go dolepuvame na kraj na nizata
                        $n = count($sortirani);
                        $sortirani[$n]->firstname = $s->firstname;
                        $sortirani[$n]->lastname = $s->lastname;
                        $sortirani[$n]->ostanati = $broi_ostanati;
                        $sortirani[$n]->rating = $rank->rating;
                        $sortirani[$n]->prifateni = $broi_prifateni;
                        $sortirani[$n]->odbieni = $broi_odbieni;
                        $sortirani[$n]->kazneti = $broi_kazneti;
                        $sortirani[$n]->user = $user;
                        $sortirani[$n]->userid = $r1->userid;
                    }
                }
            }
        }
    }
    for ($i = 0; $i < count($sortirani); $i++) {
        ?>    
        <tr align="center">
            <td style="border:1px solid #7CAFFC" > <?php echo $OUT->user_picture($sortirani[$i]->user, array("size" => 30)); ?> </td>
            <td style="border:1px solid #7CAFFC" > <?php echo $sortirani[$i]->firstname . " " . $sortirani[$i]->lastname; ?> </td>
            <td style="border:1px solid #7CAFFC" > <?php echo '<div class="links"> <a href="pregled_classes.php?userid=' . $sortirani[$i]->userid . '&id=' . $module_id . '">' . get_string('Review', 'ontology') . '</a> </div>'; ?> </td>
            <td style="border:1px solid #7CAFFC" > <?php echo '<div class="links"> <a href="teachernote.php?userid=' . $sortirani[$i]->userid . '&id=' . $module_id . '">' . get_string('Enter_comment', 'ontology') . '</a> </div>'; ?> </td>
            <td style="border:1px solid #7CAFFC" > <?php echo $sortirani[$i]->ostanati; ?> </td>
            <td style="border:1px solid #7CAFFC" > <?php echo $sortirani[$i]->rating; ?> </td>
            <td style="border:1px solid #7CAFFC" > <?php echo $sortirani[$i]->prifateni; ?> </td>
            <td style="border:1px solid #7CAFFC" > <?php echo $sortirani[$i]->odbieni; ?> </td>
            <td style="border:1px solid #7CAFFC" > <?php echo $sortirani[$i]->kazneti; ?> </td>
        </tr>
        <?php
    }
}
function check_students_entry($module_id) {
    global $DB;
    $c = count($DB->get_records_sql('(SELECT userid from mdl_ontology_class where course_modulesid=? and status=1) UNION (SELECT userid from mdl_ontology_individual where course_modulesid=? and status=1) UNION (SELECT userid from mdl_ontology_class_expression where course_modulesid=? and status=1) UNION (SELECT userid from mdl_ontology_individual_expression where course_modulesid=? and status=1) UNION (SELECT userid from mdl_ontology_individual_property_data where course_modulesid=? and status=1) UNION (SELECT userid from mdl_ontology_individual_property_individual where course_modulesid=? and status=1) UNION (SELECT userid from mdl_ontology_property_data where course_modulesid=? and status=1) UNION (SELECT userid from mdl_ontology_property_disjoint where course_modulesid=? and status=1) UNION (SELECT userid from mdl_ontology_property_equivalent where course_modulesid=? and status=1) UNION (SELECT userid from mdl_ontology_property_expression where course_modulesid=? and status=1) UNION (SELECT userid from mdl_ontology_property_individual where course_modulesid=? and status=1);', array($module_id, $module_id, $module_id, $module_id, $module_id, $module_id, $module_id, $module_id, $module_id, $module_id, $module_id)));
    if ($c == 0)
        return false;
    else
        return true;
}
function check_finished_students($module_id) {
    global $DB;
    $k = $DB->get_record('course_modules', array('id' => $module_id));
    $r = $DB->get_records_sql('SELECT distinct userid FROM mdl_role_assignments WHERE (roleid=5 AND contextid IN (SELECT id FROM mdl_context WHERE (instanceid=? AND contextlevel=50)));', array($k->course));
    $c = $DB->get_records_sql('(SELECT userid from mdl_ontology_class where course_modulesid=?) UNION (SELECT userid from mdl_ontology_individual where course_modulesid=?) UNION (SELECT userid from mdl_ontology_class_expression where course_modulesid=?) UNION (SELECT userid from mdl_ontology_individual_expression where course_modulesid=?) UNION (SELECT userid from mdl_ontology_individual_property_data where course_modulesid=?) UNION (SELECT userid from mdl_ontology_individual_property_individual where course_modulesid=?) UNION (SELECT userid from mdl_ontology_property_data where course_modulesid=?) UNION (SELECT userid from mdl_ontology_property_disjoint where course_modulesid=?) UNION (SELECT userid from mdl_ontology_property_equivalent where course_modulesid=?) UNION (SELECT userid from mdl_ontology_property_expression where course_modulesid=?) UNION (SELECT userid from mdl_ontology_property_individual where course_modulesid=?);', array($module_id, $module_id, $module_id, $module_id, $module_id, $module_id, $module_id, $module_id, $module_id, $module_id, $module_id));
    foreach ($c as $r1) {
        $ima = false;
        foreach ($r as $r2) {
            if ($r1->userid == $r2->userid) {
                $ima = true;
                break;
            }
        }
        if ($ima) {
            $s = $DB->get_record_sql('SELECT * FROM mdl_user WHERE id=?;', array($r1->userid));
            $user = $DB->get_record('user', array('id' => $s->id), '*', MUST_EXIST);
            $ontology = $DB->get_record('course_modules', array('id' => $module_id));
            $rank = $DB->get_record_sql('SELECT * from mdl_ontology_student_rank WHERE userid=? AND ontologyid=?;', array($r1->userid, $ontology->instance));

            // broenje na ostanati poimi za preglduvanje po student
            $broi1_ostanati = count($DB->get_records_sql('SELECT * from mdl_ontology_class where (status=1 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi2_ostanati = count($DB->get_records_sql('SELECT * from mdl_ontology_individual where (status=1 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi3_ostanati = count($DB->get_records_sql('SELECT * from mdl_ontology_class_expression where (status=1 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi4_ostanati = count($DB->get_records_sql('SELECT * from mdl_ontology_individual_expression where (status=1 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi5_ostanati = count($DB->get_records_sql('SELECT * from mdl_ontology_individual_property_data where (status=1 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi6_ostanati = count($DB->get_records_sql('SELECT * from mdl_ontology_individual_property_individual where (status=1 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi7_ostanati = count($DB->get_records_sql('SELECT * from mdl_ontology_property_data where (status=1 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi8_ostanati = count($DB->get_records_sql('SELECT * from mdl_ontology_property_disjoint where (status=1 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi9_ostanati = count($DB->get_records_sql('SELECT * from mdl_ontology_property_equivalent where (status=1 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi10_ostanati = count($DB->get_records_sql('SELECT * from mdl_ontology_property_expression where (status=1 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi11_ostanati = count($DB->get_records_sql('SELECT * from mdl_ontology_property_individual where (status=1 and course_modulesid=? and userid=?);', array($module_id, $r1->userid)));
            $broi_ostanati = $broi1_ostanati + $broi2_ostanati + $broi3_ostanati + $broi4_ostanati + $broi5_ostanati + $broi6_ostanati + $broi7_ostanati + $broi8_ostanati + $broi9_ostanati + $broi10_ostanati + $broi11_ostanati;
            if ($broi_ostanati == 0)
                return true;
        }
    }
    return false;
}
function check_non_active_students($module_id) {
    global $DB;
    $k = $DB->get_record('course_modules', array('id' => $module_id));
    $r = $DB->get_records_sql('SELECT distinct userid FROM mdl_role_assignments WHERE (roleid=5 AND contextid IN (SELECT id FROM mdl_context WHERE (instanceid=? AND contextlevel=50)));', array($k->course));
    $c = $DB->get_records_sql('(SELECT userid from mdl_ontology_class where course_modulesid=?) UNION (SELECT userid from mdl_ontology_individual where course_modulesid=?) UNION (SELECT userid from mdl_ontology_class_expression where course_modulesid=?) UNION (SELECT userid from mdl_ontology_individual_expression where course_modulesid=?) UNION (SELECT userid from mdl_ontology_individual_property_data where course_modulesid=?) UNION (SELECT userid from mdl_ontology_individual_property_individual where course_modulesid=?) UNION (SELECT userid from mdl_ontology_property_data where course_modulesid=?) UNION (SELECT userid from mdl_ontology_property_disjoint where course_modulesid=?) UNION (SELECT userid from mdl_ontology_property_equivalent where course_modulesid=?) UNION (SELECT userid from mdl_ontology_property_expression where course_modulesid=?) UNION (SELECT userid from mdl_ontology_property_individual where course_modulesid=?);', array($module_id, $module_id, $module_id, $module_id, $module_id, $module_id, $module_id, $module_id, $module_id, $module_id, $module_id));
    foreach ($r as $r1) {
        $ima = false;
        foreach ($c as $c1) {
            if ($r1->userid == $c1->userid)
                $ima = true;
        }
        if ($ima == false)
            return true;
    }
    return false;
}
function insertArrayIndex($array, $new_element, $index) {
    //funkcija koja dodava vo niza nov element na i-ta pozicija
    /*     * * get the start of the array ** */
    $start = array_slice($array, 0, $index);
    /*     * * get the end of the array ** */
    $end = array_slice($array, $index);
    /*     * * add the new element to the array ** */
    $start[] = $new_element;
    /*     * * glue them back together and return ** */
    return array_merge($start, $end);
}
function class_hierarhy($id, $fime, $ismultiple, $moduleid, $userid) { //generiranje na klasna hierarhija vo listbox
    //id                id na listboxot na hierarhijata
    //fime              ime na javascript funkcijata koja se povikuva
    //ismultiple        dali moze da se selektiraat poveke vrednosti vo listboxot
    //moduleid          id na modulot
    //userid            id na korisnikot
    global $DB;
    $ontologyinstanceid = $DB->get_record('course_modules', array('id' => $moduleid)); //id na instancata na ontologijata
    $ontologyid = $DB->get_record('modules', array('name' => 'ontology')); //id na modulot ontologija
    $niza = array(); //ID na sekoja klasa
    $niza2 = array(); //ime na sekoja klasa
    $niza3 = array(); //broj na prazni mesta na sekoja klasa
    $niza4 = array(); //za oznacuvanje dali e vneseno od korisnikot ovaa nedela ili e vo ontologijata od porano
    $classes = $DB->get_records_sql('SELECT * from mdl_ontology_class WHERE ( status=2 AND course_modulesid IN (SELECT id FROM mdl_course_modules WHERE instance=? AND module=?)) OR (status=1 AND course_modulesid IN (SELECT id FROM mdl_course_modules WHERE instance=? AND module=?) AND userid=? );', array($ontologyinstanceid->instance, $ontologyid->id, $ontologyinstanceid->instance, $ontologyid->id, $userid));
    $lista1 = array(); //vo ovaa lista ke bidat smesteni site klasi koi imaat id na superklasa pogolemo od id na klasa
    while (count($classes) != 0) {
        $lista1 = array();
        $size=count($classes);
        foreach ($classes as $key => $value) {
            if ($value->name == "Основна") {
                array_push($niza, $key);
                array_push($niza2, $value->name);
                array_push($niza3, 0);
                array_push($niza4, 0);
            } else {
                $goima = false;
                for ($i = 0; $i < count($niza); $i++)
                    if ($niza[$i] == $value->superclass) {
                        $index = $i;
                        $goima = true;
                        break;
                    }
                if ($goima) {
                    $i = $index;
                    $niza = insertArrayIndex($niza, $key, $i + 1);
                    $niza3 = insertArrayIndex($niza3, $niza3[$i] + 3, $i + 1);
                    $string = "";
                    for ($j = 0; $j < $niza3[$i + 1]; $j++) {
                        $string = $string . "&nbsp";
                    }

                    $niza2 = insertArrayIndex($niza2, $string . $value->name, $i + 1);
                    if ($value->status == 1)
                        $niza4 = insertArrayIndex($niza4, 1, $i + 1);
                    else
                        $niza4 = insertArrayIndex($niza4, 0, $i + 1);
                }
                else {
                    //$n = count($lista1);
                    $lista1[$key] = $classes[$key];
                }
            }
        }
        $classes = $lista1;
        if (count($lista1)==$size){
            break;
        }
    }
    if (count($classes)!=0){
        foreach ($classes as $key => $value){
            $value->status=5;
            $DB->update_record('ontology_class', $value);
        }   
    }
    ?>
    <select name=<?php echo "\"" . $id . "\"" ?> id=<?php echo "\"" . $id . "\"" ?> size="10" <?php if ($ismultiple == 1) {
        echo "multiple=\"multiple\"";
    } ?> onChange=<?php echo "\"" . $fime . "(this);\"" ?>>

    <?php
    for ($i = 0; $i < count($niza); $i++)
        if ($niza4[$i] == 0)
            echo "<option value=\"" . $niza[$i] . "\">" . $niza2[$i] . "</option>";
        else
            echo "<option style=\"color: red\" value=\"" . $niza[$i] . "\">" . $niza2[$i] . "</option>";
    ?>
    </select>
    <?php
}
function class_hierarhy2($id, $fime, $ismultiple, $moduleid, $userid) { //generiranje na klasna hierarhija vo listbox
    //id                id na listboxot na hierarhijata
    //fime              ime na javascript funkcijata koja se povikuva
    //ismultiple        dali moze da se selektiraat poveke vrednosti vo listboxot
    //moduleid          id na modulot
    //userid            id na korisnikot
    global $DB;
    $ontologyinstanceid = $DB->get_record('course_modules', array('id' => $moduleid)); //id na instancata na ontologijata
    $ontologyid = $DB->get_record('modules', array('name' => 'ontology')); //id na modulot ontologija
    $niza = array(); //ID na sekoja klasa
    $niza2 = array(); //ime na sekoja klasa
    $niza3 = array(); //broj na prazni mesta na sekoja klasa
    $niza4 = array(); //za oznacuvanje dali e vneseno od korisnikot ovaa nedela ili e vo ontologijata od porano
    $classes = $DB->get_records_sql('SELECT * from mdl_ontology_class WHERE (status=2 AND course_modulesid IN (SELECT id FROM mdl_course_modules WHERE instance=? AND module=?)) OR (course_modulesid=? AND userid=? );', array($ontologyinstanceid->instance, $ontologyid->id, $moduleid, $userid));
    $lista1 = array(); //vo ovaa lista ke bidat smesteni site klasi koi imaat id na superklasa pogolemo od id na klasa

    while (count($classes) != 0) {
        $lista1 = array();
        $size=count($classes);
        foreach ($classes as $key => $value) {
            if ($value->name == "Основна") {
                array_push($niza, $key);
                array_push($niza2, $value->name);
                array_push($niza3, 0);
                array_push($niza4, 0);
            } else {
                $goima = false;
                for ($i = 0; $i < count($niza); $i++)
                    if ($niza[$i] == $value->superclass) {
                        $index = $i;
                        $goima = true;
                        break;
                    }
                if ($goima) {
                    $i = $index;
                    $niza = insertArrayIndex($niza, $key, $i + 1);
                    $niza3 = insertArrayIndex($niza3, $niza3[$i] + 3, $i + 1);
                    $string = "";
                    for ($j = 0; $j < $niza3[$i + 1]; $j++) {
                        $string = $string . "&nbsp";
                    }
                    $expressions = $DB->get_records('ontology_class_expression', array('ontology_classid' => $key, 'status' => 1, 'userid' => $userid, 'course_modulesid' => $moduleid));
                    if (count($expressions) == 0)
                        $niza2 = insertArrayIndex($niza2, $string . $value->name, $i + 1);
                    else
                        $niza2 = insertArrayIndex($niza2, $string . $value->name . '(' . count($expressions) . ')', $i + 1);
                    if ($value->status == 1)
                        $niza4 = insertArrayIndex($niza4, 1, $i + 1);
                    if ($value->status == 2)
                        $niza4 = insertArrayIndex($niza4, 2, $i + 1);
                    if ($value->status == 3)
                        $niza4 = insertArrayIndex($niza4, 3, $i + 1);
                    if ($value->status == 4)
                        $niza4 = insertArrayIndex($niza4, 4, $i + 1);
                    if ($value->status == 5)
                        $niza4 = insertArrayIndex($niza4, 5, $i + 1);
                }
                else {
                    //$n = count($lista1);
                    $lista1[$key] = $classes[$key];
                }
            }
        }
        $classes = $lista1;
        if (count($lista1)==$size){
            break;
        }
    }
    if (count($classes)!=0){
        foreach ($classes as $key => $value){
            $value->status=5;
            $DB->update_record('ontology_class', $value);
        }   
    }
    ?>
    <select name=<?php echo "\"" . $id . "\"" ?> id=<?php echo "\"" . $id . "\"" ?> size="10" <?php if ($ismultiple == 1) {
        echo "multiple=\"multiple\"";
    } ?> onChange=<?php echo "\"" . $fime . "(this);\"" ?>>

    <?php
    for ($i = 0; $i < count($niza); $i++) {
        if ($niza4[$i] == 0)
            echo "<option value=\"" . $niza[$i] . "\">" . $niza2[$i] . "</option>";
        if ($niza4[$i] == 1)
            echo "<option style=\"color: orange\" value=\"" . $niza[$i] . "\">" . $niza2[$i] . "</option>";
        if ($niza4[$i] == 2)
            echo "<option value=\"" . $niza[$i] . "\">" . $niza2[$i] . "</option>";
        if ($niza4[$i] == 3)
            echo "<option style=\"color: green\" value=\"" . $niza[$i] . "\">" . $niza2[$i] . "</option>";
        if ($niza4[$i] == 4)
            echo "<option style=\"color: red\" value=\"" . $niza[$i] . "\">" . $niza2[$i] . "</option>";
        if ($niza4[$i] == 5)
            echo "<option style=\"color: green\" value=\"" . $niza[$i] . "\">" . $niza2[$i] . "</option>";
    }
    ?>
    </select>
    <?php
}
function class_hierarhy3($id, $fime, $ismultiple, $moduleid, $userid, $classnames) { //generiranje na klasna hierarhija vo listbox
    //id                id na listboxot na hierarhijata
    //fime              ime na javascript funkcijata koja se povikuva
    //ismultiple        dali moze da se selektiraat poveke vrednosti vo listboxot
    //moduleid          id na modulot
    //userid            id na korisnikot
    //classnames        lista na iminja na klasi od koi edna ke bide selektirana
    global $DB;
    $ontologyinstanceid = $DB->get_record('course_modules', array('id' => $moduleid)); //id na instancata na ontologijata
    $ontologyid = $DB->get_record('modules', array('name' => 'ontology')); //id na modulot ontologija
    $niza = array(); //ID na sekoja klasa
    $niza2 = array(); //ime na sekoja klasa
    $niza3 = array(); //broj na prazni mesta na sekoja klasa
    $niza4 = array(); //za oznacuvanje dali e vneseno od korisnikot ovaa nedela ili e vo ontologijata od porano
    $classes = $DB->get_records_sql('SELECT * from mdl_ontology_class WHERE ( status=2 AND course_modulesid IN (SELECT id FROM mdl_course_modules WHERE instance=? AND module=?)) OR (status=1 AND course_modulesid IN (SELECT id FROM mdl_course_modules WHERE instance=? AND module=?) AND userid=? );', array($ontologyinstanceid->instance, $ontologyid->id, $ontologyinstanceid->instance, $ontologyid->id, $userid));
    $lista1 = array(); //vo ovaa lista ke bidat smesteni site klasi koi imaat id na superklasa pogolemo od id na klasa
    while (count($classes) != 0) {
        $lista1 = array();
        $size=count($classes);
        foreach ($classes as $key => $value) {
            if ($value->name == "Основна") {
                array_push($niza, $key);
                array_push($niza2, $value->name);
                array_push($niza3, 0);
                array_push($niza4, 0);
            } else {
                $goima = false;
                for ($i = 0; $i < count($niza); $i++)
                    if ($niza[$i] == $value->superclass) {
                        $index = $i;
                        $goima = true;
                        break;
                    }
                if ($goima) {
                    $i = $index;
                    $niza = insertArrayIndex($niza, $key, $i + 1);
                    $niza3 = insertArrayIndex($niza3, $niza3[$i] + 3, $i + 1);
                    $string = "";
                    for ($j = 0; $j < $niza3[$i + 1]; $j++) {
                        $string = $string . "&nbsp";
                    }

                    $niza2 = insertArrayIndex($niza2, $string . $value->name, $i + 1);
                    if ($value->status == 1)
                        $niza4 = insertArrayIndex($niza4, 1, $i + 1);
                    else
                        $niza4 = insertArrayIndex($niza4, 0, $i + 1);
                }
                else {
                    //$n = count($lista1);
                    $lista1[$key] = $classes[$key];
                }
            }
        }
        $classes = $lista1;
        if (count($lista1)==$size){
            break;
        }
    }
    if (count($classes)!=0){
        foreach ($classes as $key => $value){
            $value->status=5;
            $DB->update_record('ontology_class', $value);
        }   
    }
    ?>
    <select name=<?php echo "\"" . $id . "\"" ?> id=<?php echo "\"" . $id . "\"" ?> size="10" <?php if ($ismultiple == 1) {
        echo "multiple=\"multiple\"";
    } ?> onChange=<?php echo "\"" . $fime . "(this);\"" ?>>

    <?php
    $isselected = false;
    for ($i = 0; $i < count($niza); $i++)
        if ($niza4[$i] == 0) {
            echo "<option value=\"" . $niza[$i] . "\"";
            if (!$isselected) {
                foreach ($classnames as $key => $value)
                    if ($value == $niza[$i]) {
                        echo "selected=\"selected\"";
                        $isselected = true;
                        break;
                    }
            }
            echo ">" . $niza2[$i] . "</option>";
        }
        else
            echo "<option style=\"color: red\" value=\"" . $niza[$i] . "\">" . $niza2[$i] . "</option>";
    ?>
    </select>
    <?php
    return $isselected;
}
//Klasna hierarhija 4 - hiearhija na site klasi, osven na klasata $classid i site nejzini podklasi i kako default e selektirana nejzinata superklasa
function class_hierarhy4($id, $fime, $ismultiple, $moduleid, $userid, $classid) { //generiranje na klasna hierarhija vo listbox
    //id                id na listboxot na hierarhijata
    //fime              ime na javascript funkcijata koja se povikuva
    //ismultiple        dali moze da se selektiraat poveke vrednosti vo listboxot
    //moduleid          id na modulot
    //userid            id na korisnikot
    //classnames        lista na iminja na klasi od koi edna ke bide selektirana
    global $DB;
    $ontologyinstanceid = $DB->get_record('course_modules', array('id' => $moduleid)); //id na instancata na ontologijata
    $ontologyid = $DB->get_record('modules', array('name' => 'ontology')); //id na modulot ontologija
    $niza = array(); //ID na sekoja klasa
    $niza2 = array(); //ime na sekoja klasa
    $niza3 = array(); //broj na prazni mesta na sekoja klasa
    $niza4 = array(); //za oznacuvanje dali e vneseno od korisnikot ovaa nedela ili e vo ontologijata od porano
    $classes = $DB->get_records_sql('SELECT * from mdl_ontology_class WHERE ( status=2 AND course_modulesid IN (SELECT id FROM mdl_course_modules WHERE instance=? AND module=?)) OR (status=1 AND course_modulesid IN (SELECT id FROM mdl_course_modules WHERE instance=? AND module=?) AND userid=? );', array($ontologyinstanceid->instance, $ontologyid->id, $ontologyinstanceid->instance, $ontologyid->id, $userid));
    $lista1 = array(); //vo ovaa lista ke bidat smesteni site klasi koi imaat id na superklasa pogolemo od id na klasa

    while (count($classes) != 0) {
        $lista1 = array();
        $broj = count($niza);
        foreach ($classes as $key => $value) {
            if ($key == $classid) {
                
            } else
            if ($value->name == "Основна") {
                array_push($niza, $key);
                array_push($niza2, $value->name);
                array_push($niza3, 0);
                array_push($niza4, 0);
            } else {
                $goima = false;
                for ($i = 0; $i < count($niza); $i++)
                    if ($niza[$i] == $value->superclass) {
                        $index = $i;
                        $goima = true;
                        break;
                    }
                if ($goima) {
                    $i = $index;
                    $niza = insertArrayIndex($niza, $key, $i + 1);
                    $niza3 = insertArrayIndex($niza3, $niza3[$i] + 3, $i + 1);
                    $string = "";
                    for ($j = 0; $j < $niza3[$i + 1]; $j++) {
                        $string = $string . "&nbsp";
                    }

                    $niza2 = insertArrayIndex($niza2, $string . $value->name, $i + 1);
                    if ($value->status == 1)
                        $niza4 = insertArrayIndex($niza4, 1, $i + 1);
                    else
                        $niza4 = insertArrayIndex($niza4, 0, $i + 1);
                }
                else {
                    //$n = count($lista1);
                    $lista1[$key] = $classes[$key];
                }
            }
        }
        if (count($niza) == $broj)
            break;
        $classes = $lista1;
    }
    if (count($classes)!=0){
        foreach ($classes as $key => $value){
            $value->status=5;
            $DB->update_record('ontology_class', $value);
        }   
    }
    ?>
    <select name=<?php echo "\"" . $id . "\"" ?> id=<?php echo "\"" . $id . "\"" ?> size="10" <?php if ($ismultiple == 1) {
        echo "multiple=\"multiple\"";
    } ?> onChange=<?php echo "\"" . $fime . "(this);\"" ?>>

    <?php
    $class = $DB->get_record('ontology_class', array('id' => $classid));
    $superclass = $class->superclass;
    for ($i = 0; $i < count($niza); $i++) {
        echo "<option value=\"" . $niza[$i] . "\"";
        if ($superclass == $niza[$i])
            echo "selected=\"selected\"";
        if ($niza4[$i] != 0)
            echo "style=\"color: red\"";
        echo ">" . $niza2[$i] . "</option>";
    }
    ?>
    </select>
    <?php
}
function object_property_hierarhy($id, $fime, $ismultiple, $moduleid, $userid, $isnotvisible) {//generiranje na hierarhija na objektni svojstva
    //id                id na listboxot na hierarhijata
    //fime              ime na javascript funkcijata koja se povikuva
    //ismultiple        dali moze da se selektiraat poveke vrednosti vo listboxot
    //moduleid          id na modulot
    //userid            id na korisnikot
    //isnotvisible       dali e nevidlivo  
    global $DB;
    $ontologyinstanceid = $DB->get_record('course_modules', array('id' => $moduleid)); //id na instancata na ontologijata
    $ontologyid = $DB->get_record('modules', array('name' => 'ontology')); //id na modulot ontologija
    $niza = array(); //ID na sekoja klasa
    $niza2 = array(); //ime na sekoja klasa
    $niza3 = array(); //broj na prazni mesta na sekoja klasa
    $niza4 = array(); //za oznacuvanje dali e vneseno od korisnikot ovaa nedela ili e vo ontologijata od porano
    $properties = $DB->get_records_sql('SELECT * from mdl_ontology_property_individual WHERE ( status=2 AND course_modulesid IN (SELECT id FROM mdl_course_modules WHERE instance=? AND module=?)) OR (status=1 AND course_modulesid IN (SELECT id FROM mdl_course_modules WHERE instance=? AND module=?) AND userid=? );', array($ontologyinstanceid->instance, $ontologyid->id, $ontologyinstanceid->instance, $ontologyid->id, $userid));
    $lista1 = array(); //vo ovaa lista ke bidat smesteni site klasi koi imaat id na superklasa pogolemo od id na klasa

    while (count($properties) != 0) {
        $lista1 = array();
        $broj=count($properties);
        foreach ($properties as $key => $value) {
            if ($value->name == "Основно") {
                array_push($niza, $key);
                array_push($niza2, $value->name);
                array_push($niza3, 0);
                array_push($niza4, 0);
            } else {
                $goima = false;
                for ($i = 0; $i < count($niza); $i++)
                    if ($niza[$i] == $value->superproperty) {
                        $index = $i;
                        $goima = true;
                        break;
                    }
                if ($goima) {
                    $i = $index;
                    $niza = insertArrayIndex($niza, $key, $i + 1);
                    $niza3 = insertArrayIndex($niza3, $niza3[$i] + 3, $i + 1);
                    $string = "";
                    for ($j = 0; $j < $niza3[$i + 1]; $j++) {
                        $string = $string . "&nbsp";
                    }

                    $niza2 = insertArrayIndex($niza2, $string . $value->name, $i + 1);
                    if ($value->status == 1)
                        $niza4 = insertArrayIndex($niza4, 1, $i + 1);
                    else
                        $niza4 = insertArrayIndex($niza4, 0, $i + 1);
                }
                else {
                    //$n = count($lista1);
                    $lista1[$key] = $properties[$key];
                }
            }
        }
        $properties = $lista1;
        if (count($properties)==$broj){
            break;
        }
        
    }
    ?>
    <select name=<?php echo "\"" . $id . "\"" ?> id=<?php echo "\"" . $id . "\"" ?> size="10" <?php if ($ismultiple == 1) echo "multiple=\"multiple\""; if ($isnotvisible == 1) echo 'style="visibility: hidden;"'; ?> onChange=<?php echo "\"" . $fime . "(this);\"" ?>>

    <?php
    for ($i = 0; $i < count($niza); $i++)
        if ($niza4[$i] == 0)
            echo "<option value=\"" . $niza[$i] . "\">" . $niza2[$i] . "</option>";
        else
            echo "<option style=\"color: red\" value=\"" . $niza[$i] . "\">" . $niza2[$i] . "</option>";
    ?>
    </select>
    <?php
}
function object_property_hierarhy2($id, $fime, $ismultiple, $moduleid, $userid, $isnotvisible) {//generiranje na hierarhija na objektni svojstva
    //id                id na listboxot na hierarhijata
    //fime              ime na javascript funkcijata koja se povikuva
    //ismultiple        dali moze da se selektiraat poveke vrednosti vo listboxot
    //moduleid          id na modulot
    //userid            id na korisnikot
    //isnotvisible       dali e nevidlivo  
    global $DB;
    $ontologyinstanceid = $DB->get_record('course_modules', array('id' => $moduleid)); //id na instancata na ontologijata
    $ontologyid = $DB->get_record('modules', array('name' => 'ontology')); //id na modulot ontologija
    $niza = array(); //ID na sekoja klasa
    $niza2 = array(); //ime na sekoja klasa
    $niza3 = array(); //broj na prazni mesta na sekoja klasa
    $niza4 = array(); //za oznacuvanje dali e vneseno od korisnikot ovaa nedela ili e vo ontologijata od porano
    $properties = $DB->get_records_sql('SELECT * from mdl_ontology_property_individual WHERE ( status=2 AND course_modulesid IN (SELECT id FROM mdl_course_modules WHERE instance=? AND module=?)) OR (course_modulesid=? AND userid=? );', array($ontologyinstanceid->instance, $ontologyid->id, $moduleid, $userid));
    $lista1 = array(); //vo ovaa lista ke bidat smesteni site klasi koi imaat id na superklasa pogolemo od id na klasa

    while (count($properties) != 0) {
        $lista1 = array();
        foreach ($properties as $key => $value) {
            if ($value->name == "Основно") {
                array_push($niza, $key);
                array_push($niza2, $value->name);
                array_push($niza3, 0);
                array_push($niza4, 0);
            } else {
                $goima = false;
                for ($i = 0; $i < count($niza); $i++)
                    if ($niza[$i] == $value->superproperty) {
                        $index = $i;
                        $goima = true;
                        break;
                    }
                if ($goima) {
                    $i = $index;
                    $niza = insertArrayIndex($niza, $key, $i + 1);
                    $niza3 = insertArrayIndex($niza3, $niza3[$i] + 3, $i + 1);
                    $string = "";
                    for ($j = 0; $j < $niza3[$i + 1]; $j++) {
                        $string = $string . "&nbsp";
                    }
                    $expressions = $DB->get_records('ontology_property_expression', array('ontology_propertyid' => $key, 'status' => 1, 'userid' => $userid, 'course_modulesid' => $moduleid, 'type' => '1'));
                    $expressions2 = $DB->get_records('ontology_property_expression', array('ontology_propertyid' => $key, 'status' => 1, 'userid' => $userid, 'course_modulesid' => $moduleid, 'type' => '2'));
                    $equivalent = $DB->get_records('ontology_property_equivalent', array('ontology_propertyid' => $key, 'status' => 1, 'userid' => $userid, 'course_modulesid' => $moduleid, 'type' => '1'));
                    $disjoint = $DB->get_records('ontology_property_disjoint', array('ontology_propertyid' => $key, 'status' => 1, 'userid' => $userid, 'course_modulesid' => $moduleid, 'type' => '1'));
                    $number = count($expressions) + count($expressions2) + count($equivalent) + count($disjoint);
                    if ($number == 0)
                        $niza2 = insertArrayIndex($niza2, $string . $value->name, $i + 1);
                    else
                        $niza2 = insertArrayIndex($niza2, $string . $value->name . '(' . $number . ')', $i + 1);
                    if ($value->status == 1)
                        $niza4 = insertArrayIndex($niza4, 1, $i + 1);
                    if ($value->status == 2)
                        $niza4 = insertArrayIndex($niza4, 2, $i + 1);
                    if ($value->status == 3)
                        $niza4 = insertArrayIndex($niza4, 3, $i + 1);
                    if ($value->status == 4)
                        $niza4 = insertArrayIndex($niza4, 4, $i + 1);
                    if ($value->status == 5)
                        $niza4 = insertArrayIndex($niza4, 5, $i + 1);
                }
                else {
                    //$n = count($lista1);
                    $lista1[$key] = $properties[$key];
                }
            }
        }
        $properties = $lista1;
    }
    ?>
    <select name=<?php echo "\"" . $id . "\"" ?> id=<?php echo "\"" . $id . "\"" ?> size="10" <?php if ($ismultiple == 1) echo "multiple=\"multiple\""; if ($isnotvisible == 1) echo 'style="visibility: hidden;"'; ?> onChange=<?php echo "\"" . $fime . "(this);\"" ?>>

    <?php
    for ($i = 0; $i < count($niza); $i++) {
        if ($niza4[$i] == 0)
            echo "<option value=\"" . $niza[$i] . "\">" . $niza2[$i] . "</option>";
        if ($niza4[$i] == 1)
            echo "<option style=\"color: orange\" value=\"" . $niza[$i] . "\">" . $niza2[$i] . "</option>";
        if ($niza4[$i] == 2)
            echo "<option value=\"" . $niza[$i] . "\">" . $niza2[$i] . "</option>";
        if ($niza4[$i] == 3)
            echo "<option style=\"color: green\" value=\"" . $niza[$i] . "\">" . $niza2[$i] . "</option>";
        if ($niza4[$i] == 4)
            echo "<option style=\"color: red\" value=\"" . $niza[$i] . "\">" . $niza2[$i] . "</option>";
        if ($niza4[$i] == 5)
            echo "<option style=\"color: green\" value=\"" . $niza[$i] . "\">" . $niza2[$i] . "</option>";
    }
    ?>
    </select>
    <?php
}
function object_property_hierarhy3($id, $fime, $ismultiple, $moduleid, $userid, $isnotvisible, $propertynames) {//generiranje na hierarhija na objektni svojstva
    //id                id na listboxot na hierarhijata
    //fime              ime na javascript funkcijata koja se povikuva
    //ismultiple        dali moze da se selektiraat poveke vrednosti vo listboxot
    //moduleid          id na modulot
    //userid            id na korisnikot
    //isnotvisible       dali e nevidlivo  
    global $DB;
    $ontologyinstanceid = $DB->get_record('course_modules', array('id' => $moduleid)); //id na instancata na ontologijata
    $ontologyid = $DB->get_record('modules', array('name' => 'ontology')); //id na modulot ontologija
    $niza = array(); //ID na sekoja klasa
    $niza2 = array(); //ime na sekoja klasa
    $niza3 = array(); //broj na prazni mesta na sekoja klasa
    $niza4 = array(); //za oznacuvanje dali e vneseno od korisnikot ovaa nedela ili e vo ontologijata od porano
    $properties = $DB->get_records_sql('SELECT * from mdl_ontology_property_individual WHERE ( status=2 AND course_modulesid IN (SELECT id FROM mdl_course_modules WHERE instance=? AND module=?)) OR (status=1 AND course_modulesid IN (SELECT id FROM mdl_course_modules WHERE instance=? AND module=?) AND userid=? );', array($ontologyinstanceid->instance, $ontologyid->id, $ontologyinstanceid->instance, $ontologyid->id, $userid));
    $lista1 = array(); //vo ovaa lista ke bidat smesteni site klasi koi imaat id na superklasa pogolemo od id na klasa

    while (count($properties) != 0) {
        $lista1 = array();
        foreach ($properties as $key => $value) {
            if ($value->name == "Основно") {
                array_push($niza, $key);
                array_push($niza2, $value->name);
                array_push($niza3, 0);
                array_push($niza4, 0);
            } else {
                $goima = false;
                for ($i = 0; $i < count($niza); $i++)
                    if ($niza[$i] == $value->superproperty) {
                        $index = $i;
                        $goima = true;
                        break;
                    }
                if ($goima) {
                    $i = $index;
                    $niza = insertArrayIndex($niza, $key, $i + 1);
                    $niza3 = insertArrayIndex($niza3, $niza3[$i] + 3, $i + 1);
                    $string = "";
                    for ($j = 0; $j < $niza3[$i + 1]; $j++) {
                        $string = $string . "&nbsp";
                    }

                    $niza2 = insertArrayIndex($niza2, $string . $value->name, $i + 1);
                    if ($value->status == 1)
                        $niza4 = insertArrayIndex($niza4, 1, $i + 1);
                    else
                        $niza4 = insertArrayIndex($niza4, 0, $i + 1);
                }
                else {
                    //$n = count($lista1);
                    $lista1[$key] = $properties[$key];
                }
            }
        }
        $properties = $lista1;
    }
    ?>
    <select name=<?php echo "\"" . $id . "\"" ?> id=<?php echo "\"" . $id . "\"" ?> size="10" <?php if ($ismultiple == 1) echo "multiple=\"multiple\""; if ($isnotvisible == 1) echo 'style="visibility: hidden;"'; ?> onChange=<?php echo "\"" . $fime . "(this);\"" ?>>

    <?php
    $isselected = false;
    for ($i = 0; $i < count($niza); $i++)
        if ($niza4[$i] == 0) {
            echo "<option value=\"" . $niza[$i] . "\"";
            if (!$isselected) {
                foreach ($propertynames as $key => $value)
                    if ($value == $niza[$i]) {
                        echo "selected=\"selected\"";
                        $isselected = true;
                        break;
                    }
            }
            echo ">" . $niza2[$i] . "</option>";
        }
        else
            echo "<option style=\"color: red\" value=\"" . $niza[$i] . "\">" . $niza2[$i] . "</option>";
    ?>
    </select>
    <?php
    return $isselected;
}
//Objektna hierarhija 4 - hiearhija na site objektni svojstva, osven na svojstvoto $propertyid i site nejzini podsvojstva i kako default e selektirana nejzinoto supersvojstvo
function object_property_hierarhy4($id, $fime, $ismultiple, $moduleid, $userid, $isnotvisible, $propertyid) {//generiranje na hierarhija na objektni svojstva
    //id                id na listboxot na hierarhijata
    //fime              ime na javascript funkcijata koja se povikuva
    //ismultiple        dali moze da se selektiraat poveke vrednosti vo listboxot
    //moduleid          id na modulot
    //userid            id na korisnikot
    //isnotvisible       dali e nevidlivo  
    global $DB;
    $ontologyinstanceid = $DB->get_record('course_modules', array('id' => $moduleid)); //id na instancata na ontologijata
    $ontologyid = $DB->get_record('modules', array('name' => 'ontology')); //id na modulot ontologija
    $niza = array(); //ID na sekoja klasa
    $niza2 = array(); //ime na sekoja klasa
    $niza3 = array(); //broj na prazni mesta na sekoja klasa
    $niza4 = array(); //za oznacuvanje dali e vneseno od korisnikot ovaa nedela ili e vo ontologijata od porano
    $properties = $DB->get_records_sql('SELECT * from mdl_ontology_property_individual WHERE ( status=2 AND course_modulesid IN (SELECT id FROM mdl_course_modules WHERE instance=? AND module=?)) OR (status=1 AND course_modulesid IN (SELECT id FROM mdl_course_modules WHERE instance=? AND module=?) AND userid=? );', array($ontologyinstanceid->instance, $ontologyid->id, $ontologyinstanceid->instance, $ontologyid->id, $userid));
    $lista1 = array(); //vo ovaa lista ke bidat smesteni site klasi koi imaat id na superklasa pogolemo od id na klasa

    while (count($properties) != 0) {
        $lista1 = array();
        $broj = count($niza);
        foreach ($properties as $key => $value) {
            if ($key == $propertyid) {
                
            } else
            if ($value->name == "Основно") {
                array_push($niza, $key);
                array_push($niza2, $value->name);
                array_push($niza3, 0);
                array_push($niza4, 0);
            } else {
                $goima = false;
                for ($i = 0; $i < count($niza); $i++)
                    if ($niza[$i] == $value->superproperty) {
                        $index = $i;
                        $goima = true;
                        break;
                    }
                if ($goima) {
                    $i = $index;
                    $niza = insertArrayIndex($niza, $key, $i + 1);
                    $niza3 = insertArrayIndex($niza3, $niza3[$i] + 3, $i + 1);
                    $string = "";
                    for ($j = 0; $j < $niza3[$i + 1]; $j++) {
                        $string = $string . "&nbsp";
                    }

                    $niza2 = insertArrayIndex($niza2, $string . $value->name, $i + 1);
                    if ($value->status == 1)
                        $niza4 = insertArrayIndex($niza4, 1, $i + 1);
                    else
                        $niza4 = insertArrayIndex($niza4, 0, $i + 1);
                }
                else {
                    //$n = count($lista1);
                    $lista1[$key] = $properties[$key];
                }
            }
        }
        if (count($niza) == $broj)
            break;
        $properties = $lista1;
    }
    ?>
    <select name=<?php echo "\"" . $id . "\"" ?> id=<?php echo "\"" . $id . "\"" ?> size="10" <?php if ($ismultiple == 1) echo "multiple=\"multiple\""; if ($isnotvisible == 1) echo 'style="visibility: hidden;"'; ?> onChange=<?php echo "\"" . $fime . "(this);\"" ?>>

        <?php
        $property = $DB->get_record('ontology_property_individual', array('id' => $propertyid));
        $superproperty = $property->superproperty;
        for ($i = 0; $i < count($niza); $i++) {
            echo "<option value=\"" . $niza[$i] . "\"";
            if ($superproperty == $niza[$i])
                echo "selected=\"selected\"";
            if ($niza4[$i] != 0)
                echo "style=\"color: red\"";
            echo ">" . $niza2[$i] . "</option>";
        }
        ?>
    </select>
    <?php
}
function data_property_hierarhy($id, $fime, $ismultiple, $moduleid, $userid, $isnotvisible) { //generiranje na hiearhija na podatocni svojstva
    //id                id na listboxot na hierarhijata
    //fime              ime na javascript funkcijata koja se povikuva
    //ismultiple        dali moze da se selektiraat poveke vrednosti vo listboxot
    //moduleid          id na modulot
    //userid            id na korisnikot
    global $DB;
    $ontologyinstanceid = $DB->get_record('course_modules', array('id' => $moduleid)); //id na instancata na ontologijata
    $ontologyid = $DB->get_record('modules', array('name' => 'ontology')); //id na modulot ontologija
    $niza = array(); //ID na sekoja klasa
    $niza2 = array(); //ime na sekoja klasa
    $niza3 = array(); //broj na prazni mesta na sekoja klasa
    $niza4 = array(); //za oznacuvanje dali e vneseno od korisnikot ovaa nedela ili e vo ontologijata od porano
    $properties = $DB->get_records_sql('SELECT * from mdl_ontology_property_data WHERE ( status=2 AND course_modulesid IN (SELECT id FROM mdl_course_modules WHERE instance=? AND module=?)) OR (status=1 AND course_modulesid IN (SELECT id FROM mdl_course_modules WHERE instance=? AND module=?) AND userid=? );', array($ontologyinstanceid->instance, $ontologyid->id, $ontologyinstanceid->instance, $ontologyid->id, $userid));
    $lista1 = array(); //vo ovaa lista ke bidat smesteni site klasi koi imaat id na superklasa pogolemo od id na klasa

    while (count($properties) != 0) {
        $lista1 = array();
        $broj=count($properties);
        foreach ($properties as $key => $value) {
            if ($value->name == "Основно") {
                array_push($niza, $key);
                array_push($niza2, $value->name);
                array_push($niza3, 0);
                array_push($niza4, 0);
            } else {
                $goima = false;
                for ($i = 0; $i < count($niza); $i++)
                    if ($niza[$i] == $value->superproperty) {
                        $index = $i;
                        $goima = true;
                        break;
                    }
                if ($goima) {
                    $i = $index;
                    $niza = insertArrayIndex($niza, $key, $i + 1);
                    $niza3 = insertArrayIndex($niza3, $niza3[$i] + 3, $i + 1);
                    $string = "";
                    for ($j = 0; $j < $niza3[$i + 1]; $j++) {
                        $string = $string . "&nbsp";
                    }

                    $niza2 = insertArrayIndex($niza2, $string . $value->name, $i + 1);
                    if ($value->status == 1)
                        $niza4 = insertArrayIndex($niza4, 1, $i + 1);
                    else
                        $niza4 = insertArrayIndex($niza4, 0, $i + 1);
                }
                else {
                    //$n = count($lista1);
                    $lista1[$key] = $properties[$key];
                }
            }
        }
        $properties = $lista1;
        if (count($properties)==$broj)
            break;
    }
    ?>
    <select name=<?php echo "\"" . $id . "\"" ?> id=<?php echo "\"" . $id . "\"" ?> size="10" <?php if ($ismultiple == 1) echo "multiple=\"multiple\""; if ($isnotvisible == 1) echo 'style="visibility: hidden;"'; ?> onChange=<?php echo "\"" . $fime . "(this);\"" ?>>

    <?php
    for ($i = 0; $i < count($niza); $i++)
        if ($niza4[$i] == 0)
            echo "<option value=\"" . $niza[$i] . "\">" . $niza2[$i] . "</option>";
        else
            echo "<option style=\"color: red\" value=\"" . $niza[$i] . "\">" . $niza2[$i] . "</option>";
    ?>
    </select>
    <?php
}
function data_property_hierarhy2($id, $fime, $ismultiple, $moduleid, $userid, $isnotvisible) { //generiranje na hiearhija na podatocni svojstva
    //id                id na listboxot na hierarhijata
    //fime              ime na javascript funkcijata koja se povikuva
    //ismultiple        dali moze da se selektiraat poveke vrednosti vo listboxot
    //moduleid          id na modulot
    //userid            id na korisnikot
    global $DB;
    $ontologyinstanceid = $DB->get_record('course_modules', array('id' => $moduleid)); //id na instancata na ontologijata
    $ontologyid = $DB->get_record('modules', array('name' => 'ontology')); //id na modulot ontologija
    $niza = array(); //ID na sekoja klasa
    $niza2 = array(); //ime na sekoja klasa
    $niza3 = array(); //broj na prazni mesta na sekoja klasa
    $niza4 = array(); //za oznacuvanje dali e vneseno od korisnikot ovaa nedela ili e vo ontologijata od porano
    $properties = $DB->get_records_sql('SELECT * from mdl_ontology_property_data WHERE ( status=2 AND course_modulesid IN (SELECT id FROM mdl_course_modules WHERE instance=? AND module=?)) OR (course_modulesid=? AND userid=?);', array($ontologyinstanceid->instance, $ontologyid->id, $moduleid, $userid));
    $lista1 = array(); //vo ovaa lista ke bidat smesteni site klasi koi imaat id na superklasa pogolemo od id na klasa

    while (count($properties) != 0) {
        $lista1 = array();
        foreach ($properties as $key => $value) {
            if ($value->name == "Основно") {
                array_push($niza, $key);
                array_push($niza2, $value->name);
                array_push($niza3, 0);
                array_push($niza4, 0);
            } else {
                $goima = false;
                for ($i = 0; $i < count($niza); $i++)
                    if ($niza[$i] == $value->superproperty) {
                        $index = $i;
                        $goima = true;
                        break;
                    }
                if ($goima) {
                    $i = $index;
                    $niza = insertArrayIndex($niza, $key, $i + 1);
                    $niza3 = insertArrayIndex($niza3, $niza3[$i] + 3, $i + 1);
                    $string = "";
                    for ($j = 0; $j < $niza3[$i + 1]; $j++) {
                        $string = $string . "&nbsp";
                    }
                    $expressions = $DB->get_records('ontology_property_expression', array('ontology_propertyid' => $key, 'status' => 1, 'userid' => $userid, 'course_modulesid' => $moduleid, 'type' => '3'));
                    $equivalent = $DB->get_records('ontology_property_equivalent', array('ontology_propertyid' => $key, 'status' => 1, 'userid' => $userid, 'course_modulesid' => $moduleid, 'type' => '2'));
                    $disjoint = $DB->get_records('ontology_property_disjoint', array('ontology_propertyid' => $key, 'status' => 1, 'userid' => $userid, 'course_modulesid' => $moduleid, 'type' => '2'));
                    $number = count($expressions) + count($equivalent) + count($disjoint);
                    if ($number == 0)
                        $niza2 = insertArrayIndex($niza2, $string . $value->name, $i + 1);
                    else
                        $niza2 = insertArrayIndex($niza2, $string . $value->name . '(' . $number . ')', $i + 1);
                    if ($value->status == 1)
                        $niza4 = insertArrayIndex($niza4, 1, $i + 1);
                    if ($value->status == 2)
                        $niza4 = insertArrayIndex($niza4, 2, $i + 1);
                    if ($value->status == 3)
                        $niza4 = insertArrayIndex($niza4, 3, $i + 1);
                    if ($value->status == 4)
                        $niza4 = insertArrayIndex($niza4, 4, $i + 1);
                    if ($value->status == 5)
                        $niza4 = insertArrayIndex($niza4, 5, $i + 1);
                }
                else {
                    //$n = count($lista1);
                    $lista1[$key] = $properties[$key];
                }
            }
        }
        $properties = $lista1;
    }
    ?>
    <select name=<?php echo "\"" . $id . "\"" ?> id=<?php echo "\"" . $id . "\"" ?> size="10" <?php if ($ismultiple == 1) echo "multiple=\"multiple\""; if ($isnotvisible == 1) echo 'style="visibility: hidden;"'; ?> onChange=<?php echo "\"" . $fime . "(this);\"" ?>>

    <?php
    for ($i = 0; $i < count($niza); $i++) {
        if ($niza4[$i] == 0)
            echo "<option value=\"" . $niza[$i] . "\">" . $niza2[$i] . "</option>";
        if ($niza4[$i] == 1)
            echo "<option style=\"color: orange\" value=\"" . $niza[$i] . "\">" . $niza2[$i] . "</option>";
        if ($niza4[$i] == 2)
            echo "<option value=\"" . $niza[$i] . "\">" . $niza2[$i] . "</option>";
        if ($niza4[$i] == 3)
            echo "<option style=\"color: green\" value=\"" . $niza[$i] . "\">" . $niza2[$i] . "</option>";
        if ($niza4[$i] == 4)
            echo "<option style=\"color: red\" value=\"" . $niza[$i] . "\">" . $niza2[$i] . "</option>";
        if ($niza4[$i] == 5)
            echo "<option style=\"color: green\" value=\"" . $niza[$i] . "\">" . $niza2[$i] . "</option>";
    }
    ?>
    </select>
    <?php
}
function data_property_hierarhy3($id, $fime, $ismultiple, $moduleid, $userid, $isnotvisible, $propertynames) { //generiranje na hiearhija na podatocni svojstva
    //id                id na listboxot na hierarhijata
    //fime              ime na javascript funkcijata koja se povikuva
    //ismultiple        dali moze da se selektiraat poveke vrednosti vo listboxot
    //moduleid          id na modulot
    //userid            id na korisnikot
    global $DB;
    $ontologyinstanceid = $DB->get_record('course_modules', array('id' => $moduleid)); //id na instancata na ontologijata
    $ontologyid = $DB->get_record('modules', array('name' => 'ontology')); //id na modulot ontologija
    $niza = array(); //ID na sekoja klasa
    $niza2 = array(); //ime na sekoja klasa
    $niza3 = array(); //broj na prazni mesta na sekoja klasa
    $niza4 = array(); //za oznacuvanje dali e vneseno od korisnikot ovaa nedela ili e vo ontologijata od porano
    $properties = $DB->get_records_sql('SELECT * from mdl_ontology_property_data WHERE ( status=2 AND course_modulesid IN (SELECT id FROM mdl_course_modules WHERE instance=? AND module=?)) OR (status=1 AND course_modulesid IN (SELECT id FROM mdl_course_modules WHERE instance=? AND module=?) AND userid=? );', array($ontologyinstanceid->instance, $ontologyid->id, $ontologyinstanceid->instance, $ontologyid->id, $userid));
    $lista1 = array(); //vo ovaa lista ke bidat smesteni site klasi koi imaat id na superklasa pogolemo od id na klasa

    while (count($properties) != 0) {
        $lista1 = array();
        foreach ($properties as $key => $value) {
            if ($value->name == "Основно") {
                array_push($niza, $key);
                array_push($niza2, $value->name);
                array_push($niza3, 0);
                array_push($niza4, 0);
            } else {
                $goima = false;
                for ($i = 0; $i < count($niza); $i++)
                    if ($niza[$i] == $value->superproperty) {
                        $index = $i;
                        $goima = true;
                        break;
                    }
                if ($goima) {
                    $i = $index;
                    $niza = insertArrayIndex($niza, $key, $i + 1);
                    $niza3 = insertArrayIndex($niza3, $niza3[$i] + 3, $i + 1);
                    $string = "";
                    for ($j = 0; $j < $niza3[$i + 1]; $j++) {
                        $string = $string . "&nbsp";
                    }

                    $niza2 = insertArrayIndex($niza2, $string . $value->name, $i + 1);
                    if ($value->status == 1)
                        $niza4 = insertArrayIndex($niza4, 1, $i + 1);
                    else
                        $niza4 = insertArrayIndex($niza4, 0, $i + 1);
                }
                else {
                    //$n = count($lista1);
                    $lista1[$key] = $properties[$key];
                }
            }
        }
        $properties = $lista1;
    }
    ?>
    <select name=<?php echo "\"" . $id . "\"" ?> id=<?php echo "\"" . $id . "\"" ?> size="10" <?php if ($ismultiple == 1) echo "multiple=\"multiple\""; if ($isnotvisible == 1) echo 'style="visibility: hidden;"'; ?> onChange=<?php echo "\"" . $fime . "(this);\"" ?>>

    <?php
    $isselected = false;
    for ($i = 0; $i < count($niza); $i++)
        if ($niza4[$i] == 0) {
            echo "<option value=\"" . $niza[$i] . "\"";
            if (!$isselected) {
                foreach ($propertynames as $key => $value)
                    if ($value == $niza[$i]) {
                        echo "selected=\"selected\"";
                        $isselected = true;
                        break;
                    }
            }
            echo ">" . $niza2[$i] . "</option>";
        }
        else
            echo "<option style=\"color: red\" value=\"" . $niza[$i] . "\">" . $niza2[$i] . "</option>";
    ?>
    </select>
    <?php
    return $isselected;
}
//Podatocna hierarhija 4 - hiearhija na site podatocni svojstva, osven na svojstvoto $propertyid i site nejzini podsvojstva i kako default e selektirano nejzinoto supersvojstvo
function data_property_hierarhy4($id, $fime, $ismultiple, $moduleid, $userid, $isnotvisible, $propertyid) { //generiranje na hiearhija na podatocni svojstva
    //id                id na listboxot na hierarhijata
    //fime              ime na javascript funkcijata koja se povikuva
    //ismultiple        dali moze da se selektiraat poveke vrednosti vo listboxot
    //moduleid          id na modulot
    //userid            id na korisnikot
    global $DB;
    $ontologyinstanceid = $DB->get_record('course_modules', array('id' => $moduleid)); //id na instancata na ontologijata
    $ontologyid = $DB->get_record('modules', array('name' => 'ontology')); //id na modulot ontologija
    $niza = array(); //ID na sekoja klasa
    $niza2 = array(); //ime na sekoja klasa
    $niza3 = array(); //broj na prazni mesta na sekoja klasa
    $niza4 = array(); //za oznacuvanje dali e vneseno od korisnikot ovaa nedela ili e vo ontologijata od porano
    $properties = $DB->get_records_sql('SELECT * from mdl_ontology_property_data WHERE ( status=2 AND course_modulesid IN (SELECT id FROM mdl_course_modules WHERE instance=? AND module=?)) OR (status=1 AND course_modulesid IN (SELECT id FROM mdl_course_modules WHERE instance=? AND module=?) AND userid=? );', array($ontologyinstanceid->instance, $ontologyid->id, $ontologyinstanceid->instance, $ontologyid->id, $userid));
    $lista1 = array(); //vo ovaa lista ke bidat smesteni site klasi koi imaat id na superklasa pogolemo od id na klasa

    while (count($properties) != 0) {
        $lista1 = array();
        $broj = count($niza);
        foreach ($properties as $key => $value) {
            if ($key == $propertyid) {
                
            } else
            if ($value->name == "Основно") {
                array_push($niza, $key);
                array_push($niza2, $value->name);
                array_push($niza3, 0);
                array_push($niza4, 0);
            } else {
                $goima = false;
                for ($i = 0; $i < count($niza); $i++)
                    if ($niza[$i] == $value->superproperty) {
                        $index = $i;
                        $goima = true;
                        break;
                    }
                if ($goima) {
                    $i = $index;
                    $niza = insertArrayIndex($niza, $key, $i + 1);
                    $niza3 = insertArrayIndex($niza3, $niza3[$i] + 3, $i + 1);
                    $string = "";
                    for ($j = 0; $j < $niza3[$i + 1]; $j++) {
                        $string = $string . "&nbsp";
                    }

                    $niza2 = insertArrayIndex($niza2, $string . $value->name, $i + 1);
                    if ($value->status == 1)
                        $niza4 = insertArrayIndex($niza4, 1, $i + 1);
                    else
                        $niza4 = insertArrayIndex($niza4, 0, $i + 1);
                }
                else {
                    //$n = count($lista1);
                    $lista1[$key] = $properties[$key];
                }
            }
        }
        if (count($niza) == $broj)
            break;
        $properties = $lista1;
    }
    ?>
    <select name=<?php echo "\"" . $id . "\"" ?> id=<?php echo "\"" . $id . "\"" ?> size="10" <?php if ($ismultiple == 1) echo "multiple=\"multiple\""; if ($isnotvisible == 1) echo 'style="visibility: hidden;"'; ?> onChange=<?php echo "\"" . $fime . "(this);\"" ?>>

    <?php
    $property = $DB->get_record('ontology_property_data', array('id' => $propertyid));
    $superproperty = $property->superproperty;
    for ($i = 0; $i < count($niza); $i++) {
        echo "<option value=\"" . $niza[$i] . "\"";
        if ($superproperty == $niza[$i])
            echo "selected=\"selected\"";
        if ($niza4[$i] != 0)
            echo "style=\"color: red\"";
        echo ">" . $niza2[$i] . "</option>";
    }
    ?>
    </select>
    <?php
}
function individual_hierarhy($id, $fime, $ismultiple, $moduleid, $userid, $isnotvisible) { //generiranje na hiearhija na individuite
    //id                id na listboxot na hierarhijata
    //fime              ime na javascript funkcijata koja se povikuva
    //ismultiple        dali moze da se selektiraat poveke vrednosti vo listboxot
    //moduleid          id na modulot
    //userid            id na korisnikot
    global $DB;
    $ontologyinstanceid = $DB->get_record('course_modules', array('id' => $moduleid)); //id na instancata na ontologijata
    $ontologyid = $DB->get_record('modules', array('name' => 'ontology')); //id na modulot ontologija
    $niza = array(); //ID na sekoja individua
    $niza2 = array(); //ime na sekoja individua
    $niza4 = array(); //za oznacuvanje dali e vneseno od korisnikot ovaa nedela ili e vo ontologijata od porano
    $individuals = $DB->get_records_sql('SELECT * from mdl_ontology_individual WHERE ( status=2 AND course_modulesid IN (SELECT id FROM mdl_course_modules WHERE instance=? AND module=?)) OR (status=1 AND course_modulesid IN (SELECT id FROM mdl_course_modules WHERE instance=? AND module=?) AND userid=? );', array($ontologyinstanceid->instance, $ontologyid->id, $ontologyinstanceid->instance, $ontologyid->id, $userid));
    $i = 0;
    foreach ($individuals as $key => $value) {
        $niza = insertArrayIndex($niza, $key, $i + 1);
        $niza2 = insertArrayIndex($niza2, $value->name, $i + 1);
        if ($value->status == 1)
            $niza4 = insertArrayIndex($niza4, 1, $i + 1);
        else
            $niza4 = insertArrayIndex($niza4, 0, $i + 1);
        $i++;
    }
    ?>
    <select name=<?php echo "\"" . $id . "\"" ?> id=<?php echo "\"" . $id . "\"" ?> size="10" <?php if ($ismultiple == 1) echo "multiple=\"multiple\""; if ($isnotvisible == 1) echo 'style="visibility: hidden;"'; ?> onChange=<?php echo "\"" . $fime . "(this);\"" ?>>

    <?php
    for ($i = 0; $i < count($niza); $i++)
        if ($niza4[$i] == 0)
            echo "<option value=\"" . $niza[$i] . "\">" . $niza2[$i] . "</option>";
        else
            echo "<option style=\"color: red\" value=\"" . $niza[$i] . "\">" . $niza2[$i] . "</option>";
    ?>
    </select>
    <?php
}
function individual_hierarhy2($id, $fime, $ismultiple, $moduleid, $userid, $isnotvisible) { //generiranje na hiearhija na individuite
    //id                id na listboxot na hierarhijata
    //fime              ime na javascript funkcijata koja se povikuva
    //ismultiple        dali moze da se selektiraat poveke vrednosti vo listboxot
    //moduleid          id na modulot
    //userid            id na korisnikot
    global $DB;
    $ontologyinstanceid = $DB->get_record('course_modules', array('id' => $moduleid)); //id na instancata na ontologijata
    $ontologyid = $DB->get_record('modules', array('name' => 'ontology')); //id na modulot ontologija
    $niza = array(); //ID na sekoja individua
    $niza2 = array(); //ime na sekoja individua
    $niza4 = array(); //za oznacuvanje dali e vneseno od korisnikot ovaa nedela ili e vo ontologijata od porano
    $individuals = $DB->get_records_sql('SELECT * from mdl_ontology_individual WHERE ( status=2 AND course_modulesid IN (SELECT id FROM mdl_course_modules WHERE instance=? AND module=?)) OR (course_modulesid=? AND userid=? );', array($ontologyinstanceid->instance, $ontologyid->id, $moduleid, $userid));
    $i = 0;
    foreach ($individuals as $key => $value) {
        $niza = insertArrayIndex($niza, $key, $i + 1);
        $expressions = $DB->get_records('ontology_individual_expression', array('ontology_individualid' => $key, 'status' => 1, 'userid' => $userid, 'course_modulesid' => $moduleid));
        $oproperties = $DB->get_records('ontology_individual_property_individual', array('ontology_individualid' => $key, 'status' => 1, 'userid' => $userid, 'course_modulesid' => $moduleid));
        $dproperties = $DB->get_records('ontology_individual_property_data', array('ontology_individualid' => $key, 'status' => 1, 'userid' => $userid, 'course_modulesid' => $moduleid));
        $number = count($expressions) + count($oproperties) + count($dproperties);
        if ($number == 0)
            $niza2 = insertArrayIndex($niza2, $value->name, $i + 1);
        else
            $niza2 = insertArrayIndex($niza2, $value->name . '(' . $number . ')', $i + 1);
        if ($value->status == 1)
            $niza4 = insertArrayIndex($niza4, 1, $i + 1);
        if ($value->status == 2)
            $niza4 = insertArrayIndex($niza4, 2, $i + 1);
        if ($value->status == 3)
            $niza4 = insertArrayIndex($niza4, 3, $i + 1);
        if ($value->status == 4)
            $niza4 = insertArrayIndex($niza4, 4, $i + 1);
        if ($value->status == 5)
            $niza4 = insertArrayIndex($niza4, 5, $i + 1);

        $i++;
    }
    ?>
    <select name=<?php echo "\"" . $id . "\"" ?> id=<?php echo "\"" . $id . "\"" ?> size="10" <?php if ($ismultiple == 1) echo "multiple=\"multiple\""; if ($isnotvisible == 1) echo 'style="visibility: hidden;"'; ?> onChange=<?php echo "\"" . $fime . "(this);\"" ?>>

    <?php
    for ($i = 0; $i < count($niza); $i++) {
        if ($niza4[$i] == 0)
            echo "<option value=\"" . $niza[$i] . "\">" . $niza2[$i] . "</option>";
        if ($niza4[$i] == 1)
            echo "<option style=\"color: orange\" value=\"" . $niza[$i] . "\">" . $niza2[$i] . "</option>";
        if ($niza4[$i] == 2)
            echo "<option value=\"" . $niza[$i] . "\">" . $niza2[$i] . "</option>";
        if ($niza4[$i] == 3)
            echo "<option style=\"color: green\" value=\"" . $niza[$i] . "\">" . $niza2[$i] . "</option>";
        if ($niza4[$i] == 4)
            echo "<option style=\"color: red\" value=\"" . $niza[$i] . "\">" . $niza2[$i] . "</option>";
        if ($niza4[$i] == 5)
            echo "<option style=\"color: green\" value=\"" . $niza[$i] . "\">" . $niza2[$i] . "</option>";
    }
    ?>
    </select>
    <?php
}
function get_name_of_class($classid) {
    global $DB;
    $class = $DB->get_record('ontology_class', array('id' => $classid));
    return '<span style="color: #FF9D00">' . $class->name . '</span>';
}
function get_user_profile($userid) {
    global $DB;
    global $CFG;
    $user = $DB->get_record('user', array('id' => $userid));
    $s = '<a href="' . $CFG->wwwroot . '/user/profile.php?id=' . $userid . '">' . $user->firstname . ' ' . $user->lastname . '</a>';
    return $s;
}
function get_opis_of_class($classid) {
    global $DB;
    $class = $DB->get_record('ontology_class', array('id' => $classid));
    if ($class->description == '')
        return get_string('No_description', 'ontology');
    else
        return get_string('Description', 'ontology') . ': ' . $class->description;
}
function get_opis_of_oproperty($propertyid) {
    global $DB;
    $property = $DB->get_record('ontology_property_individual', array('id' => $propertyid));
    if ($property->description == '')
        return get_string('No_description', 'ontology');
    else
        return get_string('Description', 'ontology') . ': ' . $property->description;
}
function get_opis_of_dproperty($propertyid) {
    global $DB;
    $property = $DB->get_record('ontology_property_data', array('id' => $propertyid));
    if ($property->description == '')
        return get_string('No_description', 'ontology');
    else
        return get_string('Description', 'ontology') . ': ' . $property->description;
}
function get_opis_of_individual($individualid) {
    global $DB;
    $individual = $DB->get_record('ontology_individual', array('id' => $individualid));
    if ($individual->description == '')
        return get_string('No_description', 'ontology');
    else
        return get_string('Description', 'ontology') . ': ' . $individual->description;
}
function get_expression_in_color($inputtxt) {
    $a[] = "q";
    $style[] = "#000000";
    $a[] = "(";
    $style[] = "#000000";
    $a[] = ")";
    $style[] = "#000000";
    $a[] = "{";
    $style[] = "#000000";
    $a[] = "}";
    $style[] = "#000000";
    $a[] = "[";
    $style[] = "#000000";
    $a[] = "]";
    $style[] = "#000000";
    $a[] = "and";
    $style[] = "#9F0EA9";
    $a[] = "or";
    $style[] = "#9F0EA9";
    $a[] = "not";
    $style[] = "#9F0EA9";
    $a[] = "min";
    $style[] = "#122B6B";
    $a[] = "max";
    $style[] = "#122B6B";
    $a[] = "exactly";
    $style[] = "#122B6B";
    $a[] = "value";
    $style[] = "#122B6B";
    $a[] = "some";
    $style[] = "#122B6B";
    $a[] = "only";
    $style[] = "#122B6B";
    $a[] = "<";
    $style[] = "#122B6B";
    $a[] = ">";
    $style[] = "#122B6B";
    $a[] = "<=";
    $style[] = "#122B6B";
    $a[] = ">=";
    $style[] = "#122B6B";
    $a[] = "lenght";
    $style[] = "#122B6B";
    $a[] = "maxlenght";
    $style[] = "#122B6B";
    $a[] = "minlenght";
    $style[] = "#122B6B";
    $a[] = "totalDigits";
    $style[] = "#122B6B";
    $a[] = "klasa";
    $style[] = "#FF9D00";
    $a[] = "tip";
    $style[] = "#0E2B79";
    $a[] = "broj";
    $style[] = "#D51010";
    $a[] = "individua";
    $style[] = "#FF0404";
    $a[] = "psvojstvo";
    $style[] = "#19B411";
    $a[] = "osvojstvo";
    $style[] = "#006FFF";
    $a[] = "$";
    $input = explode(" ", $inputtxt);
    global $DB;
    if ($input[count($input) - 1] == '$')
        $size = count($input) - 1;
    else
        $size = count($input);
    for ($i = 0; $i < $size; $i++) {
        $posledenvnes = $input[$i];
        if ($posledenvnes[0] == '^') {
            if ($posledenvnes[1] == 'k') {
                $index = 24;
                $class = $DB->get_record('ontology_class', array('id' => substr($posledenvnes, 2 - strlen($posledenvnes))));
                $txt = $class->name;
            } else
            if ($posledenvnes[1] == 't') {
                $index = 25;
                $txt = substr($posledenvnes, 2 - strlen($posledenvnes));
            } else
            if ($posledenvnes[1] == 'b') {
                $index = 26;
                $txt = substr($posledenvnes, 2 - strlen($posledenvnes));
            } else
            if ($posledenvnes[1] == 'i') {
                $index = 27;
                $individual = $DB->get_record('ontology_individual', array('id' => substr($posledenvnes, 2 - strlen($posledenvnes))));
                $txt = $individual->name;
            } else
            if ($posledenvnes[1] == 'p') {
                $index = 28;
                $psvojstvo = $DB->get_record('ontology_property_data', array('id' => substr($posledenvnes, 2 - strlen($posledenvnes))));
                $txt = $psvojstvo->name;
            } else
            if ($posledenvnes[1] == 'o') {
                $index = 29;
                $osvojstvo = $DB->get_record('ontology_property_individual', array('id' => substr($posledenvnes, 2 - strlen($posledenvnes))));
                $txt = $osvojstvo->name;
            }
        } else {
            for ($j = 1; $j < 24; $j++) {
                if ($a[$j] == $posledenvnes) {
                    $index = $j;
                    $txt = $a[$index];
                }
            }
        }
        echo"<span style=\"color: " . $style[$index] . "\">" . $txt . " </span>";
    }
}
function get_name_of_individual($individualid) {
    global $DB;
    $individual = $DB->get_record('ontology_individual', array('id' => $individualid));
    return '<span style="color: #FF0404">' . $individual->name . '</span>';
}
function get_name_of_oproperty($propertyid) {
    global $DB;
    $property = $DB->get_record('ontology_property_individual', array('id' => $propertyid));
    return '<span style="color: #006FFF">' . $property->name . '</span>';
}
function get_name_of_dproperty($propertyid) {
    global $DB;
    $property = $DB->get_record('ontology_property_data', array('id' => $propertyid));
    return '<span style="color: #19B411">' . $property->name . '</span>';
}
function insert_class($class) {
    global $DB;
    $class->status = 1;
    $class->points = 0;
    return $DB->insert_record('ontology_class', $class);
}
function insert_restriction_II($restriction) {
    global $DB;
    $restriction->status = 1;
    $restriction->points = 0;
    return $DB->insert_record('ontology_class_restrictions', $restriction);
}
/**
 * Return a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return null
 * @todo Finish documenting this function
 */
function ontology_user_outline($course, $user, $mod, $ontology) {
    $return = new stdClass;
    $return->time = 0;
    $return->info = '';
    return $return;
}
/**
 * Print a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @return boolean
 * @todo Finish documenting this function
 */
function ontology_user_complete($course, $user, $mod, $ontology) {
    return true;
}
/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in newmodule activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @return boolean
 * @todo Finish documenting this function
 */
function ontology_print_recent_activity($course, $viewfullnames, $timestart) {
    return false;  //  True if anything was printed, otherwise false
}
/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @return boolean
 * @todo Finish documenting this function
 * */
function ontology_cron() {
    return true;
}
/**
 * Must return an array of users who are participants for a given instance
 * of newmodule. Must include every user involved in the instance,
 * independient of his role (student, teacher, admin...). The returned
 * objects must contain at least id property.
 * See other modules as example.
 *
 * @param int $newmoduleid ID of an instance of this module
 * @return boolean|array false if no participants, array of objects otherwise
 */
function ontology_get_participants($ontologyid) {
    return false;
}
/**
 * This function returns if a scale is being used by one newmodule
 * if it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $newmoduleid ID of an instance of this module
 * @return mixed
 * @todo Finish documenting this function
 */
function ontology_scale_used($ontologyid, $scaleid) {
    global $DB;

    $return = false;

    //$rec = $DB->get_record("newmodule", array("id" => "$newmoduleid", "scale" => "-$scaleid"));
    //
    //if (!empty($rec) && !empty($scaleid)) {
    //    $return = true;
    //}

    return $return;
}
/**
 * Checks if scale is being used by any instance of newmodule.
 * This function was added in 1.9
 *
 * This is used to find out if scale used anywhere
 * @param $scaleid int
 * @return boolean True if the scale is used by any newmodule
 */
function ontology_scale_used_anywhere($scaleid) {
    global $DB;

    if ($scaleid and $DB->record_exists('ontology', 'grade', -$scaleid)) {
        return true;
    } else {
        return false;
    }
}
/**
 * Execute post-uninstall custom actions for the module
 * This function was added in 1.9
 *
 * @return boolean true if success, false on error
 */
function ontology_uninstall() {
    return true;
}
function user_entry($user_id, $module_id) {
    global $DB;
    $rows = $DB->get_records('ontology_class', array('userid' => $user_id, 'course_modulesid' => $module_id));
    if (count($rows) > 0)
        return true;
    $rows = $DB->get_records('ontology_individual', array('userid' => $user_id, 'course_modulesid' => $module_id));
    if (count($rows) > 0)
        return true;
    $rows = $DB->get_records('ontology_class_expression', array('userid' => $user_id, 'course_modulesid' => $module_id));
    if (count($rows) > 0)
        return true;
    $rows = $DB->get_records('ontology_property_individual', array('userid' => $user_id, 'course_modulesid' => $module_id));
    if (count($rows) > 0)
        return true;
    $rows = $DB->get_records('ontology_property_disjoint', array('userid' => $user_id, 'course_modulesid' => $module_id));
    if (count($rows) > 0)
        return true;
    $rows = $DB->get_records('ontology_property_equivalent', array('userid' => $user_id, 'course_modulesid' => $module_id));
    if (count($rows) > 0)
        return true;
    $rows = $DB->get_records('ontology_property_data', array('userid' => $user_id, 'course_modulesid' => $module_id));
    if (count($rows) > 0)
        return true;
    $rows = $DB->get_records('ontology_property_expression', array('userid' => $user_id, 'course_modulesid' => $module_id));
    if (count($rows) > 0)
        return true;
    $rows = $DB->get_records('ontology_individual_expression', array('userid' => $user_id, 'course_modulesid' => $module_id));
    if (count($rows) > 0)
        return true;
    $rows = $DB->get_records('ontology_individual_property_individual', array('userid' => $user_id, 'course_modulesid' => $module_id));
    if (count($rows) > 0)
        return true;
    $rows = $DB->get_records('ontology_individual_property_data', array('userid' => $user_id, 'course_modulesid' => $module_id));
    if (count($rows) > 0)
        return true;
    return false;
}
?>
<?php
function read_class($user_id, $module_id) {
    global $DB;
    $rows = $DB->get_records('ontology_class', array('userid' => $user_id, 'course_modulesid' => $module_id));
    ?>
    <br />

    <?php
    foreach ($rows as $row) {
        if ($row->status == 1)
            $status = get_string('Waiting_for_approval', 'ontology');
        if ($row->status == 2)
            $status = get_string('Accepted_in_ontology', 'ontology');
        if ($row->status == 3)
            $status = get_string('Accepted_not_in_ontology', 'ontology');
        if ($row->status == 4)
            $status = get_string('Rejected', 'ontology');
        if ($row->status == 5)
            $status = get_string('Accepted_not_in_ontology', 'ontology');
        ?>

        <tr bgcolor="#FFFFCC">
            <td style="border:1px solid #111"><?php echo get_name_of_class($row->id) ?> </td>
            <td style="border:1px solid #111"><?php echo get_string('Class', 'ontology'); ?> </td>
            <td style="border:1px solid #111"><?php echo $status ?> </td>
            <td style="border:1px solid #111"><?php echo $row->points ?> </td>
        </tr>
    <?php } ?>
    <?php
}
function read_individual($user_id, $module_id) {
    global $DB;
    $rows = $DB->get_records('ontology_individual', array('userid' => $user_id, 'course_modulesid' => $module_id));
    foreach ($rows as $row) {
        if ($row->status == 1)
            $status = get_string('Waiting_for_approval', 'ontology');
        if ($row->status == 2)
            $status = get_string('Accepted_in_ontology', 'ontology');
        if ($row->status == 3)
            $status = get_string('Accepted_not_in_ontology', 'ontology');
        if ($row->status == 4)
            $status = get_string('Rejected', 'ontology');
        if ($row->status == 5)
            $status = get_string('Accepted_not_in_ontology', 'ontology');
        ?>

        <tr bgcolor="#FCE9EC">
            <td style="border:1px solid #111"><?php echo get_name_of_individual($row->id) ?> </td>
            <td style="border:1px solid #111"><?php echo get_string('Individual', 'ontology'); ?> </td>
            <td style="border:1px solid #111"><?php echo $status ?> </td>
            <td style="border:1px solid #111"><?php echo $row->points ?> </td>
        </tr>
        <?php
    }
}
function read_class_expression($user_id, $module_id) {
    global $DB;
    $rows = $DB->get_records('ontology_class_expression', array('userid' => $user_id, 'course_modulesid' => $module_id));
    foreach ($rows as $row) {
        if ($row->status == 1)
            $status = get_string('Waiting_for_approval', 'ontology');
        if ($row->status == 2)
            $status = get_string('Accepted_in_ontology', 'ontology');
        if ($row->status == 3)
            $status = get_string('Accepted_not_in_ontology', 'ontology');
        if ($row->status == 4)
            $status = get_string('Rejected', 'ontology');
        if ($row->status == 5)
            $status = get_string('Accepted_not_in_ontology', 'ontology');
        ?>

        <tr bgcolor="#FEF8EF">
        <?php
        $klasa = $DB->get_record('ontology_class', array('id' => $row->ontology_classid));
        if ($row->type == 1)
            $tip = get_string('Expression_for_the_superclass', 'ontology');
        if ($row->type == 2)
            $tip = get_string('Expression_for_the_equivalent_class', 'ontology');
        if ($row->type == 3)
            $tip = get_string('Expression_for_the_disjoint_class', 'ontology');
        ?>
            <td style="border:1px solid #111"><?php echo get_expression_in_color($row->expression) . " - " . $klasa->name ?> </td>
            <td style="border:1px solid #111"><?php echo $tip ?> </td>
            <td style="border:1px solid #111"><?php echo $status ?> </td>
            <td style="border:1px solid #111"><?php echo $row->points ?> </td>
        </tr>
        <?php
    }
}
function read_property_individual($user_id, $module_id) {
    global $DB;
    $rows = $DB->get_records('ontology_property_individual', array('userid' => $user_id, 'course_modulesid' => $module_id));
    $tip = get_string('Oproperty', 'ontology');
    foreach ($rows as $row) {
        if ($row->status == 1)
            $status = get_string('Waiting_for_approval', 'ontology');
        if ($row->status == 2)
            $status = get_string('Accepted_in_ontology', 'ontology');
        if ($row->status == 3)
            $status = get_string('Accepted_not_in_ontology', 'ontology');
        if ($row->status == 4)
            $status = get_string('Rejected', 'ontology');
        if ($row->status == 5)
            $status = get_string('Accepted_not_in_ontology', 'ontology');
        ?>

        <tr bgcolor="#DDEEFF">
            <td style="border:1px solid #111"><?php echo get_name_of_oproperty($row->id) ?> </td>
            <td style="border:1px solid #111"><?php echo $tip ?> </td>
            <td style="border:1px solid #111"><?php echo $status ?> </td>
            <td style="border:1px solid #111"><?php echo $row->points ?> </td>
        </tr>
        <?php
    }
}
function read_property_disjoint($user_id, $module_id) {
    global $DB;
    $rows = $DB->get_records('ontology_property_disjoint', array('userid' => $user_id, 'course_modulesid' => $module_id));

    foreach ($rows as $row) {
        if ($row->status == 1)
            $status = get_string('Waiting_for_approval', 'ontology');
        if ($row->status == 2)
            $status = get_string('Accepted_in_ontology', 'ontology');
        if ($row->status == 3)
            $status = get_string('Accepted_not_in_ontology', 'ontology');
        if ($row->status == 4)
            $status = get_string('Rejected', 'ontology');
        if ($row->status == 5)
            $status = get_string('Accepted_not_in_ontology', 'ontology');
        if ($row->type == 1) {
            $sv1 = $DB->get_record('ontology_property_individual', array('id' => $row->ontology_propertyid));
            $sv2 = $DB->get_record('ontology_property_individual', array('id' => $row->ontology_propertyid2));
            $tip = get_string('Disjoint_object_property', 'ontology');
        } else {
            if ($row->type == 2)
                $sv1 = $DB->get_record('ontology_property_data', array('id' => $row->ontology_propertyid));
            $sv2 = $DB->get_record('ontology_property_data', array('id' => $row->ontology_propertyid2));
            $tip = get_string('Disjoint_data_property', 'ontology');
        }
        ?>
        <tr bgcolor="#C5D7FE">
            <td style="border:1px solid #111"><?php if ($row->type == 1) echo get_name_of_oproperty($sv1->id) . " - " . get_name_of_oproperty($sv2->id); else echo get_name_of_dproperty($sv1->id) . " - " . get_name_of_dproperty($sv2->id) ?> </td>
            <td style="border:1px solid #111"><?php echo $tip ?> </td>
            <td style="border:1px solid #111"><?php echo $status ?> </td>
            <td style="border:1px solid #111"><?php echo $row->points ?> </td>
        </tr>
        <?php
    }
}
function read_property_equivalent($user_id, $module_id) {
    global $DB;
    $rows = $DB->get_records('ontology_property_equivalent', array('userid' => $user_id, 'course_modulesid' => $module_id));
    foreach ($rows as $row) {
        if ($row->status == 1)
            $status = get_string('Waiting_for_approval', 'ontology');
        if ($row->status == 2)
            $status = get_string('Accepted_in_ontology', 'ontology');
        if ($row->status == 3)
            $status = get_string('Accepted_not_in_ontology', 'ontology');
        if ($row->status == 4)
            $status = get_string('Rejected', 'ontology');
        if ($row->status == 5)
            $status = get_string('Accepted_not_in_ontology', 'ontology');
        if ($row->type == 1) {
            $sv1 = $DB->get_record('ontology_property_individual', array('id' => $row->ontology_propertyid));
            $sv2 = $DB->get_record('ontology_property_individual', array('id' => $row->ontology_propertyid2));
            $tip = get_string('Equivalent_object_property', 'ontology');
        } else {
            if ($row->type == 2)
                $sv1 = $DB->get_record('ontology_property_data', array('id' => $row->ontology_propertyid));
            $sv2 = $DB->get_record('ontology_property_data', array('id' => $row->ontology_propertyid2));
            $tip = get_string('Equivalent_data_property', 'ontology');
        }
        ?>
        <tr bgcolor="#C5D7FE">
            <td style="border:1px solid #111"><?php if ($row->type == 1) echo get_name_of_oproperty($sv1->id) . " - " . get_name_of_oproperty($sv2->id); else echo get_name_of_dproperty($sv1->id) . " - " . get_name_of_dproperty($sv2->id) ?> </td>
            <td style="border:1px solid #111"><?php echo $tip ?> </td>
            <td style="border:1px solid #111"><?php echo $status ?> </td>
            <td style="border:1px solid #111"><?php echo $row->points ?> </td>
        </tr>
        <?php
    }
}
function read_property_data($user_id, $module_id) {
    global $DB;
    $rows = $DB->get_records('ontology_property_data', array('userid' => $user_id, 'course_modulesid' => $module_id));
    $tip = get_string('Dproperty', 'ontology');
    ;
    foreach ($rows as $row) {
        if ($row->status == 1)
            $status = get_string('Waiting_for_approval', 'ontology');
        if ($row->status == 2)
            $status = get_string('Accepted_in_ontology', 'ontology');
        if ($row->status == 3)
            $status = get_string('Accepted_not_in_ontology', 'ontology');
        if ($row->status == 4)
            $status = get_string('Rejected', 'ontology');
        if ($row->status == 5)
            $status = get_string('Accepted_not_in_ontology', 'ontology');
        ?>
        <tr bgcolor="#E3FCE0">
            <td style="border:1px solid #111"><?php echo get_name_of_dproperty($row->id) ?> </td>
            <td style="border:1px solid #111"><?php echo $tip ?> </td>
            <td style="border:1px solid #111"><?php echo $status ?> </td>
            <td style="border:1px solid #111"><?php echo $row->points ?> </td>
        </tr>
        <?php
    }
}
function read_property_expression($user_id, $module_id) {
    global $DB;
    $rows = $DB->get_records('ontology_property_expression', array('userid' => $user_id, 'course_modulesid' => $module_id));
    foreach ($rows as $row) {
        if ($row->status == 1)
            $status = get_string('Waiting_for_approval', 'ontology');
        if ($row->status == 2)
            $status = get_string('Accepted_in_ontology', 'ontology');
        if ($row->status == 3)
            $status = get_string('Accepted_not_in_ontology', 'ontology');
        if ($row->status == 4)
            $status = get_string('Rejected', 'ontology');
        if ($row->status == 5)
            $status = get_string('Accepted_not_in_ontology', 'ontology');
        ?>

        <tr bgcolor="#FEF8EF">
        <?php
        // $klasa = $DB->get_record('ontology_class', array('id'=> $row->ontology_classid));
        if ($row->type == 1)
            $tip = get_string('Expression_for_domain_of_object_property', 'ontology');
        if ($row->type == 2)
            $tip = get_string('Expression_for_range_of_object_property', 'ontology');
        if ($row->type == 3)
            $tip = get_string('Expression_for_domain_of_data_property', 'ontology');
        ?>
            <td style="border:1px solid #111"><?php echo get_expression_in_color($row->expression) ?> </td>
            <td style="border:1px solid #111"><?php echo $tip ?> </td>
            <td style="border:1px solid #111"><?php echo $status ?> </td>
            <td style="border:1px solid #111"><?php echo $row->points ?> </td>
        </tr>
        <?php
    }
}
function read_individual_expression($user_id, $module_id) {
    global $DB;
    $rows = $DB->get_records('ontology_individual_expression', array('userid' => $user_id, 'course_modulesid' => $module_id));
    foreach ($rows as $row) {
        if ($row->status == 1)
            $status = get_string('Waiting_for_approval', 'ontology');
        if ($row->status == 2)
            $status = get_string('Accepted_in_ontology', 'ontology');
        if ($row->status == 3)
            $status = get_string('Accepted_not_in_ontology', 'ontology');
        if ($row->status == 4)
            $status = get_string('Rejected', 'ontology');
        if ($row->status == 5)
            $status = get_string('Accepted_not_in_ontology', 'ontology');
        $tip = get_string('Expression_for_superclass_of_an_individual', 'ontology');
        ?>
        <tr bgcolor="#FEF8EF">
        <?php
        $ind = $DB->get_record('ontology_individual', array('id' => $row->ontology_individualid));
        ?>
            <td style="border:1px solid #111"><?php echo get_expression_in_color($row->expression) . "  -  " . $ind->name ?> </td>
            <td style="border:1px solid #111"><?php echo $tip ?> </td>
            <td style="border:1px solid #111"><?php echo $status ?> </td>
            <td style="border:1px solid #111"><?php echo $row->points ?> </td>
        </tr>
        <?php
    }
}
function read_individual_property_individual($user_id, $module_id) {
    global $DB;
    $rows = $DB->get_records('ontology_individual_property_individual', array('userid' => $user_id, 'course_modulesid' => $module_id));
    foreach ($rows as $row) {
        if ($row->status == 1)
            $status = get_string('Waiting_for_approval', 'ontology');
        if ($row->status == 2)
            $status = get_string('Accepted_in_ontology', 'ontology');
        if ($row->status == 3)
            $status = get_string('Accepted_not_in_ontology', 'ontology');
        if ($row->status == 4)
            $status = get_string('Rejected', 'ontology');
        if ($row->status == 5)
            $status = get_string('Accepted_not_in_ontology', 'ontology');
        $tip = get_string('Object_property_of_an_individual', 'ontology');
        ?>
        <tr bgcolor="#EDEFEE">
        <?php
        $ind1 = $DB->get_record('ontology_individual', array('id' => $row->ontology_individualid));
        $ind2 = $DB->get_record('ontology_individual', array('id' => $row->ontology_individualid2));
        $sv = $DB->get_record('ontology_property_individual', array('id' => $row->ontology_propertyid));
        ?>
            <td style="border:1px solid #111"><?php echo get_name_of_individual($ind1->id) . " - " . get_name_of_oproperty($sv->id) . " - " . get_name_of_individual($ind2->id) ?> </td>
            <td style="border:1px solid #111"><?php echo $tip ?> </td>
            <td style="border:1px solid #111"><?php echo $status ?> </td>
            <td style="border:1px solid #111"><?php echo $row->points ?> </td>
        </tr>
        <?php
    }
}
function read_individual_property_data($user_id, $module_id) {
    global $DB;
    $rows = $DB->get_records('ontology_individual_property_data', array('userid' => $user_id, 'course_modulesid' => $module_id));
    foreach ($rows as $row) {
        if ($row->status == 1)
            $status = get_string('Waiting_for_approval', 'ontology');
        if ($row->status == 2)
            $status = get_string('Accepted_in_ontology', 'ontology');
        if ($row->status == 3)
            $status = get_string('Accepted_not_in_ontology', 'ontology');
        if ($row->status == 4)
            $status = get_string('Rejected', 'ontology');
        if ($row->status == 5)
            $status = get_string('Accepted_not_in_ontology', 'ontology');
        $tip = get_string('Data_property_of_an_individual', 'ontology');
        ?>
        <tr bgcolor="#E4E7E6">
        <?php
        $ind1 = $DB->get_record('ontology_individual', array('id' => $row->ontology_individualid));
        $sv = $DB->get_record('ontology_property_data', array('id' => $row->ontology_propertyid));
        ?>
            <td style="border:1px solid #111"><?php echo get_name_of_individual($ind1->id) . " - " . get_name_of_dproperty($sv->id) . " - " . $row->data ?> </td>
            <td style="border:1px solid #111"><?php echo $tip ?> </td>
            <td style="border:1px solid #111"><?php echo $status ?> </td>
            <td style="border:1px solid #111"><?php echo $row->points ?> </td>
        </tr>
            <?php
        }
    }
    function sortDataSet(&$dataSet) {
        $args = func_get_args();
        $callString = 'array_multisort(';
        $usedColumns = array();
        for ($i = 1, $count = count($args); $i < $count; ++$i) {
            switch (gettype($args[$i])) {
                case 'string':
                    $callString .= '$dataSet[\'' . $args[$i] . '\'], ';
                    array_push($usedColumns, $args[$i]);
                    break;
                case 'integer':
                    $callString .= $args[$i] . ', ';
                    break;
                default:
                    throw new Exception('expected string or integer, given ' . gettype($args[$i]));
            }
        }
        foreach ($dataSet as $column => $array) {
            if (in_array($column, $usedColumns))
                continue;
            $callString .= '$dataSet[\'' . $column . '\'], ';
        }
        eval(substr($callString, 0, -2) . ');');
    }
    function check_grades($module_id) {
        global $DB;
        $gradebook = $DB->get_record_sql('select * from mdl_grade_items where iteminstance=?', array($module_id));
        $graded = $DB->get_records_sql('select * from mdl_grade_grades where itemid=?', array($gradebook->id));
        foreach ($graded as $key => $value)
            if ($value->rawgrade != 0)
                return false;
        return true;
    }
    function studentska_proverka($courseorid = NULL, $autologinguest = true, $cm = NULL, $setwantsurltome = true, $preventredirect = false) {
        global $CFG, $SESSION, $USER, $FULLME, $PAGE, $SITE, $DB, $OUTPUT;

        // setup global $COURSE, themes, language and locale
        if (!empty($courseorid)) {
            if (is_object($courseorid)) {
                $course = $courseorid;
            } else if ($courseorid == SITEID) {
                $course = clone($SITE);
            } else {
                $course = $DB->get_record('course', array('id' => $courseorid), '*', MUST_EXIST);
            }
            if ($cm) {
                if ($cm->course != $course->id) {
                    throw new coding_exception('course and cm parameters in require_login() call do not match!!');
                }
                // make sure we have a $cm from get_fast_modinfo as this contains activity access details
                if (!($cm instanceof cm_info)) {
                    // note: nearly all pages call get_fast_modinfo anyway and it does not make any
                    // db queries so this is not really a performance concern, however it is obviously
                    // better if you use get_fast_modinfo to get the cm before calling this.
                    $modinfo = get_fast_modinfo($course);
                    $cm = $modinfo->get_cm($cm->id);
                }
                $PAGE->set_cm($cm, $course); // set's up global $COURSE
                $PAGE->set_pagelayout('incourse');
            } else {
                $PAGE->set_course($course); // set's up global $COURSE
            }
        } else {
            // do not touch global $COURSE via $PAGE->set_course(),
            // the reasons is we need to be able to call require_login() at any time!!
            $course = $SITE;
            if ($cm) {
                throw new coding_exception('cm parameter in require_login() requires valid course parameter!');
            }
        }

        // If the user is not even logged in yet then make sure they are
        if (!isloggedin()) {
            if ($autologinguest and !empty($CFG->guestloginbutton) and !empty($CFG->autologinguests)) {
                if (!$guest = get_complete_user_data('id', $CFG->siteguest)) {
                    // misconfigured site guest, just redirect to login page
                    redirect(get_login_url());
                    exit; // never reached
                }
                $lang = isset($SESSION->lang) ? $SESSION->lang : $CFG->lang;
                complete_user_login($guest, false);
                $USER->autologinguest = true;
                $SESSION->lang = $lang;
            } else {
                //NOTE: $USER->site check was obsoleted by session test cookie,
                //      $USER->confirmed test is in login/index.php
                if ($preventredirect) {
                    throw new require_login_exception('You are not logged in');
                }

                if ($setwantsurltome) {
                    // TODO: switch to PAGE->url
                    $SESSION->wantsurl = $FULLME;
                }
                if (!empty($_SERVER['HTTP_REFERER'])) {
                    $SESSION->fromurl = $_SERVER['HTTP_REFERER'];
                }
                redirect(get_login_url());
                exit; // never reached
            }
        }

        // loginas as redirection if needed
        if ($course->id != SITEID and session_is_loggedinas()) {
            if ($USER->loginascontext->contextlevel == CONTEXT_COURSE) {
                if ($USER->loginascontext->instanceid != $course->id) {
                    print_error('loginasonecourse', '', $CFG->wwwroot . '/course/view.php?id=' . $USER->loginascontext->instanceid);
                }
            }
        }

        // check whether the user should be changing password (but only if it is REALLY them)
        if (get_user_preferences('auth_forcepasswordchange') && !session_is_loggedinas()) {
            $userauth = get_auth_plugin($USER->auth);
            if ($userauth->can_change_password() and !$preventredirect) {
                $SESSION->wantsurl = $FULLME;
                if ($changeurl = $userauth->change_password_url()) {
                    //use plugin custom url
                    redirect($changeurl);
                } else {
                    //use moodle internal method
                    if (empty($CFG->loginhttps)) {
                        redirect($CFG->wwwroot . '/login/change_password.php');
                    } else {
                        $wwwroot = str_replace('http:', 'https:', $CFG->wwwroot);
                        redirect($wwwroot . '/login/change_password.php');
                    }
                }
            } else {
                print_error('nopasswordchangeforced', 'auth');
            }
        }

        // Check that the user account is properly set up
        if (user_not_fully_set_up($USER)) {
            if ($preventredirect) {
                throw new require_login_exception('User not fully set-up');
            }
            $SESSION->wantsurl = $FULLME;
            redirect($CFG->wwwroot . '/user/edit.php?id=' . $USER->id . '&amp;course=' . SITEID);
        }

        // Make sure the USER has a sesskey set up. Used for CSRF protection.
        sesskey();

        // Do not bother admins with any formalities
        if (is_siteadmin()) {
            //set accesstime or the user will appear offline which messes up messaging
            user_accesstime_log($course->id);
            return;
        }

        // Check that the user has agreed to a site policy if there is one - do not test in case of admins
        if (!$USER->policyagreed and !is_siteadmin()) {
            if (!empty($CFG->sitepolicy) and !isguestuser()) {
                if ($preventredirect) {
                    throw new require_login_exception('Policy not agreed');
                }
                $SESSION->wantsurl = $FULLME;
                redirect($CFG->wwwroot . '/user/policy.php');
            } else if (!empty($CFG->sitepolicyguest) and isguestuser()) {
                if ($preventredirect) {
                    throw new require_login_exception('Policy not agreed');
                }
                $SESSION->wantsurl = $FULLME;
                redirect($CFG->wwwroot . '/user/policy.php');
            }
        }

        // Fetch the system context, the course context, and prefetch its child contexts
        $sysctx = get_context_instance(CONTEXT_SYSTEM);
        $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id, MUST_EXIST);
        if ($cm) {
            $cmcontext = get_context_instance(CONTEXT_MODULE, $cm->id, MUST_EXIST);
        } else {
            $cmcontext = null;
        }

        // If the site is currently under maintenance, then print a message
        if (!empty($CFG->maintenance_enabled) and !has_capability('moodle/site:config', $sysctx)) {
            if ($preventredirect) {
                throw new require_login_exception('Maintenance in progress');
            }

            print_maintenance_message();
        }

        // make sure the course itself is not hidden
        if ($course->id == SITEID) {
            // frontpage can not be hidden
        } else {
            if (is_role_switched($course->id)) {
                // when switching roles ignore the hidden flag - user had to be in course to do the switch
            } else {
                if (!$course->visible and !has_capability('moodle/course:viewhiddencourses', $coursecontext)) {
                    // originally there was also test of parent category visibility,
                    // BUT is was very slow in complex queries involving "my courses"
                    // now it is also possible to simply hide all courses user is not enrolled in :-)
                    if ($preventredirect) {
                        throw new require_login_exception('Course is hidden');
                    }
                    notice(get_string('coursehidden'), $CFG->wwwroot . '/');
                }
            }
        }

        // is the user enrolled?
        if ($course->id == SITEID) {
            // everybody is enrolled on the frontpage
        } else {
            if (session_is_loggedinas()) {
                // Make sure the REAL person can access this course first
                $realuser = session_get_realuser();
                if (!is_enrolled($coursecontext, $realuser->id, '', true) and !is_viewing($coursecontext, $realuser->id) and !is_siteadmin($realuser->id)) {
                    if ($preventredirect) {
                        throw new require_login_exception('Invalid course login-as access');
                    }
                    echo $OUTPUT->header();
                    notice(get_string('studentnotallowed', '', fullname($USER, true)), $CFG->wwwroot . '/');
                }
            }

            // very simple enrolment caching - changes in course setting are not reflected immediately
            if (!isset($USER->enrol)) {
                $USER->enrol = array();
                $USER->enrol['enrolled'] = array();
                $USER->enrol['tempguest'] = array();
            }

            $access = false;

            if (is_viewing($coursecontext, $USER)) {
                // ok, no need to mess with enrol
                $access = true;
            } else {
                if (isset($USER->enrol['enrolled'][$course->id])) {
                    if ($USER->enrol['enrolled'][$course->id] == 0) {
                        $access = true;
                    } else if ($USER->enrol['enrolled'][$course->id] > time()) {
                        $access = true;
                    } else {
                        //expired
                        unset($USER->enrol['enrolled'][$course->id]);
                    }
                }
                if (isset($USER->enrol['tempguest'][$course->id])) {
                    if ($USER->enrol['tempguest'][$course->id] == 0) {
                        $access = true;
                    } else if ($USER->enrol['tempguest'][$course->id] > time()) {
                        $access = true;
                    } else {
                        //expired
                        unset($USER->enrol['tempguest'][$course->id]);
                        $USER->access = remove_temp_roles($coursecontext, $USER->access);
                    }
                }

                if ($access) {
                    // cache ok
                } else if (is_enrolled($coursecontext, $USER, '', true)) {
                    // active participants may always access
                    // TODO: refactor this into some new function
                    $now = time();
                    $sql = "SELECT MAX(ue.timeend)
                          FROM {user_enrolments} ue
                          JOIN {enrol} e ON (e.id = ue.enrolid AND e.courseid = :courseid)
                          JOIN {user} u ON u.id = ue.userid
                         WHERE ue.userid = :userid AND ue.status = :active AND e.status = :enabled AND u.deleted = 0
                               AND ue.timestart < :now1 AND (ue.timeend = 0 OR ue.timeend > :now2)";
                    $params = array('enabled' => ENROL_INSTANCE_ENABLED, 'active' => ENROL_USER_ACTIVE,
                        'userid' => $USER->id, 'courseid' => $coursecontext->instanceid, 'now1' => $now, 'now2' => $now);
                    $until = $DB->get_field_sql($sql, $params);
                    if (!$until or $until > time() + ENROL_REQUIRE_LOGIN_CACHE_PERIOD) {
                        $until = time() + ENROL_REQUIRE_LOGIN_CACHE_PERIOD;
                    }

                    $USER->enrol['enrolled'][$course->id] = $until;
                    $access = true;

                    // remove traces of previous temp guest access
                    $USER->access = remove_temp_roles($coursecontext, $USER->access);
                } else {
                    $instances = $DB->get_records('enrol', array('courseid' => $course->id, 'status' => ENROL_INSTANCE_ENABLED), 'sortorder, id ASC');
                    $enrols = enrol_get_plugins(true);
                    // first ask all enabled enrol instances in course if they want to auto enrol user
                    foreach ($instances as $instance) {
                        if (!isset($enrols[$instance->enrol])) {
                            continue;
                        }
                        // Get a duration for the guestaccess, a timestamp in the future or false.
                        $until = $enrols[$instance->enrol]->try_autoenrol($instance);
                        if ($until !== false) {
                            $USER->enrol['enrolled'][$course->id] = $until;
                            $USER->access = remove_temp_roles($coursecontext, $USER->access);
                            $access = true;
                            break;
                        }
                    }
                    // if not enrolled yet try to gain temporary guest access
                    if (!$access) {
                        foreach ($instances as $instance) {
                            if (!isset($enrols[$instance->enrol])) {
                                continue;
                            }
                            // Get a duration for the guestaccess, a timestamp in the future or false.
                            $until = $enrols[$instance->enrol]->try_guestaccess($instance);
                            if ($until !== false) {
                                $USER->enrol['tempguest'][$course->id] = $until;
                                $access = true;
                                break;
                            }
                        }
                    }
                }
            }

            if (!$access) {
                if ($preventredirect) {
                    throw new require_login_exception('Not enrolled');
                }
                $SESSION->wantsurl = $FULLME;
                redirect($CFG->wwwroot . '/enrol/index.php?id=' . $course->id);
            }
        }

        // Check visibility of activity to current user; includes visible flag, groupmembersonly,
        // conditional availability, etc
        if ($cm && !$cm->uservisible) {
            if ($preventredirect) {
                throw new require_login_exception('Activity is hidden');
            }
            redirect($CFG->wwwroot, get_string('activityiscurrentlyhidden'));
        }

        $nov_kurs_modul = $DB->get_record('course_modules', array('id' => $cm->id));
        if (!($nov_kurs_modul->availablefrom <= time() && $nov_kurs_modul->availableuntil >= time())) {
            redirect($CFG->wwwroot . '/course/view.php?id=' . $course->id, 'Активноста е затворена, беше отворена од ' . date("d.m.Y h:m", $nov_kurs_modul->availablefrom) . ' до ' . date("d.m.Y h:m", $nov_kurs_modul->availableuntil));
        }
    }
    function is_Teacher() {
        global $USER;
        global $DB;
        global $COURSE;
        $rAssign = $DB->get_records('role_assignments', array('userid' => $USER->id));
        foreach ($rAssign as $tmp) {
            $cont = $DB->get_records('context', array('id' => $tmp->contextid, 'instanceid' => $COURSE->id));
            if ($cont != null)
                if ($tmp->roleid == 3) {
                    return true;
                }
        }
        return false;
    }
?>