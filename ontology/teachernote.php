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
echo $OUTPUT->heading(get_string('note_for_the_student', 'ontology'));
echo '</div>';
global $DB;
$grditem = $DB->get_record('grade_items', array('itemmodule' => 'ontology', 'iteminstance' => $id));
$userid = $_GET['userid'];
$grdgrade = $DB->get_records('grade_grades', array('itemid' => $grditem->id, 'userid' => $userid));
?>
<style>
    .ui-dialog {
        overflow: hidden;
        padding: 0.2em;
        position: absolute;
        width: 400px;
        height: 150px;
    }
    .ui-dialog-content {
        background: none repeat scroll 0 0 transparent;
        border: 0 none;
        overflow: auto;
        padding: 0.5em 1em;
        position: relative;
    }             
</style>
<table width="100%">
    <tr>
        <td width="40%" valign="top">
            <br />
            <div class="ui-dialog ui-widget ui-widget-content ui-corner-all">
                <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
                    <span id="ui-dialog-title-dialog" class="ui-dialog-title"><center>
<?php
echo get_string('note', 'ontology');
?>
                        </center></span>
                </div>
                <div class="ui-dialog-content ui-widget-content" style="height: 83px;"> 
                            <?php
                            if (count($grdgrade) == 0)
                                echo '<input type="text" id="note" style="width:350px; height:30px" class=" ui-widget ui-state-hover" /><br />';
                            else
                                foreach ($grdgrade as $key => $value) {
                                    echo '<input type="text" id="note" value="' . $value->feedback . '" class=" ui-widget ui-state-hover" style="width:350px; height:30px"/> <br />';
                                    break;
                                }
                            ?>
                    <br />
                    <input id="ok_btn" type="button" value="<?php echo get_string('OK', 'ontology'); ?>" onclick="ajax();"/>
                    <input id="otkazi_btn" type="button" value="<?php echo get_string('cancel', 'ontology'); ?>" onclick="back();"/>
                </div>
            </div>
        </td>
        <td width="50%" valign="top">
            <div>
                <br />
<?php
echo get_string('student_note_description', 'ontology');
?>
            </div>
        </td>
        <td width="10%"></td>
    </tr>
</table>


<script type="text/javascript">
    function back(){
        window.location= <?php echo "\"teacherstudents.php?id=" . $id . "\"" ?>
    }
    function ajax()
    {
        if (window.XMLHttpRequest)
        {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
        else
        {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function()
        {
            if (xmlhttp.readyState==4)
            {
                document.getElementById("Region1").innerHTML=xmlhttp.responseText;
            }
        }
        xmlhttp.open("GET","teachernotewrite.php?id="+document.getElementById("id").value+"&userid="+document.getElementById("userid").value+"&note="+document.getElementById("note").value,true);
        xmlhttp.send();
        setTimeout("back()",200);
    }
    $(function() {
        $( "#ok_btn").button();
        $( "#otkazi_btn").button();
    });
</script>

<input type="hidden" value="<?php echo $id ?>" id="id" />
<input type="hidden" value="<?php echo $userid ?>" id="userid" />
<div id="Region1"></div>
<?php
echo $OUTPUT->footer();