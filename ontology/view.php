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
global $DB;

// Replace the following lines with you own code
//$ontology=$DB->get_record('course_modules',array('id'=>$id));
$ontologymodule = $DB->get_record('modules', array('name' => 'ontology'));
$cmcount = $DB->get_records_sql('SELECT * FROM mdl_course_modules WHERE module=? AND instance=? AND id<?;', array($ontologymodule->id, $cm->instance, $id));
echo '<div class="ui-widget-header ui-corner-all">';
echo $OUTPUT->heading($ontology->name . ', ' . get_string('Activity_number', 'ontology') . ' ' . (count($cmcount) + 1)) . '</div>';
//$reader->readFromFile($owl_file, $ontology

$rAssign = $DB->get_records('role_assignments', array('userid' => $USER->id));
foreach ($rAssign as $tmp) {
    $cont = $DB->get_records('context', array('id' => $tmp->contextid, 'instanceid' => $course->id));
    if ($cont != null) {
        switch ($tmp->roleid) {
            case 3:
                $user = $DB->get_record('user', array('id' => $USER->id), '*', MUST_EXIST);
                echo '<table><tr><td>' . $OUTPUT->user_picture($user, array('size' => 40)) . '</td><td> ' . get_string('Welcome_professor', 'ontology') . ' ' . $user->firstname . ' ' . $user->lastname . '</td></tr></table>';
                //kod za profesorot
                ?>
                <script>
                    $(function() {
                        $("input:button").button();
                        $("input:text").button();
                        $( "a",".links").button();
                    });
                </script>
            
                <a style="padding: .5em 1em; text-decoration: none;" class="ui-state-default ui-corner-all" href=" <?php echo "teacherstudents.php?id=" . $id ?> " ><?php echo get_string('View_all_terms', 'ontology'); ?></a>
                <a style="padding: .5em 1em; text-decoration: none;" href=" <?php echo "export_to_owl.php?id=" . $id ?> " class="ui-state-default ui-corner-all"><?php echo get_string('Export_to_owl', 'ontology'); ?></a>
                
               <br /> <br />
                <div class="ui-widget ui-widget-content ui-corner-all latest" style="width:272px;height:100px">
                                            <div class="ui-widget-header ui-corner-all "  style="width: 250px; height: 30px; margin-left: 3px; margin-top: 3px;margin-right: 3px; padding-left: 15px;padding-top: 2px; vertical-align: middle;">
                                                <span ><?php echo get_string('Activity_duration', 'ontology') . ':'; ?></span>
                                            </div>
                                            <div class="" style="width: 250px; height: 83px;"> 
                                                <table width="250px" style="vertical-align: middle; width: 250px;">

                    <?php
                    global $DB;
                    $moduleid = $DB->get_record('course_modules', array('id' => $id));
    
                    echo '<td> <b>' . get_string('Start', 'ontology') . ':</b> </td> <td>' . date('H:i d.m.Y', $moduleid->availablefrom) . ' </td> </tr> <tr> <td> <b>' . get_string('End', 'ontology') . ':</b> </td> <td>' . date('H:i d.m.Y', $moduleid->availableuntil) . '</td> </tr> </table> </div> </div>';
                    ?>
                        <br />  
                                         
                                        <div  class=" ui-widget ui-widget-content ui-corner-all latest" style="width: 900px;">
                                            <div class=" ui-widget-header ui-corner-all" style="width: 875px; height: 30px; margin-left: 3px; margin-top: 3px;margin-right: 3px; padding-left: 15px;padding-top: 2px; vertical-align: middle;">
                                                <span id="ui-dialog-title-dialog" class=""><?php echo get_string('activity_description', 'ontology') . ':'; ?></span>
                                            </div>
                                            <div class="" style="width: 900px;"> 
                                                <table width="900px" style="vertical-align: middle;">

                                        <?php
                                        global $DB;
                                        $description=$DB->get_record('ontology', array('id' => $ontology->id,'course'=>$course->id));
                                        echo '<tr> <td>' . $description->intro . '</td> </tr>';
                                        echo '</table></div></div>';
                                        ?>
                                        <br /><br />
                                        <div style="color: gray;"> <?php echo get_string('version', 'ontology') ?> 1.03 </div>
                                        <div style="color: gray;"> <?php echo get_string('faculty_name', 'ontology') ?>  </div>
                                       
                                       
                
                        <br /> 
                 
                                        
                <?php
                break;
            case 5:
                //  echo $OUTPUT->heading($ontology->name);
                // echo $ontology->intro;
                //kod za studentot
                $nov_kurs_modul = $DB->get_record('course_modules', array('id' => $cm->id));
                if ($nov_kurs_modul->availablefrom <= time() && $nov_kurs_modul->availableuntil >= time()) {
                    ?>

                                <h2> <?php echo get_string('Choose', 'ontology') . ':'; ?> </h2>
                                <form method="get" name="form" >
                                    &nbsp;&nbsp;
                                    <a style="padding: .5em 1em; text-decoration: none;" href=" <?php echo "classes.php?id=" . $id ?> " class="ui-state-default ui-corner-all"><?php echo get_string('View_classes', 'ontology'); ?></a>
                                    &nbsp;&nbsp;
                                    <a style="padding: .5em 1em; text-decoration: none;" href=" <?php echo "oproperties.php?id=" . $id ?> " class="ui-state-default ui-corner-all"><?php echo get_string('View_oproperties', 'ontology'); ?></a>
                                    &nbsp;&nbsp;
                                    <a style="padding: .5em 1em; text-decoration: none;" href=" <?php echo "dproperties.php?id=" . $id ?> " class="ui-state-default ui-corner-all"><?php echo get_string('View_dproperties', 'ontology'); ?></a>
                                    &nbsp;&nbsp;
                                    <a style="padding: .5em 1em; text-decoration: none;" href=" <?php echo "individuals.php?id=" . $id ?> " class="ui-state-default ui-corner-all"><?php echo get_string('View_individuals', 'ontology'); ?></a>
                                </form>
                    <?php
                }
                if ($nov_kurs_modul->availableuntil < time()) {
                    ?>
                                <div class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0 .7em;"> 
                                    <p>
                                        <br />
                                        <span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
                                <?php echo get_string('Closed_activity', 'ontology'); ?></p>
                                </div>
                    <?php
                }
                global $USER;
                ?>
                            <style>
                                .latest { overflow:visible; position:static; }
                            </style>
                            <?php
                            if (user_entry($USER->id, $id) == true) {
                                ?>
                                <br/><br/>
                                <b>
                                <?php echo get_string('Inputted_terms', 'ontology') . ':'; ?>
                                </b>
                                <table border="2"  width="900px" class="ui-widget ui-widget-content">
                                    <tr bgcolor="#DEDEDE" class="ui-widget-header">
                                        <td>
                                <?php echo get_string('Name', 'ontology') . ':'; ?>
                                        </td>
                                        <td>
                                    <?php echo get_string('Type', 'ontology') . ':'; ?>
                                        </td>
                                        <td>
                    <?php echo get_string('Status', 'ontology') . ':'; ?>
                                        </td>
                                        <td>
                                            <?php echo get_string('Points', 'ontology') . ':'; ?>
                                        </td>
                                    </tr>
                                            <?php
                                            read_class($USER->id, $id);
                                            read_individual($USER->id, $id);
                                            read_class_expression($USER->id, $id);
                                            read_property_individual($USER->id, $id);
                                            read_property_disjoint($USER->id, $id);
                                            read_property_equivalent($USER->id, $id);
                                            read_property_data($USER->id, $id);
                                            read_property_expression($USER->id, $id);
                                            read_individual_expression($USER->id, $id);
                                            read_individual_property_individual($USER->id, $id);
                                            read_individual_property_data($USER->id, $id);
                                            ?> </table>
                                    <?php
                                } else {
                                    echo '<br/><br/>' . get_string('No_inputted_terms', 'ontology') . '<br/>';
                                }
                                ?>
                            <div align="center" class="ui-dialog ui-widget ui-widget-content ui-corner-all latest" style="width: 900px;">
                                <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
                                    <span id="ui-dialog-title-dialog" class="ui-dialog-title"><?php echo get_string('Teacher_comments', 'ontology') . ':'; ?></span>
                                    <a class="ui-dialog-titlebar-close ui-corner-all" href="#"></a>
                                </div>
                                <div class="ui-dialog-content ui-widget-content" id="dialog">
                                    <table width="100%">


                            <?php
                            $grditem = $DB->get_record('grade_items', array('itemmodule' => 'ontology', 'iteminstance' => $id));
                            $userid = $USER->id;
                            $grdgrade = $DB->get_records('grade_grades', array('itemid' => $grditem->id, 'userid' => $userid));
                            if (count($grdgrade) == 0)
                                echo '<tr> <td>' . get_string('No_comments', 'ontology') . ' </td> </tr> </table></div></div>';
                            else {
                                foreach ($grdgrade as $key => $value) {
                                    echo '	<tr> <td>' . $value->feedback . '</td> </tr>';
                                    break;
                                }
                                echo '</table></div></div>';
                            }
                            ?>
                                        <br />
                                        <div align="center" class="ui-dialog ui-widget ui-widget-content ui-corner-all latest" style="width: 900px;">
                                            <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
                                                <span id="ui-dialog-title-dialog" class="ui-dialog-title"><?php echo get_string('activity_description', 'ontology') . ':'; ?></span>
                                            </div>
                                            <div class="ui-dialog-content ui-widget-content" style="width: 900px;"> 
                                                <table width="900px" style="vertical-align: middle;">

                                        <?php
                                        global $DB;
                                        $description=$DB->get_record('ontology', array('id' => $ontology->id,'course'=>$course->id));
                                        echo '<tr> <td>' . $description->intro . '</td> </tr>';
                                        echo '</table></div></div>';
                                        ?>
                                        
                                        
                                        <br />
                                        <div class="ui-dialog ui-widget ui-widget-content ui-corner-all latest" style="width=900px; height:115px ;">
                                            <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
                                                <span id="ui-dialog-title-dialog" class="ui-dialog-title"><?php echo get_string('Activity_duration', 'ontology') . ':'; ?></span>
                                            </div>
                                            <div class="ui-dialog-content ui-widget-content" style="height: 83px;"> 
                                                <table width="250px" style="vertical-align: middle;">
                                        
                                        <?php
                                        global $DB;
                                        $moduleid = $DB->get_record('course_modules', array('id' => $id));
                                        echo '<tr style=" border-bottom:1px solid #ADD7EF;"> <td>' . get_string('Start', 'ontology') . ': </td> <td>' . date('H:i d.m.Y', $moduleid->availablefrom) . ' </td> </tr>';
                                        echo '<tr> <td>' . get_string('End', 'ontology') . ': </td> <td>' . date('H:i d.m.Y', $moduleid->availableuntil) . '</td> </tr>';
                                        echo '</table></div></div>';
                                        ?>
                                        
                <?php
                break;
            default:
                break;
        }
    }
}

function subval_sort($a, $subkey) {
    foreach ($a as $k => $v) {
        $b[$k] = strtolower($v[$subkey]);
    }
    asort($b);
    foreach ($b as $key => $val) {
        $c[] = $a[$key];
    }
    return $c;
}

// Finish the page
echo $OUTPUT->footer();
?>