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
 * Prints a particular instance of newmodule
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package   mod_newmodule
 * @copyright 2010 Your Name
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/// (Replace newmodule with the name of your module and remove this line)

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n = optional_param('n', 0, PARAM_INT);  // newmodule instance ID - it should be named as the first character of the module
$PAGE->requires->js('/mod/ontology/js/jquery-1.5.1.min.js', true);
$PAGE->requires->js('/mod/ontology/js/jquery-ui-1.8.14.custom.min.js', true);
$PAGE->requires->css('/mod/ontology/css/redmond/jquery-ui-1.8.14.custom.css', true);
if ($id) {
    $cm = get_coursemodule_from_id('ontology', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $ontology = $DB->get_record('ontology', array('id' => $cm->instance), '*', MUST_EXIST);
} elseif ($n) {
    $ontology = $DB->get_record('ontology', array('id' => $n), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $ontology->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('ontology', $ontology->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);

add_to_log($course->id, 'ontology', 'view', "view.php?id=$cm->id", $ontology->name, $cm->id);

/// Print the page header

$PAGE->set_url('/mod/ontology/view.php', array('id' => $cm->id));
$PAGE->set_title($ontology->name);
$PAGE->set_heading($course->shortname);
$PAGE->set_button(update_module_button($cm->id, $course->id, get_string('modulename', 'ontology')));

// other things you may want to set - remove if not needed
//$PAGE->set_cacheable(false);
//$PAGE->set_focuscontrol('some-html-id');
// Output starts here
echo $OUTPUT->header();
echo '<div class="ui-widget-header ui-corner-all">';
echo $OUTPUT->heading(get_string('students_overview', 'ontology'));
echo '</div>';
?>
<script>
    $(function() {
        $(".btn").button();
        $( "a",".links").button();
    });
</script>
<div class="ui-dialog-content ui-widget-content ui-corner-all" id="dialog" style="padding-left:30px; padding-right: 30px;"> 
    <br />
<?php
if (check_students_entry($id) == true) {
    ?>

        <div style="font-size: 15px;"><?php echo get_string('students_participated_and_waiting', 'ontology'); ?>: </div>

        <table class="ui-widget ui-widget-content">
            <tr bgcolor="#DEDEDE" align="center" class="ui-widget-header">
                <td>
    <?php echo get_string('profile', 'ontology'); ?>
                </td>
                <td>
                    <?php echo get_string('name_and_surname', 'ontology'); ?>
                </td>
                <td>
                    <?php echo get_string('examination', 'ontology'); ?>
                </td>
                <td>
                    <?php echo get_string('note', 'ontology'); ?>
                </td>
                <td>
                    <?php echo get_string('left', 'ontology'); ?>
                </td>
                <td>
                    <?php echo get_string('rating', 'ontology'); ?>
                </td>
                <td>
                    <?php echo get_string('accepted', 'ontology'); ?>
                </td>
                <td>
                    <?php echo get_string('refused', 'ontology'); ?>
                </td>
                <td>
                    <?php echo get_string('penalty', 'ontology'); ?>
                </td>
            </tr>

                    <?php
                    read_students($id, $OUTPUT);
                    ?>
        </table>
        <?php
        } else {
            ?>
        <div class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0 .7em;" style="width: 100px;"> 
            <p>
                <br />
                <span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>

    <?php echo get_string('nothing_to_evaluate', 'ontology'); ?></p>
        </div>
    <?php
}

if (check_finished_students($id) == true) {
    ?>

        <br/>
        <div style="font-size: 15px;"> <?php echo get_string('eval_fin_for_students', 'ontology'); ?> </div>
        <table border="2" class="ui-widget ui-widget-content">
            <tr bgcolor="#DEDEDE" align="center" class="ui-widget-header ">
                <td>
    <?php echo get_string('profile', 'ontology'); ?>
                </td>
                <td>
    <?php echo get_string('name_and_surname', 'ontology'); ?>
                </td>
                <td>
                    <?php echo get_string('examination', 'ontology'); ?>
                </td>
                <td>
                    <?php echo get_string('note', 'ontology'); ?>
                </td>
                <td>
                    <?php echo get_string('left', 'ontology'); ?>
                </td>
                <td>
                    <?php echo get_string('rating', 'ontology'); ?>
                </td>
                <td>
                    <?php echo get_string('accepted', 'ontology'); ?>
                </td>
                <td>
                    <?php echo get_string('refused', 'ontology'); ?>
                </td>
                <td>
                    <?php echo get_string('penalty', 'ontology'); ?>
                </td>
            </tr>

    <?php
    read_finished_students($id, $OUTPUT);
    ?>
        </table>

        <?php
        }
        if (check_non_active_students($id)) {
            ?>
        <br/>
        <div style="font-size: 15px;"> <?php echo get_string('students_that_did_not_participated_in_this_activity', 'ontology'); ?> </div>
        <table border="2" class="ui-widget ui-widget-content">
            <tr bgcolor="#DEDEDE" align="center" class="ui-widget-header ">
                <td>
        <?php echo get_string('profile', 'ontology'); ?>
                </td>
                <td>
    <?php echo get_string('name_and_surname', 'ontology'); ?>
                </td>
                <td>
                    <?php echo get_string('rating', 'ontology'); ?>
                </td>
            </tr>
                    <?php
                    read_non_active_students($id, $OUTPUT);
                    ?>
        </table>
<?php
}
else
    echo '<div style="font-size: 15px;"> ' . get_string('in_this_activity_all_students_participated', 'ontology') . ' </div>';
?>
    <br />
</div>
<script type="text/javascript">
    function f1(){
        window.location= <?php echo "\"teacherbuildclasses.php?id=" . $id . "\"" ?>
    }
    function f2(){
        window.location= <?php echo "\"teacherbuildoproperties.php?id=" . $id . "\"" ?>
    }
    function f3(){
        window.location= <?php echo "\"teacherbuilddproperties.php?id=" . $id . "\"" ?>
    }
    function f4(){
        window.location= <?php echo "\"teacherbuildindividuals.php?id=" . $id . "\"" ?>
    }
    function f5(){
        window.location= <?php echo "\"grading.php?id=" . $id . "\"" ?>
    }
    function s1(){
        window.location= <?php echo "\"classes.php?id=" . $id . "\"" ?>
    }
    function s2(){
        window.location= <?php echo "\"oproperties.php?id=" . $id . "\"" ?>
    }
    function s3(){
        window.location= <?php echo "\"dproperties.php?id=" . $id . "\"" ?>
    }
    function s4(){
        window.location= <?php echo "\"individuals.php?id=" . $id . "\"" ?>
    }
                        
</script>
<br />
<table style="margin-left: 25px;"> 
    <tr>
        <td>
            <table class="ui-widget ui-widget-content">
                <tr class="ui-widget-header ui-corner-all ">
                    <td colspan="3">
<?php echo get_string('adding_a_notion_to_ontology', 'ontology'); ?>
                    </td>
                </tr>
                <tr style="border-bottom: 1px solid #7CAFFC;">
                    <td>
                        <button onclick="f1();" style="width: 200px;" class="btn"><?php echo get_string('classes', 'ontology'); ?></button></td>
<?php
global $DB;
$ontologyid = $DB->get_record('course_modules', array('id' => $id)); //id na ontologijata
$ontology = $DB->get_record('modules', array('name' => 'ontology')); //id na modulot ontologija
$classes = $DB->get_records_sql('SELECT * FROM mdl_ontology_class WHERE status=2 AND course_modulesid IN (SELECT id FROM mdl_course_modules WHERE instance=? AND module=?)', array($ontologyid->instance, $ontology->id));
$br = 0;
foreach ($classes as $key => $value) {
    $expressions = $DB->get_records('ontology_class_expression', array('status' => '3', 'ontology_classid' => $key));
    if (count($expressions) > 0)
        $br++;
}
$classes2 = $DB->get_records('ontology_class', array('status' => '3', 'course_modulesid' => $id));
if ($br > 0) {
    echo '<td><i>*' . get_string('there_are', 'ontology') . ' <b>' . $br . '</b> ' . get_string('updated_classes', 'ontology') . '</i></td> ';
    if (count($classes2) == 0)
        echo '<td></td>';
}

if (count($classes2) > 0) {
    echo '<td><i>*' . get_string('there_are', 'ontology') . ' <b>' . count($classes2) . '</b> ' . get_string('new_classes', 'ontology') . '</i></td> ';
    if ($br == 0)
        echo '<td></td>';
}
if ($br == 0 && count($classes2) == 0)
    echo '<td colspan="2"><i>*' . get_string('examination_finished', 'ontology') . '</i></td>';
?>
                </tr>
                <tr style="border-bottom:1px solid #7CAFFC;">
                    <td>
                        <button class="btn" onclick="f2();" style=" width: 200px;"><?php echo get_string('object_properties', 'ontology'); ?> </button></td>
                    <?php
                    global $DB;
                    $ontologyid = $DB->get_record('course_modules', array('id' => $id)); //id na ontologijata
                    $ontology = $DB->get_record('modules', array('name' => 'ontology')); //id na modulot ontologija
                    $properties = $DB->get_records_sql('SELECT * FROM mdl_ontology_property_individual WHERE status=2 AND course_modulesid IN (SELECT id FROM mdl_course_modules WHERE instance=? AND module=?)', array($ontologyid->instance, $ontology->id));
                    $br2 = 0;
                    foreach ($properties as $key => $value) {
                        $expressions = $DB->get_records('ontology_property_expression', array('status' => '3', 'ontology_propertyid' => $key, 'type' => '1'));
                        $expressions2 = $DB->get_records('ontology_property_expression', array('status' => '3', 'ontology_propertyid' => $key, 'type' => '2'));

                        $equ_properties = $DB->get_records('ontology_property_equivalent', array('status' => '3', 'ontology_propertyid' => $key, 'type' => '1'));
                        $dis_properties = $DB->get_records('ontology_property_disjoint', array('status' => '3', 'ontology_propertyid' => $key, 'type' => '1'));
                        if (count($expressions) > 0 || count($expressions2) > 0 || count($equ_properties) > 0 || count($dis_properties) > 0)
                            $br2++;
                    }
                    $properties2 = $DB->get_records('ontology_property_individual', array('status' => '3', 'course_modulesid' => $id));
                    if ($br2 > 0) {
                        echo '<td><i>*' . get_string('there_are', 'ontology') . ' <b>' . $br2 . '</b> ' . get_string('updated_object_properties', 'ontology') . '</i></td> ';
                        if (count($properties2) == 0)
                            echo '<td></td>';
                    }

                    if (count($properties2) > 0) {

                        echo '<td><i>*' . get_string('there_are', 'ontology') . ' <b>' . count($properties2) . '</b> ' . get_string('new_object_properties', 'ontology') . '</i></td> ';
                        if ($br2 == 0)
                            echo '<td></td>';
                    }
                    if ($br2 == 0 && count($properties2) == 0)
                        echo '<td colspan="2"><i>*' . get_string('examination_finished', 'ontology') . '.</i> </td>';
                    ?>
                </tr>
                <tr style="border-bottom:1px solid #7CAFFC;">
                    <td >
                        <button class="btn" onclick="f3();"  style=" width: 200px;"><?php echo get_string('data_properties', 'ontology'); ?></button></td>
                    <?php
                    global $DB;
                    $properties = $DB->get_records_sql('SELECT * FROM mdl_ontology_property_data WHERE status=2 AND course_modulesid IN (SELECT id FROM mdl_course_modules WHERE instance=? AND module=?)', array($ontologyid->instance, $ontology->id));
                    $br3 = 0;
                    foreach ($properties as $key => $value) {
                        $expressions = $DB->get_records('ontology_property_expression', array('status' => '3', 'ontology_propertyid' => $key, 'type' => '3'));
                        $equ_properties = $DB->get_records('ontology_property_equivalent', array('status' => '3', 'ontology_propertyid' => $key, 'type' => '2'));
                        $dis_properties = $DB->get_records('ontology_property_disjoint', array('status' => '3', 'ontology_propertyid' => $key, 'type' => '2'));
                        if (count($expressions) > 0 || count($equ_properties) > 0 || count($dis_properties) > 0)
                            $br3++;
                    }
                    $properties3 = $DB->get_records('ontology_property_data', array('status' => '3', 'course_modulesid' => $id));
                    if ($br3 > 0) {
                        echo '<td><i>*' . get_string('there_are', 'ontology') . ' <b>' . $br3 . '</b> ' . get_string('updated_data_properties', 'ontology') . '</i></td>';
                        if (count($properties3) == 0)
                            echo '<td></td>';
                    }

                    if (count($properties3) > 0) {
                        echo '<td><i>*' . get_string('there_are', 'ontology') . ' <b>' . count($properties3) . '</b> ' . get_string('new_data_properties', 'ontology') . '</i></td> ';
                        if ($br3 == 0)
                            echo '<td></td>';
                    }
                    if ($br3 == 0 && count($properties3) == 0)
                        echo '<td colspan="2"><i>*' . get_string('examination_finished', 'ontology') . '</i> </td>';
                    ?>
                </tr>
                <tr style="border-bottom:1px solid #7CAFFC;">
                    <td>
                        <button class="btn" onclick="f4();"  style=" width: 200px;"><?php echo get_string('individuals', 'ontology'); ?></button></td>
                    <?php
                    $individuals = $DB->get_records_sql('SELECT * FROM mdl_ontology_individual WHERE status=2 AND course_modulesid IN (SELECT id FROM mdl_course_modules WHERE instance=? AND module=?)', array($ontologyid->instance, $ontology->id));
                    $br4 = 0;
                    foreach ($individuals as $key => $value) {
                        $expressions = $DB->get_records('ontology_individual_expression', array('status' => '3', 'ontology_individualid' => $key));
                        $o_properties = $DB->get_records('ontology_individual_property_individual', array('status' => '3', 'ontology_individualid' => $key));
                        $d_properties = $DB->get_records('ontology_individual_property_data', array('status' => '3', 'ontology_individualid' => $key));
                        if (count($expressions) > 0 || count($o_properties) > 0 || count($d_properties) > 0)
                            $br4++;
                    }
                    $individuals2 = $DB->get_records('ontology_individual', array('status' => '3', 'course_modulesid' => $id));
                    if ($br4 > 0) {
                        echo '<td><i>*' . get_string('there_are', 'ontology') . ' <b>' . $br4 . '</b> ' . get_string('updated_individuals', 'ontology') . ' </i></td>';
                        if (count($individuals2) == 0)
                            echo '<td></td>';
                    }
                    if (count($individuals2) > 0) {
                        echo '<td><i>*' . get_string('there_are', 'ontology') . ' <b>' . count($individuals2) . '</b> ' . get_string('new_individuals', 'ontology') . '</id></td>';
                        if ($br4 == 0)
                            echo '<td></td>';
                    }
                    if ($br4 == 0 && count($individuals2) == 0)
                        echo '<td colspan="2"><i>*' . get_string('examination_finished', 'ontology') . '</i></td>';
                    ?>
                </tr>
                <tr style="border-bottom:1px solid #7CAFFC;">
                    <td colspan="3">
                    <?php
                    if ($br == 0 && count($classes2) == 0 && $br2 == 0 && count($properties2) == 0 && $br3 == 0 && count($properties3) == 0 && $br4 == 0 && count($individuals2) == 0) {
                        echo '<button style="color: red" onclick="f5();">' . get_string('grade_the_students', 'ontology') . '</button>';
                    } else {
                        echo '<i><font style=" color: red">' . get_string('you_still_can_not_grade_the_students', 'ontology') . '</font></i>';
                    }
                    ?>

                    </td>
                </tr>
            </table>
        </td>
        <td valign="top">
            <table class="ui-widget ui-widget-content" align="center">
                <tr class="ui-widget-header ">
                    <td align="center">
<?php echo get_string('checking_and_adding_notions_to_ontology', 'ontology'); ?>

                    </td>
                </tr>
                <tr style="border-bottom:1px solid #7CAFFC;">
                    <td align="center">
                        <button class="btn" onclick="s1();"  style=" width: 300px;"><?php echo get_string('class_review', 'ontology'); ?></button>
                    </td>
                </tr>
                <tr style="border-bottom:1px solid #7CAFFC;">
                    <td align="center">
                        <button class="btn" onclick="s2();"  style=" width: 300px;"><?php echo get_string('object_property_review', 'ontology'); ?></button>
                    </td>
                </tr>
                <tr style="border-bottom:1px solid #7CAFFC;">
                    <td align="center">
                        <button class="btn" onclick="s3();"  style=" width: 300px;"><?php echo get_string('data_property_review', 'ontology'); ?></button>
                    </td>
                </tr>
                <tr style="border-bottom:1px solid #7CAFFC;">
                    <td align="center">
                        <button class="btn" onclick="s4();"  style=" width: 300px;"><?php echo get_string('instances_review', 'ontology'); ?></button>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>      

<?php
echo $OUTPUT->footer();
?>